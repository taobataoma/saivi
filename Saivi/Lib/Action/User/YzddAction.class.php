<?php


class YzddAction extends UserAction{

	public function _initialize() {
		parent::_initialize();
		$function=M('Function')->where(array('funname'=>'Yzdd'))->find();
		$this->canUseFunction('Yzdd');
	}
	public function index(){
	
		$user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		$this->assign('group',$group);
		$this->assign('activitynum',$user['activitynum']);
		
		
		$list=M('yzdd')->where(array('token'=>session('token')))->select();
		//dump($list);
		$this->assign('count',M('yzdd')->where(array('token'=>session('token')))->count());
		$this->assign('list',$list);
		
		$this->display();	
	}
	
	public function sn(){
		if(session('gid')==1){
			$this->error('vip0无法使用抽奖活动,请充值后再使用',U('Home/Index/price'));
		}
		$id=$this->_get('id');
		$data=M('Lottery')->where(array('token'=>session('token'),'id'=>$id,'type'=>4))->find();
		$record=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->select();
		$recordcount=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->count();
		$datacount=$data['fistnums']+$data['secondnums']+$data['thirdnums'];
		$this->assign('datacount',$datacount);//奖品数量
		$this->assign('recordcount',$recordcount);//中讲数量
		$this->assign('record',$record);
		//
		$sendCount=M('Lottery_record')->where('lid='.$id.' and sendstutas=1 and sn!=""')->count();
		$this->assign('sendCount',$sendCount);
		$this->display();
	
	
	}
	public function add(){
		if(session('gid')==1){
			$this->error('vip0无法使用抽奖活动,请充值后再使用',U('Home/Index/price'));
		}
		if(IS_POST){		
			$data=D('yzdd');
			$_POST['kssj']=strtotime($_POST['kssj']);
			$_POST['jssj']=strtotime($_POST['jssj']);
			$_POST['token']=session('token');		
			if($data->create()!=false){				
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='Yzdd';
					$data1['token']=session('token');
					$data1['keyword']=$_POST['gjz'];
					M('Keyword')->add($data1);
					$user=M('Users')->where(array('id'=>session('uid')))->setInc('activitynum');
					$this->success('活动创建成功',U('Yzdd/index'));
				}else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error($data->getError());
			}
			
			
		}else{
			$this->display();
		}
	}
	
	
	public function setinc(){
		if(session('gid')==1){
			$this->error('vip0无法开启活动,请充值后再使用',U('Home/Index/price'));
		}
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Yzdd')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		
		if($user['activitynum']>=$group['activitynum']){
			$this->error('您的免费活动创建数已经全部使用完,请充值后再使用',U('Home/Index/price'));
		}
		$data=M('Yzdd')->where($where)->setInc('status');
		if($data!=false){
			$this->success('恭喜你,活动已经开始');
		}else{
			$this->error('服务器繁忙,请稍候再试');
		}

	}
	public function setdes(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Yzdd')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$data=M('Yzdd')->where($where)->setDec('status');
		if($data!=false){
			$this->success('活动已经结束');
		}else{
			$this->error('服务器繁忙,请稍候再试');
		}
	
	}
	public function edit(){
		if(IS_POST){
			$data=D('Yzdd');
			$_POST['id']=$this->_get('id');
			$_POST['token']=session('token');
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);
			$_POST['kssj']=strtotime($_POST['kssj']);
			$_POST['jssj']=strtotime($_POST['jssj']);			
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){				
				if($id=$data->where($where)->save($_POST)){
					$data1['pid']=$_POST['id'];
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
			
		}else{
			$id=$this->_get('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Yzdd');
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$lottery=$data->where($where)->find();		
			$this->assign('vo',$lottery);
			//dump($lottery);
			$this->display('add');
		}
	
	}
	public function del(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data=M('Yzdd');
		$check=$data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back=$data->where($where)->delete();
		if($back==true){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Yzdd'))->delete();
			$this->success('删除成功');
		}else{
			$this->error('操作失败');
		}
	
	
	}
	
	public function sendprize(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = time();
		$data['sendstutas'] = 1;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success('成功发奖');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function sendnull(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = '';
		$data['sendstutas'] = 0;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success('已经取消');
		}else{
			$this->error('操作失败');
		}
	}
}

?>