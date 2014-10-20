<?php

//wap

class FangchanAction extends WapAction{

	public $token;

	public $wecha_id;

	public $Fangchan_model;

	public $Fangchan_order;

	public function __construct(){

		

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

		$this->Fangchan_model=M('Fangchan');

	
		

		



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
		$count=M('Fangchan')->where($where)->count();		
		$pagecount=ceil($count/$pageSize);
		if($page > $count){$page=$pagecount;}

		if($page >=1){$p=($page-1)*$pageSize;}

		if($p==false){$p=0;}

		$info = M('Fangchan')->where($where)->order('date DESC')->limit("{$p},".$pageSize)->select();

		$date = M('Fangchan_reply')->where($where)->find();
		
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
		$leibie= $this->_get('type');
		//dump($leibie);exit;

		$where = array('token'=> $this->_get('token'),'type'=> $this->_get('type'));
		if($_GET['p']==false){

			$page=1;

		}else{

			$page=$_GET['p'];			

		}		
		$pageSize=10;
		$count=M('fangchan')->where($where)->count();		
		$pagecount=ceil($count/$pageSize);
		if($page > $count){$page=$pagecount;}

		if($page >=1){$p=($page-1)*$pageSize;}

		if($p==false){$p=0;}
		$info = M('fangchan')->where($where)->order('date DESC')->limit("{$p},".$pageSize)->select();
		
		$date = M('fangchan_reply')->where($where)->find();
		
		$this->assign('info', $info);
		$this->assign('leibie', $leibie);
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
		
		$zp = M('fangchan');	
		$zp->where("token='$token'AND id='$id'")->setInc('click');
		
		$info = M('fangchan')->where($where)->find();
		$info1 = M('fangchan')->where(array('token'=> $this->_get('token')))->order('date DESC')->limit(5)->select();
		
	   $date = M('fangchan_reply')->where(array('token'=> $this->_get('token')))->find();
		
		
		
		$this->assign('info', $info);
		$this->assign('info1', $info1);
		$this->assign('date', $date);

		$this->display();

	}

	public function fabu(){ 
	$agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
          echo '此功能只能在微信浏览器中使用';exit;
        }
	
         $date = M('fangchan_reply')->where(array('token'=> $this->_get('token')))->find();
		

		$_POST['token'] = $this->_get('token');
        	$_POST['wecha_id'] = $this->_get('wecha_id');
		 
		$checkdata = M('fangchan')->where(array('token'=> $this->_get('token')))->find();

		if(IS_POST){	
		if(empty($_POST['title'])){
			echo "<script>alert('标题必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		if(empty($_POST['contacter'])){
			echo "<script>alert('联系人必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		if(empty($_POST['phone'])){
			echo "<script>alert('联系电话必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		$_POST['date']= date("Y-m-d H:i:s ",time());
		if(empty($_POST['area'])){
			echo "<script>alert('所属地区必须填写');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";exit;};
		$_POST['date']= date("Y-m-d H:i:s ",time());
        

			if($id = M('fangchan')->add($_POST)){
					$info=M('deliemail')->where(array('token'=>$this->_get('token')))->find();
			$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
			$emailstatus=$info['fangchan'];
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

					$mail->Subject = '有新的房源信息';
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


				


			

				$this->success('添加成功！',U('Fangchan/index',array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id'))));
				

			}else{

				$this->error('添加失败！');

			}

		}else{


			$this->assign('set',$set);

			$this->assign('arr',$arr);
			$this->assign('date', $date);


			$this->display();

		}

	}

public function sms(){
	
		$this->fangchan=M('fangchan');
		$orders=$this->fangchan->where(array('token'=>$this->_get('token')))->order('date desc')->limit(0,1)->find();
		
		
		
			
			$str="\r\n类别：".$orders['type']."\r\n户型：".$orders['houseType']."\r\n地区：".$orders['area']."\r\n标题：".$orders['title']."\r\n房产详情：".$orders['content']."\r\n联系人：".$orders['contacter']."\r\n联系电话：".$orders['phone']."\r\n";
			
			
			
			return $str;
		
	}

	




	

}





?>