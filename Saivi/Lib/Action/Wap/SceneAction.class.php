<?php

//2014.9.3 微乾隆所写 QQ：283497031  请保留作者信息

class SceneAction extends WapAction{

	public $token;

	public $wecha_id;

	

	public function __construct(){

		parent::__construct();

		$this->token=session('token');

		// $this->token = $this->_get('token');

		$this->assign('token',$this->token);

		$this->wecha_id	= $this->_get('wecha_id');

		if (!$this->wecha_id){

			$this->wecha_id='null';

		}
			
		$this->assign('wecha_id',$this->wecha_id);

		$this->scene_model=M('Scene');
		$this->scene_addtp=M('scene_addtp');


	}


	public function index(){
		$token=$this->_get('token');
		$id=$this->_get('id');
		
		
		$where = array('token'=> $this->_get('token'),'id'=> $this->_get('id'));
		
		$wh = array('token'=> $this->_get('token'),'pid'=> $this->_get('id'));
		
		$set = $this->scene_model->where($where)->find();		
		
		$info =$this->scene_addtp->where($wh)->order('sort DESC')->select();
		
		$this->scene_model->where($where)->setInc('click');
		
		$this->assign('info', $info);
		$this->assign('set', $set);
		$this->display();
	}
	


}





?>