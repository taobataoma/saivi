<?php
/**
 * 欢迎信息处理类
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class WelcomeModuleProcessor extends WeModuleProcessor {

	public $name = 'WelcomeChatRobotModuleProcessor';

	public function isNeedInitContext() {
		return 0;
	}

	public function respond() {
		global $_W, $engine;
		$r['FromUserName'] = $this->message['to'];
		$r['ToUserName'] = $this->message['from'];
		$r['MsgType'] = 'text';
		$sql = "SELECT `welcome` FROM " . tablename('wechats') . " WHERE `weid`=:weid";
		$default = pdo_fetchcolumn($sql, array(':weid' => $_W['weid']));
		if (is_array(iunserializer($default))) {
			$default = iunserializer($default);
			$_W['module'] = $default['module'];
			$processor = WeUtility::createModuleProcessor($default['module']);
			$processor->message = $this->message;
			$processor->inContext = $this->inContext;
			$processor->rule = $default['id'];
			$engine->response['rule'] = $default['id'];
			return $processor->respond();
		}
		$r['Content'] = $default;
		return $r;
	}

	public function isNeedSaveContext() {
		return false;
	}
}
