<?php
class HekaAction extends UserAction{
	public function _initialize() {
		parent::_initialize();
		$this->canUseFunction('Heka');
	}
	//贺卡设置
	public function index(){
		if( IS_POST ){
			$data['title'] = strip_tags( $_POST['title'] );
			$data['info'] = strip_tags( $_POST['info'] );
			$data['picurl'] = strip_tags( $_POST['picurl'] );
			$data['background'] = strip_tags( $_POST['background'] );
			
			if( empty( $data['title'] ) || empty( $data['info'] ) ){
				$this->error('标题与内容不能够为空');
				exit;
			}
			$HekaData = M('Heka');
			$status = $HekaData->where(array('token'=>session('token')))->find();
			if( !empty( $status['title'] ) ){
				$result = $HekaData->where(array('token'=>session('token')))->save($data);
				if($result){
					$this->success('贺卡回复更新成功!');
					exit;
				}else{
					$this->error('贺卡回复失败!');
					exit;
				}
			}else{
				$data['token'] = session('token');
				$result = $HekaData->add($data);
				if($result > 0){
					$this->success('贺卡增加成功!');
					exit;
				}else{
					$this->error('贺卡增加失败!');
					exit;
				}
			}
			
		}
		$info = M('Heka')->where(array('token'=>session('token')))->find();
		$this->assign('info',$info);
		$this->display();
	}
	
	//增加贺卡
	public function add(){
		$heka = M('Heka')->where(array('token'=>session('token')))->find();
		
		if( IS_POST ){
			$data['hid'] = $heka['id'];
			$data['token'] = session('token');
			$data['picurl'] = strip_tags( $_POST['picurl'] );
			$data['title'] = strip_tags( $_POST['title'] );
			$data['backmusic'] = strip_tags( $_POST['backmusic'] );
			
			if( empty( $data['title'] ) || empty( $data['picurl'] ) ){
				$this->error('标题与图片链接不能够为空!');
				exit;
			}
			
			$result = M('HekaList')->add($data);
			if( $result > 0 ){
				$this->success('贺卡添加成功');
				exit;
			}else{
				$this->error('贺卡添加失败!');
				exit;
			}
		}
		//所有贺卡列表
		$count = M('HekaList')->where(array('token'=>session('token')))->count();
		
		$page = new Page($count,25);
		
		$info = M('HekaList')->where(array('token'=>session('token')))->limit($page->firstRow.','.$page->listRows)->select();
		
		$this->assign('info',$info);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	
	public function edit(){
		$lid = (isset($_GET['id'])&&!empty($_GET['id'])) ? intval($_GET['id']) : exit('非法访问');
		$lid = intval($_POST['id']);
		$data['title'] = strip_tags($_POST['title']);
		$data['picurl'] = strip_tags($_POST['picurl']);
		$data['backmusic'] = strip_tags($_POST['backmusic']);
		$res = M('HekaList')->where(array('id'=>$lid ,'token'=>session('token')))->save($data);
		if( $res > 0 ){
			$this->success('内容修改成功!');
			exit;
		}else{
			$this->error('修改失败!');
		}
	}
	
	public function delete(){
		$id = (isset($_GET['id']) && !empty($_GET['id'])) ? intval($_GET['id']) : exit('非法访问!');
		$result = M('HekaList')->where(array('id'=>$id,'token'=>session('token')))->delete();
		
		if($result){
			$this->success('内容删除成功!');
			exit;
		}else{
			$this->error('内容删除失败!');
			exit;
		}
	}
	
}