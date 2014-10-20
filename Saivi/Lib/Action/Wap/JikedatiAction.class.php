<?php

//wap

class JikedatiAction extends WapAction{

	public $token;

	public $wecha_id;


	public function __construct(){

		

		parent::__construct();
		if($this->_get('wecha_id')){
			$cover = 0;
		}else{
			$cover = 1;
		}
		$this->assign('cover',$cover);

		$this->token=session('token');

		// $this->token = $this->_get('token');

		$this->assign('token',$this->token);

		$this->wecha_id	= $this->_get('wecha_id');

		if (!$this->wecha_id){

			$this->wecha_id='null';

		}
			$where['token']=$this->token;

		$this->assign('wecha_id',$this->wecha_id);
     



	}



	

	//预约列表

	//预约列表
	public function index(){
		$pid = $this->_get('id');
		$wecha_id = $this->_get('wecha_id');
		$info = M('Jikedati')->where(array('token'=> $this->_get('token')))->select();
		$flash=M('Jikedati_flash')->where(array('token'=> $this->_get('token')))->find();
        $count      = M('Jikedati')->where(array('token'=> $this->_get('token')))->count();
		for($i=1;$i<5;$i++){

			if(!empty($flash['picurl'.$i])){

				$flash['picurl'][]=$flash['picurl'.$i];

				unset($flash['picurl'.$i]);

			}

		}

		// dump($info);
		$copyright=M('Jikedati_reply')->where(array('token'=> $this->_get('token')))->find();
		$this->assign('copyright',$copyright);
		$this->assign('info', $info);
		$this->assign('flash', $flash);
		$this->assign('count', $count);
		$this->display();
	}
	
	public function info(){
		$title = M('Jikedati')->where(array('token'=> $this->_get('token'),'id'=>$this->_get('id')))->find();
		$pid = $this->_get('id');
		$where = array('token'=> $this->_get('token'),'pid'=>$pid);
		
		$cast = array(
			'token'=> $this->_get('token'),
			'wecha_id'=> $this->_get('wecha_id')
		);
		$info = M('Jikedati_setcin')->where($where)->select();
		
        $copyright=M('Jikedati_reply')->where(array('token'=> $this->_get('token')))->find();
		$this->assign('copyright',$copyright);
		$this->assign('info', $info);
		$this->assign('title', $title);

		$this->display();

	}

	public function wrong(){
		
		$id = htmlspecialchars($this->_post('id'));
		$arr_id = explode('_', $id);
		$wrong = array();
		$_SESSION['id'][$arr_id[0]] = $arr_id[1];
		$this->ajaxReturn($_SESSION['id'],'','','json');
	}

	public function learning(){
		if($this->checkReg()){
			foreach ($_SESSION['id'] as $key => $value) {
				$where .= ' id='.$key.' or';
			}
			$where = substr(trim($where), 0,strlen($where)-3);
			$jikedati = M('Jikedati');
			$data = $jikedati->where($where)->select();
			foreach ($data as $key => $value) {
				$data[$key]['wrong'] = $_SESSION['id'][$data[$key]['id']];
			}
			// dump($data);
			$this->assign('data',$data);

			$this->display();			
		}else{
			$this->display('reg',array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id')));
		}

	}

	public function show(){
			if($this->checkReg()){

			
				$wxuser = M('Wxuser');
				$jikedati_record = M('Jikedati_record');
				
				$userdata = $this->checkReg();

				
				$data['userinfo_id'] = $userdata['id'];
				$data['bestscore'] = $_GET['score'];
				$data['time'] = date('Y-m-d H:i:s');
				
				// 对userinfo_id进行唯一性检测
				$isViewd = $jikedati_record->where(array('userinfo_id'=>$userdata['id']))->find();
				if($isViewd){
					//数据更新
					if($isViewd['bestscore'] < $data['bestscore']){
						
						$jikedati_record->where(array('id'=>$isViewd['id']))->save($data);					
					}

				}else{
					//添加新数据
					
					$jikedati_record->add($data);
				}
			

			//关联查询
			import('./Saivi/Lib/ORG/Page.class.php');
			$count = $jikedati_record->count();
			$page = new Page($count,30);
			$page->setConfig('theme', '<li><a>%totalRow% %header%</a></li> %upPage%  %linkPage% %downPage% ');
			$show = $page->show();
			$list = $jikedati_record->join('tp_userinfo u on tp_jikedati_record.userinfo_id=u.id')->field('tp_jikedati_record.*,u.portrait,u.wechaname')->order('tp_jikedati_record.bestscore desc')->limit($page->firstRow.','.$page->listRows)->select();
			$this->assign('list',$list);
			$this->assign('page',$show);
			
			$this->display();
		}else{
			$this->display('reg',array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id'),'score'=>$this->_get('score')));
		}
	}

	protected function checkReg(){
		$userinfo = M('Userinfo');
		$where['token'] = htmlspecialchars($_GET['token']);
		$where['wecha_id'] = htmlspecialchars($_GET['wecha_id']);
		$userdata = $userinfo->where($where)->find();
		
		if($userdata){
			return $userdata;
		}else{
			return false;
		}
	}

	public function reg(){
		$this->checkAgent();
		if($_POST){
			$userinfo = M('Userinfo');
			$data['wechaname'] = $this->_post('wechaname');
			$data['portrait'] = $this->_post('portrait');
			$data['tel'] = $this->_post('tel');
			$data['token'] = $this->_get('token');
			$data['wecha_id'] = $this->_get('wecha_id');
			if($userinfo->add($data)){
				echo 1;exit;
			}
		}
		$this->display();
	}	

	   }


?>