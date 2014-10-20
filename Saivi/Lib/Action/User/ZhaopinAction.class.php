<?php

//web

class ZhaopinAction extends UserAction{

	public $token;

	public $Zhaopin_model;


	public function _initialize() {

		parent::_initialize();

		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();

		if(!strpos($token_open['queryname'],'Zhaopin')){

            	$this->error('您还开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));

		}



		$this->Zhaopin_model=M('Zhaopin');


		$this->token=session('token');

		$this->assign('token',$this->token);

		$this->assign('module','Zhaopin');


	}
	 public function reply(){
	 	$where['token'] = session('token');
		$Cdata = M('Zhaopin_reply');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['copyright'] = strip_tags($_POST['copyright']);
			$data['title'] = strip_tags($_POST['title']);
			$data['tp'] = strip_tags($_POST['tp']);
			$data['jiesao'] = $_POST['jiesao'];
			$data['gdtell'] = strip_tags($_POST['gdtell']);
			$data['sjtell'] = strip_tags($_POST['sjtell']);
			$data['address'] = strip_tags($_POST['address']);
			$data['longitude'] = strip_tags($_POST['longitude']);
			$data['latitude'] = strip_tags($_POST['latitude']);
			
			$data['info'] = strip_tags($_POST['info']);
			
			//$res = M('Vcard')->where($where)->find();
			if($info){
				$result = M('Zhaopin_reply')->where($where)->save($data);
				if($result){
					$this->success('回复信息更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('Zhaopin_reply')->add($data);
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
	public function flash(){
	 	$where['token'] = session('token');
		$Cdata = M('Zhaopin_flash');
		$info = $Cdata->where($where)->find();
		$this->info = $info;
		if(IS_POST){
			$where['token'] = session('token');
			$data['picurl1'] = strip_tags($_POST['picurl1']);
			$data['picurl2'] = strip_tags($_POST['picurl2']);
			$data['picurl3'] = strip_tags($_POST['picurl3']);
			$data['picurl4'] = strip_tags($_POST['picurl4']);
		
			
			//$res = M('Vcard')->where($where)->find();
			if($info){
				$result = M('Zhaopin_flash')->where($where)->save($data);
				if($result){
					$this->success('展示图片更新成功!');
				}else{
					$this->error('服务器繁忙 更新失败!');
				}
			}else{
				$data['token'] = session('token');
				$insert = M('Zhaopin_flash')->add($data);
				if($insert > 0){
					$this->success('展示图片添加成功!');
				}else{
					$this->error('展示图片添加失败!');
				}
			}
		}else{
			$this->display();
		}
	}

		public function index(){

		$where = array('token'=> $this->token);

		$count      = $this->Zhaopin_model->where($where)->count();

		$Page       = new Page($count,20);

		$show       = $Page->show();

		$data = $this->Zhaopin_model->where($where)->order('id desc')->select();

		

		//$type='Yiliao';

		//$this->assign('type',$type);

		$this->assign('page',$show);

		$this->assign('data',$data);

		$this->display();

		

		

		

	}
	public function jianli(){

		$where = array('token'=> $this->token);

		$count      = M('zhaopin_jianli')->where($where)->count();

		$Page       = new Page($count,20);

		$show       = $Page->show();

		$data = M('zhaopin_jianli')->where($where)->order('id desc')->select();

		

		//$type='Yiliao';

		//$this->assign('type',$type);

		$this->assign('page',$show);

		$this->assign('data',$data);

		$this->display();

		

		

		

	}



	public function add(){ 

		

		$_POST['token'] = $this->token;

		 
		$checkdata = $this->Zhaopin_model->where(array('token'=>$this->token,'type'=>$this->type))->find();

		if(IS_POST){	

			$_POST['date']= date("Y-m-d H:i:s ",time());

			if($id = $this->Zhaopin_model->add($_POST)){

			

				$this->success('添加成功！',U('Zhaopin/index',array('token'=>$this->token)));

			}else{

				$this->error('添加失败！');

			}

		}else{


			$this->assign('set',$set);

			$this->assign('arr',$arr);

			$this->display('set');

		}

	}
	public function jladd(){ 

		

		$_POST['token'] = $this->token;

		 
		$checkdata = M('zhaopin_jianli')->where(array('token'=>$this->token,'type'=>$this->type))->find();

		if(IS_POST){	

			$_POST['date']= date("Y-m-d H:i:s ",time());

			if($id =M('zhaopin_jianli')->add($_POST)){

				

				$this->success('添加成功！',U('Zhaopin/jianli',array('token'=>$this->token)));

			}else{

				$this->error('添加失败！');

			}

		}else{


			$this->assign('set',$set);

			$this->assign('arr',$arr);

			$this->display('jlset');

		}

	}

	

	//修改预约

	public function set(){

		

        $id = intval($this->_get('id')); 

		$checkdata = $this->Zhaopin_model->where(array('id'=>$id))->find();

		if(empty($checkdata)||$checkdata['token']!=$this->token){

            $this->error("没有相应记录.您现在可以添加.",U('Zhaopin/add'));

        }

		

		

		if(IS_POST){ 

            $where=array('id'=>$this->_post('id'),'token'=>$this->token);

			$check=$this->Zhaopin_model->where($where)->find();

			if($check==false)$this->error('非法操作');

			if($this->Zhaopin_model->create()){

				
				//print_r($_POST);die;

				if($this->Zhaopin_model->where($where)->save($_POST)){

					$this->success('修改成功',U('Zhaopin/index',array('token'=>$this->token)));

					

					

				}else{

					$this->error('操作失败');

				}

			}else{

				$this->error($this->Zhaopin_model->getError());

			}

		}else{

			$this->assign('isUpdate',1);

			$this->assign('set',$checkdata);

			$this->assign('arr',$arr);

			$this->assign('act',$id);

			$this->display();	

		

		}

	}
		public function jlset(){

		

        $id = intval($this->_get('id')); 

		$checkdata = M('zhaopin_jianli')->where(array('id'=>$id))->find();

		if(empty($checkdata)||$checkdata['token']!=$this->token){

            $this->error("没有相应记录.您现在可以添加.",U('Zhaopin/jladd'));

        }

		

		

		

		if(IS_POST){ 

            $where=array('id'=>$this->_post('id'),'token'=>$this->token);

			$check=M('zhaopin_jianli')->where($where)->find();

			if($check==false)$this->error('非法操作');

			if(M('zhaopin_jianli')->create()){

				

				//print_r($_POST);die;

				if($this->Zhaopin_model->where($where)->save($_POST)){

					$this->success('修改成功',U('Zhaopin/index',array('token'=>$this->token)));


				}else{

					$this->error('操作失败');

				}

			}else{

				$this->error($this->Zhaopin_model->getError());

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

            $check=$this->Zhaopin_model->where($where)->find();

            if($check==false)   $this->error('非法操作');

            $back=$this->Zhaopin_model->where($where)->delete();

            if($back==true){

			

                $this->success('操作成功',U('Zhaopin/index',array('token'=>$this->token,'pid'=>$id)));
				

            }else{

                 $this->error('服务器繁忙,请稍后再试',U('Zhaopin/index',array('token'=>$this->token)));

            }

        }        

	}
public function jldel(){

		if($this->_get('token')!=$this->token){$this->error('非法操作');}

        $id = intval($this->_get('id'));

        if(IS_GET){                              

            $where=array('id'=>$id,'token'=>$this->token);

			$wher=array('pid'=>$id,'token'=>$this->token);

            $check=M('zhaopin_jianli')->where($where)->find();

            if($check==false)   $this->error('非法操作');

            $back=M('zhaopin_jianli')->where($where)->delete();

            if($back==true){

				

			

            	

                $this->success('操作成功',U('Zhaopin/jianli',array('token'=>$this->token,'pid'=>$id)));
				

            }else{

                 $this->error('服务器繁忙,请稍后再试',U('Zhaopin/jianli',array('token'=>$this->token)));

            }

        }        

	}

	//订单列表显示

	public function infos(){

		

		$pid=$this->_get('pid');
		$where= array('token'=> $this->token,'pid'=>$pid);
		$count = $this->yuyue_order->where($where)->count();
	
		$Page = new Page($count,20);

		$show = $Page->show();

		$data = $this->yuyue_order->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		//
		 if(isset($_GET['download'])){
            $reports = array();
            if(!empty($data)){
                foreach($data as $rs){
                    $_rs = array(
                        'name' => $rs['name'],
                        'phone' => $rs['phone'],
						'time' => $rs['time'],
						'or_date' => $rs['or_date'],
						'time' => $rs['time'],
						'kind' => $rs['kind'],
						 'date' => $rs['date'],
						
                    );
                    $reports[] = $_rs;
                }
            }
            $keynames = array('name'=>'姓名','phone'=>'电话','kind'=>'类型','or_date'=>'预订时间','time'=>'预约时段','kind'=>'预订类型','date'=>'下单时间');
            $name = "预约订单数据_".date('Ymd');
            $this -> generate_xls($keynames, $reports, $name);
        }
		//

		$this->assign('page',$show);

		$this->assign('data', $data);
		$this->assign('pid', $pid);

		

		$this->display();
	

	}

	

	//订单详细信息

	public function infos_detail(){

		$where = array('token'=> $this->token,'id'=>$this->_get('id'));

		$data = $this->yuyue_order->where($where)->order('id desc')->select();

		$info=$data[0]['fieldsigle'].$data[0]['fielddownload'];

		$info=substr($info,1);

		$info=explode('$',$info);

		$detail=array();

		foreach($info as $v){

			$detail['info'][]=explode('#',$v);	

		}

		$detail['all']=$data[0];



		$this->assign('detail', $detail);

		$this->display();

	}

	

	//删除订单

	public function delinfos(){

		if($this->_get('token')!=$this->token){$this->error('非法操作');}

        $id = intval($this->_get('id'));

        if(IS_GET){                              

            $where=array('id'=>$id,'token'=>$this->token);

            $check=M('yuyue_order')->where($where)->find();

            if($check==false)   $this->error('非法操作');

            $back=M('yuyue_order')->where($where)->delete();

            if($back==true){

                $this->success('操作成功',U($this->type.'/infos',array('token'=>$this->token,'pid'=>$check['pid'])));

            }else{

                 $this->error('服务器繁忙,请稍后再试',U($this->type.'/infos',array('token'=>$this->token,'id'=>$check['xid'])));

            }

        }        

	}

	

	//处理订单

	public function setType(){

		if($this->_get('token')!=$this->token){$this->error('非法操作');}

        $id = intval($this->_get('id'));

		$type = intval($this->_get('type'));

		$pid = intval($this->_get('pid'));

        if(IS_GET){                              

			$where = array(

				'id'=> $id,
				

				'token'=> $this->token,
				

			);

			$data = array(

				'type'=> $type
				
				

			);

			if($this->yuyue_order->where($where)->setField($data)){

				$this->success('修改成功！',U($this->type.'/infos',array('pid'=>$pid,'token'=>$this->token)));

			}else{

				$this->error('修改失败！');

			}

        }

	}

	

	public function inputs(){

		$where['xid'] = $this->_get('id');

		$where['token'] = $this->_get('token');

		if(IS_POST){

			$key = $this->_post('searchkey');

			if(empty($key)){

				$this->error("关键词不能为空");

			}



			$where['name'] = array('like',"%$key%");

			$list = M('Canyu')->where($where)->order('time DESC')->select();

			$count      = M('Canyu')->where($where)->count();

			$Page       = new Page($count,20);

			$show       = $Page->show();

			$this->assign('key',$key);

		}else {

			$count      = M('Canyu')->where($where)->count();

			

			$Page       = new Page($count,20);

			$show       = $Page->show();

			$list=M('Canyu')->where($where)->order('time DESC')->select();

		}

		$num = 0;

		foreach($list as $key=>$val){

			$num += $val['number'];

		}

		

		$this->assign('num',$num);

		$this->assign('list',$list);

		$this->assign('page',$show);

		$this->display();

	}

	

	//子分类设置

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

	 public function setinfo(){ 

		$pid=$this->_get('pid');

		$checkdata=$this->Zhaopin_model->where(array('pid'=>$pid))->find();

		

		$_POST['token'] = $this->token;
		

		//print_r($_GET["token"]);die;

		$setinfo=M('setinfo');

		$data=$setinfo->where(array('token'=>$this->token,'type'=>$this->type,'pid'=>$pid))->select();

		$str=array();

		if(!empty($data)){

			foreach($data as $v){

				$str[$v["name"]]=$v["value"];

			}

		}else{

			$str=array("person" => 1 ,"phone" => 1 ,"date" => 1 ,"time" => 1,);

			$setinfo->add(array('token'=>$this->token,'name'=>'person','value'=>1,'kind'=>1,'type'=>$this->type,'pid'=>$pid));

			$setinfo->add(array('token'=>$this->token,'name'=>'phone','value'=>1,'kind'=>1,'type'=>$this->type,'pid'=>$pid));

			$setinfo->add(array('token'=>$this->token,'name'=>'date','value'=>1,'kind'=>2,'type'=>$this->type,'pid'=>$pid));

			$setinfo->add(array('token'=>$this->token,'name'=>'time','value'=>1,'kind'=>2,'type'=>$this->type,'pid'=>$pid));

			$setinfo->add(array('token'=>$this->token,'name'=>'留言','kind'=>5,'type'=>$this->type,'pid'=>$pid));

		}

		$this->assign('data',$str);

		$arr=$setinfo->where(array('token'=>$this->token,'kind'=>'3','type'=>$this->type,'pid'=>$pid))->select();

		if(empty($arr[0][name])){

			$arr[0][name]="预定人数";

			$arr[0][value]="请输入具体人数";

		}

		//print_r($arr);die;

		$this->assign('arr',$arr);

		$list=$setinfo->where(array('token'=>$this->token,'kind'=>'4','type'=>$this->type,'pid'=>$pid))->select();

		if(empty($list[0][name])){

			$list[0][name]="选择房间标准";

			$list[0][value]="单人房|双人房|标准房|豪华房|总统房";

		}

		//print_r($list);die;

		$this->assign('list',$list);

		$line=$setinfo->where(array('token'=>$this->token,'kind'=>'5','type'=>$this->type,'pid'=>$pid))->select();

		$this->assign('line',$line);

		$check=0;

		//print_r($_POST["person"]);die;

		if(IS_POST){

			//print_r($_POST);die;

			foreach($arr as $key=> $val){

				$id[]=$val['id'];

			}

			foreach($list as $key=> $val){

				$id[]=$val['id'];

			}

			//print_r($id);die;

			for($i=0;$i<12;$i++){

				 //echo $_POST['name'.$i];

				 

				 if($_POST['name'.$i]!=""){

				 

					//echo "/3333";

					//$count=$setinfo->count('id');

					$add['value'] = 1;

					$add['token'] = $_POST['token'];

					$add['type'] = $this->type;

					$add['id']=$_POST['id'.$i];

					if(!empty($add['id'])&&in_array($add['id'],$id)){

						//echo $add['id']."kk";

						$setinfo->where(array('id'=>$add['id']))->save(array('name'=>$_POST['name'.$i],'value'=>$_POST['content'.$i]));

						$check++;

					}else{

						if($i<6){

							//$add['orderid'] = $count;

							$add['name']= $_POST['name'.$i];

							$add['value'] = $_POST['content'.$i];

							$add['kind']= '3';

							$add['pid']=$pid;

							//echo "die;";die;

							$setinfo->add($add);

							$check++;

						// }elseif($i!=11){

							// $add['name']= $_POST['name'.$i];

							// $add['show']=1;

							// $add['token']=this->$token;

							// $setinfo->add($add);

						}else{

							$add['name']= $_POST['name'.$i];

							$add['value'] = $_POST['content'.$i];

							$add['kind']= '4';

							$add['pid']= $pid;

							$add['type'] = $this->type;

							$setinfo->add($add);

							$check++;

							

						}

					}

					

				 }else{

					$add['id']=$_POST['id'.$i];

					if(in_array($add['id'],$id)){

						$setinfo->where(array('id'=>$add['id']))->delete();

						$check++;

					}

				 }



			}

			if(!empty($_POST['id'])){

					$setinfo->where(array('id'=>$_POST['id']))->save(array('name'=>$_POST['textname'],'value'=>$_POST['text'],'pid'=>$pid));

					$check++;

			}



		

		}

		if($check != 0 ){

			$setinfo->where(array('token'=>$this->token,'name'=>'person','type'=>$this->type,'pid'=>$pid))->save(array('value'=>$_POST['person']));

			$setinfo->where(array('token'=>$this->token,'name'=>'phone','type'=>$this->type,'pid'=>$pid))->save(array('value'=>$_POST['phone']));

			$setinfo->where(array('token'=>$this->token,'name'=>'date','type'=>$this->type,'pid'=>$pid))->save(array('value'=>$_POST['date']));

			$setinfo->where(array('token'=>$this->token,'name'=>'time','type'=>$this->type,'pid'=>$pid))->save(array('value'=>$_POST['time']));



			$this->success('修改成功！',U($this->type.'/setinfo',array('token'=>$this->token,'pid'=>$pid)));die;

		}

		

		$this->display();

	}
	 public function exportForms()
    {
        $where = array('token' => $this->token);
        $list = M('zhaopin')->where($where)->order('date desc')->select();
        $data = array();
        $title = array('企业名称', '招聘岗位');
        $fields = array('岗位分类','招聘人数','薪资水平','工作地点','联系人','联系电话','发布时间');
        $title = array_merge($title, $fields);
        foreach ($list as $key => $value) {
            $data[$key][] = $value['jigou'];
			$data[$key][] = $value['gangwei'];
			$data[$key][] = $value['leibie'];
			$data[$key][] = $value['ren'];
			$data[$key][] = $value['xinzi'];
			$data[$key][] = $value['address'];
			$data[$key][] = $value['people'];
			$data[$key][] = $value['tell'];
            $data[$key][] =  $value['date'];
            
        }
        $exname = '招聘信息';
        $this->exportexcel($data, $title, $exname);
    }
	 public function exportFormss()
    {
        $where = array('token' => $this->token);
        $list = M('zhaopin_jianli')->where($where)->order('date desc')->select();
        $data = array();
        $title = array('姓名', '性别');
       $fields = array('年龄','联系电话','学历','期望工作','期望薪资','期望工作地点','发布时间');
        $title = array_merge($title, $fields);
        foreach ($list as $key => $value) {
            $data[$key][] = $value['name'];
			$data[$key][] = $value['sex'];
			$data[$key][] = $value['age'];
		
			$data[$key][] = $value['phone'];
			$data[$key][] = $value['education'];
			$data[$key][] = $value['job'];
			$data[$key][] = $value['salary'];
            $data[$key][] =  $value['workarea'];
			 $data[$key][] =  $value['date'];
          
        }
        $exname = '简历信息';
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