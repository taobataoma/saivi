<?php 
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

$sql = 'SELECT * FROM ' . tablename('members');
$members = pdo_fetchall($sql);
$founders = explode(',', $_W['config']['setting']['founder']);
foreach($members as &$m) {
    $m['founder'] = in_array($m['uid'], $founders);
}

template('member/display');
