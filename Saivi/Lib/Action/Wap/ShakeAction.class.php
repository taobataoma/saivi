<?php
class ShakeAction extends BaseAction{
	public $shake_model;
	public function __construct(){
		parent::__construct();
		$this->token		= $this->_get('token');
		$this->assign('token',$this->token);
		$this->wecha_id	= $this->_get('wecha_id');
		if (!$this->wecha_id){
			$this->wecha_id='null';
		}
		$this->assign('wecha_id',$this->wecha_id);
		$this->shake_model=M('Shake');
	}
	public function index(){
		$info=array();
		$info['phone'] 		= $this->_get('phone');
		$thisShake=$this->shake_model->where(array('token'=>$this->token,'id'=>intval($_GET['id']),'isopen'=>1))->find();
		$thisShake['rule']=nl2br($thisShake['rule']);
		$thisShake['info']=nl2br($thisShake['info']);
		$this->assign('info',$thisShake);
		$shakeRt=M('Shake_rt')->where(array('shakeid'=>intval($_GET['id']),'wecha_id'=>$this->wecha_id))->find();
		if (!$shakeRt||!$shakeRt['phone']){
			exit('请先填写手机号');
		}
		$this->display();
	}
	public function shakeActivityStatus(){
		$thisShake=$this->shake_model->where(array('token'=>$this->token,'id'=>intval($_GET['id'])))->find();
		echo'{"isact":'.$thisShake['isact'].'}';exit;
	}

    public function refreshScreen(){
    	$where=array();
    	$where['token']=$this->_post('token');
    	$where['id']=intval($_POST['id']);
    	$thisShake=$this->shake_model->where($data)->find();
		if ($thisShake){
			$shakeRt=M('Shake_rt')->where(array('shakeid'=>$where['id'],'wecha_id'=>$this->_post('wecha_id')))->find();
			$data=array();
			$data['token'] 		= $this->_post('token');
			$data['wecha_id'] = $this->_post('wecha_id');
			$data['shakeid'] = $this->_post('id');
			$data['count']=intval($_POST['count']);
			if ($shakeRt){
				M('Shake_rt')->where(array('shakeid'=>$where['id'],'wecha_id'=>$this->_post('wecha_id')))->save($data);
			}else {
				M('Shake_rt')->add($data);
			}
		}
	}
}
?>