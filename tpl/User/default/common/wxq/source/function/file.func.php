<?php
/**
 * 文件操作
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 将数据写入某文件，如果文件或目录不存在，则创建
 * @param string $filename 要写入的目标
 * @param string $data 要写入的数据
 * @return bool
 */
function file_write($filename, $data) {
	global $_W;
	//兼容其它平台上传
	if ($func = platform('file_write')) {
		return call_user_func($func, $filename, $data);
	}
	$filename = IA_ROOT . '/' . $GLOBALS['_W']['config']['upload']['attachdir'] . $filename;
	mkdirs(dirname($filename));
	file_put_contents($filename, $data);
	@chmod($filename, $_W['config']['setting']['filemode']);
	return is_file($filename);
}

/**
 * 将文件移动至目标位置，如果目标位置目录不存在，则创建
 * @param string $filename 要移动的文件
 * @param string $desc 移动的目标位置
 * @return bool
 */
function file_move($filename, $desc) {
	global $_W;
	mkdirs(dirname($desc));
	if(is_uploaded_file($filename)) {
		move_uploaded_file($filename, $desc);
	} else {
		rename($filename, $desc);
	}
	@chmod($filename, $_W['config']['setting']['filemode']);
	return is_file($desc);
}

/**
 * 递归创建目录树
 * @param string $path 目录树
 * @return bool
 */
function mkdirs($path) {   
	if(!is_dir($path)) {
		mkdirs(dirname($path));
		mkdir($path);   
	}   
	return is_dir($path);   
}

/**
 * 删除目录（递归删除内容）
 * @param string $path 目录位置
 * @param bool $clean 不删除目录，仅删除目录内文件
 * @return bool
 */
function rmdirs($path, $clean=false) {
	if(!is_dir($path)) {
		return false;
	}
	$files = glob($path . '/*');
	if($files) {
		foreach($files as $file) {
			is_dir($file) ? rmdirs($file) : @unlink($file);
		}
	}
	return $clean ? true : @rmdir($path);
}

/**
 * 上传文件保存，缩略图暂未实现
 * @param string $fname 上传的$_FILE字段
 * @param string $type 上传类型（将按分类保存不同子目录，image -> images）
 * @param string $sname 保存的文件名，如果为 auto 则自动生成文件名，否者请指定从附件目录开始的完整相对路径（包括文件名，不包括文件扩展名）
 * @return array 返回结果数组，字段包括：success => bool 是否上传成功，path => 保存路径（从附件目录开始的完整相对路径），message => 提示信息
 */
function file_upload($file, $type = 'image', $sname = 'auto') {
	if(empty($file)) {
		return error(-1, '没有上传内容');
	}
	global $_W;
	if (empty($_W['uploadsetting'])) {
		$_W['uploadsetting'] = array();
		$_W['uploadsetting']['image']['folder'] = 'images';
		$_W['uploadsetting']['image']['extentions'] = $_W['config']['upload']['image']['extentions'];
		$_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
	}
	$settings = $_W['uploadsetting'];
	if(!array_key_exists($type, $settings)) {
		return error(-1, '未知的上传类型');
	}
	$extention = pathinfo($file['name'], PATHINFO_EXTENSION);
	if(!in_array($extention, $settings[$type]['extentions'])) {
		return error(-1, '不允许上传此类文件');
	}
	if(!empty($settings[$type]['limit']) && $settings[$type]['limit'] * 1024 < filesize($file['tmp_name'])) {
		return error(-1, "上传的文件超过大小限制，请上传小于 {$settings[$type]['limit']}k 的文件");
	}
   	//兼容其它平台上传
	if ($func = platform('file_upload')) {
		return call_user_func($func, $file, $type);
	}
	
	$result = array();
	$path = '/'.$_W['config']['upload']['attachdir'];

	if($sname == 'auto') {
		$result['path'] = "{$settings[$type]['folder']}/" . date('Y/m/');
		mkdirs(IA_ROOT . $path . $result['path']);
		do {
			$filename = random(30) . ".{$extention}";
		} while(file_exists(IA_ROOT . $path . $filename));
		$result['path'] .= $filename;
	} else {
		$result['path'] = "{$settings[$type]['folder']}/" . $sname . '.' . $extention;  
		mkdirs(IA_ROOT . dirname($path));
	}
	$filename = IA_ROOT . $path . $result['path'];
	if(!file_move($file['tmp_name'], $filename)) {
		return error(-1, '保存上传文件失败');
	}
	$result['success'] = true;
	return $result; 
}
/**
 * 删除文件
 * 
 */
function file_delete($file) {
	global $_W;
	if (empty($file)) {
		return FALSE;	
	}	
	//兼容其它平台上传
	if ($func = platform('file_delete')) {
		return call_user_func($func, $file);
	}
	if (file_exists(IA_ROOT . '/' . $_W['config']['upload']['attachdir'] . '/' . $file)) {
		unlink(IA_ROOT . '/' . $_W['config']['upload']['attachdir'] . '/' . $file);
	}
	return TRUE;
}

