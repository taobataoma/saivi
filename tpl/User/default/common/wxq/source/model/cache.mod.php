<?php
function cache_build_template() {
	//更新模板
	rmdirs(IA_ROOT . '/data/tpl/default', true);
}

function cache_build_setting() {
	$sql = 'SELECT * FROM ' . tablename('settings');
	$setting = pdo_fetchall($sql, array(), 'key');
	if(is_array($setting)) {
		foreach($setting as $k => $v) {
			$setting[$v['key']] = iunserializer($v['value']);
		}
		cache_write('setting', $setting);
	}
}

/**
 * 更新模块缓存
 */
function cache_build_modules() {
	$modules = pdo_fetchall("SELECT * FROM " . tablename('modules') . ' ORDER BY `mid` ASC', array(), 'name');
	if (!empty($modules)) {
		foreach ($modules as $mid => &$module) {
			if (!empty($module['menus'])) {
				$module['menus'] = unserialize($module['menus']);
			}
		}
	}
	cache_write('modules', $modules);
}

/**
 * 更新用户下的公众号缓存
 * @param int $uid
 */
function cache_build_account($uid = 0) {
	global $_W;
	isetcookie('wechatloaded', '0');
	$uid = empty($uid) ? $_W['uid'] : $uid;
	cache_build_modules();
	cache_load('modules');
	$modules = $_W['modules'];

	$wechats = pdo_fetchall("SELECT * FROM " . tablename('wechats') . " WHERE uid = '{$uid}' ORDER BY `weid` DESC", array(), 'weid');
	$sysmodules = pdo_fetchall("SELECT mid, name FROM ".tablename('modules')." WHERE issystem = '1'", array(), 'mid');
	foreach ($sysmodules as $mid => &$module) {
		$module['issystem'] = 1;
		$module['displayorder'] = -1;
		$module['enabled'] = 1;
	}
	if(!empty($wechats)) {
		$founder = explode(',', $_W['config']['setting']['founder']);
		foreach ($wechats as $index => $row) {
			if (in_array($uid, $founder)) {
				$membermodules = pdo_fetchall("SELECT mid, name FROM ".tablename('modules') . " ORDER BY issystem DESC, mid ASC", array(), 'mid');
				$modulelist  = array();
			} else {
				$membermodules = pdo_fetchall("SELECT b.mid, b.name FROM ".tablename('members_modules')." AS a LEFT JOIN ".tablename('modules')." AS b ON a.mid = b.mid WHERE a.uid = :uid AND b.name <> '' ORDER BY issystem DESC, mid ASC", array(':uid' => $uid), 'mid');
				$modulelist = $sysmodules;
			}
			$mymodules = pdo_fetchall("SELECT mid, enabled, displayorder FROM ".tablename('wechats_modules')." WHERE weid = '{$row['weid']}' AND mid IN (".implode(",", array_keys($membermodules)).") ORDER BY enabled DESC, displayorder ASC, mid ASC", array(), 'mid');
			//拼接模块
			if (!empty($mymodules)) {
				foreach ($mymodules as $mid => $row){
					if (empty($row['enabled'])) {
						unset($membermodules[$mid]);
						continue;
					}
					if (!empty($membermodules[$mid])) {
						$modulelist[$mid] = $membermodules[$mid];
						$modulelist[$mid]['enabled'] = $row['enabled'];
						$modulelist[$mid]['displayorder'] = $row['displayorder'];
						unset($membermodules[$mid]);
					}
				}
			}
			
			if (!empty($membermodules)) {
				$modulelist = array_merge($modulelist, $membermodules);
			} elseif (in_array($uid, $founder)) {
				$modulelist  = $membermodules;
			}
			unset($row);
			foreach ($modulelist as $mid => &$row) {
				if (!isset($row['enabled'])) {
					$row['enabled'] = 1;
					$row['displayorder'] = 127;
				}
			}
			unset($row);
			$wechats[$index]['modules'] = $modulelist;
		}
	}
	cache_write('account:'.$uid, $wechats);
}

function cache_build_announcement() {
	$response = ihttp_get('http://www.we7.cc/api/v1/announcement.php');
	$response['content'] = json_decode($response['content'], TRUE);
	$cache = array(
		'status' => $response['status'],
		'content' => $response['content'],
		'lastupdate' => TIMESTAMP,
	);
	cache_write('announcement', $cache);
}

function cache_build_hook($uid = 0) {
	global $_W;
	$uid = empty($uid) ? $_W['uid'] : $uid;
	cache_load('account:'.$uid);

	if (!empty($_W['cache']['account'][$uid])) {
		foreach ($_W['cache']['account'][$uid] as $weid => $wechat) {
			$modules[$weid] = $wechat['modules'];
		}
	}
	if (!empty($modules)) {
		foreach ($modules as $id => $mymodules) {
			foreach ($mymodules as $mid => $module) {
				$file = IA_ROOT . "/source/modules/{$module['name']}/processor.php";
				if (!file_exists($file)) {
					continue;
				}
				include_once $file;
			}

			$classes = get_declared_classes();
			$classnames = $hooks =array();
			$namekey = 'ModuleProcessor';
			$namekeyLen = strlen($namekey);

			foreach($classes as $classname) {
				if(substr($classname, -$namekeyLen) == $namekey) {
					$classnames[] = $classname;
				}
			}
			foreach($classnames as $index => $classname) {
				$methods = get_class_methods($classname);
				foreach($methods as $funcname) {
					preg_match('/hook(.*)/', $funcname, $match);
					if (empty($match[1])) {
						continue;
					}
					$hookname = strtolower($match[1]);
					$modulename = strtolower(str_replace($namekey, '', $classname));
					if (in_array($modulename, array_keys($mymodules))) {
						$hooks[$hookname][] = array($modulename, $funcname);
					}
				}
			}
			if (!empty($hooks)) {
				cache_write("hooks:$id", $hooks);
			}
		}
	}
}

function cache_build_category($weid = 0) {
	global $_W;
	$weid = empty($weid) ? $_W['weid'] : $weid;
	$category = pdo_fetchall("SELECT * FROM " . tablename('category') . " WHERE weid = '{$weid}'", array(), 'id');
	cache_write('category:'.$weid, $category);
}