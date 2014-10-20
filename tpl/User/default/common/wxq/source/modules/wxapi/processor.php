<?php
/**
 * 乐享接口模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
class WxapiModuleProcessor extends WeModuleProcessor {

	public $name = 'wxapi';

	public function isNeedInitContext() {
		return 0;
	}

	public function respond() {
		ob_start();
		ob_clean();
		include 'wxapi/wxapi.php';
		$response = ob_get_contents();
		ob_clean();
		if ($response == '到期了！') {
			$r = array();
			$r['FromUserName'] = $this->message['to'];
			$r['ToUserName'] = $this->message['from'];
			$r['MsgType'] = 'text';
			$r['Content'] = '乐享接口到期了！';
			return $r;
		}
		return $response;
	}

	public function isNeedSaveContext() {
		return false;
	}
}