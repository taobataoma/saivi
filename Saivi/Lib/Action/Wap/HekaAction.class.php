<?php
class HekaAction extends BaseAction{
	public function index(){
		$token = $this->_get('token');
		$id = $this->_get('id');
		$heka = M('Heka')->where(array('token'=>$token))->find();
		if( empty( $heka['title'] ) ) exit('非法TOKEN或者您还未开通');
		$hekalist = M('HekaList')->where(array('token'=>$token,'id'=>$id))->find();
		
		$company = M('company')->where(array('token'=>$token))->find();
		$froms = (isset($_GET['froms']) && !empty($_GET['froms'])) ? strip_tags($_GET['froms']) : ( empty($company['shortname']) ? '小微' : $company['shortname']);
		$to = (isset($_GET['to']) && !empty( $_GET['to'] )) ? urldecode($_GET['to']) : everyone;
		$content = (isset($_GET['content']) && !empty($_GET['content'])) ? urldecode($_GET['content']) : ( !empty($heka['info']) ?$heka['info'] : "在这个特别的日子里送上我最诚挚的祝福。祝您一生幸福!") ;
		$this->hekalist = $hekalist;
		$this->heka = $heka;
		$this->froms = $froms;
		$this->to = $to;
		$this->id = $id;
		$this->content = $content;
		$this->token = $token;
		$this->display();
	}
	
	//贺卡列表
	public function hklist(){
		$token = $this->_get('token');
		if( empty( $token ) ) exit('参数丢失!');
		//获取所有贺卡列表
		$count = M('HekaList')->where(array('token'=>$token))->count();
		
		$page = new Page($count,14);
		
		$hekalist = M('HekaList')->where(array('token'=>$token))->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('token',$token);
		$this->assign('hekalist',$hekalist);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	//处理用户祝福
	public function add(){
		$froms = strip_tags($_POST['froms']);
		$to = strip_tags($_POST['to']);
		$content = strip_tags($_POST['content']);
		$token = strip_tags($_POST['token']);
		$id = intval($_POST['id']);
		//$from = base64_encode(urlencode($from));
		$to = urlencode($to);
		$content = urlencode($content);
		$url = U('Wap/Heka/index',array('froms'=>$froms,'to'=>$to,'content'=>$content,'token'=>$token,'id'=>$id));
		echo $url;
		exit;
	}
	
	
}