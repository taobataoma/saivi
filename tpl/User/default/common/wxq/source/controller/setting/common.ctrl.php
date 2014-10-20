<?php 
/**
 * BAE相关设置选项
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
include model('setting');
if (checksubmit('bae_delete_update') || checksubmit('bae_delete_install')) {
	if (!empty($_GPC['bae_delete_update'])) {
		unlink(IA_ROOT . '/data/update.lock');
	} elseif (!empty($_GPC['bae_delete_install'])) {
		unlink(IA_ROOT . '/data/install.lock');
	}
	message('操作成功！', create_url('setting/common'), 'success');
} elseif (checksubmit('submit')) {
	setting_save(array('msg_history' => $_GPC['msg_history'], 'msg_maxday' => intval($_GPC['msg_maxday']), 'use_ratio' => intval($_GPC['use_ratio'])), 'stat');
	message('更新设置成功！', create_url('setting/common'));
} else {
	$settings = pdo_fetchall('SELECT * FROM ' . tablename('settings'), array(), 'key');
	if(is_array($settings)) {
		foreach($settings as $k => &$v) {
			$settings[$k] = iunserializer($v['value']);
		}
	}
	template('setting/common');
}