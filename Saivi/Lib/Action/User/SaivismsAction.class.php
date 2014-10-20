<?php
class SaivismsAction extends UserAction{
	public $delisms_config;
	public function _initialize() {
		parent::_initialize();
		$this->delisms_config=M('delisms');
		if (!$this->token){
			exit();
		}
	}
	public function index(){
		$config = $this->delisms_config->where(array('token'=>$this->token))->find();
		if(IS_POST){
			$row['phone']=$this->_post('phone');
			$row['password']=$this->_post('password');
			$row['name']=$this->_post('name');
			$row['token']=$this->_post('token');
			$row['shangcheng']=$this->_post('shangcheng');
			$row['yuyue']=$this->_post('yuyue');
			$row['baom']=$this->_post('baom');
			$row['zxyy']=$this->_post('zxyy');
			$row['dingcan']=$this->_post('dingcan');
			$row['car']=$this->_post('car');
			$row['yiliao']=$this->_post('yiliao');
			$row['jdbg']=$this->_post('jdbg');
			$row['ktv']=$this->_post('ktv');
			$row['huisuo']=$this->_post('huisuo');
			$row['jiuba']=$this->_post('jiuba');
			
			foreach($row as $k=>$v){
				if($v==null){
					$row[$k] = '0';
				}
			}
			if ($config){
				$where=array('token'=>$this->token);
				$this->delisms_config->where($where)->save($row);
			}else {
				$this->delisms_config->add($row);
			}
			$this->success('设置成功',U('Saivisms/index',$where));
		}else{
			$this->assign('config',$config);
			$this->display();
		}
	}
}


?>