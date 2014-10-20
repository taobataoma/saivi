<?php 
if (empty($_GPC['name'])) {
	message('抱歉，模块不存在或是已经被！');
}
$modulename = !empty($_GPC['name']) ? $_GPC['name'] : 'basic';
$module = module($modulename);

if (is_error($module)) {
	exit($module['errormsg']);
}

$method = 'do'.$_GPC['do'];

if (method_exists($module, $method)) {
	exit($module->$method());
}