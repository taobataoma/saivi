<?php

//wap

class FanyanAction extends WapAction{

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

		$this->Fanyan_model=M('Fanyan');
     



	}



	

	//预约列表

	//预约列表
	public function index(){
		$pid = $this->_get('id');
		$wecha_id = $this->_get('wecha_id');
		$info = M('Fanyan')->where(array('token'=> $this->_get('token')))->select();
		$count=M('Fanyan')->where(array('token'=> $this->_get('token')))->count();
		
		$copyright=M('Fanyan_reply')->where(array('token'=> $this->_get('token')))->find();
		$copyright[url]=str_replace('{siteUrl}','',$copyright[url]);
		$copyright[url]=str_replace('{wechat_id}',$wecha_id,$copyright[url]);
		$this->assign('copyright',$copyright);$this->assign('copyright',$copyright);
		$this->assign('count',$count);
		$this->assign('info', $info);
		
		$this->display();
	}
	
	public function info(){
		$title = M('Fanyan')->where(array('token'=> $this->_get('token'),'id'=>$this->_get('id')))->find();
		$pid = $this->_get('id');
		$where = array('token'=> $this->_get('token'),'pid'=>$pid);
		
		$cast = array(
			'token'=> $this->_get('token'),
			'wecha_id'=> $this->_get('wecha_id')
		);
		$info = M('Fanyan_setcin')->where($where)->select();
		
        $copyright=M('Fanyan_reply')->where(array('token'=> $this->_get('token')))->find();
		$this->assign('copyright',$copyright);
		$this->assign('info', $info);
		$this->assign('title', $title);

		$this->display();

	}
	

	

	   }


?>