<?php
/**
 * 基本文字回复处理类
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class BasicModuleProcessor extends WeModuleProcessor {
	
	public $name = 'Basic';

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
		$sql = "SELECT * FROM " . tablename('basic_reply') . " WHERE `rid` IN ({$this->rule})  ORDER BY RAND() LIMIT 1";
		$reply = pdo_fetch($sql);
		$reply['content'] = htmlspecialchars_decode($reply['content']);
		//过滤HTML
		$reply['content'] = str_replace(array('<br>', '&nbsp;'), array("\n", ' '), $reply['content']);
		$reply['content'] = strip_tags($reply['content'], '<a>');
		
		$response = array();
		$response['FromUserName'] = $this->message['to'];
		$response['ToUserName'] = $this->message['from'];
		$response['MsgType'] = 'text';
		$response['Content'] = htmlspecialchars_decode($reply['content']);
		return $response;
	}

	public function isNeedSaveContext() {
		return false;
	}
}
