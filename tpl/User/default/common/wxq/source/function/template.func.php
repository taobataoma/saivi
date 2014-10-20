<?php
/**
 * 模板操作
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 导入全局变量，并直接显示模板页内容。
 * @var int
 */
define('TEMPLATE_DISPLAY', 0);

/**
 * 导入全局变量，并返回模板页内容的字符串
 * @var int
*/
define('TEMPLATE_FETCH', 1);

/**
 * 返回模板编译文件的包含路径
 * @var int
*/
define('TEMPLATE_INCLUDEPATH', 2);

/**
 * 缓存输出信息，@todo 未完成
 * @var int
*/
define('TEMPLATE_CACHE', 3);

function template($filename, $flag = TEMPLATE_DISPLAY) {
	global $_W;
	$source = "{$_W['template']['source']}/{$_W['template']['current']}/{$filename}.html";
//    exit($source);
	if(!is_file($source)) {
		$source = "{$_W['template']['source']}/default/{$filename}.html";
	}
	if(!is_file($source)) {
		exit("Error: template source '{$filename}' is not exist!");
	}
	$compile = "{$_W['template']['compile']}/{$_W['template']['current']}/{$filename}.tpl.php";
	if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
		template_compile($source, $compile);
	}
	switch ($flag) {
		case TEMPLATE_DISPLAY:
		default:
			extract($GLOBALS, EXTR_SKIP);
			include $compile;
			break;
		case TEMPLATE_FETCH:
			extract($GLOBALS, EXTR_SKIP);
			ob_start();
			ob_clean();
			include $compile;
			$contents = ob_get_contents();
			ob_clean();
			return $contents;
			break;
		case TEMPLATE_INCLUDEPATH:
			return $compile;
			break;
		case TEMPLATE_CACHE:
			exit('暂未支持');
			break;
	}
}

function template_compile($from, $to) {
	$path = dirname($to);
	if (!is_dir($path))
		mkdirs($path);
	$content = template_parse(file_get_contents($from));
	file_put_contents($to, $content);
}

function template_parse($str) {
	$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
	$str = preg_replace('/{template\s+(.+?)}/', '<?php include template($1, TEMPLATE_INCLUDEPATH);?>', $str);
	$str = preg_replace('/{php\s+(.+?)}/', '<?php $1?>', $str);
	$str = preg_replace('/{if\s+(.+?)}/', '<?php if($1) { ?>', $str);
	$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
	$str = preg_replace('/{else ?if\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
	$str = preg_replace('/{\/if}/', '<?php } ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
	$str = preg_replace('/{\/loop}/', '<?php } } ?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\[\]\'\"\$]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/<\?php([^\?]+)\?>/es', "template_addquote('<?php$1?>')", $str);
	$str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
	$str = str_replace('{##', '{', $str);
	$str = str_replace('##}', '}', $str);
	$str = "<?php defined('IN_IA') or exit('Access Denied');?>" . $str;
	return $str;
}
function template_addquote($code) {
	$code = preg_replace('/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s', "['$1']", $code);
	return str_replace('\\\"', '\"', $code);
}
