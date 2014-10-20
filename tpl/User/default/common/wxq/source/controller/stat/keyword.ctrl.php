<?php 
/**
 * 关键字利用率
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
$do = !empty($_GPC['do']) ? $_GPC['do'] : 'hit';

$where = '';
$starttime = empty($_GPC['starttime']) ? strtotime(date('Y-m-d')) : strtotime($_GPC['starttime']);
$endtime = empty($_GPC['endtime']) ? TIMESTAMP : strtotime($_GPC['endtime']) + 86399;
$where .= " AND createtime >= '$starttime' AND createtime < '$endtime'";

if ($do == 'hit') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$list = pdo_fetchall("SELECT * FROM ".tablename('stat_keyword')." WHERE  weid = '{$_W['weid']}' $where ORDER BY hit DESC LIMIT ".($pindex - 1) * $psize.','. $psize);
	if (!empty($list)) {
		foreach ($list as $index => &$history) {
			if (!empty($history['rid'])) {
				$rids[$history['rid']] = $history['rid'];
			}
			$kids[$history['kid']] = $history['kid'];
		}
	}
	if (!empty($rids)) {
		$rules = pdo_fetchall("SELECT name, id, module FROM ".tablename('rule')." WHERE id IN (".implode(',', $rids).")", array(), 'id');
	}
	if (!empty($kids)) {
		$keywords = pdo_fetchall("SELECT content, id FROM ".tablename('rule_keyword')." WHERE id IN (".implode(',', $kids).")", array(), 'id');
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('stat_keyword')." WHERE  weid = '{$_W['weid']}' $where");
	$pager = pagination($total, $pindex, $psize);
	template('stat/keyword_hit');
} elseif ($do == 'miss') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$list = pdo_fetchall("SELECT content, id, module, rid FROM ".tablename('rule_keyword')." WHERE weid = '{$_W['weid']}' AND id NOT IN (SELECT kid FROM ".tablename('stat_keyword')." WHERE  weid = '{$_W['weid']}' $where) LIMIT ".($pindex - 1) * $psize.','. $psize);
	if (!empty($list)) {
		foreach ($list as $index => $row) {
			if (!empty($row['rid'])) {
				$rids[$row['rid']] = $row['rid'];
			}
		}
	}
	if (!empty($rids)) {
		$rules = pdo_fetchall("SELECT name, id, module FROM ".tablename('rule')." WHERE id IN (".implode(',', $rids).")", array(), 'id');
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('rule_keyword')." WHERE weid = '{$_W['weid']}' AND id NOT IN (SELECT kid FROM ".tablename('stat_keyword')." WHERE  weid = '{$_W['weid']}' $where)");
	$pager = pagination($total, $pindex, $psize);
	template('stat/keyword_miss');
}