<?php
class ArticalAction extends BackAction{
	public function index(){
		$db=D('Artical');
		$where='';
		S('Artical',null);
		if (!C('agent_version')){
			$Artical=$db->where('status=1')->limit(32)->select();
		}else {
			$Artical=$db->where('status=1 AND agentid=0')->limit(32)->order('date desc')->select();
			$where=array('agentid'=>0);
		}
		
		S('Artical',$Artical);
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->order('date desc')->select();
		$this->assign('info',$info);
		$this->assign('page',$page->show());
		$this->display();
	}
	public function add(){
		$this->display();
	}
	
	public function edit(){
		$id=$this->_get('id','intval');
		$info=D('Artical')->find($id);
		$this->assign('info',$info);
		$this->display('add');
	}
	
	public function del(){
		$db=D('Artical');
		$id=$this->_get('id','intval');
		if($db->delete($id)){
			$this->success('操作成功',U('Artical/index'));
		}else{
			$this->error('操作失败',U('Artical/index'));
		}
	}
	
	public function insert(){
		$thumb['width']='48';
		$thumb['height']='48';
		//$arr=$this->_upload($_FILES['img'],$dir='',$thumb);
		/*
		if($arr['error']===0){
			$_POST['img']=C('site_url').$arr['info'][0]['savepath'].$arr['info'][0]['savename'];
		}else{
			$this->error($arr['info'],U('Case/index'));
		}
		*/
		$db=D('Artical');
		if(IS_POST){$_POST['date']= date("Y-m-d H:i:s",time());}
		if($db->create()){
	         
			if($db->add()){
						
				$this->success('操作成功',U('Artical/index'));
			}else{
				unlink($arr['info'][0]['savepath'].$arr['info'][0]['savename']);
				$this->error('操作成功',U('Artical/index'));
			}
		}else{
			$this->error($db->getError(),U('Artical/index'));
		}
	}
	
	public function upsave(){
		$db=D('Artical');
		/*
		if($_POST['img']!=false){
			$thumb['width']='48';
			$thumb['height']='48';
			//$arr=$this->_upload($_FILES['img'],$dir='',$thumb);
			if($arr['error']===0){
				$_POST['img']=C('site_url').$arr['info'][0]['savepath'].$arr['info'][0]['savename'];
			}else{
				$this->error($arr['info'],U('Case/index'));
			}
		}
		*/
		if($db->create()){
			if($db->save()){
				$this->success('操作成功',U('Artical/index'));
			}else{
				unlink($arr['info'][0]['savepath'].$arr['info'][0]['savename']);
				$this->error('操作成功',U('Artical/index'));
			}
		}else{
			$this->error($db->getError(),U('Artical/index'));
		}
	}
	
}