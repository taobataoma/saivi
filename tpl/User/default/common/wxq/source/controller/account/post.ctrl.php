<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
include model('account');
$id = intval($_GPC['id']);
if (!checkpermission('wechats', $id)) {
	message('公众号不存在或是您没有权限操作！');
}
if (checksubmit('submit')) {
	if (empty($_GPC['name'])) {
		message('抱歉，请填写公众号名称！');
	}
	$data = array(
		'uid' => $_W['uid'],
		'name' => $_GPC['name'],
		'account' => $_GPC['account'],
		'original' => $_GPC['original'],
		'token' => $_GPC['wetoken'],
		'key' => $_GPC['key'],
		'secret' => $_GPC['secret'],
		'signature' => '',
		'country' => '',
		'province' => '',
		'city' => '',
		'username' => '',
		'password' => '',
		'welcome' => '',
		'default' => '',
		'lastupdate' => '0',
		'default_period' => '0',
	);
	if (!empty($_GPC['islogin']) && !empty($_GPC['wxusername']) && !empty($_GPC['wxpassword'])) {
		$loginstatus = account_weixin_login($_GPC['wxusername'], md5($_GPC['wxpassword']), $_GPC['verify']);
		$data['username'] = $_GPC['wxusername'];
		$data['password'] = md5($_GPC['wxpassword']);
		$data['lastupdate'] = 0;
	} else {

	}
	if (!empty($id)) {
		$update = array(
			'uid' => $data['uid'],
			'name' => $data['name'],
			'account' => $data['account'],
			'original' => $data['original'],
			'token' => $data['token'],
			'key' => $data['key'],
			'secret' => $data['secret'],
		);
		if (!empty($data['password'])) {
			$update['username'] = $data['username'];
			$update['password'] = $data['password'];
			$update['lastupdate'] = $data['lastupdate'];
		}
		pdo_update('wechats', $update, array('weid' => $id));
		cache_build_account();
	} else {
		$data['hash'] = random(5);
		$data['token'] = random(32);
		if (pdo_insert('wechats', $data)) {
			$id = pdo_insertid();	
		}
	}
	cache_build_account();
	message('更新公众号成功！', create_url('account/post', array('id' => $id)));

} else {
	$wechat = array();
	if (!empty($id)) {
		$wechat = pdo_fetch("SELECT * FROM ".tablename('wechats')." WHERE weid = '$id'");
	}
	if(!empty($wechat['username']) && (empty($wechat['lastupdate']) || TIMESTAMP - $wechat['lastupdate'] > 86400 * 7)) {
		$loginstatus = account_weixin_login($wechat['username'], $wechat['password']);
		$basicinfo = account_weixin_basic();
		if (!empty($basicinfo['name'])) {
			$update = array(
				'name' => $basicinfo['name'],
				'account' => $basicinfo['username'],
				'original' => $basicinfo['original'],
				'signature' => $basicinfo['signature'],
				'country' => $basicinfo['country'],
				'province' => $basicinfo['province'],
				'city' => $basicinfo['city'],
				'lastupdate' => TIMESTAMP,
			);
			pdo_update('wechats', $update, array('weid' => $id));
			cache_build_account();
			$wechat['name'] = $basicinfo['name'];
			$wechat['account'] = $basicinfo['username'];
			$wechat['original'] = $basicinfo['original'];
			$wechat['signature'] = $basicinfo['signature'];
			$wechat['country'] = $basicinfo['country'];
			$wechat['province'] = $basicinfo['province'];
			$wechat['city'] = $basicinfo['city'];
		}
	}
	template('account/post');
}
