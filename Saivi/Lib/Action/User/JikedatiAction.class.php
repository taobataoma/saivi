<?php

//web

class JikedatiAction extends UserAction{

	public $token;


	public function _initialize() {

		parent::_initialize();

		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();

		if(!strpos($token_open['queryname'],'Jikedati')){

            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));

		}



		$this->Jikedati_model=M('Jikedati');


		$this->token=session('token');

		$this->assign('token',$this->token);

		$this->assign('module','Jikedati');


	}
	 public function reply(){
	 	$where['token'] = session('token');
		$Cdata = M('Jikedati_reply');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['copyright'] = strip_tags($_POST['copyright']);
			$data['title'] = strip_tags($_POST['title']);
			$data['tp'] = strip_tags($_POST['tp']);
			$data['banner'] = strip_tags($_POST['banner']);
			$data['scorename'] = strip_tags($_POST['scorename']);
			$data['tip1'] = strip_tags($_POST['tip1']);
			$data['tip2'] = strip_tags($_POST['tip2']);
			$data['tip3'] = strip_tags($_POST['tip3']);
			
			$data['info'] = strip_tags($_POST['info']);
			
			//$res = M('Vcard')->where($where)->find();
			if($info){
				$result = M('Jikedati_reply')->where($where)->save($data);
				if($result){
					$this->success('回复信息更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('Jikedati_reply')->add($data);
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

		public function index(){

		$where = array('token'=> $this->token);

		$count      = $this->Jikedati_model->where($where)->count();

		$Page       = new Page($count,20);

		$show       = $Page->show();

		$data = $this->Jikedati_model->where($where)->order('id desc')->select();

		

		//$type='Yiliao';

		//$this->assign('type',$type);

		$this->assign('page',$show);

		$this->assign('data',$data);

		$this->display();

		

		

		

	}


	public function add(){ 

		

		$_POST['token'] = $this->token;

		
		$checkdata = $this->Jikedati_model->where(array('token'=>$this->token,'type'=>$this->type))->find();

		if(IS_POST){	

			

			if($id = $this->Jikedati_model->add($_POST)){

				

				$this->success('添加成功！',U('Jikedati/index',array('token'=>$this->token)));

			}else{

				$this->error('添加失败！');

			}

		}else{


			$this->assign('set',$set);

			$this->assign('arr',$arr);

			$this->display('set');

		}

	}

	

	//修改预约

	public function set(){

		

        $id = intval($this->_get('id')); 

		$checkdata = $this->Jikedati_model->where(array('id'=>$id))->find();

		if(empty($checkdata)||$checkdata['token']!=$this->token){

            $this->error("没有相应记录.您现在可以添加.",U('Jikedati/add'));

        }

		$lbs=M("Company")->where(array('token'=>$this->token))->select();

		$arr=array();

		foreach($lbs as $v){

			$arr[$v['catid']]=array('catid'=>$v['catid'],'address'=>$v['address'],'phone'=>$v['tel'],'latitude'=>$v['latitude'],'longitude'=>$v['longitude']);

		}

		if(IS_POST){ 

            $where=array('id'=>$this->_post('id'),'token'=>$this->token);

			$check=$this->Jikedati_model->where($where)->find();

			if($check==false)$this->error('非法操作');

			if($this->Jikedati_model->create()){

				if($_POST["lbs"]==1){

					$cid=$_POST['cid'];

					$_POST['phone']=$arr[$cid]['phone'];

					$_POST['address']=$arr[$cid]['address'];

					$_POST['longitude']=$arr[$cid]['longitude'];

					$_POST['latitude']=$arr[$cid]['latitude'];

				}

				//print_r($_POST);die;

				if($this->Jikedati_model->where($where)->save($_POST)){

					$this->success('修改成功',U('Jikedati/index',array('token'=>$this->token)));

					$keyword_model=M('Keyword');

					$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>$this->type))->save(array('keyword'=>$_POST['keyword']));

				}else{

					$this->error('操作失败');

				}

			}else{

				$this->error($this->Jikedati_model->getError());

			}

		}else{

			$this->assign('isUpdate',1);

			$this->assign('set',$checkdata);

			$this->assign('arr',$arr);

			$this->assign('act',$id);

			$this->display();	

		

		}

	}

	//删除预约

	public function del(){

		if($this->_get('token')!=$this->token){$this->error('非法操作');}

        $id = intval($this->_get('id'));

        if(IS_GET){                              

            $where=array('id'=>$id,'token'=>$this->token);

			$wher=array('pid'=>$id,'token'=>$this->token);

            $check=$this->Jikedati_model->where($where)->find();

            if($check==false)   $this->error('非法操作');

            $back=$this->Jikedati_model->where($where)->delete();

            if($back==true){

				M('yuyue_order')->where($wher)->delete();

				M('setinfo')->where($wher)->delete();

            	M('Keyword')->where(array('token'=>$this->token,'pid'=>$id,'module'=>$this->type))->delete();

                $this->success('操作成功',U('Jikedati/index',array('token'=>$this->token,'pid'=>$id)));
				

            }else{

                 $this->error('服务器繁忙,请稍后再试',U('Jikedati/index',array('token'=>$this->token)));

            }

        }        

	}




	


}

?>