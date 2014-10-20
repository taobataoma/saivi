<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

if(!$_W['isajax']) {
	exit('Access Denied');
}
if($_GPC['tab'] == 'rule') {
    $current['rule'] = ' active';
} else {
    $current['url'] = ' active';
}
template('menu/action');
