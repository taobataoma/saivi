<?php 
/**
 * 帮助
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
$page = $_GPC['do'] ? $_GPC['do'] : 'index';
$nav[$page] = ' class="current"';
template('help/'.$page);