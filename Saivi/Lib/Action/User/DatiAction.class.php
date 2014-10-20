<?php
class DatiAction extends UserAction{
	public function _initialize() {
		parent::_initialize();
		$this->canUseFunction('Dati');
	}
	//选择题列表
	public function index(){
		$where['type'] = 0;
		$where['token'] = session('token');
		$count = M('Dati')->where($where)->count();
		$page = new Page($count,25);
		$info = M('Dati')->where($where)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->info = $info;
		$this->assign('page',$page->show());
		$this->display();
	}
	
	//简答题列表
	public function jdt(){
		$where['type'] = 1;
		$where['token'] = session('token');
		$count = M('Dati')->where($where)->count();
		$page = new Page($count,25);
		$info = M('Dati')->where($where)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->info = $info;
		$this->assign('page',$page->show());
		$this->display();
	}
	
	//看图猜列表
	public function ktc(){
		$where['type'] = 2;
		$where['token'] = session('token');
		$count = M('Dati')->where($where)->count();
		$page = new Page($count,25);
		$info = M('Dati')->where($where)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->info = $info;
		$this->assign('page',$page->show());
		$this->display();
	}
	
	//游戏说明
	public function info(){
		$this->display();
	}
	
	//添加题目
	public function add(){
		
		$type = $this->_get('type') ? $this->_get('type') : 0;
		
		if(IS_POST){
			
			$data['type'] = intval($_POST['type']);
			$data['token'] = session('token');
			$data['title'] = strip_tags($_POST['title']);
			$data['daan'] = strip_tags($_POST['daan']);
			$data['score'] = intval($_POST['score']);
			
			if($type == 2){
				$data['picurl'] = strip_tags($_POST['picurl']);
				$data['info'] = strip_tags($_POST['info']);
				
				if(empty($data['picurl']) || empty($data['info'])){
					$this->error('图片与详细信息不能够为空!');
				}
			}
			
			if(empty($data['title']) || empty($data['daan'])) $this->error('标题与答案都不能够为空');
			$insert = M('Dati')->add($data);
			
			if( $insert > 0) {
				$this->success('题目增加成功!');
				exit;
			}else{
				$this->error('系统繁忙!');
				exit;
			}
		}
		
		$this->type = $type;
		$this->display();
	}
	
	//编辑题目
	public function edit(){
		$type = $this->_get('type') ? $this->_get('type','intval') : 0;
		$id = $this->_get('id') ? $this->_get('id','intval') : 0;
		
		$info = M('Dati')->where(array('id'=>$id,'type'=>$type,'token'=>session('token')))->find();
		
		if( IS_POST ){
			
			$where['type'] = intval($_POST['type']);
			$where['token'] = session('token');
			$data['title'] = strip_tags($_POST['title']);
			$data['daan'] = strip_tags($_POST['daan']);
			$data['score'] = intval($_POST['score']);
			$where['id'] = intval($_POST['id']);
			
			if($type == 2){
				$data['picurl'] = strip_tags($_POST['picurl']);
				$data['info'] = strip_tags($_POST['info']);
				
				if(empty($data['picurl']) || empty($data['info'])){
					$this->error('图片与详细信息不能够为空!');
				}
			}
			
			if(empty($data['title']) || empty($data['daan'])) $this->error('标题与答案都不能够为空');
			$insert = M('Dati')->where($where)->save($data);
			
			if( $insert ) {
				$this->success('题目修改成功!');
				exit;
			}else{
				$this->error('系统繁忙!');
			}
			
		}
		
		$this->type = $type;
		$this->info = $info;
		$this->display();
	}
	
	//删除题目
	public function delete(){
		
		$where['id'] = $this->_get('id');
		$where['type'] = $this->_get('type');
		$where['token'] = session('token');
		
		$result = M('Dati')->where($where)->delete();
		
		if($result){
			$this->success('题目成功删除!');
		}else{
			$this->error('题目删除失败!');
		}
	}
	
	//参与用户以及积分
	public function playshow(){
		
	}
}