<?php

class Car_replyAction extends UserAction{
	public function index(){
		
		
		$token_open=M('token_open')->field('queryname')->where(array('token'=>$_SESSION['token']))->find();

		if(!strpos($token_open['queryname'],'Car')){
            $this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>$_SESSION['token'],'id'=>session('wxid'))));}
		
			$data=M('Car_reply')->where(array('token'=>$_SESSION['token']))->find();
		if(IS_POST){
			//dump($_POST);EXIT;
			$_POST['token']=$_SESSION['token'];			
			if($data==false){				
				$this->all_insert('Car_reply','/index');
			}else{
				$_POST['id']=$data['id'];
				$this->all_save('Car_reply','/index');
			}
		}else{
			$this->assign('info',$data);

		     $this->display();
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