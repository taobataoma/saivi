<?php
class WqlcmswifiAction extends UserAction{
	
	public function _initialize() {
		parent::_initialize();
			if(session('gid')==1){

			$this->error('vip0无法使用OEMwifi');

		}
		$this->wqlcmswifi_db=M('wqlcmswifi');
	
		if (!$this->token){
			exit();
		}
	}
	//微信wifi
	public function index(){
		$p=M('saiviwifi.member ','wifi_')->where(array('token'=>$this->token))->find();
		$m=M('saiviwifi.routing ','wifi_')->where(array('uid'=>$p['id']))->order('create_time desc')->limit(1)->find();
		
		$wifi = $this->wqlcmswifi_db->where(array('token'=>$this->token))->find();	
			
		
		if(IS_POST){	
	
			$row['token']=session('token');
			$row['mac']=$this->_post('mac');
			$row['title']=$this->_post('title');
			$row['picurl']=$this->_post('picurl');
			$row['keyword']=$this->_post('keyword');
			
			$a=$this->_post('mac');
			if(empty($a)){
				$this->error('MAC必须填写');
				}
			if ($wifi){
				$where=array('token'=>$this->token);	
				$this->wqlcmswifi_db->where($where)->save($row);
				M('Keyword')->where(array('token'=>session('token'),'module'=>'Wqlcmswifi'))->save(array('keyword'=>$_POST['keyword']));
			}else {
				$id=$this->wqlcmswifi_db->add($row);
				
				
						$data1['pid']=$id;
						$data1['module']='Wqlcmswifi';
						$data1['token']=session('token');
						$data1['keyword']=$_POST['keyword'];
						M('Keyword')->add($data1);
						
					}
			
			$this->success('设置成功');
		}else{
			$this->assign('wifi',$wifi);
			$this->assign('m',$m);
			$this->display();
		}
	}
	
	//删除公众号
public function del(){
	$mac=$_GET['mac'];
	$wifi = $this->wqlcmswifi_db->where(array('mac'=>$mac))->find();
	if ($wifi==true){
			$del=$this->wqlcmswifi_db->where(array('token'=>$this->token,'mac'=>$mac))->delete();
			if($del==false){
				$this->error('服务器繁忙');
			}else{
				$this->success('删除成功');
			}
	}else{
		$this->error('操作失败');
		}
}

	
}
?>