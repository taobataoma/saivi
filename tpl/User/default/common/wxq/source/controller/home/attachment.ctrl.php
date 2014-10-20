<?php 
/**
 * 上传图片
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
checklogin();
$do = !empty($_GPC['do']) ? $_GPC['do'] : exit('Access Denied');
$result = array('error' => 1, 'message' => '');
if ($do == 'upload') {
	if (!empty($_FILES['imgFile']['name'])) {
		if ($_FILES['imgFile']['error'] != 0) {
			$result['message'] = '上传失败，请重试！';
			exit(json_encode($result));
		}
		$_W['uploadsetting'] = array();
		$_W['uploadsetting']['image']['folder'] = 'images';
		$_W['uploadsetting']['image']['extentions'] = $_W['config']['upload']['image']['extentions'];
		$_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit']; 
		$file = file_upload($_FILES['imgFile'], 'image');
		if (is_error($file)) {
			$result['message'] = $file['message'];
			exit(json_encode($result));
		}
		$result['url'] = $file['url'];
		$result['error'] = 0;
		$result['filename'] = $file['path'];
		$result['url'] = $_W['attachurl'].$result['filename'];
		exit(json_encode($result));
	} else {
		$result['message'] = '请选择要上传的图片！';
		exit(json_encode($result));
	}
} elseif ($do == 'delete') {
	if (empty($_GPC['filename'])) {
		$result['message'] = '请选择要删除的图片！';
		exit(json_encode($result));
	}
	file_delete($_GPC['filename']);
	$result['error'] = 0;
	exit(json_encode($result));
}
?>