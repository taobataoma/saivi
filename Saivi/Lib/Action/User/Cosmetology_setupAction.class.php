<?php

class Cosmetology_setupAction extends UserAction{
	public function index(){
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();
		if(!strpos($token_open['queryname'],'Cosmetology')){
            $this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Cosmetology_setup/index',array('token'=>$_SESSION['token'],'id'=>session('wxid'))));}
		$db=D('Cosmetology_setup');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$infoa=M('Cosmetology_setup_control')->where(array('token'=>$this->_GET('token')))->find();
		
		$this->assign('infoa',$infoa);
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	public function add(){
	    $ks=M('Cosmetology_departments')->find();
		$id=M('Cosmetology_departments')->where(array('token'=>$this->_GET('token')))->order('id desc')->select(); 
		$this->assign('ks',$ks);
		$this->assign('id',$id);
		$this->display();
	}
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('Cosmetology_setup')->find($id);
	    $ks=M('Cosmetology_departments')->find();
		$id=M('Cosmetology_departments')->where(array('token'=>$this->_GET('token')))->order('id desc')->select(); 
		$this->assign('ks',$ks);
		$this->assign('id',$id);
		$this->assign('info',$info);
		$this->display();
	}
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
		$this->all_insert();
	}
	public function upsave(){
		$this->all_save();
	}
}
?>
