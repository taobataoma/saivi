<?php
class CouponAction extends LotteryBaseAction {
	public function _initialize() {
		parent::_initialize();
		$function=M('Function')->where(array('funname'=>'choujiang'))->find();
		if (intval($this->user['gid'])<intval($function['gid'])){
			$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>$this->token)));
		}
	}
	public function index(){
		parent::index(3);
		$this->display();
	}
	public function sn(){
		parent::sn(3);
		$this->display('Lottery:sn');
	}
	public function add(){
		parent::add(3);
	}
	public function edit(){
		parent::edit(3);
	}
}


?>