<?php 
/**
 * 用户聊天记录
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */

defined('IN_IA') or exit('Access Denied');

$where = '';
$starttime = empty($_GPC['starttime']) ? strtotime(date('Y-m-d')) : strtotime($_GPC['starttime']);
$endtime = empty($_GPC['endtime']) ? TIMESTAMP : strtotime($_GPC['endtime']) + 86399;
$where .= " AND createtime >= '$starttime' AND createtime < '$endtime'";
!empty($_GPC['keyword']) && $where .= " AND message LIKE '%{$_GPC['keyword']}%'";

switch ($_GPC['searchtype']) {
	case 'default':
		$where .= " AND module = 'default'";
		break;
	case 'rule':
	default:
		$where .= " AND module <> 'default'";
		break;
}
$pindex = max(1, intval($_GPC['page']));
$psize = 50;
$list = pdo_fetchall("SELECT * FROM ".tablename('stat_msg_history')." WHERE weid = '{$_W['weid']}' $where ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','. $psize);
if (!empty($list)) {
	foreach ($list as $index => &$history) {
		if ($history['type'] == 'link') {
			$history['message'] = iunserializer($history['message']);
			$history['message'] = '<a href="'.$history['message']['link'].'" target="_blank" title="'.$history['message']['description'].'">'.$history['message']['title'].'</a>';
		} elseif ($history['type'] == 'image') {
			$history['message'] = '<a href="'.$history['message'].'" target="_blank">查看图片</a>';
		} elseif ($history['type'] == 'location') {
			$history['message'] = iunserializer($history['message']);
			$history['message'] = '<a href="http://st.map.soso.com/api?size=800*600&center='.$history['message']['y'].','.$history['message']['x'].'&zoom=16&markers='.$history['message']['y'].','.$history['message']['x'].',1" target="_blank">查看方位</a>';
		} else {
			$history['message'] = emotion($history['message']);
		}
		if (!empty($history['rid'])) {
			$rids[$history['rid']] = $history['rid'];
		}
	}

}
if (!empty($rids)) {
	$rules = pdo_fetchall("SELECT name, id FROM ".tablename('rule')." WHERE id IN (".implode(',', $rids).")", array(), 'id');
}
$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('stat_msg_history') . " WHERE weid = '{$_W['weid']}' $where");
$pager = pagination($total, $pindex, $psize);
template('stat/history');