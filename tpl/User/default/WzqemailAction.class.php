<?php
class WzqemailAction extends UserAction{
	public $deliemail_config;
	public function _initialize() {
		parent::_initialize();
		$this->deliemail_config=M('deliemail');
		if (!$this->token){
			exit();
		}
	}
	public function index(){
		$config = $this->deliemail_config->where(array('token'=>$this->token))->find();
		if(IS_POST){
			$row['token']=$this->token;
			$row['type']=$this->_post('type');
			$row['smtpserver']=$this->_post('smtpserver');
			$row['port']=$this->_post('port');
			$row['name']=$this->_post('name');
			$row['password']=$this->_post('password');
			$row['receive']=$this->_post('receive');
			$row['shangcheng']=$this->_post('shangcheng');
			$row['yuyue']=$this->_post('yuyue');
			
			$row['zxyy']=$this->_post('zxyy');
			$row['dingcan']=$this->_post('dingcan');
			$row['car']=$this->_post('car');
			$row['yiliao']=$this->_post('yiliao');
			$row['jdbg']=$this->_post('jdbg');
$row['fangchan']=$this->_post('fangchan');
			$row['ktv']=$this->_post('ktv');
$row['zhaopin']=$this->_post('zhaopin');
$row['jianli']=$this->_post('jianli');
			$row['huisuo']=$this->_post('huisuo');
			$row['jiuba']=$this->_post('jiuba');
			if ($config){
				$where=array('token'=>$this->token);
				$this->deliemail_config->where($where)->save($row);
			}else {
				$this->deliemail_config->add($row);
			}
			$this->success('设置成功',U('Wzqemail/index',$where));
		}else{
			$this->assign('config',$config);
			$this->display();
		}
	}
}


?>