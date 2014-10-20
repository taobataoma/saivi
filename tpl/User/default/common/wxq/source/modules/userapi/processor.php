<?php
/**
 * 调用第三方数据接口处理类
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class UserapiModuleProcessor extends WeModuleProcessor {
	
	public $name = 'UserapiModuleProcessor';

	public function isNeedInitContext() {
		return 0;
	}
	
	/**
	 * 回复可以为微信默信回复XML数据，将直接返回给微信客户端，请保证数据正确性。
	 * 回复可以为JSON串，由微擎系统构造返回数据格式。
	 */
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$module = $_W['modules']['userapi'];
		$module['settings'] = iunserializer($module['settings']);
		if (empty($rid)) {
			$item['apiurl'] = $module['settings']['apiurl'];
			$item['default-text'] = $module['settings']['default'];
		} else {
			$sql = "SELECT * FROM " . tablename('userapi_reply') . " WHERE `rid`=:rid ORDER BY id DESC limit 1";
			$item = pdo_fetch($sql, array(':rid' => $rid));
			if (empty($item['id'])) {
				return array();
			}
		}
		$key = md5($item['id'].$this->message['from']);
		if ($item['cachetime'] > 0) {
			$cache = pdo_fetch("SELECT * FROM " . tablename('userapi_cache') . " WHERE `key` = '$key' LIMIT 1");
			if (!empty($cache) && TIMESTAMP - $cache['lastupdate'] <= $item['cachetime']) {
				return iunserializer($cache['content']);
			}	
		}
		if (!strexists($item['apiurl'], 'http://') && !strexists($item['apiurl'], 'https://')) {
			$file = IA_ROOT . '/source/modules/userapi/api/' . $item['apiurl'];
			if (!file_exists($file)) {
				return array();
			}	
			include_once $file;
			return $response;
		}
		if (!strexists($item['apiurl'], '?')) {
			$item['apiurl'] .= '?';
		} else {
			$item['apiurl'] .= '&';
		}
		
		$token = $_W['account']['token'];
		$sign = array(
			'timestamp' => TIMESTAMP,
			'nonce' => random(10, 1),
		);
		$signkey = array($token, $sign['timestamp'], $sign['nonce']);
		sort($signkey, SORT_STRING);
		$sign['signature'] = sha1(implode($signkey));
		$item['apiurl'] .= http_build_query($sign, '', '&');
		
		$response = ihttp_request($item['apiurl'], $GLOBALS["HTTP_RAW_POST_DATA"], array('CURLOPT_HTTPHEADER' => array('Content-Type: text/xml; charset=utf-8')));
		$result = array();
		if ($response['code'] == '200') {
			$temp = json_decode($response['content'], true);
			if (is_array($temp)) {
				$result = $this->buildResponse($temp);
			} else {
				$result = $response['content'];
			}
		} else {
			if (!empty($item['default-text'])) {
				$result['MsgType'] = 'text';
				$result['Content'] = $item['default_text'];
			}
			$response = ihttp_request($item['default_apiurl']);
			if ($response['code'] == '200') {
				$temp = json_decode($response['content'], true);
				if (is_array($temp)) {
					$result = $this->buildResponse($temp);
				} else {
					$result = $response['content'];
				}
			} else {
				return array();
			}
		}
		if (is_array($result)) {
			$result['FromUserName'] = $this->message['to'];
			$result['ToUserName'] = $this->message['from'];
		}
		if ($item['cachetime'] > 0) {
			if (empty($cache)) {
				pdo_insert('userapi_cache', array('key' => $key, 'content' => iserializer($result), 'lastupdate' => TIMESTAMP));
			} else {
				pdo_update('userapi_cache', array('content' => iserializer($result), 'lastupdate' => TIMESTAMP), array('key' => $key));
			}
		}
		return $result;
	}

	public function isNeedSaveContext() {
		return false;
	}
	
	private function buildResponse($data = array()) {
		$result = array();
		$result['MsgType'] = $data['type'];
		$data = $data['content'];
		
		if ($result['MsgType'] == 'text') {
			$result['Content'] = $data;
		} elseif ($result['MsgType'] == 'news') {
			$result['ArticleCount'] = $data['ArticleCount'];
			$result['Articles'] = array();
			if (!isset($data[0])) {
				$temp[0] = $data;
				$data = $temp;
			}
			foreach ($data as $row) {
				$result['Articles'][] = array(
					'Title' => $row['Title'],
					'Description' => $row['Description'],
					'PicUrl' => $row['PicUrl'],
					'Url' => $row['Url'],
					'TagName' => 'item',
				);
			}
		} elseif ($result['MsgType'] == 'music') {
			$result['Music'] = array(
				'Title'	=> $data['Title'],
				'Description' => $data['Description'],
				'MusicUrl' => $data['MusicUrl'],
				'HQMusicUrl' => $data['HQMusicUrl'],
			);
		}
		return $result;
	}
	
}