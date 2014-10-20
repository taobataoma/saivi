<?php 
/**
 * 1、接收POST进来的xml数据处理
 * 2、查询接口得到数据
 * 3、返回给微擎结果
 */
//如果是引用本地文件，可直接使用微擎中的消息变量 $this->message
//如果是引用其它远程文件，此处只能得到POST过来的值自行解析数据

//$message = userApiUtility::parse($GLOBALS["HTTP_RAW_POST_DATA"]);
$message = $this->message;

/**
 * 处理用户发送来的内容信息，这里的需求是需要取出包含的城市信息
 * 还有很多种处理方式，根据自己设定的关键字来处理。
 */
preg_match('/(.*)天气/', $message['content'], $match);
$city = $match[1];

//调用接口
$url = 'http://php.weather.sina.com.cn/xml.php?city=%s&password=DJOYnieT8234jlsK&day=0';
$url = sprintf($url, urlencode(iconv('utf-8', 'gb2312', $city)));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
$data = curl_exec($ch);
$status = curl_getinfo($ch);
$errno = curl_errno($ch);
curl_close($ch);

//返回的消息记录，如果是本地文件必须构造一个response数组变量，并填充相关信息。
//如果是远程URL，返回的数据可以是response数组的json串，也可以是微信公众平台的标准XML数据接口。

$response = array();
if ($status['http_code'] = 200) {
	$obj = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
	$response['FromUserName'] = $message['to'];
	$response['ToUserName'] = $message['from'];
	$response['MsgType'] = 'text';
	$response['Content'] = '';
	$response['Content'] = $obj->Weather->city . '今日天气' . PHP_EOL .
							'今天白天'.$obj->Weather->status1.'，'. $obj->Weather->temperature1 . '摄氏度。' . PHP_EOL .
							$obj->Weather->direction1 . '，' . $obj->Weather->power1 . PHP_EOL .
							'今天夜间'.$obj->Weather->status2.'，'. $obj->Weather->temperature2 . '摄氏度。' . PHP_EOL .
							$obj->Weather->direction2 . '，' . $obj->Weather->power2 . PHP_EOL . 
							'==================' . PHP_EOL .
							'【穿衣指数】：' . $obj->Weather->chy_shuoming . PHP_EOL .PHP_EOL .
							'【感冒指数】：' . $obj->Weather->gm_l . $obj->Weather->gm_s . PHP_EOL .PHP_EOL .
							'【空调指数】：' . $obj->Weather->ktk_s . PHP_EOL .PHP_EOL .
							'【污染物扩散条件】：' . $obj->Weather->pollution_l . $obj->Weather->pollution_s . PHP_EOL .PHP_EOL .
							'【洗车指数】：' . $obj->Weather->xcz_l . $obj->Weather->xcz_s . PHP_EOL .PHP_EOL .
							'【运动指数】：' . $obj->Weather->yd_l . $obj->Weather->yd_s . PHP_EOL .PHP_EOL .
							'【紫外线指数】：' . $obj->Weather->zwx_l . $obj->Weather->zwx_s . PHP_EOL .PHP_EOL .
							'【体感度指数】：' . $obj->Weather->ssd_l . $obj->Weather->ssd_s . PHP_EOL ;
}
return $response;

class userApiUtility{
	/**
	 * 签名验证
	 * @param string $sign
	 * @param string $token 在微擎“用户自定义接口”模块设置中设置的token值
	 * @return boolean
	 */
	static public function checkSign($sign = '', $token = '') {
		return $_GET['sign'] == sha1($_GET['time'].$token);
	}
	
	/**
	 * 格式化接收到的xml数据
	 * @param string $message
	 * @return multitype:string Ambigous <string>
	 */
	static public function parse($message) {
		$packet = array();
		if (!empty($message)){
			$obj = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
			if($obj instanceof SimpleXMLElement) {
				$packet['from'] = strval($obj->FromUserName);
				$packet['to'] = strval($obj->ToUserName);
				$packet['time'] = strval($obj->CreateTime);
				$packet['type'] = strval($obj->MsgType);
				$packet['event'] = strval($obj->Event);
	
				foreach ($obj as $variable => $property) {
					$packet[strtolower($variable)] = (string)$property;
				}
				if($packet['type'] == 'event') {
					$packet['type'] = $packet['event'];
					unset($packet['content']);
				}
			}
		}
		return $packet;
	}
}
