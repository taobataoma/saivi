<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
$current['login'] = ' class="current"';
if(checksubmit()) {
	_login($_GPC['referer']);
}
template('member/login');
