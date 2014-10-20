<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 将设置信息保存至数据库，将会同时更新全局变量 $_W['setting']，过期缓存
 * @param mixed $data 如果提供 $data，则将 $data 做为指定键的 $key 的值来更新
 * @param string $key 如果提供 $key，则至更新指定键名
 * @return void
 */
function setting_save($data = '', $key = '') {
	if (empty($data) && empty($key)) {
		return FALSE;
	}
	if (is_array($data) && empty($key)) {
		foreach ($data as $key => $value) {
			$record[] = "('$key', '".iserializer($value)."')";
		}
		if ($record) {
			$return = pdo_query("REPLACE INTO ".tablename('settings')." (`key`, `value`) VALUES " . implode(',', $record));
		}
	} else {
		$record = array();
		$record['key'] = $key;
		$record['value'] = iserializer($data);
		$return = pdo_insert('settings', $record, TRUE);
	}
	cache_build_setting();
	return $return;
}

function setting_cache_account_by_uid($uid) {
	global $_W;
	cache_build_account($uid);
	cache_load("account:{$uid}");

	if (!empty($_W['cache']['account'][$uid])) {
		foreach ($_W['cache']['account'][$uid] as $weid => $wechat) {
			cache_build_category($weid);
		}
	}
	cache_build_hook($uid);
}
function setting_cache_account_by_founder() {
	$users = pdo_fetchall("SELECT uid FROM ".tablename('members') . " WHERE status > '-1'", array(), 'uid');
	if (!empty($users)) {
		foreach ($users as $uid => $user) {
			setting_cache_account_by_uid($uid);
		}
	}
}

function setting_module_manifest($modulename) {
	$manifest = array();
	$filename = IA_ROOT . '/source/modules/' . $modulename . '/manifest.xml';
	if (!file_exists($filename)) {
		return array();
	}
	$xml = str_replace(array('&'), array('&amp;'), file_get_contents($filename));
	$xml = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	if (empty($xml)) {
		return array();
	}
	$attributes = $xml->attributes();
	$manifest['version'] = strval($attributes['versionCode']);
	$manifest['install'] = strval($xml->install);
	$manifest['uninstall'] = strval($xml->uninstall);
	$manifest['upgrade'] = strval($xml->upgrade);
	$attributes = $xml->application->attributes();
	$manifest['application'] = array(
		'name' => strval($xml->application->name),
		'identifie' => strval($xml->application->identifie),
		'version' => strval($xml->application->version),
		'ability' => strval($xml->application->ability),
		'description' => strval($xml->application->description),
		'author' => strval($xml->application->author),
		'setting' => strval($attributes['setting']) == 'true',
	);
	$hooks = @(array)$xml->hooks->children();
	if (!empty($hooks['hook'])) {
		foreach ((array)$hooks['hook'] as $hook) {
			$manifest['hooks'][strval($hook['name'])] = strval($hook['name']);
		}
	}
	$menus = @(array)$xml->menus->children();
	if (!empty($menus['menu'])) {
		foreach ((array)$menus['menu'] as $menu) {
			$manifest['menus'][] = array(strval($menu['name']), strval($menu['value']));
		}
	}
	return $manifest;
}

