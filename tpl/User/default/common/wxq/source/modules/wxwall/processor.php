<?php
/**
 * 图文回复处理类
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class WxwallModuleProcessor extends WeModuleProcessor {

	public $name = 'WxwallChatRobotModuleProcessor';

	public function isNeedInitContext() {
		return 0;
	}

	public function respond() {
		global $_W;
		$rid = $this->rule;
		$member = pdo_fetch("SELECT id, lastupdate, nickname FROM ".tablename('wxwall_members')." WHERE from_user = '{$this->message['from']}' LIMIT 1");
		$wall = pdo_fetch("SELECT * FROM ".tablename('wxwall_reply')." WHERE rid = '{$rid}' LIMIT 1");
		if (empty($wall)) {
			return array();
		}
		if ($wall['isjoin'] == 1 && !empty($wall['timeout']) && !empty($member) && $wall['timeout'] > 0 && TIMESTAMP - $member['lastupdate'] >= $wall['timeout']) {
			pdo_update('wxwall_members', array('isjoin' => 0), array('from_user' => $this->message['from']));
			$response = array();
			$response['FromUserName'] = $this->message['to'];
			$response['ToUserName'] = $this->message['from'];
			$response['MsgType'] = 'text';
			$response['Content'] = htmlspecialchars_decode($wall['quit_tips']);
			exit(WeUtility::response($response));
		}
		if (empty($member)) {
			$data = array(
				'from_user' => $this->message['from'],
				'rid' => $rid,
				'lastupdate' => TIMESTAMP,
				'isjoin' => 1,
			);
			pdo_insert('wxwall_members', $data);
		} else {
			pdo_update('wxwall_members', array('isjoin' => 1, 'rid' => $rid, 'lastupdate' => TIMESTAMP), array('from_user' => $this->message['from']));
		}
		$response = array();
		$response['FromUserName'] = $this->message['to'];
		$response['ToUserName'] = $this->message['from'];
		$response['MsgType'] = 'text';
		$response['Content'] = $wall['enter_tips'] . (empty($member['nickname']) ? ' - 发表话题前请<a target="_blank" href="'.$_W['siteroot'] . create_url('index/module', array('name' => 'wxwall', 'do' => 'register', 'from' => $this->message['from'])).'">登记</a>您的信息。' : '');
		return $response;
	}

	public function isNeedSaveContext() {
		return false;
	}


	public function hookBefore() {
		global $_W, $engine;
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$message = WeUtility::parse($postStr);
		if (!in_array($message['msgtype'], array('text', 'image'))) {
			return false;
		}
		$member = pdo_fetch("SELECT id, lastupdate, rid, nickname, isjoin, isblacklist FROM ".tablename('wxwall_members')." WHERE from_user = '{$message['from']}' LIMIT 1");
		if (empty($member) || empty($member['isjoin'])) {
			return false;
		}
		$wall = pdo_fetch("SELECT * FROM ".tablename('wxwall_reply')." WHERE rid = '{$member['rid']}' LIMIT 1");
		
		if ((!empty($wall['timeout']) && $wall['timeout'] > 0 && TIMESTAMP - $member['lastupdate'] >= $wall['timeout'])) {
			pdo_update('wxwall_members', array('isjoin' => 0, 'lastupdate' => TIMESTAMP), array('from_user' => $message['from']));
			return false;
		}
		if ((empty($wall['quit_command']) && $message['content'] == '退出') ||
			(!empty($wall['quit_command']) && $message['content'] == $wall['quit_command'])) {
			pdo_update('wxwall_members', array('isjoin' => 0, 'lastupdate' => TIMESTAMP), array('from_user' => $message['from']));
			$response = array();
			$response['FromUserName'] = $message['to'];
			$response['ToUserName'] = $message['from'];
			$response['MsgType'] = 'text';
			$response['Content'] = $wall['quit_tips'];
			exit(WeUtility::response($response));
		}
		if (empty($member['nickname'])) {
			$response = array();
			$response['FromUserName'] = $message['to'];
			$response['ToUserName'] = $message['from'];
			$response['MsgType'] = 'text';
			$response['Content'] = '发表话题前请<a target="_blank" href="'.$_W['siteroot'] . create_url('index/module', array('name' => 'wxwall', 'do' => 'register', 'from' => $message['from'])).'">登记</a>您的信息。';
			exit(WeUtility::response($response));
		}
		$data = array(
			'rid' => $member['rid'],
			'from_user' => $message['from'],
			'type' => $message['type'],
			'createtime' => TIMESTAMP,
		);
		if (empty($wall['isshow']) && empty($member['isblacklist'])) {
			$data['isshow'] = 1;
		} else {
			$data['isshow'] = 0;
		}
		if ($message['type'] == 'text') {
			$data['content'] = $message['content'];
		}
		if ($message['type'] == 'image') {
			$image = ihttp_request($message['picurl']);
			$filename = 'wxwall/' . $member['rid'] . '/' . random(30) . '.jpg';
			file_write($filename, $image['content']);
			$data['content'] = $filename;
		}
		if ($message['type'] == 'link') {
			$data['content'] = iserializer(array('title' => $message['title'], 'description' => $message['description'], 'link' => $message['link']));
		}
		pdo_insert('wxwall_message', $data);
		pdo_update('wxwall_members', array('lastupdate' => TIMESTAMP), array(from_user => $message['from']));
		$engine->response = array(
			'module' => 'wxwall',
			'rule' => $member['rid'],
		);
		$engine->stat();
		$response = array();
		$response['FromUserName'] = $message['to'];
		$response['ToUserName'] = $message['from'];
		$response['MsgType'] = 'text';
		$response['Content'] = htmlspecialchars_decode($wall['send_tips']);
		if (!empty($member['isblacklist'])) {
			$response['Content'] .= '你已被列入黑名单，发送的消息需要管理员审核！';
		}
		exit(WeUtility::response($response));
	}
}
