<?php
/**
 * 模板生成助手
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

ob_start();
/**
 * 输出页头，务必对应 tpl_footer
 */
function tpl_header() {
	global $_G;
	if (!defined('ADMIN_HEADER_OUTPUT')) {
		define('ADMIN_HEADER_OUTPUT', true);
	} else {
		return true;
	}
	$frame = $_G['gp_frame'] != 'no' ? 1 : 0;
	echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$_W['charset']}">
<meta http-equiv="x-ua-compatible" content="ie=7" />
<link rel="stylesheet" type="text/css" href="./resource/style/reset.css" />
<link rel="stylesheet" type="text/css" href="./resource/style/management.css" media="screen" />
<script type="text/javascript" src="./resource/script/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="./resource/script/management.js"></script>
<script type="text/JavaScript">
var IN_ADMINCP = true, STATICURL='static/', SITEURL = '{$_W['siteroot']}';
</script>
</head>
<body style="height:100%; background:#ffffff;">
<div id="content"> 
EOF;
}

/**
 * 输出页尾，与 tpl_header 对应
 */
function tpl_footer() {
	if (!defined('ADMIN_FOOTER_OUTPUT')) {
		define('ADMIN_FOOTER_OUTPUT', true);
	} else {
		return true;
	}
	echo <<<EOF
</div>
</body>
</html>
EOF;
	ob_flush();
}

/**
 * 获取当前输出缓存中的内容，并清空缓存
 * @param boolean $send 是否发送当前缓存的内容
 */
function tpl_buffer($send = true) {
	if ($send) {
		$content = ob_get_contents();
		ob_flush();
		return $content;
	} else {
		return ob_get_clean();
	}
}

/**
 * 设置导航路径，可以接受多个参数，每个参数为一级导航，支持html
 */
function tpl_path() {
	$args = func_get_args();
	echo '<script type="text/javascript">parent.$("#admincpnav").html("' . implode('&nbsp;&raquo;&nbsp;', $args) . '")</script>';
}

/**
 * 显示子导航菜单
 * @param string $title 菜单名称
 * @param array $menus array('名称 | string','链接 | string','是否选中状态 | boolean','是否新窗口链接')
 * @param string $right 右侧提示信息
 */
function tpl_tab_menu($title, $menus = array(), $right = '') {
	if (empty($menus)) {
		$s = '<div class="box"><div class="title">' . $right . '<h5>' . $title . '</h5></div></div>';
	} elseif (is_array($menus)) {
		$s = '<div class="box"><div class="title">' . $right . '<h5>' . $title . '</h5>';
		if (is_array($menus)) {
			$s .= '<ul class="links">';
			foreach ($menus as $k => $menu) {
				if (is_array($menu[0])) {
					$s .= '<li id="addjs' . $k . '" class="' . ($menu[2] ? ' current' : 'hasdropmenu') . '" onmouseover="dropmenu(this);"><a href="#"><span>' . $menu[0]['menu'] . '<em>&nbsp;&nbsp;</em></span></a><div id="addjs' . $k . 'child" class="dropmenu" style="display:none;">';
					if (is_array($menu[0]['submenu'])) {
						foreach ($menu[0]['submenu'] as $submenu) {
							$s .= '<a href="' . $submenu[1] . '">' . $submenu[0] . '</a>';
						}
					}
					$s .= '</div></li>';
				} else {
					$s .= '<li' . ($menu[2] ? ' class="on"' : '') . '><a href="' . $menu[1] . '"' . ($menu[3] ? ' target="_blank"' : '') . '><span>' . $menu[0] . '</span></a></li>';
				}
			}
			$s .= '</ul>';
		}
		$s .= '</div></div>';
	}
	echo !empty($menus) ? '<div class="floattop">' . $s . '</div><div class="floattopempty"></div>' : $s;
}

/**
 * 显示子导航菜单，导航锚点
 * FIXME 这个操作不完善，抽空重构一下。
 * @param string $title 菜单名称
 * @param array $menus array('名称','链接','是否选中状态')
 * @param string $right 右侧提示信息
 */
function tpl_tab_anchor($title, $menus = array(), $right = '') {
	if (!$title || !$menus || !is_array($menus)) {
		return;
	}
	echo <<<EOT
<script type="text/JavaScript">var currentAnchor = '$GLOBALS[anchor]';</script>
EOT;
	$s = '<div class="itemtitle">' . $right . '<h3>' . $title . '</h3>';
	$s .= '<ul class="tab1" id="submenu">';
	foreach ($menus as $k => $menu) {
		if ($menu && is_array($menu)) {
			if (is_array($menu[0])) {
				$s .= '<li id="nav_m' . $k . '" class="hasdropmenu" onmouseover="dropmenu(this);"><a href="#"><span>' . $menu[0]['menu'] . '<em>&nbsp;&nbsp;</em></span></a><div id="nav_m' . $k . 'child" class="dropmenu" style="display:none;"><ul>';
				if (is_array($menu[0]['submenu'])) {
					foreach ($menu[0]['submenu'] as $submenu) {
						$s .= '<li ' . (!$submenu[3] ? ' id="nav_' . $submenu[1] . '" onclick="showanchor(this)"' : '') . ($submenu[2] ? ' class="current"' : '') . '><a href="' . ($submenu[3] ? 'admin.php?action=' . $submenu[1] : '#') . '">' . $submenu[0] . '</a></li>';
					}
				}
				$s .= '</ul></div></li>';
			} else {
				$s .= '<li' . (!$menu[3] ? ' id="nav_' . $menu[1] . '" onclick="showanchor(this)"' : '') . ($menu[2] ? ' class="current"' : '') . '><a href="' . ($menu[3] ? 'admin.php?action=' . $menu[1] : '#') . '"><span>' . $menu[0] . '</span></a></li>';
			}
		}
	}
	$s .= '</ul>';
	$s .= '</div>';
	echo !empty($menus) ? '<div class="floattop">' . $s . '</div><div class="floattopempty"></div>' : $s;
}

/**
 * XXX 这里未完善
 * @param unknown_type $tips
 * @param unknown_type $id
 * @param unknown_type $display
 */
function tpl_tips($tips, $id = 'tips', $display = TRUE) {
	showtableheader('技巧提示', '', 'id="' . $id . '"' . (!$display ? ' style="display: none;"' : ''), 0);
	showtablerow('', 'class="tipsblock"', '<ul id="' . $id . 'lis">' . $tips . '</ul>');
	showtablefooter();
}

/**
 * 输出标记头，如果不是单标记，请务必与 tpl_tag_footer 对应
 * @param string $tag 标记名称
 * @param array|string $attributes 标记属性集合
 * @param boolean $closure 是否是单标记（不需要关闭的标记）
 */
function tpl_tag_header($tag, $attributes = array(), $closure = false) {
	echo '<', $tag;
	if (is_array($attributes)) {
		foreach ($attributes as $key => $value)
			echo ' ', $key, '="', $value, '"';
	} else {
		echo $attributes ? ' ' . $attributes : '';
	}
	echo $closure ? ' />' : '>';
}

/**
 * 输出标记尾，与 tpl_tag_header 对应
 * @param string $tag 标记名称
 */
function tpl_tag_footer($tag) {
	echo '</', $tag, '>';
}

/**
 * 输出表格头，请务必于 tpl_table_footer 对应
 * @param string $title 表格标题
 * @param array $tableattrs 表格扩展属性
 * @param array $titleattrs 表格标题扩展属性
 */
function tpl_table_header($title = '', $tableattrs = array(), $titleattrs = array()) {
	if ($tableattrs['class']) {
		$tableattrs['class'] = str_replace(array('nobottom', 'notop'), array('nobdb', 'nobdt'), $tableattrs['class']);
	}
	$tableattrs['class'] = 'box tb ' . ($tableattrs['class'] ? ' ' . $tableattrs['class'] : '');
	echo "\r\n";
	tpl_tag_header('table', $tableattrs);
	if ($title) {
		tpl_table_title($title, $titleattrs);
	}
}

/**
 * 输出表格中标题行，务必保证在 show_table_header 与 show_table_footer 之间使用
 * @param string $title 标题文字
 * @param array $attrs 当前行附加属性
 */
function tpl_table_title($title, $attrs = array()) {
	$attrs['colspan'] = $attrs['colspan'] ? $attrs['colspan'] : '15';
	$attrs['class'] = $attrs['class'] ? "title {$attrs['class']}" : 'title';
	echo "\r\n<tr>";
	tpl_tag_header('th', $attrs);
	echo "<h5>$title</h5>", '</th></tr>';
}

/**
 * 输出表格列标题，务必保证在 show_table_header 与 show_table_footer 之间使用
 * @param array $title 表格的标题行文本
 */
function tpl_table_column_title($title = array()) {
	if (!is_array($title))
		return;
	echo "\r\n<tr class=\"header\">";
	foreach ($title as $v) {
		if (trim($v)) {
			echo '<th>', $v, '</th>';
		}
	}
	echo '</tr>';
}
/**
 * 输出表格行，务必保证在 show_table_header 与 show_table_footer 之间使用 
 * @param array $text 当前表格行的单元格集合
 * @param array $rowattrs 当前表格行的属性集合
 * @param array $cellsattrs 当前表格行的单元格集合的属性集合
 */
function tpl_table_row($text = array(), $rowattrs = array(), $cellsattrs = array()) {
	$rowattrs['class'] = $rowattrs['class'] ? "hover {$rowattrs['class']}" : 'hover';
	tpl_tag_header('tr', $rowattrs);
	foreach ($text as $key => $value) {
		tpl_tag_header('td', $cellsattrs[$key]);
		echo $value, '</td>';
	}
	echo '</tr>';
}

/**
 * 输出表格结束，务必和 tpl_table_footer 对应
 */
function tpl_table_footer() {
	echo '</table>' . "\n";
}

/**
 * 输出表单头，务必和 tpl_form_footer 对应
 * @param array $attrs
 */
function tpl_form_header($action = '', $method = '', $enctype = false, $extra = array()) {
	$attrs['method'] = $method ? $method : 'post';
	$attrs['action'] = $action ? $action : '';
	$attrs['class'] = 'form';
	$enctype && $attrs['enctype'] = $enctype ? 'multipart/form-data' : '';
	if (!empty($extra) && is_array($extra)) {
		$attrs = array_merge($attrs, $extra);
	}
	tpl_tag_header('form', $attrs);
	echo '<input type="hidden" name="token" value="', $GLOBALS['_W']['token'], '" />';
}

/**
 * 设置表单项的排列方式，true 为水平排列，false 为垂直排列（默认值）
 * @param boolean $horizontal
 */
function tpl_form_field_behaviour($horizontal) {
	define('FORM_FIELD_HORIZONTAL', $horizontal);
}

/**
 * 显示一个表单项
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param string $content 表单项内容
 * @param string $comment 提示信息
 */
function tpl_form_field($title = '', $name = '', $content = '', $comment = '') {
	if (defined('FORM_FIELD_HORIZONTAL') && FORM_FIELD_HORIZONTAL === true) {
		tpl_table_row(array($title, $content, $comment), array('name' => $name, 'style' => 'height:35px;'), array(array('class' => 'td27'), array('class' => 'vtop rowform'), array('class' => 'vtop tips2')));
	} else {
		tpl_table_row(array("<label for=\"$name\">$title</label>"), array(), array(array('colspan' => '2', 'class' => 'field noborder')));
		tpl_table_row(array($content, $comment), array('class' => 'noborder', 'name' => $name), array(array('class' => 'vtop input field-line', 'style' => 'border-right:none;'), array('class' => 'vtop tips field-line')));
	}
}

/**
 * 显示一个开关项
 * @param string $title 标题
 * @param string $name 表单项名称
 * @param boolean $value 开关值，1 打开，0 关闭
 * @param string $comment 描述信息
 * @param array $labels 开关标签，二元数组，默认为 开启，关闭
 * @param array $toggles 此开关想要控制的显示切换项 界面需要用tbody包起来需要隐藏的内容，ID为tbody_$name
 * @param boolean $disabled 当前项是否禁用
 * @param string $extra 附加的数据
 */
function tpl_form_field_switch($title, $name, $value = 0, $comment = '', $labels = array('开启','关闭'), $toggle = FALSE, $disabled = 0, $extra = '') {
	$check = array();
	$check['disabled'] = $disabled ? ' disabled="disabled"' : '';
	$check['true'] = $value ? ' checked="checked"' : '';
	$check['false'] = $value ? '' : ' checked="checked"';

	if ($toggle) {
		$check['hidden1'] .= "form_toggle('tbody_$name', true);";
		$check['hidden0'] .= "form_toggle('tbody_$name', false);";
	}
	
	$check['hidden1'] = $check['hidden1'] ? " onclick=\"{$check['hidden1']}\"" : '';
	$check['hidden0'] = $check['hidden0'] ? " onclick=\"{$check['hidden0']}\"" : '';
	$s .= '<ul class="changestyle">' . '<li><label ' . ($check['true'] ? ' class="checked"' : '') . '><input class="radio" type="radio" name="' . $name . '" value="1"' . $check['true'] . $check['hidden1'] . $check['disabled'] . $extra . '>&nbsp;' . $labels[0] . '</label></li>' . '<li><label ' . ($check['false'] ? ' class="checked"' : '') . '><input class="radio" type="radio" name="' . $name . '" value="0"' . $check['false'] . $check['hidden0'] . $check['disabled'] . $extra . '>&nbsp;' . $labels[1] . '</label></li>' . '</ul>';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个文本框
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param string $value 值
 * @param string $comment 描述信息
 * @param string $extra 附加信息
 */
function tpl_form_field_text($title, $name, $value = '', $comment = '', $extra = '') {
	$s = '<input name="' . $name . '" value="' . htmlspecialchars($value) . '" type="text" class="txt" ' . $extra . ' />';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个隐藏域
 * @param string $name 表单项名称
 * @param string $value 值
 * @param string $extra 附加信息
 */
function tpl_form_field_hidden($name, $value = '', $extra = '') {
	$s = '<input name="' . $name . '" value="' . htmlspecialchars($value) . '" type="hidden" ' . $extra . ' />';
	echo $s;
}

/**
 * 显示一个密码框
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param string $value 值
 * @param string $comment 描述信息
 * @param string $extra 附加信息
 */
function tpl_form_field_password($title, $name, $value = '', $comment = '', $extra = '') {
	$s = '<input name="' . $name . '" value="' . htmlspecialchars($value) . '" type="password" class="txt" ' . $extra . ' />';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个图像上传框
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param string $comment 描述信息
 * @param string $extra 附加信息
 */
function tpl_form_field_file($title, $name, $comment = '', $extra = '') {
	//XXX 添加有值时图片显示
	$s = '<input name="' . $name . '" value="" type="file" class="txt uploadbtn marginbot" ' . $extra . ' />';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个文本区域
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param string $value 值
 * @param string $comment 描述信息
 * @param string $extra 附加信息
 */
function tpl_form_field_textarea($title, $name, $value = '', $comment = '', $extra = '') {
	$s = "<div class=\"textarea\"><textarea rows=\"6\" ondblclick=\"textareasize(this, 1)\" onkeyup=\"textareasize(this, 0)\" name=\"$name\" id=\"$name\" cols=\"50\" '.$extra.'>" . htmlspecialchars($value) . "</textarea></div>";
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个下拉列表
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param array $options 下拉列表项集合，二元数组，键名为项值，键值为项文本
 * @param string $value 值
 * @param string $comment 描述信息
 * @param string $extra 附加信息
 */
function tpl_form_field_select($title, $name, $options = array(), $value = '', $comment = '', $extra = '') {
	$s = '<select class="select" name="' . $name . '" ' . $extra . '>';
	foreach ($options as $k => $v) {
		$selected = $v == $value ? ' selected="selected"' : '';
		$s .= "<option value=\"{$k}\"$selected>{$v}</option>\n";
	}
	$s .= '</select>';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个单选列表
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param array $options 选项集合，二元数组，键名为项值，键值为文本
 * @param string $value 值
 * @param boolean $horizontal 指定列表项是否横排展示
 * @param string $comment 描述信息
 * @param string $extra 附加信息
 */
function tpl_form_field_mradio($title, $name, $options = array(), $value = '', $horizontal = true, $comment = '', $extra = '') {
	$s .= '<ul' . ($horizontal ? '' : ' class="nofloat"') . '>';
	foreach ($options as $k => $v) {
		$checked = $k == $value ? ' checked="checked"' : '';
		if (is_array($v)) {
			$onclick = '';
			if (is_array($v[1])) {
				foreach ($v[1] as $ctrlid => $display) {
					$onclick .= "form_toggle('tbody_$ctrlid', $display);";
				}
			}
			$onclick && $onclick = ' onclick="'.$onclick.'"';
			$v = $v[0];
		}
		$s .= '<li><label ' . ($v == $value ? ' class="checked"' : '') . '><input class="radio" type="radio" name="' . $name . '" value="' . $k . '"' . $checked . ' '.$onclick.'>&nbsp;' . $v . '</label></li>';
	}
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个单选列表
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param array $options 选项集合，二元数组，键名为项值，键值为文本
 * @param array $value 值
 * @param boolean $horizontal 指定列表项是否横排展示
 * @param string $comment 描述信息
 * @param string $extra 附加信息
 */
function tpl_form_field_mcheckbox($title, $name, $options = array(), $value = array(), $horizontal = true, $comment = '', $extra = '') {
	$s .= '<ul' . ($horizontal ? ' class="changestyle"' : ' class="nofloat changestyle"') . '>';
	foreach ($options as $k => $v) {
		$checked = in_array($k, $value) ? ' checked="checked"' : '';
		$s .= '<li><label ' . ($v == $value ? ' class="checked"' : '') . '><input class="checkbox" type="checkbox" name="' . $name . '" value="' . $k . '"' . $checked . '>&nbsp;' . $v . '</label></li>';
	}
	tpl_form_field($title, $name, $s, $comment);
}
function tpl_form_field_binmcheckbox($title, $name, $value = '', $comment = '', $extra = '') {
	$s = '此方法没有实现';
	tpl_form_field($title, $name, $s, $comment);
}
function tpl_form_field_mselect($title, $name, $value = '', $comment = '', $extra = '') {
	$s = '此方法没有实现';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个颜色选择项
 * @param string $title 标题
 * @param string $name 名称
 * @param string $value 值
 * @param string $comment 描述
 */
function tpl_form_field_color($title, $name, $value = '', $comment = '') {
	$s = '
	<link rel="stylesheet" href="static/colorpicker/css/colorpicker.css" type="text/css" media="all" />
	<script type="text/javascript" src="static/js/colorpicker.js"></script>
	<div style="background:' . htmlspecialchars($value) . ' url(./static/colorpicker/images/select.gif) no-repeat;width:28px;height:28px;cursor:pointer;" class="colorpickerpreview"></div><input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '">';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个日期项
 * @param string $title 标题
 * @param string $name 名称
 * @param string $value 值
 * @param string $comment 描述
 */
function tpl_form_field_calendar($title, $name, $value = '', $comment = '') {
	$s = '<input type="text" class="datepicker txt" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个日期范围项
 * @param string $title 标题
 * @param string $name 名称
 * @param array $value array('begin'=>'开始时间','end'=>'结束时间')
 * @param string $comment 描述
 */
function tpl_form_field_daterange($title, $name, $value = array(), $comment = '') {
	static $calendarid = 0;
	$calendarid++;
	$s = '<input type="text" class="datepicker txt" range="begin" caleid="' . $calendarid . '" name="' . $name . '[begin]" value="' . htmlspecialchars($value['begin']) . '" style="width:108px;margin-right:5px;" />';
	$s .= ' -- ';
	$s .= '<input type="text" class="datepicker txt" range="end" caleid="' . $calendarid . '" name="' . $name . '[end]" value="' . htmlspecialchars($value['end']) . '" style="width:108px;margin-left:5px;" />';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个编辑器项
 * @param string $title 表单项标题
 * @param string $name 表单项名称
 * @param string $value 编辑器内容
 * @param string $comment 描述信息
 */
function tpl_form_field_editor($title, $name, $value = '', $comment = '') {
	static $editorid = 0;
	$editorid++;
	$s = '
	<link rel="stylesheet" href="kindeditor/skins/default.css" type="text/css" media="all" />
	<script type="text/javascript" src="static/kindeditor/kindeditor-min.js"></script>
	<script>
	KE.show({
		id : "editor' . $editorid . '",
		resizeMode:1,
		allowUpload:false,
		urlType:\'absolute\',
		items : [\'bold\',\'italic\',\'underline\',\'strikethrough\',\'textcolor\',\'bgcolor\',\'fontname\',\'fontsize\',\'removeformat\',\'wordpaste\',\'insertorderedlist\',\'insertunorderedlist\',\'indent\',\'outdent\',\'justifyleft\',\'justifycenter\',\'justifyright\',\'link\',\'unlink\',\'image\',\'flash\',\'advtable\',\'emoticons\',\'source\']
	});
	</script>
	<textarea name="' . $name . '" id="editor' . $editorid . '" style="width:600px;height:150px;">' . rhtmlspecialchars($value) . '</textarea>';
	tpl_form_field($title, $name, $s, $comment);
}

/**
 * 显示一个表单提交按钮
 * @param string $name 提交按钮名称
 * @param string $value 提交按钮文本
 * @param string $before 按钮之前的文本项 del | select_all 将显示全选按钮
 * @param string $after 按钮之后的文本项 more_options 将显示更多选项
 * @param string $floatright 右侧浮动内容
 */
function tpl_form_field_submit($name = 'submit', $value = '提交', $before = '', $after = '', $floatright = '') {
	$str = '<tr>';
	$str .= in_array($before, array('del', 'select_all', 'td')) ? '<td class="selected">' . ($before != 'td' ? '<input type="checkbox" name="chkall" id="chkall" class="checkbox" onclick="checkAll(this.form, \'select\')" /><label for="chkall">全选 ' : '') . '</label></td>' : '';
	$str .= '<td colspan="15">';
	$str .= $floatright ? '<div class="cuspages fr">' . $floatright . '</div>' : '';
	$str .= '<div class="buttons">';
	$str .= $before && !in_array($before, array('del', 'select_all', 'td')) ? $before . ' &nbsp;' : '';
	$str .= $name ? '<input type="submit" class="btn" id="submit_' . $name . '" name="' . $name . '" title="按 Enter 键可随时提交您的修改" value="' . $value . '" />' : '';
	$after = $after == 'more_options' ? '<input class="checkbox" type="checkbox" value="1" onclick="$(\'advanceoption\').style.display = $(\'advanceoption\').style.display == \'none\' ? \'\' : \'none\'; this.value = this.value == 1 ? 0 : 1; this.checked = this.value == 1 ? false : true" id="btn_more" /><label for="btn_more">more_options</label>' : "$after";
	$str = $after ? $str . (($before && $before != 'del') || $name ? ' &nbsp;' : '') . $after : $str;
	$str .= '</div></td>';
	$str .= '</tr>';
	echo $str . ($name ? '
	<script type="text/JavaScript">
		$(document).keydown(function(event){
			if(event.keyCode != 13){
				return;
			}
			if(event.target.tagName != "TEXTAREA"){
				$("#submit_' . $name . '")[0].click();
			}
		});
	</script>' : '');
}

/**
 * 输出表单结束，务必和 tpl_form_footer 对应
 */
function tpl_form_footer() {
	echo '</form>' . "\n";
}














