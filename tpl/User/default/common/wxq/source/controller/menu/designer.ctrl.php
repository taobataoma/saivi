<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
$current['designer'] = ' class="current"';
checkaccount();
$menusetcookie = 'menuset-' . $_W['weid'];
if($_W['ispost']) {
	if($_GPC['do'] == 'remove') {
		$token = client_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$token}";
		$content = ihttp_get($url);
		if(empty($content)) {
			message('接口调用失败，请重试！');
		}
		$dat = $content['content'];
		$result = @json_decode($dat, true);
		if($result['errcode'] == '0') {
			isetcookie($menusetcookie, '', -500);
			message('已经成功删除菜单， 请重新创建. ', create_url('menu'));
		} else {
			message('公众平台返回接口错误, 错误内容为: ' . $menus['errmsg']);
		}
	}
	if($_GPC['do'] == 'refresh') {
		isetcookie($menusetcookie, '', -500);
		message('已清空缓存， 将重新从公众平台接口获取菜单信息. ', create_url('menu'));
	}
	require model('rule');
	$mDat = $_GPC['do'];
	$menus = json_decode($mDat, true);
	if(!is_array($menus)) {
		message('操作非法.');
	}
    foreach($menus as &$m) {
        $m['name'] = urlencode($m['name']);
        if(is_array($m['sub_button'])) {
            foreach($m['sub_button'] as &$s) {
                $s['name'] = urlencode($s['name']);
            }
        }
    }
	$ms = array();
	$ms['button'] = $menus;
	$dat = json_encode($ms);
	$dat = urldecode($dat);
	$token = client_token();
	$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token}";
	$content = ihttp_post($url, $dat);
	$dat = $content['content'];
	$result = @json_decode($dat, true);
	if($result['errcode'] == '0') {
		isetcookie($menusetcookie, '', -500);
		message('已经成功创建菜单. ', create_url('menu'));
	} else {
		message('公众平台返回接口错误, 错误内容为: ' . $menus['errmsg']);
	}
}
$dat = $_GPC[$menusetcookie];
if(empty($dat)) {
	$token = client_token();
	$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$token}";
	$content = ihttp_get($url);
	if(empty($content)) {
		message('获取菜单数据失败，请重试！');
	}
	$dat = $content['content'];
}
$menus = @json_decode($dat, true);
if(empty($menus) || !is_array($menus)) {
	message('获取菜单数据失败，请重试！');
}
if($menus['errcode'] && !in_array($menus['errcode'], array(46003))) {
	message('公众平台返回接口错误, 错误内容为: ' . $menus['errmsg']);
}
isetcookie($menusetcookie, $dat, 86400);

if(is_array($menus['menu']['button'])) {
    foreach($menus['menu']['button'] as &$m) {
        if($m['key']) {
            $pieces = explode(':', $m['key'], 2);
            $m['module'] = $pieces[0];
            $m['rid'] = $pieces[1];
        }
        if(is_array($m['sub_button'])) {
            foreach($m['sub_button'] as &$s) {
                $pieces = explode(':', $s['key'], 2);
                $s['module'] = $pieces[0];
                $s['rid'] = $pieces[1];
            }
        }
    }
}
template('menu/designer');

function client_token() {
	global $_W;
	if (empty($_W['account']['key']) || empty($_W['account']['secret'])) {
		message('请填写公众号的appid及appsecret！', create_url('account/post', array('id' => $_W['weid'])), 'error');
	}
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$_W['account']['key']}&secret={$_W['account']['secret']}";
	$content = ihttp_get($url);
	if(empty($content)) {
		message('获取菜单数据失败，请重试！');
	}
	$token = @json_decode($content['content'], true);
	if(empty($token) || !is_array($token)) {
		message('获取菜单数据失败，请重试！');
	}
	$token = $token['access_token'];
	if(empty($token)) {
		message('获取菜单数据失败，请重试！');
	}
	return $token;
}
