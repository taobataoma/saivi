<?php

class CosmetologyAction extends UserAction{
	public function index(){
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();
		if(!strpos($token_open['queryname'],'Cosmetology')){
            $this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Cosmetology/index',array('token'=>$_SESSION['token'],'id'=>session('wxid'))));}	
		$db=D('Cosmetology');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();	
		$infoa=M('Cosmetology_setup_control')->where(array('token'=>$this->_GET('token')))->find();	
		$tj=M('Cosmetology')->where(array('token'=>$this->_GET('token')))->count();
		$this->assign('tj',$tj);	
		$this->assign('page',$page->show());
		$this->assign('infoa',$infoa);
		$this->assign('info',$info);
		$this->display();
	}
	public function add(){
	$infoa=M('Cosmetology_setup_control')->where(array('token'=>$this->_GET('token')))->find();
	$this->assign('infoa',$infoa);
		$this->display();
	}	
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('Cosmetology')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	public function del(){
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
		M('Keyword')->where(array('pid'=>$where['id'],'token'=>session('token'),'module'=>'Cosmetology'))->delete();
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){			
	$tj=M('Cosmetology')->where(array('token'=>SESSION('token')))->count();
	if($tj==0){
	$this->all_insert();
	}
	else
	{
	$this->error('操作失败',U(MODULE_NAME.'/index'));
	}
	}
	public function upsave(){
	$token=$this->_get('token');
$infoa=M('Cosmetology')->where(array('token'=>$this->_GET('token')))->find();
	$this->assign('infoa',$infoa);
	$this->all_save();
	}
}
?>
