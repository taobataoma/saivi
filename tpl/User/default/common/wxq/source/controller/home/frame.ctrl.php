<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */

defined('IN_IA') or exit('Access Denied');
checklogin();
if($_GPC['iframe']) {
	$iframe='?'.str_replace('&amp;', '&', $_GPC['iframe']);
} else {
	$iframe='?act=welcome';
}

/**
 * '菜单ID' => array(
 *		title => 菜单标题名称,
 *		dos => array 当前模块允许的action,
 *		items => array(
 *		菜单项...
 *		array(菜单名称, 链接，是否为模块默认项)
 *		)
 * )
 *
 */
$menus = array();
$menus[0] = array(
				'title' => '当前公众号',
				'items' => array(
					array('规则管理', create_url('rule/display'),
						'childItems' => array('添加规则', create_url('rule/post')),
					),
					array('自定义菜单设置', create_url('menu')),
					array('系统回复设置', create_url('rule/system')),
					array('分类管理', create_url('setting/category')),
					array('模块设置', create_url('member/module')),
				)
			);
$menus[1] = array(
				'title' => array('公众号管理', create_url('account/display')),
			);
if (!empty($_W['isfounder'])) {
	$menus[2] = array(
					'title' => array('用户管理', create_url('member/display')),
				);
}
$menus[3] = array(
				'title' => '统计分析',
				'items' => array(
					array('聊天记录', create_url('stat/history')),
					array('规则使用率', create_url('stat/rule')),
					array('关键字使用率', create_url('stat/keyword')),
				)
			);
$menus[4] = array(
				'title' => '系统管理',
				'items' => array(
					array('账户管理', create_url('setting/profile')),
				)
			);
if (!empty($_W['isfounder'])) {
	$menus[4]['items'][] = array('模块管理', create_url('setting/module'));
	$menus[4]['items'][] = array('其它设置', create_url('setting/common'));
}
$menus[4]['items'][] = array('更新缓存', create_url('setting/updatecache'));

template('home/frame');
