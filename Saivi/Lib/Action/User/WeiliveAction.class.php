<?php
class WeiliveAction extends UserAction{
	public function index(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		//$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		//dump($token_open);
		//if(!strpos($token_open['queryname'],'api')){$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));}

		$data=M('Member_wei_category');
		$count      = $data->where(array('token'=>$_SESSION['token']))->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $data->where(array('token'=>$_SESSION['token']))->limit($Page->firstRow.','.$Page->listRows)->order('displayorder')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	public function reply(){
		 $this->reply_info_model=M('Weilivereply_info');
		$thisInfo = $this->reply_info_model->where(array('token'=>$this->token))->find();
		
		if ($thisInfo&&$thisInfo['token']!=$this->token){
			exit();
		}

		if(IS_POST){
			$row['title']=$this->_post('title');
			$row['info']=$this->_post('info');
			$row['picurl']=$this->_post('picurl');
			$row['copyright']=$this->_post('copyright');
			$row['tel']=$this->_post('tel');
			$row['biaoti']=$this->_post('biaoti');
			
			$row['token']=$this->_post('token');
			
			
			if ($thisInfo){
				$where=array('token'=>$this->token);
				$this->reply_info_model->where($where)->save($row);

				$keyword_model=M('Keyword');
				//$keyword_model->where(array('token'=>$this->token,'pid'=>$thisInfo['id'],'module'=>'Reply_info'))->save(array('keyword'=>$_POST['keyword']));
				$this->success('修改成功',U('Weilive/reply',$where));
						
			}else {
				$where=array('token'=>$this->token);
				$this->reply_info_model->add($row);
				$this->success('设置成功',U('Weilive/reply',$where));
			}
		}else{
			//
			
			
			//
			$this->assign('set',$thisInfo);
			
			$this->display();
		}
	}
	
	public function business_index(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		
		$data=M('Member_business');
		$count      = $data->where(array('token'=>$_SESSION['token'],'catid'=>$_GET['catid']))->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $data->where(array('token'=>$_SESSION['token'],'catid'=>$_GET['catid']))->limit($Page->firstRow.','.$Page->listRows)->order('displayorder')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}


	public function product_index(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$data=M('Member_business_product');
		$count      = $data->where(array('token'=>$_SESSION['token'],'catid'=>$_GET['catid'],'bid'=>$_GET['bid']))->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $data->where(array('token'=>$_SESSION['token'],'catid'=>$_GET['catid'],'bid'=>$_GET['bid']))->limit($Page->firstRow.','.$Page->listRows)->order('displayorder')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}


	public function case_index(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$data=M('Member_business_case');
		$count      = $data->where(array('token'=>$_SESSION['token'],'bid'=>$_GET['bid']))->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $data->where(array('token'=>$_SESSION['token'],'bid'=>$_GET['bid']))->limit($Page->firstRow.','.$Page->listRows)->order('addtime')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}


	public function ad(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$data=M('Member_business_ad');
		$count      = $data->where(array('token'=>$_SESSION['token']))->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $data->where(array('token'=>$_SESSION['token']))->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);


		$this->display();
	}



	public function fav_index(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$data=M('Member_business_fav');
		$count      = $data->where(array('token'=>$_SESSION['token'],'bid'=>$_GET['bid']))->count();
		$Page       = new Page($count,15);
		$show       = $Page->show();
		$list = $data->where(array('token'=>$_SESSION['token'],'bid'=>$_GET['bid']))->limit($Page->firstRow.','.$Page->listRows)->order('addtime desc')->select();
		$this->assign('page',$show);
		$this->assign('list',$list);


		$this->display();
	}


	public function fav_add(){
		if(IS_POST){
			$P = M("Member_business_fav");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				$P->starttime = strtotime($this->_post("starttime"));
				$P->endtime = strtotime($this->_post("endtime"));
				$P->bid = $_GET['bid'];
				$P->addtime = time();
				$P->token = $this->_get('token');
				if($result = $P->add()) {
					$this->success('添加成功！',U('Weilive/fav_index',array('token'=>$_SESSION['token'],'bid'=>$_GET['bid'])));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$this->display();
		}
	}	
	


	public function ad_add(){
		$da = M('Member_business')->where(array('token'=>$this->_get('token')))->select();
		
		$this->assign('da',$da);
		if(IS_POST){
			$P = M("Member_business_ad");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				$tmp = findBy("Member_business","name='".$this->_post('name')."'","id");
				if(!$tmp || empty($_POST['name'])){
					$this->error('添加失败，该商户不存在！');
				}
				$P->bid = $tmp['id'];
				$P->addtime = time();
				$P->token = $this->_get('token');
				if($result = $P->add()) {
					$this->success('添加成功！',U('Weilive/ad',array('token'=>$_SESSION['token'])));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$this->display();
		}
		
	}	
	

	public function ad_edit(){
	
		if(IS_POST){
			$P = M("Member_business_ad");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				// 写入帐号数据
				if($result = $P->save()) {
					$this->success('修改成功！',U('Weilive/ad',array('token'=>$_SESSION['token'])));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$where = array('token'=>$this->_get('token'),'id'=>$this->_get('id'));
			$data=M('Member_business_ad')->where($where)->find();
			$this->assign('vo',$data);
			$this->display();
		}
	}
	
	public function fav_edit(){
		if(IS_POST){
			$P = M("Member_business_fav");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				$P->starttime = strtotime($this->_post("starttime"));
				$P->endtime = strtotime($this->_post("endtime"));
				// 写入帐号数据
				if($result = $P->save()) {
					$this->success('修改成功！',U('Weilive/fav_index',array('token'=>$_SESSION['token'],'bid'=>$this->_post("bid"))));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$where = array('token'=>$this->_get('token'),'id'=>$this->_get('id'));
			$data=M('Member_business_fav')->where($where)->find();
			$this->assign('vo',$data);
			$this->display();
		}
	}


	public function case_edit(){
		if(IS_POST){
			$P = M("Member_business_case");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				// 写入帐号数据
				if($result = $P->save()) {
					$this->success('修改成功！',U('Weilive/case_index',array('token'=>$_SESSION['token'],'bid'=>$this->_post("bid"))));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$where = array('token'=>$this->_get('token'),'id'=>$this->_get('id'));
			$data=M('Member_business_case')->where($where)->find();
			$this->assign('vo',$data);
			$this->display();
		}
	}


	public function product_edit(){
		if(IS_POST){
			$P = M("Member_business_product");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				// 写入帐号数据
				if($result = $P->save()) {
					$this->success('修改成功！',U('Weilive/product_index',array('token'=>$_SESSION['token'],'catid'=>$this->_post('catid'),'bid'=>$this->_post('bid'))));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$where = array('token'=>$this->_get('token'),'id'=>$this->_get('id'));
			$data=M('Member_business_product')->where($where)->find();
			$this->assign('vo',$data);
			$this->display();
		}
	}	
		
	

	public function product_add(){
		if(IS_POST){
			if(empty($_GET['bid'])){
				$P = M("Member_business");
				$data['token'] = $this->_get('token');
				$data['crate_time'] = time();
				$data['catid'] = $this->_get('catid');
				$result = $P->data($data)->add(); 

				//add keyword					
				$data['pid'] = $result;
				$data['token'] = $this->_get('token');
				$data['module'] = 'Weilive';
				$tmp = M("Keyword");
				$tmp->data($data)->add();
				
			}else{
				$result = $_GET['bid'];
			}
			$P = M("Member_business_product");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				$P->bid = $result;
				$P->crate_time = time();
				$P->token = $this->_get('token');
				$P->catid = $this->_get('catid');
				if($result = $P->add()) {
					$this->success('添加成功！',U('Weilive/product_index',array('token'=>$_SESSION['token'],'catid'=>$this->_get('catid'),'bid'=>$result)));
				}else{
					$this->error('添加失败！');
				}
			}
			
		}else{
			$this->display();
		}
	}
	


	public function case_add(){
		if(IS_POST){
			if(empty($_GET['bid'])){
				$this->error('添加失败，请先添加商户！');
			}
			$P = M("Member_business_case");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				$P->bid = $this->_get('bid');
				$P->addtime = time();
				$P->token = $this->_get('token');
				if($result = $P->add()) {
					$this->success('添加成功！',U('Weilive/case_index',array('token'=>$_SESSION['token'],'bid'=>$this->_get('bid'))));
				}else{
					$this->error('添加失败！');
				}
			}
			
		}else{
			$this->display();
		}
	}	

	
	public function delCategory(){
			$data=M('Member_wei_category')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($data==false){
				$this->error('服务器繁忙请稍后再试');
			}else{
				$this->success('操作成功',U('Weilive/index',array('token'=>$_SESSION['token'])));
			}
	}


		
	public function delProduct(){
			$data=M('Member_business_product')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($data==false){
				$this->error('服务器繁忙请稍后再试');
			}else{
				$this->success('操作成功');
			}
	}

	public function delAd(){
			$data=M('Member_business_ad')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($data==false){
				$this->error('服务器繁忙请稍后再试');
			}else{
				$this->success('操作成功');
			}
	}
	

	public function delCase(){
			$data=M('Member_business_case')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($data==false){
				$this->error('服务器繁忙请稍后再试');
			}else{
				$this->success('操作成功');
			}
	}

	public function delFav(){
			$data=M('Member_business_fav')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($data==false){
				$this->error('服务器繁忙请稍后再试');
			}else{
				$this->success('操作成功');
			}
	}
		
	public function delBusiness(){
			$data=M('Member_business')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($data==false){
				$this->error('服务器繁忙请稍后再试');
			}else{
				//del keyword
				M('keyword')->where(array('token'=>session('token'),'pid'=>$this->_get('id'),''=>'Weilive'))->delete();
				
				$this->success('操作成功');
			}
	}

	
	public function business_add(){
		if(IS_POST){
			$P = M("Member_business");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				$P->crate_time = time();
				$P->token = $this->_get('token');
				$P->catid = $this->_get('catid');
				
				// 写入帐号数据
				if($result = $P->add()) {
					//add keyword					
					$data['keyword'] = $_POST['keyword'];
					$data['pid'] = $result;
					$data['token'] = $this->_get('token');
					$data['module'] = 'Weilive';
					$tmp = M("Keyword");
					$tmp->data($data)->add();
				
					$this->success('添加成功！',U('Weilive/business_index',array('token'=>$_SESSION['token'],'catid'=>$this->_get('catid'))));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$this->display();
		}
	}

	public function business_edit(){
		if(IS_POST){
			$P = M("Member_business");
			if(!$P->create()) {
				$this->error($P->getError());
			}else{
				// 写入帐号数据
				if($result = $P->save()) {
					//edit keyword					
					$tmp = M("Keyword");
					$tmp->where("pid=".$this->_get('id')." and token='".$this->_get('token')."' and module='Weilive' ")->save(array('keyword'=>$_POST['keyword']));
				
					$this->success('修改成功！',U('Weilive/business_index',array('token'=>$_SESSION['token'],'catid'=>$this->_post('catid'))));
				}else{
					$this->error('添加失败！');
				}
			}
		}else{
			$where = array('token'=>$this->_get('token'),'id'=>$this->_get('id'));
			$data=M('Member_business')->where($where)->find();
			$this->assign('vo',$data);
			$this->display();
		}
	}	
	
	
	public function category_edit(){
		if(IS_POST){
			$data['token']=$this->_get('token');
			$data['title']=$this->_post('title');
			$data['displayorder']=$this->_post('displayorder');
			$data['summary']=$this->_post('summary');
			if($data['title']&&$data['displayorder']&&$data['summary']){
				$where = array('token'=>$this->_get('token'),'id'=>$this->_get('id'));
				$sql=M('Member_wei_category')->where($where)->data($data)->save();
				if($sql){
					$this->success('操作成功',U('Weilive/index',array('token'=>$_SESSION['token'])));
				}else{
					$this->error('服务繁忙，请稍候再试');
				}
			}else{
				$this->error('所有表单都必须填写');
			}
		}else{
			$where = array('token'=>$this->_get('token'),'id'=>$this->_get('id'));
			$data=M('Member_wei_category')->where($where)->find();
			$this->assign('vo',$data);
			$this->display();
		}
	}
	
	public function category_add(){
		if(IS_POST){
			$data['token']=$this->_get('token');
			$data['title']=$this->_post('title');
			$data['displayorder']=$this->_post('displayorder');
			$data['summary']=$this->_post('summary');
			if($data['title']&&$data['displayorder']&&$data['summary']){
				$sql=M('Member_wei_category')->data($data)->add();
				if($sql){
					$this->success('操作成功',U('Weilive/index',array('token'=>$_SESSION['token'])));
				}else{
					$this->error('服务繁忙，请稍候再试');
				}
			}else{
				$this->error('所有表单都必须填写');
			}
		}else{
			$this->display();
		}
	}	
	
	
}

?>