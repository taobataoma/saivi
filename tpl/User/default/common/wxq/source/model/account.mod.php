<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

define('WEIXIN_ROOT', 'https://mp.weixin.qq.com');

function account_search() {
	global $_W;
	$condition = $_W['isfounder'] ? '' : " WHERE uid = '{$_W['uid']}'";
	$sql = "SELECT * FROM " . tablename('wechats') . " $condition ORDER BY `weid` DESC";
	return pdo_fetchall($sql, array(), 'weid');
}

function account_weixin_login($username = '', $password = '', $imgcode = '') {
	global $_W;
	if (empty($username) || empty($password)) {
		$username = $_W['account']['username'];
		$password = $_W['account']['password'];
	}
	$auth = cache_load('wxauth:'.$username.':');
	if (!empty($auth)) {
		$response = ihttp_request(WEIXIN_ROOT . '/cgi-bin/indexpage?t=wxm-index&lang=zh_CN', '', array('CURLOPT_NOBODY' => 1, 'CURLOPT_COOKIE' => $auth['cookie']));
		if (strpos($response['headers']['Location'], 'indexpage') !== FALSE || strexists($response['content'], 'logout?t')) {
			preg_match('/token=([0-9]+)/', $response['headers']['Location'], $match);
			cache_write('wxauth:'.$username.':token', $match[1]);
			//cache_write('wxauth:'.$username.':cookie', implode('; ', $response['headers']['Set-Cookie']));
			return true;
		}
	}
	$loginurl = WEIXIN_ROOT . '/cgi-bin/login?lang=zh_CN';	
	$post = array(
		'username' => $username,
		'pwd' => $password,
		'imgcode' => $imgcode,
		'f' => 'json',	
	);
	$response = ihttp_request($loginurl, $post, array('CURLOPT_REFERER' => 'https://mp.weixin.qq.com/cgi-bin/loginpage?t=wxm2-login&lang=zh_CN'));
	$data = json_decode($response['content'], true);
	if ($data['ErrCode'] == 0) {
		preg_match('/token=([0-9]+)/', $data['ErrMsg'], $match);
		cache_write('wxauth:'.$username.':token', $match[1]);
		cache_write('wxauth:'.$username.':cookie', implode('; ', $response['headers']['Set-Cookie']));
	} else {
		switch ($data['ErrCode']) {
			case "-1":
				$msg = "系统错误，请稍候再试。";
				break;
			case "-2":
				$msg = "微信公众帐号或密码错误。";
				break;
			case "-3":
				$msg = "微信公众帐号密码错误，请重新输入。";
				break;
			case "-4":
				$msg = "不存在该微信公众帐户。";
				break;
			case "-5":
				$msg = "您的微信公众号目前处于访问受限状态。";
				break;
			case "-6":
				$msg = "登录受限制，需要输入验证码，稍后再试！";
				break;
			case "-7":
				$msg = "此微信公众号已绑定私人微信号，不可用于公众平台登录。";
				break;
			case "-8":
				$msg = "微信公众帐号登录邮箱已存在。";
				break;
			case "-200":
				$msg = "因您的微信公众号频繁提交虚假资料，该帐号被拒绝登录。";
				break;
			case "-94":
				$msg = "请使用微信公众帐号邮箱登陆。";
				break;
			case "10":
				$msg = "该公众会议号已经过期，无法再登录使用。";
				break;
			default:
				$msg = "未知的返回。";
		}
		message($msg, referer(), 'error');
		return false;
	}
	return true;
}

function account_weixin_basic() {
	global $wechat;
	$response = account_weixin_http($wechat['username'], WEIXIN_ROOT . '/cgi-bin/userinfopage?t=wxm-setting&lang=zh_CN');
	$info = array();
	preg_match('/FakeID.*?"([0-9]+?)"/', $response['content'], $match);
	$fakeid = $match[1];
	preg_match('/(\{"username.*\})/', $response['content'], $match);
	$info = json_decode($match[1], true);
	$image = account_weixin_http($wechat['username'], WEIXIN_ROOT . '/cgi-bin/getheadimg?fakeid='.$fakeid);
	file_write('headimg_'.$wechat['weid'].'.jpg', $image['content']);
	$image = account_weixin_http($wechat['username'], WEIXIN_ROOT . '/cgi-bin/getqrcode?fakeid='.$fakeid.'&style=1&action=download');
	file_write('qrcode_'.$wechat['weid'].'.jpg', $image['content']);
	preg_match('/(gh_[a-z0-9A-Z]+)/', $response['meta'], $match);
	$info['original'] = $match[1];
	preg_match('/NickName \: "(.+?)"/', $response['content'], $match);
	$info['name'] = $match[1];
	return $info;
}

function account_weixin_http($username, $url, $post = '') {
	global $_W;
	if (empty($_W['cache']['wxauth'][$username])) {
		cache_load('wxauth:'.$username.':');
	}
	$auth = $_W['cache']['wxauth'][$username];
	return ihttp_request($url . '&token=' . $auth['token'], $post, array('CURLOPT_COOKIE' => $auth['cookie']));
}

function account_weixin_userlist($pindex = 0, $psize = 1, &$total = 0) {
	global $_W;
	$url = WEIXIN_ROOT . '/cgi-bin/contactmanagepage?t=wxm-friend&lang=zh_CN&type=0&keyword=&groupid=0&pagesize='.$psize.'&pageidx='.$pindex;
	$response = account_weixin_http($_W['account']['username'], $url);
	$html = $response['content'];
	preg_match('/PageCount \: \'(\d+)\'/', $html, $match);
	$total = $match[1];
	preg_match_all('/"fakeId" : "([0-9]+?)"/', $html, $match);
	return $match[1];
}

function account_weixin_send($uid, $message = '') {
	global $_W;
	$username = $_W['account']['username'];
	if (empty($_W['cache']['wxauth'][$username])) {
		cache_load('wxauth:'.$username.':');
	}
	$auth = $_W['cache']['wxauth'][$username];
	$url = WEIXIN_ROOT . '/cgi-bin/singlesend?t=ajax-response&lang=zh_CN';
	$post = array(
		'ajax' => 1,
		'content' => $message,
		'error' => false,
		'tofakeid' => $uid,
		'token' => $auth['token'],
		'type' => 1,
	);
	$response = ihttp_request($url, $post, array(
		'CURLOPT_COOKIE' => $auth['cookie'],
		'CURLOPT_REFERER' => WEIXIN_ROOT . '/cgi-bin/singlemsgpage?token='.$auth['token'].'&fromfakeid='.$uid.'&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN',
	));
}