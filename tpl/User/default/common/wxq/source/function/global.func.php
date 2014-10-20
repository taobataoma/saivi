<?php
/**
 * 公共函数
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 转义引号字符串
 * 支持单个字符与数组
 *
 * @param string or array $var
 * @return string or array
 *			 返回转义后的字符串或是数组
 */
function istripslashes($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[stripslashes($key)] = istripslashes($value);
		}
	} else {
		$var = stripslashes($var);
	}
	return $var;
}

/**
 * 转义字符串的HTML
 * @param string or array $var
 * @return string or array
 *			 返回转义后的字符串或是数组
 */
function ihtmlspecialchars($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[htmlspecialchars($key)] = ihtmlspecialchars($value);
		}
	} else {
		$var = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', str_replace('&', '&amp;', htmlspecialchars($var, ENT_QUOTES)));
	}
	return $var;
}

/**
 * 写入cookie值
 * @param string $key
 *			 cookie名称
 * @param string $value
 *			 cookie值
 * @param int $maxage
 *			 cookie的生命周期,当前时间开始的$maxage秒
 * @return boolean
 */
function isetcookie($key, $value, $maxage = 0) {
	global $_W;
	$expire = $maxage != 0 ? time() + $maxage : 0;
	return setcookie($_W['config']['cookie']['pre'] . $key, $value, $expire, $_W['config']['cookie']['path'], $_W['config']['cookie']['domain']);
}

/**
 * 获取客户ip
 * @return string
 *			 返回IP地址
 *			 如果未获取到返回unknown
 */
function getip() {
	static $onlineip = '';
	if ($onlineip) {
		return $onlineip;
	}
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$onlineip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	preg_match('/[\d\.]{7,15}/', $onlineip, $onlineipmatches);
	$onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	return $onlineip;
}

/**
 * 消息提示窗
 * @param string $msg
 * 提示消息内容
 *
 * @param string $redirect
 * 跳转地址
 *
 * @param string $type 提示类型
 * 		success		成功
 * 		error		错误
 * 		question	询问(问号)
 * 		attention	注意(叹号)
 * 		tips		提示(灯泡)
 * 		ajax		json
 */
function message($msg, $redirect = '', $type = '') {
	global $_W;
    if($redirect == 'refresh') {
        $redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
    }
	if($redirect == '') {
		$type = in_array($type, array('success', 'error', 'question', 'attention', 'tips', 'ajax')) ? $type : 'error';
	} else {
		$type = in_array($type, array('success', 'error', 'question', 'attention', 'tips', 'ajax')) ? $type : 'success';
	}
	if($_W['isajax'] || $type == 'ajax') {
		$vars = array();
		$vars['message'] = $msg;
		$vars['redirect'] = $redirect;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}
	//后台消息提示
	if (defined('IN_MANAGEMENT')) {
		$message = '<div class="message"><div  class="message message-'.$type.'">';
		$message .= '<div class="image"><img src="resource/image/management/'.$type.'.png" alt="'.$type.'" height="32" /></div>';
		$message .= '<div class="text"><h6>'.$msg.'</h6>';
		if (!empty($redirect)) {
			$message .= '<span><p><a href="'.$redirect.'">如果你的浏览器没有自动跳转，请点击此链接</a></p></span></div>';
			$message .= '<script type="text/javascript">setTimeout(function () {location.href = "'.$redirect.'";}, 3000);</script>';
		} elseif ($type == 'error') {
			$message .= '<span><p><a href="javascript:history.go(-1);">点击返回上一页</a></p></span></div>';
		}
		$message .= '</div>';
		exit($message);
	} else {
		if (empty($msg) && !empty($redirect)) {
			header('Location: '.$redirect);
		}
		//前台消息提示
		include template('common/message', TEMPLATE_INCLUDEPATH);
		exit();
	}
}

/**
 * 生成token
 */
function token($specialadd = '') {
	global $_W;
	$hashadd = defined('IN_MANAGEMENT') ? 'for management' : '';
	return substr(md5($_W['uid'] . $_W['config']['setting']['authkey'] . $hashadd . $specialadd), 8, 8);
}

function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	if($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

/**
 * 运行钩子
 * @param string $name 钩子名称
 * @param mixed $context 传递给钩子函数的上下文数据，引用传递
 * @return void
 */
function hooks($name, &$context = null) {

}

/**
 * 提交来源检查
 */
function checksubmit($var = 'submit', $allowget = 0) {
	global $_W, $_GPC;
	if (empty($_GPC[$var])) {
		return FALSE;
	}
	if ($allowget || (($_W['ispost'] && !empty($_W['token']) && $_W['token'] == $_GPC['token']) && (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
		return TRUE;
	}
	return FALSE;
}

/**
 * 检查是否登录
 * @param boolean $redirect 是否自动跳转登录
 * @return boolean
 */
function checklogin($redirect = true) {
	global $_W;
	if (empty($_W['uid'])) {
		if (empty($redirect)) {
			return false;
		} else {
			exit('<script type="text/javascript">window.top.location.href="' . create_url('member/login', array('referer' => $_W['script_name'])) . '";</script>');
		}
	}
	return true;
}

function checkaccount() {
	global $_W;
	if (empty($_W['weid']) || empty($_W['account'])) {
		message('请您先添加您的公众账号', '', 'error');
	}
}

function checkpermission($type, $target) {
	global $_W;
	if (!empty($_W['isfounder']) || empty($target)) {
		return true;
	}
	if ($type == 'wechats') {
		if (is_array($target)) {
			$account = $target;
		} else {
			$account = pdo_fetch("SELECT uid FROM ".tablename('wechats')." WHERE weid = '$target' LIMIT 1");
		}
		if ($account['uid'] != $_W['uid']) {
			return false;
		}
	}
	return true;
}

/**
 * 返回完整数据表名(加前缀)
 * @param string $table
 * @return string
 */
function tablename($table) {
	return "`{$GLOBALS['_W']['config']['db']['tablepre']}{$table}`";
}

function router($controller, $action) {
	$controllerfile = IA_ROOT . '/source/controller/' . ($controller ? $controller . '/' : '') . $action . '.ctrl.php';
	if (file_exists($controllerfile)) {
		return $controllerfile;
	} else {
		trigger_error('Invalid Controller "'.$action.'"', E_USER_ERROR);
		return '';
	}
}

function model($model) {
	$file = IA_ROOT . '/source/model/' . $model . '.mod.php';
	if (file_exists($file)) {
		return $file;
	} else {
		trigger_error('Invalid Model', E_USER_ERROR);
		return '';
	}
}

/**
 * 创建指定模块(工厂函数)
 * @param string $name 模块标识
 * @return WeModule
 */
function module($name) {

	$classname = ucfirst($name).'Module';
	if(!class_exists($classname)) {
		$file = IA_ROOT . "/source/modules/{$name}/module.php";
		if(!is_file($file)) {
			trigger_error('Module Definition File Not Found', E_USER_ERROR);
			return null;
		}
		require $file;
	}
	if(!class_exists($classname)) {
		trigger_error('Module Definition Class Not Found', E_USER_ERROR);
		return null;
	}
	$o = new $classname();
	if($o instanceof WeModule) {
		return $o;
	} else {
		trigger_error('Module Class Definition Error', E_USER_ERROR);
		return null;
	}
}


/**
 * 该函数从一个数组中取得若干元素。该函数测试（传入）数组的每个键值是否在（目标）数组中已定义；如果一个键值不存在，该键值所对应的值将被置为FALSE，或者你可以通过传入的第3个参数来指定默认的值。
 * @param array $items 需要筛选的键名定义
 * @param array $array 要进行筛选的数组
 * @param mixed $default 如果原数组未定义的键，则使用此默认值返回
 * @return array
 */
function array_elements($items, $array, $default = FALSE) {
	$return = array();
	if(!is_array($items)) {
		$items = array($items);
	}
	foreach($items as $item) {
		if(isset($array[$item])) {
			$return[$item] = $array[$item];
		} else {
			$return[$item] = $default;
		}
	}
	return $return;
}
/**
 * JSON编码,加上转义操作,适合于JSON入库
 *
 * @param string $value
 */
function ijson_encode($value) {
	if (empty($value)) {
		return false;
	}
	return addcslashes(json_encode($value), "\\\'\"");
}
/**
 * 序列化操作
 *
 * @param string $value
 */
function iserializer($value) {
	return serialize($value);
}
/**
 * 解序列化
 *
 * @param array $value
 */
function iunserializer($value) {
	if (empty($value)) {
		return '';
	}
	if (is_array($value)) {
		return $value;
	}
	$result = unserialize($value);
	return empty($result) ? $value : $result;
}

/**
 * 生成分页数据
 * @param int $currentPage 当前页码
 * @param int $totalCount 总记录数
 * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
 * @param int $pageSize 分页大小
 * @return string 分页HTML
 */
function pagination($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '')) {
	global $_W;
	$pdata = array(
		'tcount' => 0,
		'tpage' => 0,
		'cindex' => 0,
		'findex' => 0,
		'pindex' => 0,
		'nindex' => 0,
		'lindex' => 0,
		'options' => ''
	);
	if($context['ajaxcallback']) {
		$context['isajax'] = true;
	}

	$pdata['tcount'] = $tcount;
	$pdata['tpage'] = ceil($tcount / $psize);
	if($pdata['tpage'] <= 1) {
		return '';
	}
	$cindex = $pindex;
	$cindex = min($cindex, $pdata['tpage']);
	$cindex = max($cindex, 1);
	$pdata['cindex'] = $cindex;
	$pdata['findex'] = 1;
	$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
	$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
	$pdata['lindex'] = $pdata['tpage'];
	$currentpage = $_GET['page'];
	unset($_GET['page']);
	if($context['isajax']) {
		if(!$url) {
			$url = $_W['script_name'] . '?' . http_build_query($_GET);
		}
		$pdata['faa'] = 'href="javascript:;" onclick="pager(\'' . $url . '\', \'' . $pdata['findex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['paa'] = 'href="javascript:;" onclick="pager(\'' . $url . '\', \'' . $pdata['pindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['naa'] = 'href="javascript:;" onclick="pager(\'' .  $url . '\', \'' . $pdata['nindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['laa'] = 'href="javascript:;" onclick="pager(\'' . $url . '\', \'' . $pdata['lindex'] . '\', ' . $context['ajaxcallback'] . ')"';
	} else {
		if(!$url) {
			$url = http_build_query($_GET) . '&page=*';
		}
		if($url) {
			$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
			$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
			$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
			$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
		} else {
			$currentpage = $pdata['findex'];
			$pdata['faa'] = 'href="?' . http_build_query($_GET) . '"';
			$currentpage = $pdata['pindex'];
			$pdata['paa'] = 'href="?' . http_build_query($_GET) . '"';
			$currentpage = $pdata['nindex'];
			$pdata['naa'] = 'href="?' . http_build_query($_GET) . '"';
			$currentpage = $pdata['lindex'];
			$pdata['laa'] = 'href="?' . http_build_query($_GET) . '"';
		}
	}

	$html = '<div class="pagination pagination-centered"><ul>';
	if($pdata['cindex'] > 1) {
		$html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
		$html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
	}
	//页码算法：前5后4，不足10位补齐
	if(!$context['before']) {
		$context['before'] = 5;
	}
	if(!$context['after']) {
		$context['after'] = 4;
	}
	$range = array();
	$range['start'] = max(1, $pdata['cindex'] - $context['before']);
	$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
	if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
		$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
		$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
	}
	for ($i = $range['start']; $i <= $range['end']; $i++) {
		if($context['isajax']) {
			$aa = 'href="javascript:;" onclick="pager(\'' . $url . '\', \'' . $i . '\', ' . $context['ajaxcallback'] . ')"';
		} else {
			if($url) {
				$aa = 'href="?' . str_replace('*', $i, $url) . '"';
			} else {
				$currentpage = $i;
				$aa = 'href="?' . http_build_query($_GET) . '"';
			}
		}
		$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
	}
	if($pdata['cindex'] < $pdata['tpage']) {
		$html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
		$html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
	}
	$html .= '</ul></div>';
	return $html;
}

/**
 * 构造错误数组
 *
 * @param string $errormsg 错误信息，通知上层应用具体错误信息。
 * @param int $errorcode 错误码，0为无任何错误。
 * @return array
 */
function error($code, $msg = '') {
	return array(
		'code' => $code,
		'message' => $msg,
	);
}

/**
 * 检测返回值是否产生错误
 *
 * 产生错误则返回true，否则返回false
 *
 * @param mixed $data   待检测的数据
 * @return boolean
 */
function is_error($data) {
	if (empty($data) || !is_array($data) || !array_key_exists('code', $data) || $data['code'] == 0) {
		return false;
	} else {
		return true;
	}
}
/**
 * 生成URL，统一生成方便管理
 * @param string $router
 * @param array $params
 * @return string
 */
function create_url($router, $params = array()) {
	list($module, $controller, $do) = explode('/', $router);
	$queryString = http_build_query($params, '', '&');
	return $module.'.php?act='.$controller . (empty($do) ? '' : '&do='.$do) . '&'. $queryString;
}
/**
 * 获取引用页
 */
function referer($default = '') {
	global $_GPC, $_W;

	$_W['referer'] = !empty($_GPC['referer']) ? $_GPC['referer'] : $_SERVER['HTTP_REFERER'];;
	$_W['referer'] = substr($_W['referer'], -1) == '?' ? substr($_W['referer'], 0, -1) : $_W['referer'];

	if(strpos($_W['referer'], 'member.php?act=login')) {
		$_W['referer'] = $default;
	}
	$_W['referer'] = htmlspecialchars($_W['referer'], ENT_QUOTES);
	$_W['referer'] = str_replace('&amp;', '&', $_W['referer']);
	$reurl = parse_url($_W['referer']);

	if(!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.'.$_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.'.$reurl['host']))) {
		$_W['referer'] = $_W['siteroot'];
	} elseif(empty($reurl['host'])) {
		$_W['referer'] = $_W['siteroot'].'./'.$_W['referer'];
	}
	return strip_tags($_W['referer']);
}

/**
 * 是否包含子串
 */

function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}
/**
 * 兼容其它平台环境
 * @param string $func 方法名
 * @param string $platform 平台名
 * @return boolean|string
 */
function platform($func = '', $platform = '') {
	global $_W;
	$platform = empty($platform) ? $_W['platform'] : $platform;
	$func = $func . $platform;
	if (empty($func) || empty($platform)) {
		return FALSE;
	}
	if (!function_exists($func)) {
		$file = IA_ROOT . '/source/function/'.$platform.'.func.php';
		if (!file_exists($file)) {
			return FALSE;
		}
		include_once $file;
	}
	if (!function_exists($func)) {
		return FALSE;
	}
	return $func;
}
/**
 * 运行模块勾子
 * @param string $hookname
 */
function runhook($hookname) {
	global $_W;
	$hooks = $_W['cache']['hooks'][$_W['weid']];
	if (empty($hooks)) {
		cache_build_hook($_W['uid']);
		$hooks = cache_load("hooks:{$_W['weid']}");
	}
	if (!empty($hooks[$hookname])) {
		foreach ($hooks[$hookname] as $hook) {
			$hookobj = WeUtility::createModuleProcessor($hook[0]);
			if (method_exists($hookobj, $hook[1])) {
				call_user_func(array($hookobj, $hook[1]));
			}
		}
	}
}

function cutstr($string, $length, $havedot=0, $charset='') {
	global $_W;
	if(empty($charset)) {
		$charset = $_W['charset'];
	}
	if(strtolower($charset) == 'gbk') {
		$charset = 'gbk';
	} else {
		$charset = 'utf8';
	}
	if(istrlen($string, $charset) <= $length) {
		return $string;
	}
	if(function_exists('mb_strcut')) {
		$string = mb_substr($string, 0, $length, $charset);
	} else {
		$pre = '{%';
		$end = '%}';
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

		$strcut = '';
		$strlen = strlen($string);

		if($charset == 'utf8') {
			$n = $tn = $noc = 0;
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc++;
				} elseif(224 <= $t && $t <= 239) {
					$tn = 3; $n += 3; $noc++;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc++;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc++;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc++;
				} else {
					$n++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		} else {
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t > 127) {
					$tn = 2; $n += 2; $noc++;
				} else {
					$tn = 1; $n++; $noc++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		}
		$string = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	}

	if($havedot) {
		$string = $string . "...";
	}

	return $string;
}

function istrlen($string, $charset='') {
	global $_W;
	if(empty($charset)) {
		$charset = $_W['charset'];
	}
	if(strtolower($charset) == 'gbk') {
		$charset = 'gbk';
	} else {
		$charset = 'utf8';
	}
	if(function_exists('mb_strlen')) {
		return mb_strlen($string, $charset);
	} else {
		$n = $noc = 0;
		$strlen = strlen($string);

		if($charset == 'utf8') {

			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$n += 2; $noc++;
				} elseif(224 <= $t && $t <= 239) {
					$n += 3; $noc++;
				} elseif(240 <= $t && $t <= 247) {
					$n += 4; $noc++;
				} elseif(248 <= $t && $t <= 251) {
					$n += 5; $noc++;
				} elseif($t == 252 || $t == 253) {
					$n += 6; $noc++;
				} else {
					$n++;
				}
			}

		} else {

			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t>127) {
					$n += 2; $noc++;
				} else {
					$n++; $noc++;
				}
			}

		}

		return $noc;
	}
}

function emotion($message = '', $size = '24px') {
	$emotions = array(
		"/::)","/::~","/::B","/::|","/:8-)","/::<","/::$","/::X","/::Z","/::'(",
		"/::-|","/::@","/::P","/::D","/::O","/::(","/::+","/:--b","/::Q","/::T",
		"/:,@P","/:,@-D","/::d","/:,@o","/::g","/:|-)","/::!","/::L","/::>","/::,@",
		"/:,@f","/::-S","/:?","/:,@x","/:,@@","/::8","/:,@!","/:!!!","/:xx","/:bye",
		"/:wipe","/:dig","/:handclap","/:&-(","/:B-)","/:<@","/:@>","/::-O","/:>-|",
		"/:P-(","/::'|","/:X-)","/::*","/:@x","/:8*","/:pd","/:<W>","/:beer","/:basketb",
		"/:oo","/:coffee","/:eat","/:pig","/:rose","/:fade","/:showlove","/:heart",
		"/:break","/:cake","/:li","/:bome","/:kn","/:footb","/:ladybug","/:shit","/:moon",
		"/:sun","/:gift","/:hug","/:strong","/:weak","/:share","/:v","/:@)","/:jj","/:@@",
		"/:bad","/:lvu","/:no","/:ok","/:love","/:<L>","/:jump","/:shake","/:<O>","/:circle",
		"/:kotow","/:turn","/:skip","/:oY","/:#-0","/:hiphot","/:kiss","/:<&","/:&>"
	);
	foreach ($emotions as $index => $emotion) {
		$message = str_replace($emotion, '<img style="width:'.$size.';vertical-align:middle;" src="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/'.$index.'.gif" />', $message);
	}
	return $message;
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : $GLOBALS['_W']['config']['setting']['authkey']);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}
