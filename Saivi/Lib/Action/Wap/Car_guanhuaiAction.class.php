<?php
class Car_guanhuaiAction extends BaseAction{
    public function index(){
        $agent = $_SERVER['HTTP_USER_AGENT']; 
        if(!strpos($agent,"MicroMessenger")) {
          //  echo '此功能只能在微信浏览器中使用';exit;
        }
        $where['token']      = $this->_get('token'); 
          
        $set =  M('Car_guanhuai')->where($where)->select();
        $this->assign('set',$set);
        $this->display();
    }
	
	    public function content(){
     
        $where['token']      = $this->_get('token'); 
        $where['id']      = $this->_get('id');   
        $set =  M('Car_guanhuai')->where($where)->find();
        $this->assign('set',$set);
        $this->display();
    }
    
  
        
 

}
    
?>