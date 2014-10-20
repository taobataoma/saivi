<?php 
/**
 * 调用第三方数据接口模块
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class UserapiModule extends WeModule {
	public $name = 'userapi';
	public $title = '第三方接口回复';
	public $ability = '';
	public $tablename = 'userapi_reply';
	
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		if (!empty($rid)) {
			$row = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			if (!strexists($row['apiurl'], 'http://') && !strexists($row['apiurl'], 'https://')) {
				$row['apilocal'] =  $row['apiurl'];
				$row['apiurl'] = '';
			}
			
		} else {
			$row = array(
				'cachetime' => 0,
			);
		}

		$path = IA_ROOT . '/source/modules/userapi/api';
		if (is_dir($path)) {
			$apis = array();
			if ($handle = opendir($path)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						$apis[] = $file;
					}
				}
			}
		}
		include $this->template('userapi/form');
	}
	
	public function fieldsFormValidate($rid = 0) {
		return true;
	}
	
	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'apiurl' => empty($_GPC['apiurl']) ? $_GPC['apilocal'] : $_GPC['apiurl'],
			'default_text' => $_GPC['default-text'],
			'default_apiurl' => $_GPC['default-apiurl'],
			'cachetime' => $_GPC['cachetime'],
		);
		if (!empty($insert['apiurl'])) {
			if (empty($id)) {
				pdo_insert($this->tablename, $insert);
			} else {
				pdo_update($this->tablename, $insert, array('id' => $id));
			}
		}
		return true;
	}
	
	public function ruleDeleted($rid = 0) {
		
	}
	
	public function doFormDisplay() {
		
	}
	
	public function doDelete() {
		
	}
	
	public function settingsFormDisplay($settings = array()) {
		include $this->template('userapi/setting');
	}
}