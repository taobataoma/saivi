<?php

//wap

class ZhaopinAction extends WapAction{

	public $token;

	public $wecha_id;

	public $Zhaopin_model;

	

	public function __construct(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            echo '此功能只能在微信浏览器中使用';exit;
        }

		

		parent::__construct();

		$this->token=session('token');

		// $this->token = $this->_get('token');

		$this->assign('token',$this->token);

		$this->wecha_id	= $this->_get('wecha_id');

		if (!$this->wecha_id){

			$this->wecha_id='null';

		}
			$where['token']=$this->token;
		$kefu=M('Kefu')->where($where)->find();
		$this->assign('kefu',$kefu);

		$this->assign('wecha_id',$this->wecha_id);

		$this->Zhaopin_model=M('Zhaopin');

		
		

		



	}



	

	//预约列表

	//预约列表
	public function index(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            echo '此功能只能在微信浏览器中使用';exit;
        }

		$where = array('token'=> $this->_get('token'));
		if($_GET['p']==false){

			$page=1;

		}else{

			$page=$_GET['p'];			

		}		
		$pageSize=10;
		$count=M('zhaopin')->where($where)->count();		
		$pagecount=ceil($count/$pageSize);
		if($page > $count){$page=$pagecount;}

		if($page >=1){$p=($page-1)*$pageSize;}

		if($p==false){$p=0;}

		$info = M('zhaopin')->where($where)->order('date DESC')->limit("{$p},".$pageSize)->select();

		$date = M('zhaopin_reply')->where($where)->find();
		
		$this->assign('info', $info);
		$this->assign('date', $date);
		$this->assign('page',$pagecount);

		$this->assign('p',$page);
		$this->display();
	}
	public function index1(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
           echo '此功能只能在微信浏览器中使用';exit;
        }
		$leibie= $this->_get('leibie');
		//dump($leibie);exit;

		$where = array('token'=> $this->_get('token'),'leibie'=> $this->_get('leibie'));
		if($_GET['p']==false){

			$page=1;

		}else{

			$page=$_GET['p'];			

		}		
		$pageSize=10;
		$count=M('zhaopin')->where($where)->count();		
		$pagecount=ceil($count/$pageSize);
		if($page > $count){$page=$pagecount;}

		if($page >=1){$p=($page-1)*$pageSize;}

		if($p==false){$p=0;}
		$info = M('zhaopin')->where($where)->order('date DESC')->limit("{$p},".$pageSize)->select();
		
		$date = M('zhaopin_reply')->where($where)->find();
		
		$this->assign('info', $info);
		$this->assign('leibie', $leibie);
		$this->assign('date', $date);
		$this->assign('page',$pagecount);

		$this->assign('p',$page);
		$this->display();
	}
	public function hot(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
           echo '此功能只能在微信浏览器中使用';exit;
        }

		$where = array('token'=> $this->_get('token'));
		if($_GET['p']==false){

			$page=1;

		}else{

			$page=$_GET['p'];			

		}		
		$pageSize=10;
		$count=M('zhaopin')->where($where)->count();		
		$pagecount=ceil($count/$pageSize);
		if($page > $count){$page=$pagecount;}

		if($page >=1){$p=($page-1)*$pageSize;}

		if($p==false){$p=0;}
		$info = M('zhaopin')->where($where)->order('click DESC')->limit("{$p},".$pageSize)->select();
		
		$date = M('zhaopin_reply')->where($where)->find();
		
		$this->assign('info', $info);
		$this->assign('date', $date);
		$this->assign('page',$pagecount);

		$this->assign('p',$page);
		$this->display();
	}
	public function jlhot(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
          echo '此功能只能在微信浏览器中使用';exit;
        }

		$where = array('token'=> $this->_get('token'));
		if($_GET['p']==false){

			$page=1;

		}else{

			$page=$_GET['p'];			

		}		
		$pageSize=10;
		$count=M('zhaopin_jianli')->where($where)->count();		
		$pagecount=ceil($count/$pageSize);
		if($page > $count){$page=$pagecount;}

		if($page >=1){$p=($page-1)*$pageSize;}

		if($p==false){$p=0;}
		$info = M('zhaopin_jianli')->where($where)->order('click DESC')->limit("{$p},".$pageSize)->select();
		
		$date = M('zhaopin_reply')->where($where)->find();
		
		$this->assign('info', $info);
		$this->assign('date', $date);
		$this->assign('page',$pagecount);

		$this->assign('p',$page);
		$this->display();
	}
	public function jlindex(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
           echo '此功能只能在微信浏览器中使用';exit;
        }

		$where = array('token'=> $this->_get('token'));
		if($_GET['p']==false){

			$page=1;

		}else{

			$page=$_GET['p'];			

		}		
		$pageSize=10;
		$count=M('zhaopin_jianli')->where($where)->count();		
		$pagecount=ceil($count/$pageSize);
		if($page > $count){$page=$pagecount;}

		if($page >=1){$p=($page-1)*$pageSize;}

		if($p==false){$p=0;}

		$info = M('zhaopin_jianli')->where($where)->order('date DESC')->limit("{$p},".$pageSize)->select();

		$date = M('zhaopin_reply')->where($where)->find();
		
		$this->assign('info', $info);
		$this->assign('date', $date);
		$this->assign('page',$pagecount);

		$this->assign('p',$page);
		$this->display();
	}
	
	public function info(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
           echo '此功能只能在微信浏览器中使用';exit;
        }
		
		$id = $this->_get('id');
		$where = array('token'=> $this->_get('token'),'id'=>$id);
		$token=$this->_get('token');
		
		$zp = M('zhaopin');	
		$zp->where("token='$token'AND id='$id'")->setInc('click');
		
		$info = M('zhaopin')->where($where)->find();
		$info1 = M('zhaopin')->where(array('token'=> $this->_get('token')))->order('date DESC')->limit(5)->select();
		
	   $date = M('zhaopin_reply')->where(array('token'=> $this->_get('token')))->find();
		
		
		
		$this->assign('info', $info);
		$this->assign('info1', $info1);
		$this->assign('date', $date);

		$this->display();

	}
	public function jlinfo(){
		 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
           echo '此功能只能在微信浏览器中使用';exit;
        }
		
		$id = $this->_get('id');
		$where = array('token'=> $this->_get('token'),'id'=>$id);
		$token=$this->_get('token');
		
		$zp = M('zhaopin_jianli');	
		$zp->where("token='$token'AND id='$id'")->setInc('click');
		
		$info = M('zhaopin_jianli')->where($where)->find();
		$info1 = M('zhaopin_jianli')->where(array('token'=> $this->_get('token')))->order('date DESC')->limit(5)->select();
		
	   $date = M('zhaopin_reply')->where(array('token'=> $this->_get('token')))->find();
		
		
		
		$this->assign('info', $info);
		$this->assign('info1', $info1);
		$this->assign('date', $date);

		$this->display();

	}

	

	public function geren(){ 
	 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
           echo '此功能只能在微信浏览器中使用';exit;
        }
       
		

		$_POST['token'] = $this->_get('token');
		$_POST['wecha_id'] = $this->_get('wecha_id');
        $_POST['date']= date("Y-m-d H:i:s ",time());
		 
		$checkdata = M('zhaopin_jianli')->where(array('token'=> $this->_get('token')))->find();

		if(IS_POST){	
		if(empty($_POST['phone'])){
			echo "<script>alert('联系电话必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		if(empty($_POST['name'])){
			echo "<script>alert('姓名必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		if(empty($_POST['age'])){
			echo "<script>alert('年龄必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		if(empty($_POST['job'])){
			echo "<script>alert('期望工作必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};	
        

			if($id = M('zhaopin_jianli')->add($_POST)){
				
					$info=M('deliemail')->where(array('token'=>$this->_get('token')))->find();
			$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
			$emailstatus=$info['jianli'];
			$emailreceive=$info['receive'];
			$content = $this->sms1();
			if($info['type'] == 1){
			$emailsmtpserver=$info['smtpserver'];
			$emailport=$info['port'];
			$emailsend=$info['name'];
			$emailpassword=$info['password'];
			}else{
			$emailsmtpserver=C('email_server');
			$emailport=C('email_port');
			$emailsend=C('email_user');
			$emailpassword=C('email_pwd');
			}
			$emailuser=explode('@', $emailsend);
			$emailuser=$emailuser[0];
			if ($emailstatus == 1) {
				if ($content) {
					date_default_timezone_set('PRC');
					require("class.phpmailer.php");
					$mail = new PHPMailer();
					$mail->IsSMTP();                                      // set mailer to use SMTP
					$mail->Host = "$emailsmtpserver";  // specify main and backup server
					$mail->SMTPAuth = true;     // turn on SMTP authentication
					$mail->Username = "$emailuser"; // SMTP username
					$mail->Password = "$emailpassword"; // SMTP password
					$mail->From = $emailsend;
					$mail->FromName = C('site_name');
					$mail->AddAddress("$emailreceive", "商户");
					//$mail->AddAddress("ellen@example.com");                  // name is optional
					$mail->AddReplyTo($emailsend, "Information");

					$mail->WordWrap = 50;                                 // set word wrap to 50 characters
					//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
					//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
					$mail->IsHTML(false);                                  // set email format to HTML

					$mail->Subject = '有新的简历投放';
					$mail->Body    = $content;
					$mail->AltBody = "";

					if(!$mail->Send())
					{
					   echo "Message could not be sent. <p>";
					   echo "Mailer Error: " . $mail->ErrorInfo;
					   exit;
					}
					//echo "Message has been sent";    
				}
			}


				


			

				$this->success('添加成功！',U('Zhaopin/jlindex',array('token'=>$this->_get('token'))));
				

			}else{

				$this->error('添加失败！');

			}

		}else{


			$this->assign('set',$set);

			$this->assign('arr',$arr);

			$this->display();

		}

	}
	public function qiye(){ 
	 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
          echo '此功能只能在微信浏览器中使用';exit;
        }

		

		$_POST['token'] = $this->_get('token');
       	$_POST['wecha_id'] = $this->_get('wecha_id');
		 $_POST['date']= date("Y-m-d H:i:s ",time());
		$checkdata = M('zhaopin')->where(array('token'=> $this->_get('token')))->find();

		if(IS_POST){	
	
		if(empty($_POST['jigou'])){
			echo "<script>alert('企业名称必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		if(empty($_POST['gangwei'])){
			echo "<script>alert('招聘职位必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};	
		if(empty($_POST['address'])){
			echo "<script>alert('工作地点必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		if(empty($_POST['people'])){
			echo "<script>alert('联系人必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};			
		if(empty($_POST['tell'])){
			echo "<script>alert('联系电话必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};				

			

			if($id = M('zhaopin')->add($_POST)){
				$info=M('deliemail')->where(array('token'=>$this->_get('token')))->find();
			$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
			$emailstatus=$info['zhaopin'];
			$emailreceive=$info['receive'];
			$content = $this->sms();
			if($info['type'] == 1){
			$emailsmtpserver=$info['smtpserver'];
			$emailport=$info['port'];
			$emailsend=$info['name'];
			$emailpassword=$info['password'];
			}else{
			$emailsmtpserver=C('email_server');
			$emailport=C('email_port');
			$emailsend=C('email_user');
			$emailpassword=C('email_pwd');
			}
			$emailuser=explode('@', $emailsend);
			$emailuser=$emailuser[0];
			if ($emailstatus == 1) {
				if ($content) {
					date_default_timezone_set('PRC');
					require("class.phpmailer.php");
					$mail = new PHPMailer();
					$mail->IsSMTP();                                      // set mailer to use SMTP
					$mail->Host = "$emailsmtpserver";  // specify main and backup server
					$mail->SMTPAuth = true;     // turn on SMTP authentication
					$mail->Username = "$emailuser"; // SMTP username
					$mail->Password = "$emailpassword"; // SMTP password
					$mail->From = $emailsend;
					$mail->FromName = C('site_name');
					$mail->AddAddress("$emailreceive", "商户");
					//$mail->AddAddress("ellen@example.com");                  // name is optional
					$mail->AddReplyTo($emailsend, "Information");

					$mail->WordWrap = 50;                                 // set word wrap to 50 characters
					//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
					//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
					$mail->IsHTML(false);                                  // set email format to HTML

					$mail->Subject = '有新的招聘信息';
					$mail->Body    = $content;
					$mail->AltBody = "";

					if(!$mail->Send())
					{
					   echo "Message could not be sent. <p>";
					   echo "Mailer Error: " . $mail->ErrorInfo;
					   exit;
					}
					//echo "Message has been sent";    
				}
			}

				


			

				$this->success('添加成功！',U('Zhaopin/index',array('token'=>$this->_get('token'))));

			}else{

				$this->error('添加失败！');

			}

		}else{


			$this->assign('set',$set);

			$this->assign('arr',$arr);

			$this->display();

		}

	}

public function sms(){
	
		$this->zhaopin=M('zhaopin');
		$orders=$this->zhaopin->where(array('token'=>$this->_get('token')))->order('date desc')->limit(0,1)->find();
		
		
		
			
			$str="\r\n企业名称：".$orders['jigou']."\r\n招聘岗位：".$orders['job']."\r\n岗位分类：".$orders['leibie']."\r\n招聘人数：".$orders['ren']."\r\n薪资水平：".$orders['xinzi']."\r\n工作地点：".$orders['address']."\r\n岗位要求：".$orders['yaoqiu']."\r\n联系人：".$orders['people']."\r\n联系电话：".$orders['tell']."\r\n";
			
			
			
			return $str;
		
	}
	public function sms1(){
	
		$this->zhaopin=M('zhaopin_jianli');
		$orders=$this->zhaopin->where(array('token'=>$this->_get('token')))->order('date desc')->limit(0,1)->find();
		
		
		
			
			$str="\r\n姓名：".$orders['name']."\r\n性别：".$orders['sex']."\r\n年龄：".$orders['age']."\r\n联系电话：".$orders['phone']."\r\n学历：".$orders['education']."\r\n期望工作：".$orders['job']."\r\n期望薪资：".$orders['salary']."\r\n期望工作地点：".$orders['workarea']."\r\n自我简介：".$orders['introduce']."\r\n";
			
			
			
			return $str;
		
	}
	
	


	//订单列表

	public function order(){
		
		$id = $this->_get('id');

		$token = $this->_get('token');

		$wecha_id = $this->_get('wecha_id');

		$where = array(

			'wecha_id'=> $wecha_id,

			'pid'=> $id

		);

		$data = $this->yuyue_order->where($where)->order('id desc')->select();

		$info= $this->Yuyue_model->where(array('token'=> $this->_get('token'),'id'=>$id))->find();
		$info1 = $this->Yuyue_model->where(array('token'=> $this->_get('token'),'id'=>$id))->find();
		
		

		//print_r($data);die;

		$this->assign('data',$data);

		$this->assign('info',$info);
		$this->assign('info1',$info1);

		$this->display();


	}

	

	//修改订单视图

	public function set(){
		$where = array('token'=> $this->_get('token'),'type'=>$this->type);

		$id = $this->_get('id');
		

		$pid = $this->_get('pid');
		
		$data1 = M('yuyue')->where($where)->find();
		
		$data = M('yuyue_order')->where(array('id'=>$id))->find();
		$info = M('yuyue_setcin')->where(array('name'=>$data['kind']))->find();
		

		$data['pid'] = $pid;

		$data['id'] = $id;
		//2014.4.22
		$wap= M('setinfo')->where(array('pid'=>$pid))->select();

		$str=array();

		foreach($wap as $v){

			if($v['kind']==5){

				$str["message"]=$v["name"];

			}

			else{

				$str[$v["name"]]=$v["value"];

			}
			

		}
		
       
		
		//print_r($str);die;

		$arr= M('setinfo')->where(array('kind'=>'3','pid'=>$pid))->select();
		$list= M('setinfo')->where(array('kind'=>'4','pid'=>$pid))->select();
		$i=0;


		foreach($list as $v){

			$list[$i]['value']= explode("|",$v['value']);

			$i++;

		}

		//print_r($data);die;

		

		$this->assign('str', $str);

		$this->assign('arr',$arr);

		$this->assign('list',$list);

		$this->assign('list_arr',$list);

		
		
		
		

		$this->assign('data',$data);
		$this->assign('data1',$data1);
		$this->assign('info', $info);

		$this->display();

	}

	

	//修改订单

	public function runSet(){

	

		$id = $_GET['id']; 

		if(IS_POST){

			$url = U('Yuyue/order',array('token'=>$_POST['token'], 'wecha_id'=>$_POST['wecha_id'],'id'=>$_POST['pid'],));
			$url = substr($url,1);

			$where = array(

				'id' =>$id

			);

			if($this->yuyue_order->where($where)->save($_POST)){

				$json = array(

					'error'=> 1,

					'msg'=> '修改成功！',

					'url'=> $url

				);

				echo  json_encode($json);

			}else{

				$json = array(

					'error'=> 0,

					'msg'=> '修改失败！',

					'url'=> $url

				);

				echo  json_encode($json);

			}

		}

		

	}

	

	//删除订单

	public function del(){

		if(IS_POST){

			$url = U('Yuyue/order',array('token'=>$_POST['token'], 'wecha_id'=>$_POST['wecha_id'],'id'=>$_POST['pid'],));
			$url = substr($url,1);

			$where = array(

				'id' =>$_POST['id']

			);

			if($this->yuyue_order->where($where)->delete()){

				$json = array(

					'error'=> 1,

					'msg'=> '删除成功！',

					'url'=> $url

				);

				echo  json_encode($json);

			}else{

				$json = array(

					'error'=> 0,

					'msg'=> '删除失败！',

					'url'=> $url

				);

				echo  json_encode($json);

			}

		}

	}

	

}





?>