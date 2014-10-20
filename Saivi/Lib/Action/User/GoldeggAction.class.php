<?php
class GoldeggAction extends UserAction
{
    public function index()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用砸金蛋活动,请充值后再使用', U('Home/Index/price'));
        }
        $user = M('Users')->field('gid,activitynum')->where(array('id' => session('uid')))->find();
        $group = M('User_group')->where(array('id' => $user['gid']))->find();
        $this->assign('group', $group);
        $this->assign('activitynum', $user['activitynum']);
        $list = M('Goldegg')->field('id,title,joinnum,click,keyword,startdate,enddate,status')->where(array('token' => session('token')))->select();
        foreach ($list as $key => $val) {
            $list[$key]['joinnum'] = M('Goldegg_record')->where(array('token' => session('token'), 'lid' => $val['id']))->count();
        }
        $this->assign('count', M('Goldegg')->where(array('token' => session('token')))->count());
        $this->assign('list', $list);
        $this->assign('activityname', '砸金蛋');
        $this->display();
    }
    public function sn()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用抽奖活动,请充值后再使用', U('Home/Index/price'));
        }
        $id = $this->_get('id');
        $data = M('Goldegg')->where(array('token' => session('token'), 'id' => $id))->find();
        $re = M('Goldegg_record')->field('id,lid,wecha_id,islucky')->where(((('token="' . session('token')) . '" and lid=') . $id) . ' and islucky = 1 ')->select();
        foreach ($re as $key => $val) {
            $record[$key]['snlist'] = M('Sncode')->where(((((('token="' . session('token')) . '" and wecha_id ="') . $val['wecha_id']) . '" and lid=') . $val['lid']) . ' and islucky = 1 and sn!=""')->select();
        }
        $recordcount = M('Sncode')->where(((('token="' . session('token')) . '" and lid=') . $id) . ' and islucky = 1 and sn!=""')->count();
        $datacount = ($data['firstnums'] + $data['secondnums']) + $data['thirdnums'];
        $this->assign('datacount', $datacount);
        $this->assign('recordcount', $recordcount);
        $this->assign('record', $record);
        $sendCount = M('Sncode')->where(('lid=' . $id) . ' and sendstutas=1 and sn!=""')->count();
        $this->assign('sendCount', $sendCount);
        $this->assign('activityname', '砸金蛋');
        $this->display();
    }
    public function add()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法使用抽奖活动,请充值后再使用', U('Home/Index/price'));
        }
        if (IS_POST) {
            $data = D('Goldegg');
            $_POST['startdate'] = strtotime($_POST['startdate']);
            $_POST['enddate'] = strtotime($_POST['enddate']);
            $_POST['token'] = session('token');
            if ($data->create() != false) {
                if ($id = $data->add()) {
                    $data1['pid'] = $id;
                    $data1['module'] = 'Goldegg';
                    $data1['token'] = session('token');
                    $data1['keyword'] = $_POST['keyword'];
                    M('Keyword')->add($data1);
                    $user = M('Users')->where(array('id' => session('uid')))->setInc('activitynum');
                    $this->success('活动创建成功', U('Goldegg/index', array('token' => session('token'))));
                } else {
                    $this->error('服务器繁忙,请稍候再试');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $this->assign('activityname', '砸金蛋');
            $this->display();
        }
    }
    public function start()
    {
        if (session('gid') == 1) {
            $this->error('vip0无法开启活动,请充值后再使用', U('Home/Index/price'));
        }
        $id = $this->_get('id');
        $where = array('id' => $id, 'token' => session('token'));
        $check = M('Goldegg')->where($where)->find();
        if ($check == false) {
            $this->error('非法操作');
        }
        $user = M('Users')->field('gid,activitynum')->where(array('id' => session('uid')))->find();
        $group = M('User_group')->where(array('id' => $user['gid']))->find();
        if ($user['activitynum'] >= $group['activitynum']) {
            $this->error('您的免费活动创建数已经全部使用完,请充值后再使用', U('Home/Index/price'));
        }
        if ($check['status'] == 2) {
            $this->error('该活动已经结束，无法再次开启');
        }
        $data = M('Goldegg')->where($where)->setInc('status');
        if ($data != false) {
            $this->success('恭喜你,活动已经开始');
        } else {
            $this->error('服务器繁忙,请稍候再试');
        }
    }
    public function close()
    {
        $id = $this->_get('id');
        $where = array('id' => $id, 'token' => session('token'));
        $check = M('Goldegg')->where($where)->find();
        if ($check == false) {
            $this->error('非法操作');
        }
        $data = M('Goldegg')->where($where)->setInc('status');
        if ($data != false) {
            $this->success('活动已经结束');
        } else {
            $this->error('服务器繁忙,请稍候再试');
        }
    }
    public function edit()
    {
        if (IS_POST) {
            $data = D('Goldegg');
            $_POST['id'] = $this->_get('id');
            $_POST['token'] = session('token');
            $where = array('id' => $_POST['id'], 'token' => $_POST['token']);
            $_POST['startdate'] = strtotime($_POST['startdate']);
            $_POST['enddate'] = strtotime($_POST['enddate']);
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            if ($data->create()) {
                if ($id = $data->where($where)->save($_POST)) {
                    $data1['pid'] = $_POST['id'];
                    $data1['module'] = 'Goldegg';
                    $data1['token'] = session('token');
                    $da['keyword'] = $_POST['keyword'];
                    M('Keyword')->where($data1)->save($da);
                    $this->success('修改成功');
                } else {
                    $this->error('操作失败');
                }
            } else {
                $this->error($data->getError());
            }
        } else {
            $id = $this->_get('id');
            $where = array('id' => $id, 'token' => session('token'));
            $data = M('Goldegg');
            $check = $data->where($where)->find();
            if ($check == false) {
                $this->error('非法操作');
            }
            $goldegg = $data->where($where)->find();
            $this->assign('vo', $goldegg);
            $this->display('add');
        }
    }
    public function delete()
    {
        $id = $this->_get('id');
        $where = array('id' => $id, 'token' => session('token'));
        $data = M('Goldegg');
        $check = $data->where($where)->find();
        if ($check == false) {
            $this->error('非法操作');
        }
        $back = $data->where($wehre)->delete();
        if ($back == true) {
            M('Keyword')->where(array('pid' => $id, 'token' => session('token'), 'module' => 'Goldegg'))->delete();
            $this->success('删除成功');
        } else {
            $this->error('操作失败');
        }
    }
    public function sendprize()
    {
        $id = $this->_get('id');
        $lid = $this->_get('lid');
        $wecha_id = $this->_get('wecha_id');
        $where = array('id' => $id, 'lid' => $lid, 'token' => session('token'), 'wecha_id' => $wecha_id, 'module' => MODULE_NAME);
        $data['sendtime'] = time();
        $data['sendstutas'] = 1;
        $back = M('Sncode')->where($where)->save($data);
        if ($back == true) {
            $this->success('成功发奖');
        } else {
            $this->error('操作失败');
        }
    }
    public function sendnull()
    {
        $id = $this->_get('id');
        $lid = $this->_get('lid');
        $wecha_id = $this->_get('wecha_id');
        $where = array('id' => $id, 'lid' => $lid, 'token' => session('token'), 'wecha_id' => $wecha_id, 'module' => MODULE_NAME);
        $data['sendtime'] = '';
        $data['sendstutas'] = 0;
        $back = M('Sncode')->where($where)->save($data);
        if ($back == true) {
            $this->success('已经取消');
        } else {
            $this->error('操作失败');
        }
    }
}