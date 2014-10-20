<?php

//wap

class FenleiAction extends WapAction{

	public $token;

	public $wecha_id;

	public $Yuyue_model;

	public $yuyue_order;

	public function __construct(){

		

		parent::__construct();

		$this->token=session('token');

		// $this->token = $this->_get('token');

		$this->assign('token',$this->token);

		$this->wecha_id	= $this->_get('wecha_id');

		if (!$this->wecha_id){

			$this->wecha_id='null';

		}
			$where['token']=$this->token;
		$kefu=M('Kefu')->where($where)->find();
		$this->assign('kefu',$kefu);

		$this->assign('wecha_id',$this->wecha_id);

		$this->Fenlei_model=M('Fenlei');
     



	}



	

	//预约列表

	//预约列表
	public function index(){
		$pid = $this->_get('id');
		$wecha_id = $this->_get('wecha_id');
		$info = M('Fenlei')->where(array('token'=> $this->_get('token')))->select();
		$flash=M('fenlei_flash')->where(array('token'=> $this->_get('token')))->find();

		for($i=1;$i<5;$i++){

			if(!empty($flash['picurl'.$i])){

				$flash['picurl'][]=$flash['picurl'.$i];

				unset($flash['picurl'.$i]);

			}

		}
		//print_r($info);die;
		//print_r($str);die;
		$copyright=M('fenlei_reply')->where(array('token'=> $this->_get('token')))->find();
		$this->assign('copyright',$copyright);
		$this->assign('info', $info);
		$this->assign('flash', $flash);
		$this->display();
	}
	
	public function info(){
		$title = M('Fenlei')->where(array('token'=> $this->_get('token'),'id'=>$this->_get('id')))->find();
		$pid = $this->_get('id');
		$where = array('token'=> $this->_get('token'),'pid'=>$pid);
		
		$cast = array(
			'token'=> $this->_get('token'),
			'wecha_id'=> $this->_get('wecha_id')
		);
		$info = M('fenlei_setcin')->where($where)->select();
		
        $copyright=M('fenlei_reply')->where(array('token'=> $this->_get('token')))->find();
		$this->assign('copyright',$copyright);
		$this->assign('info', $info);
		$this->assign('title', $title);

		$this->display();

	}
	public function xiangqing(){
		$title = M('Fenlei')->where(array('token'=> $this->_get('token'),'id'=>$this->_get('pid')))->find();
		$pid = $this->_get('pid');
		$where = array('token'=> $this->_get('token'),'pid'=>$pid,'id'=>$this->_get('id'));
		
		$cast = array(
			'token'=> $this->_get('token'),
			'wecha_id'=> $this->_get('wecha_id')
		);
		$info = M('fenlei_setcin')->where($where)->find();
		
        $copyright=M('fenlei_reply')->where(array('token'=> $this->_get('token')))->find();
		$flash=M('fenlei_setcin')->where(array('token'=> $this->_get('token'),'pid'=>$pid,'id'=>$this->_get('id')))->find();
		//dump($flash);exit;

		for($i=1;$i<4;$i++){

			if(!empty($flash['picurl'.$i])){

				$flash['picurl'][]=$flash['picurl'.$i];

				unset($flash['picurl'.$i]);

			}

		}
		$this->assign('copyright',$copyright);
		$this->assign('info', $info);
		$this->assign('title', $title);
		$this->assign('flash', $flash);

		$this->display();
	}

	

	   }


?>