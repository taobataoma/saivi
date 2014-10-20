<?php 
/**
 * 分类管理
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
checkaccount();
$do = !empty($_GPC['do']) ? $_GPC['do'] : 'display';

if ($do == 'display') {
	if (!empty($_GPC['displayorder'])) {
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			pdo_update('category', array('displayorder' => $displayorder), array('id' => $id));
		}
		cache_build_category();
		message('分类排序更新成功！', create_url('setting/category'), 'success');
	}
	$children = array();
	$category = pdo_fetchall("SELECT * FROM ".tablename('category')." WHERE weid = '{$_W['weid']}' ORDER BY parentid ASC");
	foreach ($category as $index => $row) {
		if (!empty($row['parentid'])){
			$children[$row['parentid']][] = $row;
			unset($category[$index]);
		}
	}
	template('setting/category_display');	
} elseif ($do == 'post') {
	$parentid = intval($_GPC['parentid']);
	$id = intval($_GPC['id']);
	if(!empty($id)) {
		$category = pdo_fetch("SELECT * FROM ".tablename('category')." WHERE id = '$id'");
	} else {
		$category = array(
			'displayorder' => 0,
			'enabled' => 1,	
		);
	}
	if (!empty($parentid)) {
		$parent = pdo_fetch("SELECT id, name FROM ".tablename('category')." WHERE id = '$parentid'");
		if (empty($parent)) {
			message('抱歉，上级分类不存在或是已经被删除！', create_url('setting/category'), 'error');
		}
	}
	if (checksubmit('submit')) { 
		if (empty($_GPC['name'])) {
			message('抱歉，请输入分类名称！');
		}
		$data = array(
			'weid' => $_W['weid'],
			'name' => $_GPC['name'],
			'displayorder' => intval($_GPC['displayorder']),
			'enabled' => intval($_GPC['enabled']),
			'parentid' => intval($parentid),
			'description' => $_GPC['description'],
		);
		if (!empty($_FILES['icon']['tmp_name'])) {
			if (!empty($category['icon'])) {
				file_delete($category['icon']);
			}
			$upload = file_upload($_FILES['icon']);
			if (is_error($upload)) {
				message($upload['message']);
			}
			$data['icon'] = $upload['path'];
		}
		if (!empty($id)) {
			unset($data['parentid']);
			pdo_update('category', $data, array('id' => $id));
		} else {
			pdo_insert('category', $data);
		}
		cache_build_category();
		message('更新分类成功！', create_url('setting/category'), 'success');
	}
	template('setting/category_post');
} elseif ($do == 'fetch') {
	$category = pdo_fetchall("SELECT id, name FROM ".tablename('category')." WHERE parentid = '".intval($_GPC['parentid'])."' ORDER BY id ASC");
	message($category, '', 'ajax');
} elseif ($do == 'delete') {
	$id = intval($_GPC['id']);
	$category = pdo_fetch("SELECT id, parentid FROM ".tablename('category')." WHERE id = '$id'");
	if (empty($category)) {
		message('抱歉，分类不存在或是已经被删除！', create_url('setting/category'), 'error');
	}
	pdo_delete('category', array('id' => $id, 'parentid' => $id), 'OR');
	cache_build_category();
	message('分类删除成功！', create_url('setting/category'), 'success');
}
