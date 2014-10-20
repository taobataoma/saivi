<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
include model('setting');
$do = !empty($_GPC['do']) ? $_GPC['do'] : 'display';

if ($do == 'display') {
	$moduleids = array();
	$modules = pdo_fetchall("SELECT * FROM " . tablename('modules') . ' ORDER BY issystem DESC, `mid` ASC', array(), 'mid');
	if (!empty($modules)) {
		foreach ($modules as $mid => $module) {
			$manifest = setting_module_manifest($module['name']);
			if (!empty($manifest['application']['version'])  && $manifest['application']['version'] > $module['version']) {
				$modules[$mid]['upgrade'] = 1;
			}
			$moduleids[] = $module['name'];
		}
	}
	$uninstallModules = array();
	$path = IA_ROOT . '/source/modules/';
	if (is_dir($path)) {
		$uninstallModules = array();
		if ($handle = opendir($path)) {
			while (false !== ($modulepath = readdir($handle))) {
				$manifest = setting_module_manifest($modulepath);
				if (!empty($manifest['application']['identifie']) && !in_array($manifest['application']['identifie'], $moduleids)) {
					$uninstallModules[] = $manifest['application'];
				}
			}
		}
	}
	template('setting/module');
} elseif ($do == 'enable') {
	$mid = intval($_GPC['mid']);
	$module = pdo_fetch("SELECT mid, issystem FROM ".tablename('modules')." WHERE mid = :mid", array(':mid' => $mid));
	if (empty($module)) {
		message('抱歉，模块不存在或是已经被删除！');
	}
	$exist = pdo_fetchcolumn("SELECT id FROM ".tablename('wechats_modules')." WHERE mid = :mid AND weid = :weid", array(':mid' => $mid, ':weid' => $_W['weid']));
	if (empty($exist)) {
		pdo_insert('wechats_modules', array(
			'mid' => $mid,
			'weid' => $_W['weid'],
			'enabled' => empty($_GPC['enabled']) ? 0 : 1,
			'displayorder' => $module['issystem'] ? '-1' : 127,
		));
	} else {
		pdo_update('wechats_modules', array(
			'mid' => $mid,
			'weid' => $_W['weid'],
			'enabled' => empty($_GPC['enabled']) ? 0 : 1,
			'displayorder' => $module['issystem'] ? '-1' : 127,
		), array('id' => $exist));
	}
	cache_build_account();
	message('模块操作成功！', referer(), 'success');
} elseif ($do == 'form') {
	include model('rule');
	if (empty($_GPC['name'])) {
		message('抱歉，模块不存在或是已经被删除！');
	}
	$modulename = !empty($_GPC['name']) ? $_GPC['name'] : 'basic';
	$module = module($modulename);
	if (is_error($module)) {
		exit($module['errormsg']);
	}
	$rid = intval($_GPC['id']);
	exit($module->fieldsFormDisplay($rid));
} elseif ($do == 'displayorder') {
	$mid = intval($_GPC['mid']);
	$displayorder = intval($_GPC['displayorder']);
	$module = pdo_fetch("SELECT mid, issystem FROM ".tablename('modules')." WHERE mid = :mid", array(':mid' => $mid));
	if (empty($module)) {
		message('抱歉，模块不存在或是已经被删除！');
	}
	if ($module['issystem']) {
		message('抱歉，系统模块无法设置优先级！');
	}
	pdo_query("UPDATE ".tablename('wechats_modules')." SET displayorder = 127 WHERE displayorder = '$displayorder' AND weid = '{$_W['weid']}'");
	if (pdo_fetchcolumn("SELECT mid FROM ".tablename('wechats_modules')." WHERE mid = :mid AND weid = :weid", array(':mid' => $mid, ':weid' => $_W['weid']))) {
		pdo_update('wechats_modules', array('displayorder' => $displayorder == 0 ? 127 : $displayorder), array('mid' => $mid ,'weid' => $_W['weid']));
	} else {
		pdo_insert('wechats_modules', array('displayorder' => $displayorder == 0 ? 127 : $displayorder, 'mid' => $mid ,'weid' => $_W['weid'], 'enabled' => 1));
	}
	cache_build_account();
	message('操作成功！', referer());
} elseif ($do == 'setting') {
	$mid = intval($_GPC['mid']);
	$module = pdo_fetch("SELECT name, settings, title FROM ".tablename('modules')." WHERE mid = :mid", array(':mid' => $mid));
	if (empty($module)) {
		message('抱歉，模块不存在或是已经被删除！');
	}
	if (checksubmit('submit')) {
		$sysgpc = array('act', 'do', 'mid', 'submit', 'token', 'session');
		$mid = intval($_GPC['mid']);
		$data = array();
		if (!empty($_GPC)) {
			foreach ($_GPC as $fields => $value) {
				if (!in_array($fields, $sysgpc)) {
					$data[$fields] = $value;
				}
			}
		}
		pdo_update('modules', array('settings' => iserializer($data)), array('mid' => $mid));
		message('模块设置成功！', referer(), 'success');
	}
	$module['settings'] = iunserializer($module['settings']);
	$moduleobj = module($module['name']);
	template('setting/module_setting');
} elseif ($do == 'install') {
	$id = $_GPC['id'];
	$modulepath = IA_ROOT . '/source/modules/' . $id . '/';
	$manifest = setting_module_manifest($id);
	if (empty($manifest)) {
		message('模块安装配置文件不存在或是格式不正确！', '', 'error');
	}
	if (pdo_fetchcolumn("SELECT mid FROM ".tablename('modules')." WHERE name = '{$manifest['application']['identifie']}'")) {
		message('模块已经安装或是唯一标识已存在！', '', 'error');
	}
	if (!file_exists($modulepath . 'processor.php') || (!empty($manifest['hooks']['rule']) && !file_exists($modulepath . 'module.php'))) {
		message('模块缺少应答文件！', '', 'error');
	}
	$module = array(
		'name' => $manifest['application']['identifie'],
		'title' => $manifest['application']['name'],
		'ability' => $manifest['application']['ability'],
		'description' => $manifest['application']['description'],
		'author' => $manifest['application']['author'],
		'version' => $manifest['application']['version'],
		'menus' => serialize($manifest['menus']),
		'issettings'  => $manifest['application']['setting'] == 'true' ? 1 : 0,
		'rulefields' => empty($manifest['hooks']['rule']) ? 0 : 1,
		'issystem' => 0,
	);
	if (pdo_insert('modules', $module)) {
		if (strexists($manifest['install'], '.php')) {
			if (file_exists($modulepath . $manifest['install'])) {
				include_once $modulepath . $manifest['install'];
			}
		} else {
			pdo_run($manifest['install']);
		}
		cache_build_modules();
		$founder = explode(',', $_W['config']['setting']['founder']);
		if (!empty($founder)) {
			foreach ($founder as $uid) {
				cache_build_account($uid);
			}
		}
	}
	message('模块安装成功！', create_url('setting/module'), 'success');
} elseif ($do == 'uninstall') {
	if (!isset($_GPC['confirm'])) {
		message('卸载模块时同时删除规则数据吗？<a href="'.create_url('setting/module/uninstall', array('id' => $_GPC['id'], 'confirm' => 1)).'">是</a> &nbsp;&nbsp;<a href="'.create_url('setting/module/uninstall', array('id' => $_GPC['id'], 'confirm' => 0)).'">否</a>', '', 'tips');
	} else {
		$id = $_GPC['id'];
		$module = pdo_fetch("SELECT mid, name FROM ".tablename('modules')." WHERE name = '{$id}'");
		if (empty($module)) {
			message('模块已经被卸载或是不存在！', '', 'error');
		}
		if (!empty($module['issystem'])) {
			message('系统模块不能卸载！', '', 'error');
		}
		$modulepath = IA_ROOT . '/source/modules/' . $id . '/';
		$manifest = setting_module_manifest($module['name']);
		if (pdo_delete('modules', array('mid' => $module['mid']))) {
			if (!empty($manifest['uninstall'])) {
				if (strexists($manifest['uninstall'], '.php')) {
					if (file_exists($modulepath . $manifest['uninstall'])) {
						include_once $modulepath . $manifest['uninstall'];
					}
				} else {
					pdo_run($manifest['uninstall']);
				}
				pdo_delete('wechats_modules', array('mid' => $module['mid']));
			}
			if ($_GPC['confirm'] == '1') {
				pdo_delete('rule', array('module' => $module['name']));
				pdo_delete('rule_keyword', array('module' => $module['name']));
			}
			cache_build_modules();
			$founder = explode(',', $_W['config']['setting']['founder']);
			if (!empty($founder)) {
				foreach ($founder as $uid) {
					cache_build_account($uid);
				}
			}
		}
		message('模块卸载成功！', create_url('setting/module'), 'success');
	}
} elseif ($do == 'export') {
	$id = $_GPC['id'];
	$module = pdo_fetch("SELECT * FROM ".tablename('modules')." WHERE name = '{$id}'");
	if (empty($module)) {
		message('模块已经被卸载或是不存在！', '', 'error');
	}
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>".PHP_EOL.
			"<manifest versionCode=\"".IMS_VERSION."\">".PHP_EOL.
			"\t<application setting=\"".($module['issettings'] ? 'true' : 'false')."\">".PHP_EOL.
			"\t\t<name><![CDATA[{$module['title']}]]></name>".PHP_EOL.
			"\t\t<identifie><![CDATA[{$module['name']}]]></identifie>".PHP_EOL.
			"\t\t<version><![CDATA[{$module['version']}]]></version>".PHP_EOL.
			"\t\t<ability><![CDATA[{$module['ability']}]]></ability>".PHP_EOL.
			"\t\t<description><![CDATA[{$module['description']}]]></description>".PHP_EOL.
			"\t\t<author><![CDATA[{$module['author']}]]></author>".PHP_EOL.
			"\t</application>".PHP_EOL.
			"\t<hooks>".PHP_EOL;
	if (!empty($module['rulefields'])) {
		$xml .= "\t\t<hook name=\"rule\" />".PHP_EOL;
	}
	$file = IA_ROOT . "/source/modules/{$module['name']}/processor.php";
	if (file_exists($file)) {
		$content = file_get_contents($file);
		preg_match_all('/function (hook[a-zA-Z0-9_-]+)/', $content, $match);
		if (!empty($match[1])) {
			foreach ($match[1] as $hookname) {
				$xml .= "\t\t<hook name=\"{$hookname}\" />".PHP_EOL;
			}
		}
	}
	$xml .= "\t</hooks>".PHP_EOL;
	$xml .= "\t<menus>".PHP_EOL;
	if (!empty($module['menus'])) {
		$module['menus'] = unserialize($module['menus']);
		foreach ($module['menus'] as $menu) {
			$xml .= "\t\t<menu name=\"{$menu[0]}\" value=\"{$menu[1]}\" />".PHP_EOL;
		}
	}
	$xml .=	"\t</menus>".PHP_EOL.
			"\t<install><![CDATA[]]></install>".PHP_EOL.
			"\t<uninstall><![CDATA[]]></uninstall>".PHP_EOL.
			"\t<upgrade><![CDATA[]]></upgrade>".PHP_EOL.
			"</manifest>";
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-Encoding: none');
	header('Content-Length: '.strlen($xml));
	header('Content-Disposition: attachment; filename=manifest.xml');
	header('Content-Type: text/xml');
	print $xml;
} elseif ($do == 'upgrade') {
	$id = $_GPC['id'];
	$module = pdo_fetch("SELECT * FROM ".tablename('modules')." WHERE name = '{$id}'");
	if (empty($module)) {
		message('模块已经被卸载或是不存在！', '', 'error');
	}
	$modulepath = IA_ROOT . '/source/modules/' . $id . '/';
	$manifest = setting_module_manifest($module['name']);
	if (!empty($manifest['upgrade'])) {
		if (strexists($manifest['upgrade'], '.php')) {
			if (file_exists($modulepath . $manifest['upgrade'])) {
				include_once $modulepath . $manifest['upgrade'];
			}
		} else {
			pdo_run($manifest['upgrade']);
		}
	}
	pdo_update('modules', array('version' => $manifest['application']['version']), array('name' => $id));
	message('模块更新成功！', create_url('setting/module'), 'success');
}