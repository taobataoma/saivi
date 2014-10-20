<?php
class TokenAction extends BackAction{
	public function index(){
		$map = array();
		$UserDB = D('Wxuser');
		if (isset($_GET['agentid'])){
			$map=array('agentid'=>intval($_GET['agentid']));
		}
		$count = $UserDB->where($map)->count();
		$Page       = new Page($count,5);// 实例化分页类 传入总记录数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$list = $UserDB->where($map)->order('id ASC')->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		foreach($list as $key=>$value){
			$user=M('Users')->field('id,gid,username')->where(array('id'=>$value['uid']))->find();
			if($user){
				$list[$key]['user']['username']=$user['username'];
				$list[$key]['user']['gid']=$user['gid']-1;
			}
		}
		//dump($list);
		$this->assign('list',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		
		
	}
	public function del(){
		$id=$this->_get('id','intval',0);
		$wx=M('Wxuser')->where(array('id'=>$id))->find();
		if ($wx['agentid']){
			M('Agent')->where(array('id'=>$wx['agentid']))->setDec('wxusercount');
		}
		M('Img')->where(array('token'=>$wx['token']))->delete();
		M('Text')->where(array('token'=>$wx['token']))->delete();
		M('Lottery')->where(array('token'=>$wx['token']))->delete();
		M('Keyword')->where(array('token'=>$wx['token']))->delete();
		M('Photo')->where(array('token'=>$wx['token']))->delete();
		M('Home')->where(array('token'=>$wx['token']))->delete();
		M('Areply')->where(array('token'=>$wx['token']))->delete();
		$diy=M('Diymen_class')->where(array('token'=>$wx['token']))->delete();
		M('Wxuser')->where(array('id'=>$id))->delete();
		$this->success('操作成功');
	}
	public function edit(){
		if($_POST){
			$username = $this->_post('username');
			$usersObj = M('Users');
			
			$data = $usersObj->field('id')->where(array('username'=>$username))->find();
			$wxuserObj = M('Wxuser');
			$token = $wxuserObj->where(array('id'=>$this->_get('id')))->find();
			$imgObj = M('Img');
			

			if($wxuserObj->where(array('id'=>$this->_get('id')))->save(array('uid'=>$data['id'])) 
				&& $imgObj->where(array('token'=>$token['token']))->save(array('uid'=>$data['id']))){
				$this->success('操作成功',U('index'));
			}else{
				$this->error('操作失败',U('index'));
			}
		}

		$this->display();
	}
}
?>