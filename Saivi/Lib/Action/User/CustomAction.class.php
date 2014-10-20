<?php

class CustomAction extends UserAction
{
    public $token;
    public $set_db;
    public $limit_db;
    public $field_db;
    public $info_db;
    public function _initialize()
    {
        parent::_initialize();
        $this->token = session('token');
        $this->set_db = D('Custom_set');
        $this->limit_db = M('Custom_limit');
        $this->field_db = D('Custom_field');
        $this->info_db = D('Custom_info');
        $this->set_id = $this->_get('set_id', 'intval');
        $this->assign('set_id', $this->set_id);
    }
    public function index()
    {
        $where = array('token' => $this->token);
        if ($this->_post('keyword', 'trim')) {
            $where['title|keyword'] = array('like', '%' . $this->_post('search', 'trim') . '%');
        }
        $count = $this->set_db->where($where)->count();
        $Page = new Page($count, 15);
        $list = $this->set_db->where($where)->order('set_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['count'] = $this->info_db->where(array('token' => $this->token, 'set_id' => $value['set_id']))->count();
        }
        $this->assign('count', $count);
        $this->assign('page', $Page->show());
        $this->assign('list', $list);
        $this->display();
    }
    public function set()
    {
        $keyword_db = M('keyword');
        $where = array('token' => $this->token, 'set_id' => $this->_get('set_id', 'intval'));
        $set_info = $this->set_db->where($where)->find();
        if (IS_POST) {
            $_POST['token'] = $this->token;
            $_POST['enddate'] = strtotime($this->_post('enddate', 'trim'));
            $_POST['succ_info'] = empty($_POST['succ_info']) ? '提交成功' : $this->_post('succ_info', 'trim');
            $_POST['err_info'] = empty($_POST['succ_info']) ? '提交失败' : $this->_post('err_info', 'trim');
            if ($_POST['endtime']) {
                $limit['enddate'] = strtotime($this->_post('end_value', 'trim'));
            } else {
                $limit['today_total'] = '';
            }
            if ($_POST['today_total']) {
                $limit['today_total'] = $this->_post('today_value', 'intval');
            } else {
                $limit['today_total'] = 0;
            }
            if ($_POST['sub_total']) {
                $limit['sub_total'] = $this->_post('sub_value', 'intval');
            } else {
                $limit['sub_total'] = 0;
            }
            if ($set_info) {
                if ($this->set_db->create()) {
                    $_POST['detail'] = $this->_post('detail', 'trim');
                    $this->set_db->where($where)->save($_POST);
                    $this->limit_db->where(array('limit_id' => $set_info['limit_id']))->save($limit);
                    $keyword['pid'] = $this->_get('set_id', 'intval');
                    $keyword['module'] = 'Custom';
                    $keyword['token'] = $this->token;
                    $keyword['keyword'] = $this->_post('keyword', 'trim');
                    $keyword_db->where(array('token' => $this->token, 'pid' => $this->_post('set_id', 'intval'), 'module' => 'Custom'))->save($keyword);
                    $this->success('修改成功', U('Custom/set', array('token' => $this->token, 'set_id' => $this->_get('set_id', 'intval'))));
                } else {
                    $this->error($this->set_db->getError());
                }
            } else {
                $limit_id = $this->limit_db->add($limit);
                $_POST['addtime'] = time();
                $_POST['limit_id'] = $limit_id;
                if ($this->set_db->create()) {
                    $_POST['detail'] = $this->_post('detail', 'trim');
                    $id = $this->set_db->add($_POST);
                    $keyword['pid'] = $id;
                    $keyword['module'] = 'Custom';
                    $keyword['token'] = $this->token;
                    $keyword['keyword'] = $this->_post('keyword', 'trim');
                    $keyword_db->add($keyword);
                    $this->success('添加成功', U('Custom/index', array('token' => $this->token)));
                } else {
                    $this->error($this->set_db->getError());
                }
            }
        } else {
            if (!empty($set_info)) {
                $limit_info = $this->limit_db->where(array('limit_id' => $set_info['limit_id']))->find();
            }
            $now = strtotime('+1 day');
            $this->assign('now', $now);
            $this->assign('limit_info', $limit_info);
            $this->assign('set', $set_info);
            $this->display();
        }
    }
    public function info()
    {
        $set_id = $this->_get('set_id', 'intval');
        $name = $this->_post('name', 'trim');
        $where = array('token' => $this->token, 'set_id' => $set_id);
        if ($name) {
            $where['user_name|sub_info'] = array('like', '%' . $name . '%');
        }
        $count = $this->info_db->where($where)->count();
        $Page = new Page($count, 15);
        $list = $this->info_db->where($where)->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $key => $value) {
            $list[$key]['ex_info'] = unserialize($value['sub_info']);
        }
        $field = $this->field_db->where(array('token' => $this->token, 'set_id' => $set_id))->order('sort desc')->limit('6')->field('field_name')->select();
        $this->assign('field', $field);
        $this->assign('page', $Page->show());
        $this->assign('set_id', $set_id);
        $this->assign('list', $list);
        $this->display();
    }
    public function detail()
    {
        $where = array('token' => $this->token, 'info_id' => $this->_get('info_id', 'intval'));
        $info = $this->info_db->where($where)->find();
        $this->assign('sub_info', unserialize($info['sub_info']));
        $this->assign('set_id', $info['set_id']);
        $this->assign('info', $info);
        $this->display();
    }
    public function infoDel()
    {
        $info_id = $this->_get('info_id', 'intval');
        $token = $this->token;
        $where = array('token' => $token, 'info_id' => $info_id);
        if ($this->info_db->where($where)->delete()) {
            $this->success('删除信息成功');
        }
    }
    public function del()
    {
        $set_id = $this->_get('set_id', 'intval');
        $where = array('token' => $this->token, 'set_id' => $set_id);
        $limit_id = $this->set_db->where($where)->getField('limit_id');
        if ($this->set_db->where($where)->delete()) {
            M('keyword')->where(array('pid' => $set_id))->delete();
            $this->limit_db->where(array('limit_id' => $limit_id))->delete();
            $this->info_db->where(array('token' => $this->token, 'set_id' => $set_id))->delete();
            $this->field_db->where(array('token' => $this->token, 'set_id' => $set_id))->delete();
            $this->success('删除配置成功', U('Custom/index', array('token' => $this->token)));
        }
    }
    public function exportForms()
    {
        $where = array('token' => $this->token, 'set_id' => $this->set_id);
        $list = $this->info_db->where($where)->order('add_time desc')->select();
        $data = array();
        $title = array('用户名', '提交时间');
        $fields = $this->field_db->where($where)->order('sort desc')->getField('field_name', true);
        $title = array_merge($title, $fields);
        foreach ($list as $key => $value) {
            $data[$key][] = $value['user_name'];
            $data[$key][] = date('Y-m-d H:i:s', $value['add_time']);
            $sub_info = unserialize($value['sub_info']);
            foreach ($sub_info as $keys => $values) {
                $data[$key][] = $values['value'];
            }
        }
        $exname = $this->set_db->where($where)->getField('title');
        $this->exportexcel($data, $title, $exname);
    }
    public function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header('Content-type:application/octet-stream');
        header('Accept-Ranges:bytes');
        header('Content-type:application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename=' . $filename . '.xls');
        header('Pragma: no-cache');
        header('Expires: 0');
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv('UTF-8', 'GB2312', $v);
            }
            $title = implode('	', $title);
            echo "{$title}\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv('UTF-8', 'GB2312', $cv);
                }
                $data[$key] = implode('	', $data[$key]);
            }
            echo implode('
', $data);
        }
    }
    public function forms()
    {
        $list = $this->field_db->where(array('set_id' => $this->set_id))->order('sort desc')->select();
        $list = $this->_createInput($list);
        $this->assign('list', $list);
        $this->display();
    }
    public function _createInput($list)
    {
        foreach ($list as $key => $value) {
            $list[$key]['input'] = $this->_getInput($value['field_type']);
        }
        return $list;
    }
    public function forms_set()
    {
        $field_id = $this->_get('field_id', 'intval');
        $field_info = $this->field_db->where(array('field_id' => $field_id))->find();
        if (IS_POST) {
            if ($field_info) {
                $where = array('token' => $this->token, 'field_id' => $field_id);
                $this->field_db->where($where)->save($_POST);
                $this->success('修改成功', U('Custom/forms', array('token' => $this->token, 'set_id' => $this->set_id)));
            } else {
                $_POST['item_name'] = $this->_getItemName($this->set_id);
                $_POST['token'] = $this->token;
                if ($this->field_db->create($_POST)) {
                    $id = $this->field_db->add($_POST);
                    $this->success('添加成功', U('Custom/forms', array('token' => $this->token, 'set_id' => $this->set_id)));
                } else {
                    $this->error($this->field_db->getError());
                }
            }
        } else {
            $this->assign('set', $field_info);
            $this->assign('field_type', $this->_formConf('field_type', $field_info['field_type']));
            $this->assign('field_match', $this->_formConf('field_match', $field_info['field_match']));
            $this->display();
        }
    }
    public function forms_del()
    {
        $where = array('token' => $this->token, 'field_id' => $this->_get('field_id', 'intval'));
        if ($this->field_db->where($where)->delete()) {
            $this->success('删除成功');
        }
    }
    public function _getItemName($set_id, $length = 5)
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str_length = strlen($str);
        $item = '';
        for ($i = 0; $i <= $length; $i++) {
            $rand = mt_rand(0, $str_length);
            $item .= $str[$rand];
        }
        $item = $item . '_' . $set_id;
        if ($this->field_db->where(array('set_id' => $set_id, 'item_name' => $tiem))->find()) {
            return $this->_getItemName($set_id);
        } else {
            return $item;
        }
    }
    public function _getInput($type)
    {
        $str = '';
        switch ($type) {
            case 'text':
                $str = '<input type="text" class="px">';
                break;
            case 'textarea':
                $str = '<textarea rows="2" cols="20" style="height:35px;border:1px solid #cccccc;"></textarea>';
                break;
            case 'select':
                $str = '<select><option value="">请选择</select>';
                break;
            case 'checkbox':
                $str = '<input type="checkbox">';
                break;
            case 'radio':
                $str = '<input type="radio">';
                break;
            case 'date':
                $str = '<input type="text" class="px" value="2014-01-01">';
                break;
        }
        return $str;
    }
    public function _formConf($type = '', $select = '')
    {
        $conf = array('field_type' => array(array('value' => '', 'text' => '请选择类型'), array('value' => 'text', 'text' => '单行文本框'), array('value' => 'textarea', 'text' => '多行文本框'), array('value' => 'checkbox', 'text' => '多选选框'), array('value' => 'radio', 'text' => '单选按钮'), array('value' => 'select', 'text' => '下拉框'), array('value' => 'date', 'text' => '日期选择')), 'field_match' => array(array('value' => '', 'text' => '常用输入验证'), array('value' => '^[\\u4e00-\\u9fa5\\a-zA-Z0-9]+$', 'text' => '英文数字汉字'), array('value' => '^[A-Za-z]+$', 'text' => '英文大小写字符'), array('value' => '^[1-9]\\d*|0$', 'text' => '0或正整数'), array('value' => '^[0-9]{1,30}$', 'text' => '正整数'), array('value' => '^[-\\+]?\\d+(\\.\\d+)?$', 'text' => '小数'), array('value' => '\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*', 'text' => '邮箱'), array('value' => '^13[0-9]{9}$|^15[0-9]{9}$|^18[0-9]{9}$', 'text' => '手机')));
        $str = '';
        foreach ($conf[$type] as $key => $value) {
            if ($select == $value['value']) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $str .= '<option value="' . $value['value'] . '" ' . $selected . '>' . $value['text'] . '</option>';
        }
        return $str;
    }
    public function record()
    {
        $set_id = $this->_get('set_id', 'intval');
        if ($set_id) {
            $set_name = $this->set_db->where(array('token' => $this->token, 'set_id' => $set_id))->getField('title');
            $this->assign('set_name', $set_name);
        }
        if ($this->_get('month') == false) {
            $month = date('m');
        } else {
            $month = $this->_get('month');
        }
        $thisYear = date('Y');
        if ($this->_get('year') == false) {
            $year = $thisYear;
        } else {
            $year = $this->_get('year');
        }
        $this->assign('month', $month);
        $this->assign('year', $year);
        $lastyear = $thisYear - 1;
        if ($year == $lastyear) {
            $yearOption = '<option value="' . $lastyear . '" selected>' . $lastyear . '</option><option value="' . $thisYear . '">' . $thisYear . '</option>';
        } else {
            $yearOption = '<option value="' . $lastyear . '">' . $lastyear . '</option><option value="' . $thisYear . '" selected>' . $thisYear . '</option>';
        }
        $this->assign('yearOption', $yearOption);
        $where = array('token' => $this->token);
        $times = $this->_mFristAndLast($month, $year);
        $where['add_time'] = array(array('gt', $times['firstday']), array('lt', $times['lastday']), 'and');
        if ($month == date('m')) {
            $day_total = date('d') + 2;
        } else {
            $day_total = date('t', strtotime("{$year}-{$month}-01"));
        }
        $xml = '<chart bgColor="ffffff" outCnvBaseFontColor="666666" caption="' . $month . '月提交统计图" xAxisName="模块" yAxisName="数量" showNames="1" showValues="0" plotFillAlpha="50" numVDivLines="10" showAlternateVGridColor="1" bgAlpha="0" showBorder="0" bgColor="ffffff" AlternateVGridColor="e1f5ff" divLineColor="e1f5ff" vdivLineColor="e1f5ff" baseFontColor="666666" baseFontSize="12" borderThickness="0" canvasBorderThickness="0" showPlotBorder="0" plotBorderThickness="0" canvasBorderColor="eeeeee">';
        $categoryStr = '<categories>';
        $dataStr1 = '<dataset seriesName="真实用户" color="69C027" plotBorderColor="69C027">';
        for ($i = 1; $i <= $day_total; $i++) {
            $categoryStr .= '<category label="' . date('d', mktime(0, 0, 0, $month, $i, $year)) . '"/>';
            $dataStr1 .= '<set value="' . $this->_getSub(1, $month, $i, $year, $set_id) . '"/>';
        }
        $categoryStr .= '</categories>';
        $dataStr1 .= '</dataset>';
        $dataStr2 = '<dataset seriesName="分享用户" color="E9CB50" plotBorderColor="E9CB50">';
        for ($i = 1; $i <= $day_total; $i++) {
            $dataStr2 .= '<set value="' . $this->_getSub(2, $month, $i, $year, $set_id) . '"/>';
        }
        $dataStr2 .= '</dataset>';
        $xml .= $categoryStr . $dataStr1 . $dataStr2 . '</chart>';
        $count = $this->_getSub(3, '', '', '', $set_id);
        $today_count = $this->_getSub(0, date('m'), date('d'), date('Y'), $set_id);
        $yesterday_count = $this->_getSub(0, date('m'), date('d') - 1, date('Y'), $set_id);
        $this->assign('count', $count);
        $this->assign('today_count', $today_count);
        $this->assign('yesterday_count', $yesterday_count);
        $this->assign('set_id', $set_id);
        $this->assign('xml', $xml);
        $this->display();
    }
    public function _getSub($flag = 0, $m = 0, $d = 0, $y = 0, $set_id = 0)
    {
        $where = array('token' => $this->token);
        if ($set_id) {
            $where['set_id'] = $set_id;
        }
        if ($flag == 1) {
            $where['wecha_id'] = array('NEQ', 'NULL');
        }
        if ($flag == 2) {
            $where['wecha_id'] = array('EQ', 'NULL');
        }
        if ($flag != 3) {
            $start_time = mktime(0, 0, 0, $m, $d, $y);
            $end_time = mktime(23, 59, 59, $m, $d, $y);
            $where['add_time'] = array(array('gt', $start_time), array('lt', $end_time), 'and');
        }
        $subNum = $this->info_db->where($where)->count();
        if (empty($subNum)) {
            $subNum = 0;
        }
        return $subNum;
    }
    public function _mFristAndLast($m = '', $y = '')
    {
        if ($y == '') {
            $y = date('Y');
        }
        if ($m == '') {
            $m = date('m');
        }
        $m = sprintf('%02d', intval($m));
        $y = str_pad(intval($y), 4, '0', STR_PAD_RIGHT);
        $m > 12 || $m < 1 ? $m = 1 : ($m = $m);
        $firstday = strtotime($y . $m . '01000000');
        $firstdaystr = date('Y-m-01', $firstday);
        $lastday = strtotime(date('Y-m-d 23:59:59', strtotime("{$firstdaystr} +1 month -1 day")));
        return array('firstday' => $firstday, 'lastday' => $lastday);
    }
}