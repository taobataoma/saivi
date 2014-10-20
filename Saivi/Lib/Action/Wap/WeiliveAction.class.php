<?php
class WeiliveAction extends BaseAction{
	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$wecha_id	= $this->_get('wecha_id');

		$data=M('Member_wei_category');
		
		$list = $data->where(array('token'=>$token))->order('displayorder')->select();
		$info = M('Weilivereply_info')->where(array('token'=>$token))->find();
		$this->assign('list',$list);
		$this->assign('info',$info);
		$tmp = C("DEFAULT_THEME")."/".$vo['style'];
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_index.html');
	}
	
	

	public function search() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$map['token'] = array('eq',$token);
		
		if(isset($_POST['flag_q'])){
			$map['flag_q'] = array('eq',$this->_post('flag_q'));
		}
		if(isset($_POST['flag_x'])){
			$map['flag_x'] = array('eq',$this->_post('flag_x'));
		}
		if(isset($_POST['flag_z'])){
			$map['flag_z'] = array('eq',$this->_post('flag_z'));
		}

		$data=M('Member_business');
		$count      = $data->where($map)->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $data->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('crate_time desc')->select();
		$info = M('Weilivereply_info')->where(array('token'=>$token))->find();
		$this->assign('info',$info);
		$this->assign('page',$show);
		$this->assign('list',$list);

		$this->display();
	}


	public function info() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');

		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$this->assign('vo',$vo);
		$tmp = C("DEFAULT_THEME")."/".$vo['style'];
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_info.html');
	}

	public function company() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');

		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$this->assign('vo',$vo);
		$tmp = C("DEFAULT_THEME")."/".$vo['style'];
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_company.html');
	} 

	public function contact() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');

		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$this->assign('vo',$vo);
		$tmp = C("DEFAULT_THEME")."/".$vo['style'];
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_contact.html');
	}

	//档期
	public function schedule() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');

		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$this->assign('vo',$vo);
		$tmp = C("DEFAULT_THEME")."/".$vo['style'];
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_schedule.html');
	}
	

	public function favorable() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');

		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$style = $vo['style'];


		$data=M('Member_card_vip');
		$voList = $data->where(array('token'=>$token))->select();
		$this->assign('list',$voList);

		$tmp = C("DEFAULT_THEME")."/".$style;
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_favorable.html');
	} 



	public function coupon() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');

		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$style = $vo['style'];


		$data=M('Member_card_coupon');
		$voList = $data->where(array('token'=>$token))->select();
		$this->assign('list',$voList);
		

		$tmp = C("DEFAULT_THEME")."/".$style;
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_favorable.html');
	} 




	
	public function card() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');

		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$style = $vo['style'];


		$data=M('Member_card_set');
		$voList = $data->where(array('token'=>$token))->select();
		$this->assign('list',$voList);
		

		$tmp = C("DEFAULT_THEME")."/".$style;
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_card.html');
	} 

	//商家
	public function product() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');
		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$style = $vo['style'];


		$data=M('Member_business_product');
		$voList = $data->where(array('token'=>$token,'bid'=>$id))->select();
		$this->assign('list',$voList);
		$tmp = C("DEFAULT_THEME")."/".$style;
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_product.html');
	} 
	
	
	//案列
	public function wcase() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');
		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$style = $vo['style'];


		$data=M('Member_business_case');
		$map['token'] =  array('eq',$token);
		$map['bid'] =  array('eq',$id);		
		
		$voList = $data->where($map)->select();
		$this->assign('list',$voList);
		$tmp = C("DEFAULT_THEME")."/".$style;
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_case.html');
	} 	
	
	
	//优惠
	public function fav() { 
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		$token		= $this->_get('token');
		$id	= $this->_get('id');
		$data=M('Member_business');
		$vo = $data->where(array('token'=>$token,'id'=>$id))->find();
		$style = $vo['style'];


		$data=M('Member_business_fav');
		$map['token'] =  array('eq',$token);
		$map['bid'] =  array('eq',$id);		
		
		$voList = $data->where($map)->select();
		$this->assign('list',$voList);
		$tmp = C("DEFAULT_THEME")."/".$style;
		$this->display('./tpl/Wap/'.$tmp.'/Weilive_fav.html');
	} 		
	
	
	
}
	
?>