<?php
/**
 * 砸蛋抽奖模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class EggModule extends WeModule {
	public $name = 'EggModule';
	public $title = '砸蛋抽奖';
	public $ability = '';
	public $tablename = 'egg_reply';

	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$award = pdo_fetchall("SELECT * FROM ".tablename('egg_award')." WHERE rid = :rid ORDER BY `id` ASC", array(':rid' => $rid));
			if (!empty($award)) {
				foreach ($award as &$pointer) {
					if (!empty($pointer['activation_code'])) {
						$pointer['activation_code'] = implode("\n", iunserializer($pointer['activation_code']));
					}
				}
			}
		} else {
			$reply = array(
				'maxlottery' => 1,
			);
		}
		include $this->template('egg/form');
	}

	public function fieldsFormValidate($rid = 0) {
		return true;
	}

	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'picture' => $_GPC['picture'],
			'description' => $_GPC['description'],
			'maxlottery' => intval($_GPC['maxlottery']),
			'rule' => $_GPC['rule'],
			'default_tips' => $_GPC['default_tips'],
			'hitcredit' => intval($_GPC['hitcredit']),
			'misscredit' => intval($_GPC['misscredit']),
		);
		if (empty($id)) {
			pdo_insert($this->tablename, $insert);
		} else {
			if (!empty($_GPC['picture'])) {
				file_delete($_GPC['picture-old']);
			} else {
				unset($insert['picture']);
			}
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
		if (!empty($_GPC['award-title'])) {
			foreach ($_GPC['award-title'] as $index => $title) {
				if (empty($title)) {
					continue;
				}
				$update = array(
					'title' => $title,
					'description' => $_GPC['award-description'][$index],
					'probalilty' => $_GPC['award-probalilty'][$index],
					'total' => $_GPC['award-total'][$index],
					'activation_code' => '',
					'activation_url' => '',
				);
				if (empty($update['inkind']) && !empty($_GPC['award-activation-code'][$index])) {
					$activationcode = explode("\n", $_GPC['award-activation-code'][$index]);
					$update['activation_code'] = iserializer($activationcode);
					$update['total'] = count($activationcode);
					$update['activation_url'] = $_GPC['award-activation-url'][$index];
				}
				pdo_update('egg_award', $update, array('id' => $index));
			}
		}
		//处理添加
		if (!empty($_GPC['award-title-new'])) {
			foreach ($_GPC['award-title-new'] as $index => $title) {
				if (empty($title)) {
					continue;
				}
				$insert = array(
					'rid' => $rid,
					'title' => $title,
					'description' => $_GPC['award-description-new'][$index],
					'probalilty' => $_GPC['award-probalilty-new'][$index],
					'inkind' => intval($_GPC['award-inkind-new'][$index]),
					'total' => intval($_GPC['award-total-new'][$index]),
					'activation_code' => '',
					'activation_url' => '',
				);

				if (empty($insert['inkind'])) {
					$activationcode = explode("\n", $_GPC['award-activation-code-new'][$index]);
					$insert['activation_code'] = iserializer($activationcode);
					$insert['total'] = count($activationcode);
					$insert['activation_url'] = $_GPC['award-activation-url-new'][$index];
				}
				pdo_insert('egg_award', $insert);
			}
		}
	}

	public function ruleDeleted($rid = 0) {
		global $_W;
		$replies = pdo_fetchall("SELECT id, thumb FROM ".tablename($this->tablename)." WHERE rid = '$rid'");
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['thumb']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->tablename, "id IN ('".implode("','", $deleteid)."')");
		return true;
	}

	public function doFormDisplay() {
		global $_W, $_GPC;
		$result = array('error' => 0, 'message' => '', 'content' => '');
		$result['content']['id'] = $GLOBALS['id'] = 'add-row-news-'.$_W['timestamp'];
		$result['content']['html'] = $this->template('egg/item', TEMPLATE_FETCH);
		exit(json_encode($result));
	}

	public function doLottery() {
		global $_GPC;
		$title = '砸蛋抽奖';
		$fromuser = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		if (empty($fromuser)) {
			exit('非法参数！');
		}
		$id = intval($_GPC['id']);
		$egg = pdo_fetch("SELECT id, maxlottery, default_tips, rule FROM ".tablename('egg_reply')." WHERE rid = '$id' LIMIT 1");
		if (empty($egg)) {
			exit('非法参数！');
		}
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('egg_winner')." WHERE createtime > '".strtotime(date('Y-m-d'))."' AND from_user = '$fromuser' AND status <> 3 AND award <> ''");
		$member = pdo_fetch("SELECT * FROM ".tablename('fans')." WHERE from_user = '{$fromuser}'");
		$myaward = pdo_fetchall("SELECT award, description FROM ".tablename('egg_winner')." WHERE from_user = '{$fromuser}' AND award <> '' AND rid = '$id' ORDER BY createtime DESC");
		$sql = "SELECT a.award, b.realname FROM ".tablename('egg_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE b.mobile <> '' AND b.realname <> '' AND
				a.from_user <> '{$fromuser}' AND a.award <> '' AND a.rid = '$id' ORDER BY a.createtime DESC LIMIT 20";
		$otheraward = pdo_fetchall($sql);
		include $this->template('egg/lottery');
	}

	public function doGetAward() {
		global $_GPC, $_W;
		$fromuser = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		if (empty($fromuser)) {
			exit('非法参数！');
		}
		$id = intval($_GPC['id']);
		$egg = pdo_fetch("SELECT id, maxlottery, default_tips, misscredit, hitcredit FROM ".tablename('egg_reply')." WHERE rid = '$id' LIMIT 1");
		if (empty($egg)) {
			exit('非法参数！');
		}
		$result = array('status' => -1, 'message' => '');
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('egg_winner')." WHERE createtime > '".strtotime(date('Y-m-d'))."' AND from_user = '$fromuser' AND status <> 3");
		if (!empty($egg['maxlottery']) && $total >= $egg['maxlottery']) {
			$result['message'] = '您已经超过当日砸蛋次数';
			message($result, '', 'ajax');
		}
		$gifts = pdo_fetchall("SELECT id, probalilty FROM ".tablename('egg_award')." WHERE rid = '$id' ORDER BY probalilty ASC");
		//计算每个礼物的概率
		$probability = 0;
		$rate = 1;
		$award = array();
		foreach ($gifts as $name => $gift){
			if (empty($gift['probalilty'])) {
				continue;
			}
			if ($gift['probalilty'] < 1) {
				$temp = explode('.', $gift['probalilty']);
				$temp = pow(10, strlen($temp[1]));
				$rate = $temp < $rate ? $rate : $temp;
			}
			$probability = $probability + $gift['probalilty'] * $rate;
			$award[] = array('id' => $gift['id'], 'probalilty' => $probability);
		}
		$all = 100 * $rate;
		if($probability < $all){
			$award[] = array('title' => '','probalilty' => $all);
		}
		mt_srand((double) microtime()*1000000);
		$rand = mt_rand(1, $all);
		foreach ($award as $key => $gift){
			if(isset($award[$key - 1])){
				if($rand > $award[$key -1]['probalilty'] && $rand <= $gift['probalilty']){
					$awardid = $gift['id'];
					break;
				}
			}else{
				if($rand > 0 && $rand <= $gift['probalilty']){
					$awardid = $gift['id'];
					break;
				}
			}
		}
		$title = '';
		$result['message'] = empty($egg['default_tips']) ? '很遗憾,您没能中奖！' : $egg['default_tips'];
		$data = array(
			'rid' => $id,
			'from_user' => $fromuser,
			'status' => empty($gift['inkind']) ? 1 : 0,
			'createtime' => TIMESTAMP,
		);
		$credit = array(
			'rid' => $id,
			'award' => (empty($awardid) ? '未中' : '中') . '奖励积分',
			'from_user' => $fromuser,
			'status' => 3,
			'description' => (empty($awardid) ? $egg['misscredit'] : $egg['hitcredit']),
			'createtime' => TIMESTAMP,
		);
		if (!empty($awardid)) {
			$gift = pdo_fetch("SELECT * FROM ".tablename('egg_award')." WHERE rid = '$id' AND id = '$awardid'");
			if ($gift['total'] > 0) {
				$data['award'] = $gift['title'];
				if (!empty($gift['inkind'])) {
					$data['description'] = $gift['description'];
					pdo_query("UPDATE ".tablename('egg_award')." SET total = total - 1 WHERE rid = '$id' AND id = '$awardid'");
				} else {
					$gift['activation_code'] = iunserializer($gift['activation_code']);
					$code = array_pop($gift['activation_code']);
					pdo_query("UPDATE ".tablename('egg_award')." SET total = total - 1, activation_code = '".iserializer($gift['activation_code'])."' WHERE rid = '$id' AND id = '$awardid'");
					$data['description'] = '兑换码：' . $code . '<br /> 兑换地址：' . $gift['activation_url'];
				}
				$result['message'] = '恭喜您，得到“'.$data['award'].'”！' ;
				$result['status'] = 0;
			} else {
				$credit['description'] = $egg['misscredit'];
				$credit['award'] = '未中奖励积分';
			}
		}
		!empty($credit['description']) && $result['message'] .= '<br />' . $credit['award'] . '：'. $credit['description'];
		$data['aid'] = $gift['id'];
		if (!empty($credit['description'])) {
			pdo_insert('egg_winner', $credit);
		}
		pdo_insert('egg_winner', $data);
		$result['myaward'] = pdo_fetchall("SELECT award, description FROM ".tablename('egg_winner')." WHERE from_user = '{$fromuser}' AND award <> '' AND rid = '$id' ORDER BY createtime DESC");
		message($result, '', 'ajax');
	}

	public function doRegister() {
		global $_GPC, $_W;
		$title = '砸蛋领奖登记个人信息';
		$fromuser = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		$member = pdo_fetch("SELECT id, realname, mobile, qq FROM ".tablename('fans')." WHERE from_user = '{$fromuser}' LIMIT 1");
		if (!empty($_GPC['submit'])) {
			$data = array(
				'realname' => $_GPC['realname'],
				'mobile' => $_GPC['mobile'],
				'qq' => $_GPC['qq'],
			);
			if (empty($data['realname'])) {
				die('<script>alert("请填写您的真实姓名！");location.reload();</script>');
			}
			if (empty($data['mobile'])) {
				die('<script>alert("请填写您的手机号码！");location.reload();</script>');
			}
			if (empty($member)) {
				$data['from_user'] = $fromuser;
				pdo_insert('fans', $data);
			} else {
				pdo_update('fans', $data, array('from_user' => $fromuser));
			}
			die('<script>alert("登记成功！");location.href = "'.create_url('index/module', array('name' => 'egg', 'do' => 'lottery', 'id' => intval($_GPC['id']), 'from_user' => $_GPC['from_user'])).'";</script>');

		}
		include $this->template('egg/register');
	}

	public function doAwardlist() {
		global $_GPC, $_W;
		checklogin();
		$id = intval($_GPC['id']);
		if (checksubmit('delete')) {
			pdo_delete('egg_winner', " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('删除成功！', create_url('index/module', array('do' => 'awardlist', 'name' => 'egg', 'id' => $id, 'page' => $_GPC['page'])));
		}
		if (!empty($_GPC['wid'])) {
			$wid = intval($_GPC['wid']);
			pdo_update('egg_winner', array('status' => intval($_GPC['status'])), array('id' => $wid));
			message('标识领奖成功！', create_url('index/module', array('do' => 'awardlist', 'name' => 'egg', 'id' => $id, 'page' => $_GPC['page'])));
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$where = '';
		$starttime = !empty($_GPC['starttime']) ? strtotime($_GPC['starttime']) : 0;
		$endtime = !empty($_GPC['starttime']) ? strtotime($_GPC['endtime']) : 0;
		if (!empty($starttime) && $starttime == $endtime) {
			$endtime = $endtime + 86400 - 1;
		}
		$condition = array(
			'isregister' => array(
				'',
				" AND b.realname <> ''",
				" AND b.realname = ''",
			),
			'isaward' => array(
				'',
				" AND a.award <> ''",
				" AND a.award = ''",
			),
			'qq' => " AND b.qq ='{$_GPC['profilevalue']}'",
			'mobile' => " AND b.mobile ='{$_GPC['profilevalue']}'",
			'realname' => " AND b.realname ='{$_GPC['profilevalue']}'",
			'title' => " AND a.award = '{$_GPC['awardvalue']}'",
			'description' => " AND a.description = '{$_GPC['awardvalue']}'",
			'starttime' => " AND a.createtime >= '$starttime'",
			'endtime' => " AND a.createtime <= '$endtime'",
		);
		if (!isset($_GPC['isregister'])) {
			$_GPC['isregister'] = 1;
		}
		$where .= $condition['isregister'][$_GPC['isregister']];
		if (!isset($_GPC['isaward'])) {
			$_GPC['isaward'] = 1;
		}
		$where .= $condition['isaward'][$_GPC['isaward']];
		if (!empty($_GPC['profile'])) {
			$where .= $condition[$_GPC['profile']];
		}
		if (!empty($_GPC['award'])) {
			$where .= $condition[$_GPC['award']];
		}
		if (!empty($starttime)) {
			$where .= $condition['starttime'];
		}
		if (!empty($endtime)) {
			$where .= $condition['endtime'];
		}
		$sql = "SELECT a.id, a.award, a.description, a.status, a.createtime, b.realname, b.mobile, b.qq FROM ".tablename('egg_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id' AND a.award <> '' $where ORDER BY a.createtime DESC, a.status ASC LIMIT ".($pindex - 1) * $psize.",{$psize}";
		$list = pdo_fetchall($sql);
		if (!empty($list)) {
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('egg_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id' $where");
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('egg/awardlist');
	}

	public function doDelete() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$sql = "SELECT id FROM " . tablename('egg_award') . " WHERE `id`=:id";
		$row = pdo_fetch($sql, array(':id'=>$id));
		if (empty($row)) {
			message('抱歉，奖品不存在或是已经被删除！', '', 'error');
		}
		if (pdo_delete('egg_award', array('id' => $id))) {
			message('删除奖品成功', '', 'success');
		}
	}

}