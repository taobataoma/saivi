<?php
class ZnkefuAction extends UserAction{
	public $token;
	public $uid;
	public function _initialize() {
		parent::_initialize();
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		if(!strpos($token_open['queryname'],'Znkefu')){
            	$this->error('您还未开启该模块的使用权,请到功能模块中添加',U('Connme/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}
		$this->token=session('token');
		$this->uid=session('uid');
	}
	
	public function index(){
		$customer_service = M('customer_service');
		$kf_name=$customer_service->where(array('token'=>$this->token))->field('name')->find();
		$this->assign('kf_name',$kf_name);
		$this->display();	
	}
	
	public function mytk(){
		$my_answer= M('my_answer');
		$count=$my_answer->where(array('token'=>$this->token,'uid'=>$this->uid))->count();
		$page=new Page($count,10);
		$res=$my_answer->where(array('token'=>$this->token,'uid'=>$this->uid))->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('res',$res);
		$this->assign('page',$page->show());
		$this->display();	
	}
	
	public function rename(){
		if(IS_POST){
			$_POST['token']=$this->token;
			$_POST['uid']=$this->uid;
			$where=array('token'=>$this->token,'uid'=>$this->uid);
			$customer_service = M('customer_service');
			$kf_name=$customer_service->where($where)->find();
			if(empty($kf_name)){
				$customer_service->add($_POST);
			}else{
				$customer_service->where($where)->save($_POST);
			}
		}
	}
	
	public function guide(){
		if($_POST['action']='set_ans'){
			$que = trim($_POST['question']);
			$answer = $this->searchQuestion($this->token, $que);
			if(empty($answer)){
				echo '我不知道你的意思，教教我吧。';
			}else{
				echo $answer;
			}
		}		
	}
	
	public function save_ans(){		
		if($_POST['action']='save_ans'){
			$my_answer= M('my_answer');
			$ques = trim($_POST['ques']);
			$ans = trim($_POST['ans']);
			$my=$my_answer->where(array('token'=>$this->token,'question'=>$ques))->find();
			if(!empty($my)){
				echo 0;
			}else{
				$_POST['uid']=$this->uid;
				$_POST['token']=$this->token;
				$_POST['question']=$ques;
				$_POST['answer']=$ans;
				$my1=$my_answer->add($_POST);
				if(!empty($my1)){
					echo 1;
				}else{
					echo 2;
				}
			}
		}
	}
	
	public function getAns(){		
		if($_GET['action']='getAns'){
			$my_answer= M('my_answer');
			$ques = trim($_GET['ques']);
			$ans = trim($_GET['ans']);
			$my=$my_answer->where(array('token'=>$this->token,'question'=>$ques))->find();
			if(!empty($my)){
				$rs   = array('success'=>true,'id'=>$my['id'],'answer'=>$my['answer']);
				$json = json_encode($rs);
				echo $json;
			}else{
				$rs   = array('success'=>false);
				$json = json_encode($rs);
				echo $json;
			}
		}
	}
	
	public function del_ans(){		
		if($_POST['action']='del_ans'){
			$my_answer= M('my_answer');
			$id = trim($_POST['id']);
			$my=$my_answer->where(array('id'=>$id))->delete();
			if(!empty($my)){
				echo 1;
			}
		}
	}
	
	function searchQuestion($token,$que){
		$my_ans = M('my_answer');
		$ans=$my_ans->where(array('token'=>$token,'question'=>$que))->find();
		$answer = $ans['answer'];
		
		if(!empty($answer)){
			 return $answer .= '(来自我的库)';
		}else{
			$where['question'] = array('like','%'.$que.'%');
			$where['token'] = $token;
			$ans=$my_ans->where($where)->find();
			if(!empty($ans)){
				return $answer = ($ans['answer']. "(来自我的库)");
			}else{
				return false;
			}
		}
	}
	
}


?>