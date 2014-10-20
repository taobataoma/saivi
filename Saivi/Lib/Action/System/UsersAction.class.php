<?php
class UsersAction extends BackAction{
	public function index(){
		$db=D('Users');
		$group=M('User_group')->field('id,name')->order('id desc')->select();
		$where='';
		if (isset($_GET['agentid'])){
			$where=array('agentid'=>intval($_GET['agentid']));
		}
		if (isset($_GET['inviter'])){
			$where=array('inviter'=>intval($_GET['inviter']));
		}
		
		$count= $db->where($where)->count();
		$Page= new Page($count,25);
		$show= $Page->show();
		
		$list = $db->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($group as $key=>$val){
			$g[$val['id']]=$val['name'];
		}
		unset($group);
		$this->assign('info',$list);
		$this->assign('page',$show);
		$this->assign('group',$g);
		$this->display();
	}
	public function set(){
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
            $id = $this->_get('id','intval',0);
            if(!$id)$this->error('参数错误!');
            $map=array('status'=>1);
        	if (C('agent_version')){
				$map['agentid']=array('lt',1);
			}
            $role = M('User_group')->field('id,name')->where($map)->select();
            $info = $UserDB->find($id);
            $inviteCount=$UserDB->where(array('inviter'=>$id))->count();
            $this->assign('inviteCount',$inviteCount);
            $this->assign('tpltitle','编辑');
            $this->assign('role',$role);
            $this->assign('info',$info);
            $this->display('set');
        }
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
        	$map=array('status'=>1);
        	if (C('agent_version')){
				$map['agentid']=array('lt',1);
			}
            $role = M('User_group')->field('id,name')->where($map)->select();
            $this->assign('role',$role);
            $this->assign('tpltitle','添加');
            $this->display();
        }
    }
	public function search(){
		$name=$this->_post('name');
		$type=$this->_post('type');
		switch($type){
			case 1:
			$data['username']=$name;
			break;
			case 2:
			$data['id']=$name;
			break;
			case 3:
			$data['email']=$name;
		}
		//dump($where);
		$list=M('Users')->where($data)->select();
		$this->assign('info',$list);
		$this->display('index');
	
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
            $id = $this->_get('id','intval',0);
            if(!$id)$this->error('参数错误!');
            $map=array('status'=>1);
        	if (C('agent_version')){
				$map['agentid']=array('lt',1);
			}
            $role = M('User_group')->field('id,name')->where($map)->select();
            $info = $UserDB->find($id);
            $inviteCount=$UserDB->where(array('inviter'=>$id))->count();
            $this->assign('inviteCount',$inviteCount);
            $this->assign('tpltitle','编辑');
            $this->assign('role',$role);
            $this->assign('info',$info);
            $this->display('add');
        }
    }
	public function addfc(){
		$token_open=M('Token_open');
		$open['uid']=session('uid');
		$open['token']=$_POST['token'];
		$gid=session('gid');
		$fun=M('Function')->field('funname,gid,isserve')->where('`gid` <= '.$gid)->select();
		foreach($fun as $key=>$vo){
			$queryname.=$vo['funname'].',';
		}
		$open['queryname']=rtrim($queryname,',');
		$token_open->data($open)->add();
	}
	
	//删除用户
    public function del(){
        $id = $this->_get('id','intval',0);
        if(!$id)$this->error('参数错误!');
        $UserDB = D('Users');
        $thisUser=$UserDB->where(array('id'=>$id))->find();
        $where['uid']=$id;
        $wxUserCount=M('wxuser')->where($where)->count();
        if($UserDB->delete($id)){
        	//
        	if ($thisUser['agentid']){
        		M('Agent')->where(array('id'=>$thisUser['agentid']))->setDec('usercount');
        		M('Agent')->where(array('id'=>$thisUser['agentid']))->setDec('wxusercount',$wxUserCount);
        	}
        	//
			
			//M('wxuser')->where($where)->delete();
			//M('token_open')->where($where)->delete();
			//M('text')->where($where)->delete();
			//M('img')->where($where)->delete();
			//M('member')->where($where)->delete();
			//M('indent')->where($where)->delete();
			//M('areply')->where($where)->delete();
			$this->assign("jumpUrl");
			$this->success('删除成功！');            
        }else{
            $this->error('删除失败!');
        }
    }
    public function changeAllStatus(){
    	$data = array(
    		'cy'=>1,
    		'wy'=>1,
    		'yy'=>1,
    		'fc'=>1,
    		'jd'=>1,
    		'yl'=>1,
    		'qc'=>1,
    		'dy'=>1,
    		'mr'=>1,
    		'jb'=>1,
    		'jy'=>1,
    		'hd'=>1,
    		'zw'=>1,
    		'ly'=>1,
    		'sp'=>1,
    		'mb'=>1,
    		'wifi'=>1,
    		'csshop'=>1,
    		'wsh'=>1,
    		'zhuangxiu'=>1,
    		'zujie'=>1,
    		'wuye'=>1,
    		'ktv'=>1,
    		'weidianshang'=>1,
    		'wcx'=>1,
    		'whd'=>1,
    		'wyx'=>1,
    		'wyy'=>1,
    		'kf'=>1,
    		'huiyuan'=>1,
    		'hunqing'=>1,
    		'ggk'=>1,
    		'dzp'=>1,
    		'yhq'=>1,
    		'yzdd'=>1,
    		'zthdbm'=>1,
    		'wxq'=>1,
    		'wmp'=>1,
    		'wdy'=>1,
    		'wxc'=>1,
    		'wtp'=>1,
    		'360qj'=>1,
    		'wnbd'=>1,
    		'zxyd'=>1,
    		'wlt'=>1,
    		'wyq'=>1,
    		'zxyd'=>1,
    		'wlt'=>1,
    		'wyq'=>1,
    		'wsm'=>1,
    		'wxt'=>1,
    		'qj'=>1,
    		'fczj'=>1,
    		'zhaopin'=>1,
    		'weiyuyue'=>1,
            'yiy'=>1,
            'zpq'=>1,
            'why'=>1,
            'js'=>1,
            'zjd'=>1,
            'sgj'=>1,
            'wxqd'=>1,
            'zfhk'=>1,

    		);
    	if(M('Users')->where(array('id'=>$this->_get('id')))->save($data)){
			$this->success('修改成功',U('set',array('id'=>$this->_get('id'))));
		}else{
			$this->error('修改失败',U('set',array('id'=>$this->_get('id'))));
		}
    }

//全部关闭
 public function quanbuguanbi(){
    	$data = array(
    		'cy'=>0,
    		'wy'=>0,
    		'yy'=>0,
    		'fc'=>0,
    		'jd'=>0,
    		'yl'=>0,
    		'qc'=>0,
    		'dy'=>0,
    		'mr'=>0,
    		'jb'=>0,
    		'jy'=>0,
    		'hd'=>0,
    		'zw'=>0,
    		'ly'=>0,
    		'sp'=>0,
    		'mb'=>0,
    		'wifi'=>0,
    		'csshop'=>0,
    		'wsh'=>0,
    		'zhuangxiu'=>0,
    		'zujie'=>0,
    		'wuye'=>0,
    		'ktv'=>0,
    		'weidianshang'=>0,
    		'wcx'=>0,
    		'whd'=>0,
    		'wyx'=>0,
    		'wyy'=>0,
    		'kf'=>0,
    		'huiyuan'=>0,
    		'hunqing'=>0,
    		'ggk'=>0,
    		'dzp'=>0,
    		'yhq'=>0,
    		'yzdd'=>0,
    		'zthdbm'=>0,
    		'wxq'=>0,
    		'wmp'=>0,
    		'wdy'=>0,
    		'wxc'=>0,
    		'wtp'=>0,
    		'360qj'=>0,
    		'wnbd'=>0,
    		'zxyd'=>0,
    		'wlt'=>0,
    		'wyq'=>0,
    		'zxyd'=>0,
    		'wlt'=>0,
    		'wyq'=>0,
    		'wsm'=>0,
    		'wxt'=>0,
    		'qj'=>0,
    		'fczj'=>0,
    		'zhaopin'=>0,
    		'weiyuyue'=>0,
            'yiy'=>0,
            'zpq'=>0,
            'why'=>0,
            'js'=>0,
            'zjd'=>0,
            'sgj'=>0,
            'wxqd'=>0,
            'zfhk'=>0,

    		);
    	if(M('Users')->where(array('id'=>$this->_get('id')))->save($data)){
			$this->success('修改成功',U('set',array('id'=>$this->_get('id'))));
		}else{
			$this->error('修改失败',U('set',array('id'=>$this->_get('id'))));
		}
    }
}