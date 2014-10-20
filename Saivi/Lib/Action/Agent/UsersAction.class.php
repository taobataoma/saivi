<?php
class UsersAction extends AgentAction{
    public function _initialize(){
        parent :: _initialize();
    }
    public function index(){
        $users_db = M('Users');
        $where = $this -> agentWhere;
        if (isset($_GET['keyword'])){
            $where['username'] = $this -> _get('keyword');
        }
        $count = $users_db -> where($where) -> count();
        $Page = new Page($count, 20);
        $show = $Page -> show();
        $list = $users_db -> where($where) -> order('id DESC') -> limit($Page -> firstRow . ',' . $Page -> listRows) -> select();
        $groups = M('User_group') -> where($this -> agentWhere) -> select();
        $groupsByID = array();
        if ($groups){
            foreach ($groups as $g){
                $groupsByID[$g['id']] = $g;
            }
        }
        if ($list){
            $i = 0;
            foreach ($list as $item){
                $list[$i]['groupName'] = $groupsByID[$item['gid']]['name'];
                $i++;
            }
        }
        $this -> assign('list', $list);
        $this -> assign('page', $show);
        $this -> display();
    }
    public function addUser(){
        if (IS_POST){
			//by weiqianlong 2014.7.8
            $users_db = M('Users');
			$username = M('Users')->where(array('username'=>$_POST['username']))->find();
			
			
			if ($username){

                    $this -> error('用户名已存在！');
            };
			if (empty($_POST['password'])){

                    $this -> error('请填写密码！');
					

            };
			if (empty($_POST['email'])){

                    $this -> error('请填写邮箱！');
					

            };
			if (empty($_POST['mp'])){

                    $this -> error('请填写写手机号码！');
					

            };
            if (trim($_POST['password'])){
                $password = $this -> _post('password', 'trim', 0);
                $repassword = $this -> _post('repassword', 'trim', 0);
                if($password != $repassword){
                    $this -> error('两次输入密码不一致！');
                }
                $_POST['password'] = md5($password);
            }else{
                unset($_POST['password']);
                unset($_POST['repassword']);
            }
            $_POST['agentid'] = $this -> agentid;
            $_POST['status'] = 1;
            $_POST['viptime'] = strtotime($_POST['viptime']);
            if($users_db -> create()){
                $user_id = $users_db -> add();
                if($user_id){
                    $this -> success('添加成功！', U('Users/index'));
                }else{
                    $this -> error('添加失败!');
                }
            }else{
                $this -> error($users_db -> getError());
            }
        }else{
            $this -> assign('actionUrl', '?g=Agent&m=Users&a=addUser');
            $this -> assign('pageName', '添加用户');
            $groups = M('User_group') -> where($this -> agentWhere) -> select();
            $this -> assign('groups', $groups);
            $thisUser = array('viptime' => time());
            $this -> assign('info', $thisUser);
            $this -> display();
        }
    }
    public function updateUser(){
        if (IS_POST){
            $users_db = M('Users');
            if (trim($_POST['password'])){
                $password = $this -> _post('password', 'trim', 0);
                $repassword = $this -> _post('repassword', 'trim', 0);
                if($password != $repassword){
                    $this -> error('两次输入密码不一致！');
                }
                $_POST['password'] = md5($password);
            }else{
                unset($_POST['password']);
                unset($_POST['repassword']);
            }
            unset($_POST['dosubmit']);
            unset($_POST['__hash__']);
            $users = M('Users') -> field('gid') -> find($_POST['id']);
            $_POST['viptime'] = strtotime($_POST['viptime']);
            if($users_db -> save($_POST)){
                if($_POST['gid'] != $users['gid']){
                    $fun = M('Agent_function') -> field('funname,gid,isserve') -> where('`gid` <= ' . $_POST['gid'] . ' AND agentid=' . $this -> thisAgent['id']) -> select();
                    foreach($fun as $key => $vo){
                        $queryname .= $vo['funname'] . ',';
                    }
                    $open['queryname'] = rtrim($queryname, ',');
                    $uid['uid'] = $_POST['id'];
                    $token = M('Wxuser') -> field('token') -> where($uid) -> select();
                    if($token){
                        $token_db = M('Token_open');
                        foreach($token as $key => $val){
                            $wh['token'] = $val['token'];
                            $token_db -> where($wh) -> save($open);
                        }
                    }
                }
                $this -> success('编辑成功！', U('Users/index'));
            }else{
                $this -> error('编辑失败!');
            }
        }else{
            $id = intval($_GET['id']);
            $thisUser = M('Users') -> where(array('agentid' => $this -> thisAgent['id'], 'id' => $id)) -> find();
            if (!$thisUser){
                $this -> error('没有此用户');
            }
            $this -> assign('actionUrl', '?g=Agent&m=Users&a=updateUser');
            $this -> assign('pageName', '修改用户');
            $this -> assign('isUpdate', 1);
            $this -> assign('info', $thisUser);
            $groups = M('User_group') -> where($this -> agentWhere) -> select();
            $this -> assign('groups', $groups);
            $this -> display();
        }
    }
    public function deleteUser(){
        $id = intval($_GET['id']);
        $thisUser = M('Users') -> where(array('agentid' => $this -> thisAgent['id'], 'id' => $id)) -> find();
        if (!$thisUser){
            $this -> error('没有此用户');
        }
        $rt = M('Users') -> where(array('id' => $id)) -> delete();
        if ($rt){
            $userCount = M('Users') -> where(array('agentid' => $this -> thisAgent['id'])) -> count();
            M('Agent') -> where($this -> agentWhere) -> save(array('usercount' => $userCount));
            M('Wxuser') -> where(array('uid' => $id)) -> delete();
            $wxuserCount = M('Wxuser') -> where(array('agentid' => $this -> thisAgent['id'])) -> count();
            M('Agent') -> where($this -> agentWhere) -> save(array('wxusercount' => $wxuserCount));
        }
        $this -> success('删除成功！', U('Users/index'));
    }
    public function groups(){
        $db = M('User_group');
        $count = $db -> where($this -> agentWhere) -> count();
        $Page = new Page($count, 200);
        $show = $Page -> show();
        $list = $db -> where($this -> agentWhere) -> order('id ASC') -> select();
        if ($list){
            $i = 1;
            foreach ($list as $item){
                $db -> where(array('id' => $item['id'])) -> save(array('taxisid' => $i));
                $i++;
            }
        }
        $this -> assign('list', $list);
        $this -> assign('page', $show);
        $this -> display();
    }
    public function wxusers(){
        $db = M('Wxuser');
        $count = $db -> where($this -> agentWhere) -> count();
        $Page = new Page($count, 20);
        $show = $Page -> show();
        $list = $db -> where($this -> agentWhere) -> order('id ASC') -> limit($Page -> firstRow . ',' . $Page -> listRows) -> select();
        $uids = array();
        if ($list){
            foreach ($list as $item){
                if (!in_array($item['uid'], $uids)){
                    array_push($uids, $item['uid']);
                }
            }
        }
        if ($uids){
            $users = M('Users') -> where(array('id' => array('in', $uids))) -> select();
            $usersByID = array();
            if ($users){
                foreach ($users as $u){
                    $usersByID[$u['id']] = $u;
                }
            }
            if ($list){
                $i = 0;
                foreach ($list as $item){
                    $list[$i]['username'] = $usersByID[$item['uid']]['username'];
                    $i++;
                }
            }
        }
        $this -> assign('list', $list);
        $this -> assign('page', $show);
        $this -> display();
    }
    public function deleteWxUser(){
        $id = intval($_GET['id']);
        $thisUser = M('Wxuser') -> where(array('agentid' => $this -> thisAgent['id'], 'id' => $id)) -> find();
        if (!$thisUser){
            $this -> error('没有此公众号');
        }
        $rt = M('Wxuser') -> where(array('id' => $id)) -> delete();
        $wxuserCount = M('Wxuser') -> where(array('agentid' => $this -> thisAgent['id'])) -> count();
        M('Agent') -> where($this -> agentWhere) -> save(array('wxusercount' => $wxuserCount));
        $this -> success('删除成功！', U('Users/wxusers'));
    }
    public function groupSet(){
        $user_group_db = M('User_group');
        if (IS_POST){
            if (isset($_POST['id'])){
                if($user_group_db -> create()){
                    $user_group_db -> where(array('agentid' => $this -> thisAgent['id'], 'id' => intval($_POST['id']))) -> save($_POST);
                    $this -> success('修改成功！', U('Users/groups'));
                }
            }else{
                if($user_group_db -> create()){
                    $_POST['agentid'] = intval($this -> thisAgent['id']);
                    $user_group_db -> add($_POST);
                    $this -> success('添加成功！', U('Users/groups'));
                }
            }
        }else{
            if (isset($_GET['id'])){
                $thisGroup = $user_group_db -> where(array('agentid' => $this -> thisAgent['id'], 'id' => intval($_GET['id']))) -> find();
                $this -> assign('info', $thisGroup);
            }
            $this -> display();
        }
    }
    public function delGroup(){
        $id = $this -> _get('id', 'intval', 0);
        if($id == 0)$this -> error('非法操作');
        $info = D('User_group') -> where(array('agentid' => $this -> thisAgent['id'], 'id' => $id)) -> delete();
        $this -> success('操作成功');
    }
}
?>