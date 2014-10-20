<?php

//web

class YingyongAction extends UserAction{

	public $token;

	public $yingyong_model;


	public function _initialize() {

		parent::_initialize();

		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();

		if(!strpos($token_open['queryname'],'Weiyingyong')){

            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));

		}



		$this->Fangchan_model=M('yingyong');


		$this->token=session('token');

		$this->assign('token',$this->token);

	


	}
	 public function reply(){
	 	$where['token'] = session('token');
		$Cdata = M('yingyong_reply');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['copyright'] = strip_tags($_POST['copyright']);
			$data['title'] = strip_tags($_POST['title']);
			$data['tp'] = strip_tags($_POST['tp']);

			$data['info'] = strip_tags($_POST['info']);
			
			//$res = M('Vcard')->where($where)->find();
			if($info){
				$result = M('yingyong_reply')->where($where)->save($data);
				if($result){
					$this->success('回复信息更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('yingyong_reply')->add($data);
				if($insert > 0){
					$this->success('回复信息添加成功!');
				}else{
					$this->error('回复信息添加失败!');
				}
			}
		}else{
			$this->display();
		}
	}
	 public function index(){
	 	$where['token'] = session('token');
		$Cdata = M('yingyong');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['videourl'] = strip_tags($_POST['videourl']);
			$data['p1'] = strip_tags($_POST['p1']);
			$data['p2'] = strip_tags($_POST['p2']);
			$data['p3'] = strip_tags($_POST['p3']);
			$data['p4'] = strip_tags($_POST['p4']);
			$data['p5'] = strip_tags($_POST['p5']);
			$data['p6'] = strip_tags($_POST['p6']);
			$data['p7'] = strip_tags($_POST['p7']);
			$data['p8'] = strip_tags($_POST['p8']);
			$data['p9'] = strip_tags($_POST['p9']);
			$data['p10'] = strip_tags($_POST['p10']);
			$data['p11'] = strip_tags($_POST['p11']);
			$data['p12'] = strip_tags($_POST['p12']);
			$data['p13'] = strip_tags($_POST['p13']);
			$data['p14'] = strip_tags($_POST['p14']);
			$data['p15'] = strip_tags($_POST['p15']);
			$data['p16'] = strip_tags($_POST['p16']);
			$data['p17'] = strip_tags($_POST['p17']);
			$data['p18'] = strip_tags($_POST['p18']);
			$data['p19'] = strip_tags($_POST['p19']);
			$data['p20'] = strip_tags($_POST['p20']);
			$data['p21'] = strip_tags($_POST['p21']);
			$data['p22'] = strip_tags($_POST['p22']);
			$data['p23'] = strip_tags($_POST['p23']);
			$data['p24'] = strip_tags($_POST['p24']);
			$data['p25'] = strip_tags($_POST['p25']);
			$data['p26'] = strip_tags($_POST['p26']);
			$data['p27'] = strip_tags($_POST['p27']);
			$data['p28'] = strip_tags($_POST['p28']);
			$data['p29'] = strip_tags($_POST['p29']);
			$data['tip'] = strip_tags($_POST['tip']);
			$data['tip1'] = strip_tags($_POST['tip1']);
			$data['tip1url'] = strip_tags($_POST['tip1url']);
			$data['tip2'] = strip_tags($_POST['tip2']);
			$data['tipurl'] = strip_tags($_POST['tipurl']);
			$data['type'] = strip_tags($_POST['type']);

			$data['musicurl'] = strip_tags($_POST['musicurl']);
			
			//$res = M('Vcard')->where($where)->find();
			if($info){
				$result = M('yingyong')->where($where)->save($data);
				if($result){
					$this->success('微应用信息更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('yingyong')->add($data);
				if($insert > 0){
					$this->success('微应用设置成功!');
				}else{
					$this->error('微应用设置失败!');
				}
			}
		}else{
			$this->display();
		}
	}

	


}





?>