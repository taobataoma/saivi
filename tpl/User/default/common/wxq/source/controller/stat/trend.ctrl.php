<?php 
/**
 * 统计走势图
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */

defined('IN_IA') or exit('Access Denied');
$do = !empty($_GPC['do']) ? $_GPC['do'] : exit('Access Denied');

if ($do == 'rule') {
	$id = intval($_GPC['id']);
	$days = !empty($_GPC['days']) ? intval($_GPC['days']) : 7;
	
	$starttime = strtotime(date('Y-m-d', strtotime('-7 day')));
	$list = pdo_fetchall("SELECT createtime, hit  FROM ".tablename('stat_rule')." WHERE weid = '{$_W['weid']}' AND rid = :rid AND createtime >= :createtime ORDER BY createtime ASC", array(':rid' => $id, ':createtime' => $starttime));
	$day = $hit = array();
	if (!empty($list)) {
		foreach ($list as $row) {
			$day[] = date('m-d', $row['createtime']);
			$hit[] = intval($row['hit']);
		}
	}
	
	$list = pdo_fetchall("SELECT createtime, hit, rid, kid FROM ".tablename('stat_keyword')." WHERE weid = '{$_W['weid']}' AND rid = :rid AND createtime >= :createtime ORDER BY createtime ASC", array(':rid' => $id, ':createtime' => $starttime));
	if (!empty($list)) {
		foreach ($list as $row) {
			$keywords[$row['kid']]['hit'][] = $row['hit'];
			$keywords[$row['kid']]['day'][] = date('m-d', $row['createtime']);
		}
		$keywordnames = pdo_fetchall("SELECT content, id FROM ".tablename('rule_keyword')." WHERE id IN (".implode(',', array_keys($keywords)).")", array(), 'id');
	}
	template('stat/trend');
}