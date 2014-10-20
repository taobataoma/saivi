<?php

class BaomingAction extends WapAction {

	

	public function index(){

		$where['token'] = $this->_get('token');

		$where['id'] = intval($_GET['id']);

		$this->wecha_id = $this->_get('wecha_id');
		$this->token = $this->_get('token');
		$this->id = $this->_get('id');

		$company = M('Baoming')->where(array('token'=>$where['token']))->find();

		$info = M('BaomingList')->where($where)->find();

		$this->company = $company;

		$this->info = $info;

		



		$this->display();

	}

	public function lists(){

		$where['token'] = $this->_get('token');

		

		$this->wecha_id = $this->_get('wecha_id');

		$info1 = M('Baoming')->where(array('token'=>$where['token']))->find();
		

		$info = M('BaomingList')->where(array('token'=>$where['token']))->select ();

		
         $this->assign('info1',$info1);
		$this->info = $info;

		

		$this->display();

	}

	

	public function show(){

		$where['id'] = $this->_get('id');

		$where['token'] = $this->_get('token');

		$this->wecha_id = $this->_get('wecha_id');

		$info = M('BaomingList')->where($where)->find();

		$this->info = $info;

		$this->ewm = $this->chl($where['id'],$where['token'],$this->_get('wecha_id'));

		$this->display();

	}

	

	

	
	
	public function add(){

		
		$token = $this->_post('token');
		$wecha_id = $this->_post('wecha_id');
		$name = $this->_post('username');
		$phone = $this->_post('phone');
		$weixin = $this->_post('weixin');
		$beizhu = $this->_post('beizhu');
		$pid = $this->_post('pid');
		$info = M('baoming_order')->where(array('token' =>$token,'wecha_id'=>$wecha_id,'pid'=>$pid))->find();
		
		
		$data = array("token"=>$token,"wecha_id"=>$wecha_id,"name"=>$name,"phone"=>$phone,"weixin"=>$weixin,"beizhu"=>$beizhu,"pid"=>$pid);
		
		
		if(D("BaomingOrder")->add($data))
		{
			
		echo "<script>alert('报名成功');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";}
	}

}