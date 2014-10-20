<?php
class SjmAction extends BaseAction{
    public function index(){
       
        
        $token      = $this->_get('token'); 
		$sjmreply_info_db=M('sjmreply_info');
		$info=$sjmreply_info_db->where(array('token'=>$this->token))->find();
		$info[url]=str_replace('{siteUrl}','',$info[url]);
$info[shareurl]=str_replace('{siteUrl}','',$info[shareurl]);
		$this->assign('info',$info);
        
		

      //  $this->assign('isAndroid',isAndroid());
        $this->display();
    }
    
}
    
?>