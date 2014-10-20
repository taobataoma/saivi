<?php 
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

defined('IN_IA') or exit('Access Denied');
$owner = intval($_GPC['owner']);

$do = $_GPC['do'];
$dos = array('account', 'module');
$do = in_array($do, $dos) ? $do: 'account';

if($do == 'account') {
    require model('wechat');
    $condition = '';
    $params = array();
    if(!empty($_GPC['keyword'])) {
        $condition = '`name` LIKE :name';
        $params[':name'] = "%{$_GPC['keyword']}%";
    }
    $pindex = max(1, intval($_GPC['page']));
    $psize = 5;
    $total = 0;
    $wechats = wechat_search($condition, $params, $pindex, $psize, $total);
    $owner = $_GPC['owner'];
    foreach($wechats as &$wechat) {
        $member = member_single(array('uid' => $wechat['uid']));
        $wechat['member'] = $member;
        if($wechat['uid'] == $owner) {
            $wechat['owner'] = true;
        }
    }
    $pager = pagination($total, $pindex, $psize, '', array('ajaxcallback'=>'null'));
    template('member/select');
}

if($do == 'module') {
    $sql = "SELECT `mid` FROM " . tablename('members_modules') . ' WHERE `uid`=:uid';
	$mids = pdo_fetchall($sql, array(':uid' => $owner));
    $qMids = array();
    foreach($mids as $row) {
        array_push($qMids, $row['mid']);
    }
    $sql = 'SELECT * FROM ' . tablename('modules') . ' ORDER BY issystem DESC, mid ASC';
    $modules = pdo_fetchall($sql);
    foreach($modules as &$m) {
        $m['owner'] = in_array($m['mid'], $qMids);
    }
    template('member/select');
}
