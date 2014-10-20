<?php 
/**
 * 用户模块管理
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */

defined('IN_IA') or exit('Access Denied');
checkaccount();


$sysmodules = pdo_fetchall("SELECT * FROM ".tablename('modules')." WHERE issystem = '1'", array(), 'mid');
if (!empty($_W['isfounder'])) {
	$membermodules = pdo_fetchall("SELECT * FROM ".tablename('modules') . " WHERE issystem = '0' ORDER BY mid ASC", array(), 'mid');
} else {
	$membermodules = pdo_fetchall("SELECT b.* FROM ".tablename('members_modules')." AS a LEFT JOIN ".tablename('modules')." AS b ON a.mid = b.mid WHERE a.uid = :uid  AND b.name <> '' ORDER BY a.mid ASC", array(':uid' => $_W['uid']), 'mid');
}
$mymodules = pdo_fetchall("SELECT mid, enabled, displayorder FROM ".tablename('wechats_modules')." WHERE weid = '{$_W['weid']}' AND mid IN (".implode(",", array_keys($membermodules)).") ORDER BY enabled DESC, displayorder ASC, mid ASC", array(), 'mid');

$modulelist = array();
$modulelist = $sysmodules;
//拼接模块
foreach ((array)$mymodules as $mid => $row){
	if (!empty($membermodules[$mid])) {
		$modulelist[$mid] = $membermodules[$mid];
		$modulelist[$mid]['enabled'] = $row['enabled'];
		$modulelist[$mid]['displayorder'] = $row['displayorder'];
		unset($membermodules[$mid]);
	}
}
unset($row);
if (!empty($membermodules)) {
	$modulelist = array_merge($modulelist, $membermodules);
}
foreach ($modulelist as $mid => &$row) {
	if (!isset($row['enabled'])) {
		$row['enabled'] = 1;
		$row['displayorder'] = $row['issystem'] ? -1 : 127;
	}
}
unset($row);
template('member/module');