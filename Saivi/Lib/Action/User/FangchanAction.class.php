<?php

//web

class FangchanAction extends UserAction{

	public $token;

	public $Fangchan_model;


	public function _initialize() {

		parent::_initialize();

		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();

		if(!strpos($token_open['queryname'],'Fangchan')){

            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));

		}



		$this->Fangchan_model=M('Fangchan');


		$this->token=session('token');

		$this->assign('token',$this->token);

	


	}
	 public function reply(){
	 	$where['token'] = session('token');
		$Cdata = M('fangchan_reply');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['copyright'] = strip_tags($_POST['copyright']);
			$data['title'] = strip_tags($_POST['title']);
			$data['tp'] = strip_tags($_POST['tp']);

			$data['info'] = strip_tags($_POST['info']);
			
			//$res = M('Vcard')->where($where)->find();
			if($info){
				$result = M('fangchan_reply')->where($where)->save($data);
				if($result){
					$this->success('回复信息更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('fangchan_reply')->add($data);
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

		$count      = $this->Fangchan_model->where($where)->count();

		$Page       = new Page($count,20);

		$show       = $Page->show();

		$data = $this->Fangchan_model->where($where)->order('id desc')->select();

		

		//$type='Yiliao';

		//$this->assign('type',$type);

		$this->assign('page',$show);

		$this->assign('data',$data);

		$this->display();

		

		

		

	}

	public function add(){ 

		

		$_POST['token'] = $this->token;

		 
		$checkdata = $this->Fangchan_model->where(array('token'=>$this->token))->find();

		if(IS_POST){	

			$_POST['date']= date("Y-m-d H:i:s ",time());

			if($id = $this->Fangchan_model->add($_POST)){

			

				$this->success('添加成功！',U('Fangchan/index',array('token'=>$this->token)));

			}else{

				$this->error('添加失败！');

			}

		}else{


			$this->assign('set',$set);

			$this->assign('arr',$arr);

			$this->display('set');

		}

	}


	public function set(){

		

        $id = intval($this->_get('id')); 

		$checkdata = M('fangchan')->where(array('id'=>$id))->find();

		if(empty($checkdata)||$checkdata['token']!=$this->token){

            $this->error("没有相应记录.您现在可以添加.",U('Fangchan/add'));

        }

		

		

		if(IS_POST){ 

            $where=array('id'=>$this->_post('id'),'token'=>$this->token);

			$check=$this->Fangchan_model->where($where)->find();

			if($check==false)$this->error('非法操作');

			if($this->Fangchan_model->create()){

				
				//print_r($_POST);die;

				if($this->Fangchan_model->where($where)->save($_POST)){

					$this->success('修改成功',U('Fangchan/index',array('token'=>$this->token)));

					

					

				}else{

					$this->error('操作失败');

				}

			}else{

				$this->error($this->Fangchan_model->getError());

			}

		}else{

			$this->assign('isUpdate',1);

			$this->assign('set',$checkdata);

			$this->assign('arr',$arr);

			$this->assign('act',$id);

			$this->display();	

		

		}

	}

	public function del(){

		if($this->_get('token')!=$this->token){$this->error('非法操作');}

        $id = intval($this->_get('id'));

        if(IS_GET){                              

            $where=array('id'=>$id,'token'=>$this->token);

			$wher=array('pid'=>$id,'token'=>$this->token);

            $check=$this->Fangchan_model->where($where)->find();

            if($check==false)   $this->error('非法操作');

            $back=$this->Fangchan_model->where($where)->delete();

            if($back==true){

			

                $this->success('操作成功',U('Fangchan/index',array('token'=>$this->token,'pid'=>$id)));
				

            }else{

                 $this->error('服务器繁忙,请稍后再试',U('Fangchan/index',array('token'=>$this->token)));

            }

        }        

	}



	public function setcin(){

		$id=$this->_get('pid');
		$title=$this->Zhaopin_model->where(array('token'=>$this->token,'id'=>$id))->find();
		//dump($title);exit;

		$checkdata=$this->Zhaopin_model->where(array('id'=>$id))->find();

	

		$cin=M('Zhaopin_setcin');

		$where = array('pid'=>$id);

		$data=$cin->where($where)->select();

		$count      = $cin->where($where)->count();

		$Page       = new Page($count,20);

		$show       = $Page->show();

		//print_r($data);die;

		$this->assign('id',$id);
		$this->assign('title',$title);

		$this->assign('data',$data);

		$this->assign('set',$checkdata);

		$this->assign('page',$show);

		$this->display();

	}
	

	//增加类型

	public function addcin(){

		$pid = $this->_get('pid');

		$cin=M('Zhaopin_setcin');

		if(IS_POST){

			$_POST['pid']=$pid;


			if($cin->add($_POST)){

				//print_r($_POST);die;

				$this->success('添加成功！',U('Zhaopin/setcin',array('token'=>$this->token,'pid'=>$pid)));

			}else{

				$this->error('添加失败！');

			}

		}else{

			$this->assign('pid',$pid);

			$this->display();

		}

		

	}

	

	//修改类型

	public function updatecin(){

		$id = $this->_get('id');

		$pid = $this->_get('aid');

		$cin=M('Zhaopin_setcin');

		$data=$cin->where(array('id'=>$id))->find();

		

		if(IS_POST){

			//print_r($_POST);die;

			if($cin->where(array('id'=>$id))->save($_POST)){

				$this->success('修改成功！',U('Zhaopin/setcin',array('pid'=>$pid,'token'=>$this->token)));

			}else{

				$this->error('修改失败！');

			}

		}else{

			$this->assign('data',$data);

			$this->assign('id',$pid);

			$this->display('addcin');

		}

	}

	

	//删除类型

	public function delcin(){

		if($this->_get('token')!=$this->token){$this->error('非法操作');}

		$id = intval($this->_get('id'));

		$pid = intval($this->_get('aid'));

		$cin=M('yuyue_setcin');



        if(IS_GET){                              

            $where=array('id'=>$id);

            $check=$cin->where($where)->find();

            if($check==false)   $this->error('非法操作');

            $back=$cin->where($where)->delete();

            if($back==true){

                $this->success('操作成功',U($this->type.'/setcin',array('pid'=>$pid,'token'=>$this->token)));

            }else{

                 $this->error('服务器繁忙,请稍后再试');

            }

        }   

			

	}

	

	//订单设置

		//订单设置

	 public function exportForms()
    {
        $where = array('token' => $this->token);
        $list = M('Fangchan')->where($where)->order('date desc')->select();
        $data = array();
        $title = array('联系人', '联系电话');
        $fields = array('类别','户型','地区','标题','发布时间');
        $title = array_merge($title, $fields);
        foreach ($list as $key => $value) {
            $data[$key][] = $value['contacter'];
			$data[$key][] = $value['phone'];
			$data[$key][] = $value['type'];
			$data[$key][] = $value['houseType'];
			$data[$key][] = $value['area'];
			$data[$key][] = $value['title'];
			$data[$key][] = $value['date'];

            
        }
        $exname = '房源信息';
        $this->exportexcel($data, $title, $exname);
    }
	
    public function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header('Content-type:application/octet-stream');
        header('Accept-Ranges:bytes');
        header('Content-type:application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename=' . $filename . '.xls');
        header('Pragma: no-cache');
        header('Expires: 0');
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv('UTF-8', 'GB2312', $v);
            }
            $title = implode('	', $title);
            echo "{$title}\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv('UTF-8', 'GB2312', $cv);
                }
                $data[$key] = implode('	', $data[$key]);
            }
            echo implode('
', $data);
        }
    }

}





?>