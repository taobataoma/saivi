<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
isetcookie('session', '', -10000);
isetcookie('weid', '', -10000);
$forward = $_GPC['forward'];
if(empty($forward)) {
	$forward = './?refersh';
}
message('成功退出登录！', $forward);
