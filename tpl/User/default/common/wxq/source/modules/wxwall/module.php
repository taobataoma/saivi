<?php
/**
 * 微信墙模块
 *
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class WxwallModule extends WeModule {
	public $name = 'Wxwall';
	public $title = '微信墙';
	public $ability = '';
	public $tablename = 'wxwall_reply';

	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		} else {
			$reply = array(
				'isshow' => 0,
				'timeout' => 0,
			);
		}
		include $this->template('wxwall/form');
	}

	public function fieldsFormValidate($rid = 0) {
		return true;
	}

	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'enter_tips' => $_GPC['enter-tips'],
			'quit_tips' => $_GPC['quit-tips'],
			'send_tips' => $_GPC['send-tips'],
			'timeout' => $_GPC['timeout'],
			'isshow' => intval($_GPC['isshow']),
			'quit_command' => $_GPC['quit-command']
		);
		if (empty($id)) {
			pdo_insert($this->tablename, $insert);
		} else {
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
	}

	public function ruleDeleted($rid = 0) {

	}

	public function doDetail() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$wall = $this->getWall($id);
		$wall['onlinemember'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('wxwall_members')." WHERE rid = '{$wall['rid']}'");

		$list = pdo_fetchall("SELECT id, content, from_user, type, createtime FROM ".tablename('wxwall_message')." WHERE rid = '{$wall['rid']}' AND isshow = '2' AND from_user <> '' ORDER BY createtime DESC");
		$this->formatMsg($list);
		include $this->template('wxwall/detail');
	}

	/*
	 * 内容管理
	 */
	public function doManage() {
		global $_GPC, $_W;
		checklogin();
		$id = intval($_GPC['id']);
		if (checksubmit('verify') && !empty($_GPC['select'])) {
			pdo_update('wxwall_message', array('isshow' => 1, 'createtime' => TIMESTAMP), " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('审核成功！', create_url('index/module', array('do' => 'manage', 'name' => 'wxwall', 'id' => $id, 'page' => $_GPC['page'])));
		}
		if (checksubmit('delete') && !empty($_GPC['select'])) {
			pdo_delete('wxwall_message', " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('删除成功！', create_url('index/module', array('do' => 'manage', 'name' => 'wxwall', 'id' => $id, 'page' => $_GPC['page'])));
		}
		$isshow = isset($_GPC['isshow']) ? intval($_GPC['isshow']) : 0;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$wall = pdo_fetch("SELECT id, isshow, rid FROM ".tablename('wxwall_reply')." WHERE rid = '{$id}' LIMIT 1");
		$onlinemember = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('wxwall_members')." WHERE rid = '{$wall['rid']}' AND isjoin = '1'");
		if (!empty($wall['timeout']) && $wall['timeout'] > 0) {
			pdo_query("UPDATE ".tablename('wxwall_members')." SET isjoin = '0' WHERE lastupdate < {$_W['timestamp']} - {$wall['timeout']}");
		}
		$list = pdo_fetchall("SELECT * FROM ".tablename('wxwall_message')." WHERE rid = '{$wall['rid']}' AND isshow = '$isshow' ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.",{$psize}");
		if (!empty($list)) {
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wxwall_message') . " WHERE rid = '{$wall['rid']}' AND isshow = '$isshow'");
			$pager = pagination($total, $pindex, $psize);

			foreach ($list as &$row) {
				if ($row['type'] == 'link') {
					$row['content'] = iunserializer($row['content']);
					$row['content'] = '<a href="'.$row['content']['link'].'" target="_blank" title="'.$row['content']['description'].'">'.$row['content']['title'].'</a>';
				} elseif ($row['type'] == 'image') {
					$row['content'] = '<img src="'.$_W['attachurl'] . $row['content'].'" />';
				} else {
					$row['content'] = emotion($row['content']);
				}
				$userids[] = $row['from_user'];
			}
			unset($row);

			if (!empty($userids)) {
				$member = pdo_fetchall("SELECT avatar, nickname, from_user, isblacklist FROM ".tablename('wxwall_members')." WHERE from_user IN ('".implode("','", $userids)."')", array(), 'from_user');
			}
		}
		include $this->template('wxwall/manage');
	}

	/*
	 * 增量数据调用
	 */
	public function doIncoming() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$lastmsgtime = intval($_GPC['lastmsgtime']);
		$sql = "SELECT id, content, from_user, type, createtime FROM ".tablename('wxwall_message')." WHERE rid = '{$id}'";
		$page = max(1, intval($_GPC['page']));
		if (!empty($lastmsgtime)) {
			$sql .= " AND createtime >= '$lastmsgtime' AND isshow > 0 ORDER BY createtime ASC LIMIT ".($page-1).", 1";
		} else {
			$sql .= " AND isshow = '1' ORDER BY createtime ASC  LIMIT 1";
		}
		$row = pdo_fetch($sql);
		if (!empty($row)) {
			$member = pdo_fetch("SELECT nickname, avatar FROM ".tablename('wxwall_members')." WHERE from_user = '{$row['from_user']}'");
			$row['avatar'] = $member['avatar'];
			$row['nickname'] = $member['nickname'];
			if ($row['type'] == 'link') {
				$row['content'] = iunserializer($row['content']);
				$row['content'] = '<a href="'.$row['content']['link'].'" target="_blank" title="'.$row['content']['description'].'">'.$row['content']['title'].'</a>';
			} elseif ($row['type'] == 'image') {
				$row['content'] = '<img src="'.$_W['attachurl'] . $row['content'].'" />';
			}
			pdo_update('wxwall_message', array('isshow' => '2'), array('id' => $row['id']));
			$row['content'] = emotion($row['content'], '48px');
			message($row);
		}
	}

	/*
	 * 登记
	 */
	public function doRegister() {
		global $_GPC, $_W;
		$title = '微信墙登记';
		$member = pdo_fetch("SELECT id, nickname, avatar FROM ".tablename('wxwall_members')." WHERE from_user = '{$_GPC['from']}' LIMIT 1");
		if (!empty($_GPC['submit'])) {
			$data = array(
				'nickname' => $_GPC['nickname'],
			);
			if (empty($data['nickname'])) {
				die('<script>alert("请填写您的昵称！");location.reload();</script>');
			}
			$data['avatar'] = $_GPC['avatar_radio'];
			if (!empty($_FILES['avatar']['tmp_name'])) {
				$_W['uploadsetting'] = array();
				$_W['uploadsetting']['wxwall']['folder'] = 'wxwall/avatar';
				$_W['uploadsetting']['wxwall']['extentions'] = $_W['config']['upload']['image']['extentions'];
				$_W['uploadsetting']['wxwall']['limit'] = $_W['config']['upload']['image']['limit'];
				$upload = file_upload($_FILES['avatar'], 'wxwall', $_GPC['from']);
				if (is_error($upload)) {
					die('<script>alert("登记失败！请重试！");location.reload();</script>');
				}
				$data['avatar'] = $upload['path'];
			}
			pdo_update('wxwall_members', $data, array('from_user' => $_GPC['from']));
			die('<script>alert("登记成功！现在进入话题发表内容！");location.href = "'.create_url('index/module', array('name' => 'wxwall', 'do' => 'register', 'from' => $_GPC['from'])).'";</script>');

		}
		include $this->template('wxwall/register');
	}

	public function doBlacklist() {
		global $_W, $_GPC;
		if (checksubmit('delete')) {
			pdo_update('wxwall_members', array('isblacklist' => 0), " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('解除黑名单成功！', create_url('index/module', array('do' => 'blacklist', 'name' => 'wxwall', 'id' => $id, 'page' => $_GPC['page'])));
		}
		$id = intval($_GPC['id']);
		if (!empty($_GPC['from_user'])) {
			pdo_update('wxwall_members', array('isblacklist' => intval($_GPC['switch'])), array('from_user' => $_GPC['from_user']));
			message('黑名单操作成功！', create_url('index/module', array('do' => 'manage', 'name' => 'wxwall', 'id' => $id)));
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename('wxwall_members')." WHERE isblacklist = '1' ORDER BY lastupdate DESC LIMIT ".($pindex - 1) * $psize.",{$psize}");
		include $this->template('wxwall/blacklist');
	}

	public function doQrcode() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$wall = $this->getWall($id);
		include $this->template('wxwall/qrcode');
	}
	
	public function doLottery() {
		global $_GPC, $_W;
		checklogin();
		$id = intval($_GPC['id']);
		$type = intval($_GPC['type']);
		$wall = $this->getWall($id);
		if ($type == 2) {
			$list = pdo_fetchall("SELECT id, content, from_user, type, createtime FROM ".tablename('wxwall_message')." WHERE rid = '{$wall['rid']}' AND isshow = '2' AND from_user <> '' ORDER BY createtime DESC");
		} else {
			$list = pdo_fetchall("SELECT id, content, from_user, type, createtime FROM ".tablename('wxwall_message')." WHERE rid = '{$wall['rid']}' AND isshow = '2' AND from_user <> '' GROUP BY from_user ORDER BY createtime DESC LIMIT 10");
		}
		$this->formatMsg($list);
		include $this->template('wxwall/lottery');
	}
	
	public function doAward() {
		global $_GPC, $_W;
		checklogin();
		$message = pdo_fetch("SELECT * FROM ".tablename('wxwall_message')." WHERE id = '{$_GPC['mid']}' LIMIT 1");
		if (empty($message)) {
			message('抱歉，参数不正确！', '', 'error');
		}
		$data = array(
			'rid' => $message['rid'],
			'from_user' => $message['from_user'],
			'createtime' => TIMESTAMP,
			'status' => 0,
		);
		pdo_insert('wxwall_award', $data);
		message('', '', 'success');
	}
	
	public function doAwardlist() {
		global $_GPC, $_W;
		checklogin();
		$id = intval($_GPC['id']);
		if (checksubmit('delete')) {
			pdo_delete('wxwall_award', " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('删除成功！', create_url('index/module', array('do' => 'awardlist', 'name' => 'wxwall', 'id' => $id, 'page' => $_GPC['page'])));
		}
		if (!empty($_GPC['wid'])) {
			$wid = intval($_GPC['wid']);
			pdo_update('wxwall_award', array('status' => intval($_GPC['status'])), array('id' => $wid));
			message('标识领奖成功！', create_url('index/module', array('do' => 'awardlist', 'name' => 'wxwall', 'id' => $id, 'page' => $_GPC['page'])));
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$sql = "SELECT * FROM ".tablename('wxwall_award')." WHERE rid = '$id' ORDER BY status ASC LIMIT ".($pindex - 1) * $psize.",{$psize}";
		$list = pdo_fetchall($sql);
		if (!empty($list)) {
			$total = pdo_fetchcolumn("SELECT * FROM ".tablename('wxwall_award')." WHERE rid = '$id'");
			$pager = pagination($total, $pindex, $psize);
			foreach ($list as $index => $row) {
				$users[$row['from_user']] = $row['from_user'];
			}
			$users = pdo_fetchall("SELECT nickname, from_user FROM ".tablename('wxwall_members')." WHERE from_user IN ('".implode("','", $users)."')", array(), 'from_user');
		}
		include $this->template('wxwall/awardlist');
	}
	
	private function getWall($id) {
		$wall = pdo_fetch("SELECT id, isshow, rid FROM ".tablename('wxwall_reply')." WHERE rid = '{$id}' LIMIT 1");
		$wall['rule'] = pdo_fetch("SELECT name, weid FROM ".tablename('rule')." WHERE id = '{$id}' LIMIT 1");
		$wall['account'] = pdo_fetch("SELECT account, name FROM ".tablename('wechats')." WHERE weid = '{$wall['rule']['weid']}' LIMIT 1");
		$wall['keyword'] = pdo_fetchall("SELECT content FROM ".tablename('rule_keyword')." WHERE rid = '{$id}'");
		return $wall;
	}
	
	private function formatMsg(&$list) {
		global $_W;
		if (empty($list)) {
			return false;
		}
		$uids = $members = array();
		foreach ($list as &$row) {
			$uids[$row['from_user']] = $row['from_user'];
			if ($row['type'] == 'link') {
				$row['content'] = iunserializer($row['content']);
				$row['content'] = '<a href="'.$row['content']['link'].'" target="_blank" title="'.$row['content']['description'].'">'.$row['content']['title'].'</a>';
			} elseif ($row['type'] == 'image') {
				$row['content'] = '<img src="'.$_W['attachurl'] . $row['content'].'" />';
			}
			$row['content'] = emotion($row['content'], '48px');
		}
		unset($row);
		if (!empty($uids)) {
			$members = pdo_fetchall("SELECT nickname, avatar, from_user FROM ".tablename('wxwall_members')." WHERE from_user IN ('".implode("','", $uids)."')", array(), 'from_user');
		}
		if (!empty($members)) {
			foreach ($list as $index => &$row) {
				$row['nickname'] = $members[$row['from_user']]['nickname'];
				$row['avatar'] = $members[$row['from_user']]['avatar'];
				unset($list[$index]['from_user']);
			}
			unset($row);
		}
	}

}