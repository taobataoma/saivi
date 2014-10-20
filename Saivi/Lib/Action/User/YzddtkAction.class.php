<?php

class YzddtkAction extends UserAction{
	/*
	public function _initialize() {
		parent::_initialize();
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		if(!strpos($token_open['queryname'],'Diaoyan')){
            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}


		$this->token=session('token');
		//$this->assign('token',$this->token);
		//$this->assign('module','Yuyue');
		
	}
	*/
	//预约列表
	public function index(){
		

		$db=D('yzddtk');

		//$where['uid']=session('uid');

		$where['token']=session('token');

		$count=$db->where($where)->count();

		$page=new Page($count,25);

		$info=$db->where($where)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();

		$this->assign('page',$page->show());

		$this->assign('info',$info);

		$this->display();

	
	}
	
	public function add(){

		$this->display();

	}
	
	public function insert(){

		//$pat = "/<(\/?)(script|i?frame|style|html|body|title|font|strong|span|div|marquee|link|meta|\?|\%)([^>]*?)>/isU";

		//$_POST['info'] = preg_replace($pat,"",$_POST['info']);

		//$_POST['info']=strip_tags($this->_post('info'),'<a> <p> <br>');  

		//dump($_POST['info']);

		$this->all_insert();

	}
	
	public function edit(){

		$where['token']=session('token');

	    $where['id']=$this->_get('id','intval');

		$res=D('yzddtk')->where($where)->find();

		$this->assign('info',$res);


		$this->display();

	}
	public function upsave(){

		$this->all_save();

	}

	public function del(){

		$where['id']=$this->_get('id','intval');
        $where['token']=session('token');
		

		if(D(MODULE_NAME)->where($where)->delete()){

			

			$this->success('操作成功',U(MODULE_NAME.'/index'));

		}else{

			$this->error('操作失败',U(MODULE_NAME.'/index'));

		}

	}
	
	
}

?>