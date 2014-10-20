<?php
/**
 * 语音回复处理类
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class MusicModuleProcessor extends WeModuleProcessor {
	
	public $name = 'MusicModuleProcessor';

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('music_reply') . " WHERE `rid`=:rid ORDER BY RAND()";
		$item = pdo_fetch($sql, array(':rid' => $rid));
		if (empty($item['id'])) {
			return array();
		}
		$r['FromUserName'] = $this->message['to'];
		$r['ToUserName'] = $this->message['from'];
		$r['MsgType'] = 'music';
		$r['Music'] = array(
			'Title'	=> $item['title'],
			'Description' => $item['description'],
			'MusicUrl' => strpos($item['url'], 'http://') === FALSE ? $_W['attachurl'] . $item['url'] : $item['url'],
		);
		if (empty($item['hqurl'])) {
			$r['Music']['HQMusicUrl'] = $r['Music']['MusicUrl'];
		} else {
			$r['Music']['HQMusicUrl'] = strpos($item['hqurl'], 'http://') === FALSE ? $_W['attachurl'] . $item['hqurl'] : $item['hqurl'];
		}
		return $r;
	}

	public function isNeedSaveContext() {
		return false;
	}
}
