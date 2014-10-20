<?php
class UcAction extends BackAction{
	public function __construct(){
		parent::_initialize();
		if(C('UCUSE')){
			define('UCUSE',C('UCUSE'));
			define('UC_CONNECT',C('UC_CONNECT'));
			define('UC_DBHOST',C('UC_DBHOST'));
			define('UC_DBUSER',C('UC_DBUSER'));
			define('UC_DBNAME',C('UC_DBNAME'));
			define('UC_DBPW',C('UC_DBPW'));
			define('UC_DBCHARSET',C('UC_DBCHARSET'));
			define('UC_DBTABLEPRE',C('UC_DBTABLEPRE'));
			define('UC_DBCONNECT',C('UC_DBCONNECT'));
			define('UC_CHARSET',C('UC_CHARSET'));
			define('UC_KEY',C('UC_KEY'));
			define('UC_API',C('UC_API'));
			define('UC_IP',C('UC_IP'));
			define('UC_APPID',C('UC_APPID'));
			define('UC_PPP',C('UC_PPP'));
			include '././api/uc_client/client.php';
		}
	}

	public function _initialize() {
        parent::_initialize();  //RBAC 验证接口初始化
    }

	public function index(){
		$this->display();
	}

	// 添加用户
    public function add(){
        $UserDB = D("Users");
        if(isset($_POST['dosubmit'])) {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(empty($password) || empty($repassword)){
                $this->error('密码必须填写！');
            }
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }
			if(C('UCUSE')){//UC整合
				$username=$this->_post('username','trim');
				$password=$this->_post('password','trim');
				$email=$this->_post('email','trim');
				list($uc_id,$uc_username,$uc_email)=uc_get_user($username);
				$u=$UserDB->find(array('where'=>'`username`=\''.$username.'\''));
				if($uc_id && $u){
					$this->error('用户名已存在，请换个用户名。',U('Users/add'));
				}elseif($uc_id && !$u){
					$id = $UserDB->n_add();
					$this->error('用户名已存在，请换个用户名。',U('Users/add'));
				}elseif(!$uc_id && $u){
					$this->error('用户名已被禁止，请换个用户名。',U('Users/add'));
				}else{
					$uc_id = uc_user_register($username, $password, $email);
					if($uc_id <= 0) {
						if($uc_id == -1) {
							$this->error('用户名不合法',U('Users/add'));
						} elseif($uc_id == -2) {
							$this->error('包含要允许注册的词语',U('Users/add'));
						} elseif($uc_id == -3) {
							$this->error('用户名已经存在',U('Users/add'));
						} elseif($uc_id == -4) {
							$this->error('Email 格式有误',U('Users/add'));
						} elseif($uc_id == -5) {
							$this->error('Email 不允许注册',U('Users/add'));
						} elseif($uc_id == -6) {
							$this->error('该 Email 已经被注册',U('Users/add'));
						} else {
							$this->error('未定义',U('Users/add'));
						}
					}
				}
				$_POST['uc_id']=$uc_id;
			}
            //根据表单提交的POST数据创建数据对象
			$_POST['viptime']=strtotime($_POST['viptime']);
            if($UserDB->create()){
                $user_id = $UserDB->add();
                if($user_id){
					$this->success('添加成功！',U('Users/index'));                    
                }else{
                     $this->error('添加失败!');
                }
            }else{
                $this->error($UserDB->getError());
            }
        }else{
			$this->error('参数错误！',U('Users/add'));                    
        }
    }

    // 编辑用户
    public function edit(){
        $UserDB = D("Users");
        if(isset($_POST['dosubmit'])) {
            $password = $this->_post('password','trim',0);
            $repassword = $this->_post('repassword','trim',0);
			$users=M('Users')->field('gid')->find($_POST['id']);
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }
            if($password==false){ 
				unset($_POST['password']);
				unset($_POST['repassword']);
			}else{
				$_POST['password']=md5($password);
			}
			unset($_POST['dosubmit']);
			unset($_POST['__hash__']);
			if(C('UCUSE')){//UC整合
				$username = $this->_post('username','trim');
				$email = $this->_post('email','trim');
				$id=uc_user_edit($username,'',$password,$email,1);
				if($id == 1){
				}elseif($id == 0){
					//$this->error('没有做任何修改');
				}elseif($id == -1){
					$this->error('旧密码不正确');
				}elseif($id == -4){
					$this->error('Email 格式有误');
				}elseif($id == -5){
					$this->error('Email 不允许注册');
				}elseif($id == -6){
					$this->error('该 Email 已经被注册');
				}elseif($id == -7){
					//$this->error('没有做任何修改');
				}elseif($id == -8){
					//$this->error('该用户受保护无权限更改');
				}else{
					$this->error('未定义');
				}
			}
            //根据表单提交的POST数据创建数据对象
                $_POST['viptime']=strtotime($_POST['viptime']);
                if($UserDB->save($_POST)){
					if($_POST['gid']!=$users['gid']){
						$fun=M('Function')->field('funname,gid,isserve')->where('`gid` <= '.$_POST['gid'])->select();
						foreach($fun as $key=>$vo){
							$queryname.=$vo['funname'].',';
						}
						$open['queryname']=rtrim($queryname,',');
						$uid['uid']=$_POST['id'];
						$token=M('Wxuser')->field('token')->where($uid)->select();
						if($token){
							$token_db=M('Token_open');
							foreach($token as $key=>$val){
								$wh['token']=$val['token'];
								$token_db->where($wh)->save($open);
							}
						}
					}
                    $this->success('编辑成功！',U('Users/index'));
                }else{
                     $this->error('编辑失败!');
                }
            
        }else{
			$this->error('参数错误！');                    
        }
    }
	
	//删除用户
    public function del(){
        $id = $this->_get('id','intval',0);
        if(!$id)$this->error('参数错误!');
        $UserDB = D('Users');
		if(C('UCUSE')){//UC整合
			$users=M('Users')->field('username')->find($id);
			$delid=uc_user_delete($users['username']);
			if(!$delid){
				$this->error('删除成功！');            
			}
		}
        if($UserDB->delete($id)){
			$where['uid']=$id;
			M('wxuser')->where($where)->delete();
			M('token_open')->where($where)->delete();
			M('text')->where($where)->delete();
			M('img')->where($where)->delete();
			M('member')->where($where)->delete();
			M('indent')->where($where)->delete();
			M('areply')->where($where)->delete();
			$this->assign("jumpUrl");
			$this->success('删除成功！');            
        }else{
            $this->error('删除失败!');
        }
    }
	



}