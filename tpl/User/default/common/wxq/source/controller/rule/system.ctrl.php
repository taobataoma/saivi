<?php
/**
 * 系统回复
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
$do = !empty($_GPC['do']) && in_array($_GPC['do'], array('display', 'set', 'cancel')) ? $_GPC['do'] : 'display';
if ($do == 'display') {
	if (checksubmit('submit')) {
		$settings = array(
			'default_period' => intval($_GPC['default-period']),
		);
		isset($_GPC['default']) && $settings['default'] = $_GPC['default'];
		isset($_GPC['welcome']) && $settings['welcome'] = $_GPC['welcome'];
		pdo_update('wechats', $settings, array('weid' => $_W['weid']));
		cache_build_account();
		message('系统回复更新成功！', create_url('rule/system'));
	} else {
		include model('rule');
		if (is_array($_W['account']['default'])) {
			$wechat['default'] = rule_single($_W['account']['default']['id']);
			$wechat['defaultrid'] = $_W['account']['default']['id'];
		}
		if (is_array($_W['account']['welcome'])) {
			$wechat['welcome'] = rule_single($_W['account']['welcome']['id']);
			$wechat['welcomerid'] = $_W['account']['welcome']['id'];
		}
		template('rule/system');
	}
} elseif ($do == 'set') {
	$rid = intval($_GPC['id']);
	$rule = pdo_fetch("SELECT id, module FROM ".tablename('rule')." WHERE id = :id", array(':id' => $rid));
	if (empty($rule)) {
		message('抱歉，要设置的规则不存在或是已经被删除！', '', 'error');
	}
	$value = iserializer(array(
		'module' => $rule['module'],
		'id' => $rid, 	
	));
	if ($_GPC['type'] == 'default') {
		$data = array(
			'default' => $value,
		);
	} elseif ($_GPC['type'] == 'welcome') {
		$data = array(
			'welcome' => $value,
		);
	}
	pdo_update('wechats', $data, array('weid' => $_W['weid']));
	cache_build_account();
	message('设置系统回复更新成功！', referer(), 'success');
} elseif ($do == 'cancel') {
	if ($_GPC['type'] == 'default') {
		$data = array(
			'default' => '',
		);
	} elseif ($_GPC['type'] == 'welcome') {
		$data = array(
			'welcome' => '',
		);
	}
	pdo_update('wechats', $data, array('weid' => $_W['weid']));
	cache_build_account();
	message('取消系统回复成功！', referer(), 'success');
}
