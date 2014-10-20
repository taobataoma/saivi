<?php 
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
function rule_keywords_search($condition = '', $pindex = 0, $psize = 10, &$total = 0) {
	if(!empty($condition)) {
		$where = " WHERE {$condition} ";
	}
	$sql = 'SELECT * FROM ' . tablename('rule_keyword') . $where . 'ORDER BY id DESC';
	if($pindex > 0) {
		// 需要分页
		$start = ($pindex - 1) * $psize;
		$sql .= " LIMIT {$start},{$psize}";
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rule_keyword') . $where);
	}
	return pdo_fetchall($sql);
}

function rule_search($condition = '', $pindex = 0, $psize = 10, &$total = 0) {
	if(!empty($condition)) {
		$where = "WHERE {$condition}";
	}
	$sql = 'SELECT * FROM ' . tablename('rule') . $where;
	if($pindex > 0) {
		// 需要分页
		$start = ($pindex - 1) * $psize;
		$sql .= " LIMIT {$start},{$psize}";
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rule') . $where);
	}
	return pdo_fetchall($sql);
}

function rule_single($id) {
	$result = array();
	$id = intval($id);
	$result['rule'] = pdo_fetch("SELECT * FROM ".tablename('rule')." WHERE id = :id", array(':id' => $id));
	if (empty($result['rule'])) {
		return $result;
	}
	$category = pdo_fetch("SELECT id, name, parentid FROM ".tablename('category')." WHERE id = '{$result['rule']['cid']}' LIMIT 1");
	if ($category['parentid'] == 0) {
		$result['rule']['cate'] = array($category['id'], 0);
	} else {
		$result['rule']['cate'] = array($category['parentid'], $category['id'], $category['name']);
	}
	$result['keyword'] = pdo_fetchall("SELECT * FROM ".tablename('rule_keyword')." WHERE rid = :rid", array(':rid' => $id));
	return $result;
}

function rule_insert_keyword($data) {
	if (!isset($data['rid']) || !isset($data['content'])) {
		return error(1, '参数不完整');
	}
	if (isset($data['id'])) {
		$updateid = $data['id'];
	}
	//正则表达式需要验证其合法性
	if ($data['type'] == 3) {
					
	}
	$insert = array(
		'content' => $data['content'],
		'type' => $data['type'],
		'rid' => $data['rid'],
		'weid' => $data['weid'],
		
	);
	!empty($data['module']) && $insert['module'] = $data['module'];
	if (!empty($updateid) && $updateid > 0) {
		$result = pdo_update('rule_keyword', $insert, array('id' => $updateid));
	} else {
		$result = pdo_insert('rule_keyword', $insert);
	}
	if ($result) {
		return true;	
	} else {
		return error(1, '添加记录失败');
	}
}


?>
