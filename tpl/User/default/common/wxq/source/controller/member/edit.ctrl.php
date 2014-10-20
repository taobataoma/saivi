<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
include_once model('setting');

$uid = intval($_GPC['uid']);
$m = array();
$m['uid'] = $uid;
$member = member_single($m);
$founders = explode(',', $_W['config']['setting']['founder']);
if(empty($member) || in_array($m['uid'], $founders)) {
	message('访问错误.');
}

$do = $_GPC['do'];
$dos = array('edit', 'deny', 'delete', 'auth', 'revo', 'revos');
$do = in_array($do, $dos) ? $do: 'edit';

if($do == 'edit') {
	if(checksubmit('profile')) {
		require_once IA_ROOT . '/source/model/member.mod.php';
		$nMember = array();
		$nMember['uid'] = $uid;
		$nMember['password'] = $_GPC['password'];
		$nMember['salt'] = $member['salt'];
		if(!empty($nMember['password']) && istrlen($nMember['password']) < 8) {
			message('必须输入密码，且密码长度不得低于8位。');
		}
		$nMember['lastip'] = $_GPC['lastip'];
		$nMember['lastvisit'] = $_GPC['lastvisit'];
		$nMember['remark'] = $_GPC['remark'];
		if(member_update($nMember) !== false) {
			message('保存用户资料成功！', 'refresh');
		}
		message('保存用户资料失败，请稍候重试或联系网站管理员解决！');
	}
	require model('wechat');
	$wechats = wechat_search("`uid`='{$uid}'");

	$sql = "SELECT `mid` FROM " . tablename('members_modules') . ' WHERE `uid`=:uid';
	$mids = pdo_fetchall($sql, array(':uid' => $uid));
	$sql = 'SELECT * FROM ' . tablename('modules') . " WHERE `issystem`=1";
	if(!empty($mids)) {
		$qMids = array();
		foreach($mids as $row) {
			array_push($qMids, $row['mid']);
		}
		$mids = implode(',', $qMids);
		$sql .= " OR `mid` IN ({$mids})";
	}
	$modules = pdo_fetchall($sql);
	template('member/edit');
}

if($do == 'delete') {
	if($_W['ispost'] && $_W['isajax']) {
		$founders = explode(',', $_W['config']['setting']['founder']);
		if(in_array($uid, $founders)) {
			exit('管理员用户不能删除.');
		}
		$member = array();
		$member['uid'] = $uid;
		if(pdo_delete('members', $member) === 1) {
			exit('success');
		}
	}
}

if($do == 'deny') {
	if($_W['ispost'] && $_W['isajax']) {
		$founders = explode(',', $_W['config']['setting']['founder']);
		if(in_array($uid, $founders)) {
			exit('管理员用户不能禁用.');
		}
		$member = array();
		$member['uid'] = $uid;
		$status = $_GPC['status'];
		$member['status'] = $status == '-1' ? '-1' : '0';
		if(member_update($member)) {
			exit('success');
		}
	}
}
if($do == 'auth') {
	$mod = $_GPC['mod'];
	if($mod == 'account') {
		$weid = intval($_GPC['wechat']);
		if(empty($weid)) {
			exit('error');
		}

		if($member['status'] == '-1') {
			exit('此用户已经被禁用. ');
		}
		$wechat = array();
		$wechat['uid'] = $uid;

		if(pdo_update('wechats', $wechat, array('weid' => $weid))) {
			setting_cache_account_by_founder();
			//清除公众号相关设置
			pdo_delete('wechats_modules', array('weid' => $weid));
			exit('success');
		} else {
			exit('error');
		}
	}
	if($mod == 'module') {
		$mid = intval($_GPC['mid']);
		$sql = 'SELECT * FROM ' . tablename('modules') . " WHERE `mid`='{$mid}'";
		$module = pdo_fetch($sql);
		if(empty($module) || $module['issystem']) {
			exit('不存在的模块, 或者此模块是系统模块, 不能操作.');
		}

		$sql = 'SELECT * FROM ' . tablename('members_modules') . " WHERE `uid`='{$uid}' AND `mid`='{$mid}'";
		$mapping = pdo_fetch($sql);
		if(empty($mapping)) {
			$record = array();
			$record['uid'] = $uid;
			$record['mid'] = $mid;
			if(pdo_insert('members_modules', $record)) {
				setting_cache_account_by_founder();
				exit('success');
			}
		}
		exit('error');
	}
}

if($do == 'revo') {
	$mod = $_GPC['mod'];
	if($mod == 'account') {
		$weid = intval($_GPC['wechat']);
		if(empty($weid)) {
			exit('error');
		}

		$wechat = array();
		$wechat['uid'] = $_W['uid'];

		if(pdo_update('wechats', $wechat, array('weid' => $weid))) {
			setting_cache_account_by_founder();
			exit('success');
		} else {
			exit('error');
		}
	}
	if($mod == 'module') {
		$mid = intval($_GPC['mid']);
		$sql = 'SELECT * FROM ' . tablename('modules') . " WHERE `mid`='{$mid}'";
		$module = pdo_fetch($sql);
		if(empty($module) || $module['issystem']) {
			exit('不存在的模块, 或者此模块是系统模块, 不能操作.');
		}

		$record = array();
		$record['uid'] = $uid;
		$record['mid'] = $mid;
		if(pdo_delete('members_modules', $record)) {
			cache_build_account($uid);
			exit('success');
		}
		exit('error');
	}
}

if($do == 'revos') {
	$mod = $_GPC['mod'];
	if($mod == 'account') {
		$uid = $_W['uid'];
		$wechats = explode(',', $_GPC['wechats']);
		$weids = array();
		foreach($wechats as $w) {
			$weid = intval($w);
			if($weid) {
				array_push($weids, $weid);
			}
		}
		$weids = implode(',', $weids);
		$sql = 'UPDATE ' . tablename('wechats') . " SET `uid`=:uid WHERE `weid` IN ({$weids})";
		$params = array();
		$params[':uid'] = $uid;
		if(pdo_query($sql, $params)) {
			cache_build_account($uid);
			exit('success');
		} else {
			exit('error');
		}
	}
	if($mod == 'module') {
		$mids = explode(',', $_GPC['mids']);
		$ms = array();
		foreach($mids as $w) {
			$mid = intval($w);
			if($mid) {
				array_push($ms, $mid);
			}
		}
		$mids = implode(',', $ms);
		$sql = 'DELETE FROM ' . tablename('members_modules') . " WHERE `uid`='{$uid}' AND `mid` IN ($mids)";
		if(pdo_query($sql)) {
			cache_build_account($uid);
			exit('success');
		}
		exit('error');
	}
}

