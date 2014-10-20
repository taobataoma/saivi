<?php

//web

class ScenepinAction extends UserAction{

	public $token;

	public $Scenepin_model;


	public function _initialize() {

		parent::_initialize();

		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();

		if(!strpos($token_open['queryname'],'Scenepin')){

            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));

		}



		$this->Scenepin_model=M('Scenepin');


		$this->token=session('token');

		$this->assign('token',$this->token);

	


	}


	

	public function add(){

		

        $id = intval($this->_get('id')); 



		if(IS_POST){ 

            $where=array('id'=>$this->_post('id'),'token'=>$this->token);

			$_POST['token']=$this->token;

			if($id =$this->Scenepin_model->add($_POST)){
				$keyword_model=M('Keyword');

				$key = array(

					'keyword'=>$_POST['keyword'],

					'pid'=>$id ,

					'token'=>$this->token,

					'module'=> 'Scenepin'

				);

				$keyword_model->add($key);

				
				//print_r($_POST);die;

				

					$this->success('添加成功',U('Scenepin/index',array('token'=>$this->token)));

				}else{

					$this->error('操作失败');

				}

		}

			$this->display();	

	}
	public function index(){

		
		$set=$this->Scenepin_model->where(array('token'=>$this->token))->select();
		


		$this->assign('set',$set);


		$this->display();

	}
	public function edit(){

		$id = $this->_get('id');

		

		

		$set=$this->Scenepin_model->where(array('token'=>$this->token,'id'=>$id))->find();
		$this->assign('set',$set);

		

		if(IS_POST){

			//print_r($_POST);die;

			if($this->Scenepin_model->where(array('id'=>$id))->save($_POST)){
				$keyword_model=M('Keyword');

				
				//print_r($key);die;

				$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Scenepin'))->save(array('keyword'=>$_POST['keyword']));

				$this->success('修改成功！',U('Scene/index',array('token'=>$this->token)));

			}else{

				$this->error('修改失败！');

			}

		}else{

			
			$this->display();

		}

	}
	public function del(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$check=M('Scenepin')->where(array('token'=>$_SESSION['token'],'id'=>$this->_get('id')))->find();
		
		if($check==false){$this->error('服务器繁忙');}
		if(empty($_POST['edit'])){
			if(M('Scenepin')->where(array('id'=>$check['id'],'token'=>$_SESSION['token']))->delete()){
				M('Scenepin_addtp')->where(array('pid'=>$check['id'],'token'=>$_SESSION['token']))->delete();
				M('keyword')->where(array('pid'=>$check['id'],'token'=>$_SESSION['token'],'module'=>'Scenepin'))->delete();
				
				$this->success('操作成功');
			}else{
				$this->error('服务器繁忙,请稍后再试');
			}
		}
	}
public function addtp(){
		
		$checkdata=M('Scenepin')->where(array('token'=>$_SESSION['token'],'id'=>$this->_get('id')))->find();
		
		if($checkdata==false){$this->error('场景不存在');}
		if(IS_POST){
			unset($_POST['s']);
			
			$pid = $this->_get('id');
			unset($_POST['__hash__']);
			unset($_POST['pid']);
			$scene_addtp = M('Scenepin_addtp');

			
			foreach($_POST as $k=>$v){
				$kArr = explode('_',$k);//1.title;2.2;1.sort;2.2
				$arr[$kArr[1]][$kArr[0]] = $v;
			}
			foreach($arr as $key=>$val){
				if(!array_key_exists('status',$val)){
					$arr[$key]['status'] = '0';
				}
				if($arr[$key]['title'] == '') $arr[$key]['title'] = '12345';
				$arr[$key]['pid'] = $pid;
				$arr[$key]['token'] = $this->token;
				$arr[$key]['create_time'] = time();
				$scene_addtp->add($arr[$key]);
			}

			
			
			$this->success('保存成功');
			
			
		}else{
			$data=M('Scenepin_addtp');
			$count      = $data->where(array('token'=>$_SESSION['token'],'pid'=>$this->_get('pid')))->count();
			$Page       = new Page($count,120);
			$show       = $Page->show();
			$list = $data->where(array('token'=>$_SESSION['token'],'pid'=>$this->_get('id')))->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();	
		//upyun多文件上传
		
			$bucket = UNYUN_BUCKET;
			$form_api_secret = UNYUN_FORM_API_SECRET; /// 表单 API 功能的密匙（请访问又拍云管理后台的空间管理页面获取）

			$options = array();
			$options['bucket'] = $bucket; /// 空间名
			$options['expiration'] = time()+600; /// 授权过期时间
			$options['save-key'] = '/'.$this->token.'/{year}/{mon}/{day}/'.time().'_{random}{.suffix}'; /// 文件名生成格式，请参阅 API 文档
			$options['allow-file-type'] = C('up_exts'); /// 控制文件上传的类型，可选
			$options['content-length-range'] = '0,'.intval(C('up_size'))*1024; /// 限制文件大小，可选
			if (intval($_GET['width'])){
				$options['x-gmkerl-type'] = 'fix_width';
				$options['fix_width '] = $_GET['width'];
			}
			//$options['return-url'] = C('site_url').'/index.php?g=User&m=Upyun&a=uploadReturn&imgfrom=photo_list'; /// 页面跳转型回调地址
			//$options['notify-url'] = C('site_url').'/index.php?g=User&m=Upyun&a=uploadReturn&imgfrom=photo_list'; /// 页面跳转型回调地址
			$policy = base64_encode(json_encode($options));
			$sign = md5($policy.'&'.$form_api_secret); /// 表单 API 功能的密匙（请访问又拍云管理后台的空间管理页面获取）
			$this->assign('bucket',$bucket);
			$this->assign('sign',$sign);
			$this->assign('policy',$policy);
			$info=M('Scenepin_addtp')->where(array('token'=>$_SESSION['token'],'pid'=>$this->_get('id')))->order('sort desc')->select();
			
			
			$this->assign('info',$info);	
			$this->assign('page',$show);		
			$this->assign('photo',$list);
			$this->display();	
		
		}
		
	}

	public function list_edit(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$check=M('Scenepin_addtp')->field('id,pid')->where(array('token'=>$_SESSION['token'],'id'=>$this->_post('id')))->find();
		if($check==false){$this->error('照片不存在');}
		
		if(IS_POST){
			
			$this->all_save('Scenepin_addtp','/addtp?id='.$check['pid']);		
		}else{
			$this->error('非法操作');
		}
	}
	
	public function edittp(){
		$info=M('Scenepin_addtp')->where(array('token'=>$_SESSION['token'],'id'=>$this->_get('id')))->order('sort desc')->select();
			
			
			$this->assign('info',$info);
			$this->id=$this->_get('pid');	
		$this->display();	
		
	}

public function list_del(){
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
		$check=M('Scenepin_addtp')->field('id,pid')->where(array('token'=>$_SESSION['token'],'id'=>$this->_get('id')))->find();
		
		if($check==false){$this->error('服务器繁忙');}
		if(empty($_POST['edit'])){
			if(M('Scenepin_addtp')->where(array('id'=>$check['id']))->delete()){
				
				$this->success('操作成功');
			}else{
				$this->error('服务器繁忙,请稍后再试');
			}
		}
	}


	
	
  

}





?>