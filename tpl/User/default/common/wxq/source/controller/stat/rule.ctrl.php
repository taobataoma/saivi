<?php 
/**
 * 规则利用率
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
	$list = pdo_fetchall("SELECT * FROM ".tablename('stat_rule')." WHERE  weid = '{$_W['weid']}' $where ORDER BY hit DESC LIMIT ".($pindex - 1) * $psize.','. $psize);
	if (!empty($list)) {
		foreach ($list as $index => &$history) {
			if (!empty($history['rid'])) {
				$rids[$history['rid']] = $history['rid'];
			}	
		}
	}
	if (!empty($rids)) {
		$rules = pdo_fetchall("SELECT name, id, module FROM ".tablename('rule')." WHERE id IN (".implode(',', $rids).")", array(), 'id');
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('stat_rule')." WHERE weid = '{$_W['weid']}' $where");
	$pager = pagination($total, $pindex, $psize);
	template('stat/rule_hit');
} elseif ($do == 'miss') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$list = pdo_fetchall("SELECT name, id, module FROM ".tablename('rule')." WHERE weid = '{$_W['weid']}' AND id NOT IN (SELECT rid FROM ".tablename('stat_rule')." WHERE  weid = '{$_W['weid']}' $where) LIMIT ".($pindex - 1) * $psize.','. $psize);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('rule')." WHERE weid = '{$_W['weid']}' AND id NOT IN (SELECT rid FROM ".tablename('stat_rule')." WHERE  weid = '{$_W['weid']}' $where)");
	$pager = pagination($total, $pindex, $psize);
	template('stat/rule_miss');
}