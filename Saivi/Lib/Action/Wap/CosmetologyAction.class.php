<?php
class CosmetologyAction extends BaseAction{

public function _initialize(){
		parent::_initialize();
		
		$where['token']=$this->token;
		$kefu=M('Kefu')->where($where)->find();
		$this->assign('kefu',$kefu);

	}
	    public function index(){
       $agent = $_SERVER['HTTP_USER_AGENT']; 
        if(!strpos($agent,"MicroMessenger")&&!isset($_SESSION['token'])) {
        echo '此功能只能在微信浏览器中使用';exit;
        }
		$pt=M('Cosmetology')->where(array('token'=>$this->_GET('token')))->find();
		$this->assign('pt',$pt);
        $this->display();
	
    }
}
    
?>
