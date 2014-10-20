<?php
class HardwareAction extends UserAction{
	public function _initialize() {
		parent::_initialize();

	}
	public function wifi(){
		$this->assign('tab','wifi');
		$this->display();
	}
	public function photoprint(){
		if (IS_POST){
			D('Wxuser')->where(array('token'=>$this->token))->save(array('freephotocount'=>intval($_POST['freephotocount']),'openphotoprint'=>intval($_POST['openphotoprint'])));
			S('wxuser_'.$this->token,NULL);
			$this->success('设置成功');
		}else {
			$this->wxuser=D('Wxuser')->where(array('token'=>$this->token))->find();
			$this->assign('info',$this->wxuser);
			$this->assign('tab','photoprint');
			$this->display();
		}
	}
	public function orderprint(){
		$this->assign('tab','orderprint');
		$this->display();
	}
	public function test(){
		$this->wxuser=D('Wxuser')->where(array('token'=>$this->token))->find();
		$photoPrint=new photoPrint($this->wxuser,'oLA6VjlHpnWSNuak_YchHaCUCMwg');
		echo $photoPrint->uploadPic(urlencode('https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxgetmsgimg?type=slave&MsgID=1041563100'));
	}
	
}


?>