<?php
class Medical_setup_controlAction extends UserAction{
	public function add(){
		$this->display();
	}
	public function index(){
	
	$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();
		if(!strpos($token_open['queryname'],'Medical')){
            $this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Medical/index',array('token'=>$_SESSION['token'],'id'=>session('wxid'))));}
	
        $token= $this->_get('token'); 	
		$id=$this->_get('id','intval');
		$info=M('Medical_setup_control')->where(array('token'=>$token))->find($id);
		$infoa=M('Medical_setup_control')->where(array('token'=>$this->_GET('token')))->find();	
		$tj=M('Medical_setup_control')->where(array('token'=>$this->_GET('token')))->count();
		$this->assign('tj',$tj);	
		$this->assign('info',$info);
		$this->assign('infoa',$infoa);
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
	$token=$this->_get('token');
	$id=$this->_get('id');
	$tj=M('Medical_setup_control')->where(array('token'=>SESSION('token')))->count();
		$this->assign('tj',$tj);	
	if($tj==0){			
$column=D('Medical_setup_control');
if($column->create()){
 if($column->add()){

  $this->success('添加成功',U('Medical_setup_control/index',array('token'=>$token)));
}else{

	$this->error('添加失败',U('Medical_setup_control/index',array('token'=>$token,'token'=>$id)));
}
}
	}else{	
	$this->error('添加失败',U('Medical_setup_control/index',array('token'=>SESSION('token'))));
	}
	}
	public function upsave(){
 	
$column=D('Medical_setup_control');
$id = intval($_POST['id']);
if($column->create()){
 if($column->where("id=%d", $id)->save()){
 $this->success('更新成功！');
}else{
    $this->error('更新失败！');
}
}
	
	}
	
	
}
?>