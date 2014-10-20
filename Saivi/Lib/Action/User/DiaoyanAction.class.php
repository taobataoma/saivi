<?php

class DiaoyanAction extends UserAction{
	public $token;
	public $diaoyan_model;
	public $diaoyan_timu;
	
	public function _initialize() {
		parent::_initialize();
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		if(!strpos($token_open['queryname'],'Diaoyan')){
            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}

		$this->diaoyan_model=M('diaoyan');
		$this->diaoyan_timu=M('diaoyan_timu');

		$this->token=session('token');
		$this->assign('token',$this->token);
		$this->assign('module','Yuyue');
		
		// echo $this->token; 
		// dump($_SESSION);die;
	}
	
	//预约列表
	public function index(){
	
		$where = array('token'=> $this->token);
		
		//分页
		$count      = $this->diaoyan_model->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		
		$data = $this->diaoyan_model->where($where)->select();
		
		
		$this->assign('page',$show);
		$this->assign('data',$data);
		$this->display();
		
		
		
	}
	
	//添加调研
	public function add(){ 

		$_POST['token'] = $this->token;
		
		if(IS_POST){

			if($id = $this->diaoyan_model->add($_POST)){
		
				$keyword_model=M('Keyword');
				$key = array(
					'keyword'=>$_POST['keyword'],
					'pid'=>$id,
					'token'=>$this->token,
					'module'=> 'Diaoyan'
				);
				$keyword_model->add($key);
				$this->success('关键字和调研信息添加成功！',U('Diaoyan/index'));
			}else{
				$this->error('关键字或调研信息添加失败！');
			}
		}else{
			$set=array();
			$set['time']=time()+10*24*3600;
			$this->assign('set',$set);
			$this->display('set');
		}
	}
	
	//修改和添加预约
	public function set(){
	
        $id = intval($this->_get('id')); 
		$checkdata = $this->diaoyan_model->where(array('id'=>$id))->find();
		if(empty($checkdata)||$checkdata['token']!=$this->token){
            $this->error("没有相应记录.您现在可以添加.",U('Diaoyan/add'));
        }	
		if(IS_POST){ 
            $where=array('id'=>$this->_post('id'),'token'=>$this->token);
			$check=$this->diaoyan_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($this->diaoyan_model->create()){
				if($this->diaoyan_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Diaoyan/index',array('token'=>$this->token)));
					$keyword_model=M('Keyword');
					$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Diaoyan'))->save(array('keyword'=>$_POST['keyword']));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($this->diaoyan_model->getError());
			}
		}else{
		
			$this->assign('isUpdate',1);
			$this->assign('set',$checkdata);
			$this->display();	
		
		}
	}
	//删除调研
	public function del(){
		if($this->_get('token')!=$this->token){$this->error('非法操作');}
        $id = intval($this->_get('id'));
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>$this->token);
            $check=$this->diaoyan_model->where($where)->find();
            if($check==false)   $this->error('非法操作');
			
            $back=$this->diaoyan_model->where($where)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Diaoyan'))->delete();
				$timu = M('Diaoyan_timu');
				$timu->where(array('pid'=> $id))->delete();
                $this->success('操作成功',U('Diaoyan/index',array('token'=>$this->token)));
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Diaoyan/index',array('token'=>$this->token)));
            }
        }        
	}
	//订单列表显示
	public function timu(){
		$id = $this->_get('id');
		$data = $this->diaoyan_timu->where(array('pid'=> $id))->select();
		//分页
		$count = $this->diaoyan_timu->where(array('pid'=> $id))->count();	
		$Page = new Page($count,20);
		$show = $Page->show();
		
		$this->assign('id',$id);
		$this->assign('page',$show);
		$this->assign('data', $data);
		$this->display();
	
	}
	//添加调研
	public function addTimu(){ 

		//$_POST['token'] = $this->token;
		
		if(IS_POST){
			$pid = $this->_get('id');
			if($this->diaoyan_timu->add($_POST)){
				$this->success('题目添加成功！',U('Diaoyan/timu',array('token'=> $this->token, 'id'=> $pid)));
			}else{
				$this->error('题目添加失败！');
			}
		}else{
			$pid = $this->_get('id');
			$this->assign('pid', $pid);
			$this->display('setTimu');
		}
	}
	
	//修改和添加题目
	public function setTimu(){
	
        $id = intval($this->_get('id')); 
		$checkdata = $this->diaoyan_timu->where(array('tid'=>$id))->find();
	
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Diaoyan/addTimu'));
        }	
		if(IS_POST){ 
            $where=array('tid'=>$this->_get('id'));
			$check=$this->diaoyan_timu->where($where)->find();
			if($check==false)$this->error('非法操作!');
			if($this->diaoyan_timu->create()){
				if($this->diaoyan_timu->where($where)->save($_POST)){
					$pid = $_GET['pid'];
					$this->success('修改成功',U('Diaoyan/timu',array('token'=>$this->token, 'id'=>$pid)));
					// $keyword_model=M('Keyword');
					// $keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Diaoyan'))->save(array('keyword'=>$_POST['keyword']));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($this->diaoyan_timu->getError());
			}
		}else{
			$pid = $this->_get('pid');
			$this->assign('pid', $pid);
			$this->assign('isUpdate',1);
			$this->assign('set',$checkdata);
			$this->display();	
		
		}
	}
	//删除题目
	public function delTimu(){
		if($this->_get('token')!=$this->token){$this->error('非法操作');}
        $tid = intval($this->_get('id'));
        $pid = intval($this->_get('pid'));
        if(IS_GET){                              
            $where=array('tid'=>$tid,'token'=>$this->token);
            $check=$this->diaoyan_timu->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $back=$this->diaoyan_timu->where($where)->delete();
            if($back==true){
            	//$keyword_model=M('Keyword');
            	//$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Diaoyan'))->delete();
                $this->success('操作成功',U('Diaoyan/timu',array('id'=>$pid,'token'=>$this->token)));
            }else{
                 $this->error('服务器繁忙,请稍后再试');
            }
        }        
	}
	
	//统计
	public function survey(){
		$id = $this->_get('id');
		$data = $this->diaoyan_model->find($id);
		
		$user = M('diaoyan_user');
		$count = $user->where(array('diaoyan_id'=>$id))->count();
		
		$timu = $this->diaoyan_timu->where(array('pid'=> $id))->select();
		
		$sur = array();
		foreach($timu as $v){
			$res =0;	
			$res += $v['perca'];
			$res += $v['percb'];
			$res += $v['percc'];
			$res += $v['percd'];
			$res += $v['perce'];
			$sur[] = $res;
		}
		
		$this->assign('timu', $timu);
		$this->assign('count', $count);
		$this->assign('diaoyan', $data);
		$this->display();
	}
	
	
	
	
	
	
	
	
}


?>