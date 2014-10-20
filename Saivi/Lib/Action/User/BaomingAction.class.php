<?php
class BaomingAction extends UserAction{
	public function _initialize() {
		parent::_initialize();
		$this->canUseFunction('Baoming');
	}
	public function index(){
		$where['token'] = session('token');
		$Data = M('BaomingList');
		$count = $Data->where($where)->count();
		$page = new Page($count,25);
		$info = $Data->where($where)->order('sort DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->info = $info;
		$this->page = $page->show();
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data['zhuti']	= strip_tags($_POST['zhuti']);
			$data['banner']	= strip_tags($_POST['banner']);
			$data['feiyong'] 	= strip_tags($_POST['feiyong']);
			$data['token'] 	= session('token');
			$data['time'] 	= strip_tags($_POST['time']);
			$data['info'] 	= strip_tags($_POST['info']);
			
			if(empty($data['zhuti'])) $this->error('主题不能够为空!');
			$insert = M('BaomingList')->add($data);
			if($insert > 0){
				$this->success('主题活动添加成功!');
			}else{
				$this->error('主题活添加失败!');
			}
		}else{
			$this->display();
		}
	}
	
	public function edit(){
		$id = $this->_get('id');
		$where['id'] = $id;
		$where['token'] = session('token');
		if(IS_POST){
			$data['banner']	= strip_tags($_POST['banner']);
			$data['zhuti']	= strip_tags($_POST['zhuti']);
			$data['time'] 	= strip_tags($_POST['time']);
			$data['feiyong'] 	= strip_tags($_POST['feiyong']);
			$data['info'] 	= strip_tags($_POST['info']);
			
			if(empty($data['zhuti'])) $this->error('主题不能够为空!');
			$up = M('BaomingList')->where($where)->save($data);
			if($up){
				$this->success('主题活动更新成功!');
			}else{
				$this->error('主题活动更新失败!');
			}
		}else{
			$info = M('BaomingList')->where($where)->find();
			$this->info = $info;
			$this->display();
		}
	}
	
	public function delete(){
		$where['id'] = $this->_get('id');
		$where['token'] = session('token');
		$info = M('BaomingList')->where($where)->delete();
		if($info){
			$this->success('主题活动删除成功!');
		}else{
			$this->error('主题活动删除失败!');
		}
	}
	public function infos(){

		

		$pid=$this->_get('pid');
		$where= array('token'=> $this->token,'pid'=>$pid);
		$count = M('baoming_order')->where($where)->count();
	
		$Page = new Page($count,20);

		$show = $Page->show();

		$data = M('baoming_order')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();


		$this->assign('page',$show);

		$this->assign('data', $data);
		$this->assign('pid', $pid);

		

		$this->display();
	

	}
	public function delinfos(){

		if($this->_get('token')!=$this->token){$this->error('非法操作');}

        $id = intval($this->_get('id'));

        if(IS_GET){                              

            $where=array('id'=>$id,'token'=>$this->token);

            $check=M('baoming_order')->where($where)->find();

            if($check==false)   $this->error('非法操作');

            $back=M('baoming_order')->where($where)->delete();

            if($back==true){

                $this->success('操作成功',U('Baoming/infos',array('token'=>$this->token,'pid'=>$check['pid'])));

            }else{

                 $this->error('服务器繁忙,请稍后再试',U('Baoming/infos',array('token'=>$this->token,'pid'=>$check['pid'])));

            }

        }        

	}


	
	public function company(){
		$where['token'] = session('token');
		$Cdata = M('Baoming');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['company'] = strip_tags($_POST['company']);
			$data['logo'] = strip_tags($_POST['logo']);
			$data['title'] = strip_tags($_POST['title']);
			$data['jianjie'] = strip_tags($_POST['jianjie']);
			$data['tp'] = strip_tags($_POST['tp']);
			
			$data['info'] = strip_tags($_POST['info']);
			
			//$res = M('Vcard')->where($where)->find();
			if($info){
				$result = M('Baoming')->where($where)->save($data);
				if($result){
					$this->success('回复信息更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('Baoming')->add($data);
				if($insert > 0){
					$this->success('回复信息添加成功!');
				}else{
					$this->error('回复信息添加失败!');
				}
			}
		}else{
			$this->display();
		}
	}
}