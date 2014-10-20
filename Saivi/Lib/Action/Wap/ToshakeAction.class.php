<?php
class ToshakeAction extends BaseAction{
	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			//echo '请使用微信操作';
			//exit;
		}
		
		$data=array();
		$data['phone'] 		= $this->_get('phone');
		$data['token'] 		= $this->_get('token');
		$data['wecha_id'] = $this->_get('wecha_id');
		$ifact=M('Shake')->where(array('token'=>$data['token'],'isopen'=>array('neq',2)))->find();
		if($ifact==false){echo '<script>alert ("商家目前没有进行中的摇一摇活动")</script>'; return;}
		$exst=M('Toshake')->where(array('wecha_id'=>$data['wecha_id']))->select();
		if($exst==false){M('Toshake')->add($data);}
		$this->assign('info',$data);
		$this->assign('ctime',$ifact['clienttime']);
		$this->assign('endshake',$ifact['endshake']);
		$this->assign('music',$ifact['wapsound']);
		$this->display();
		
	}

    public function repoint(){
		
		$data=array();
		$data['phone'] 		= $this->_post('phone');
		$data['token'] 		= $this->_post('token');
		$data['wecha_id'] = $this->_post('wecha_id');
		$data['point'] = $this->_post('point');
		$exst=M('Shake')->where(array('token'=>$data['token'],'isact'=>'1','isopen'=>array('neq',2)))->select();
		if($exst==false){echo '1'; return;}
		$where['wecha_id'] = $data['wecha_id'];
		$where['token'] = $data['token'];
		//看情况加入用户不刷新页面情况下加入游戏
		//$a=M('Toshake')->where($where)->find();
		//if($a!==false)
		$act=M('Toshake')->where($where)->save($data);
		//else
		//$act=M('Toshake')->where($where)->add($data);
		//echo json_encode($data);
	}
	
	
}
?>