<?php
class SelfformAction extends BaseAction{
	public $token;
	public $wecha_id;
	public $selfform_model;
	public $selfform_input_model;
	public $selfform_value_model;
	public function __construct(){
		parent::__construct();
		$this->token		= $this->_get('token');
		$this->assign('token',$this->token);
		$this->wecha_id	= $this->_get('wecha_id');
		if (!$this->wecha_id){
			$this->wecha_id='null';
		}
		$this->assign('wecha_id',$this->wecha_id);
		//
		$this->selfform_model=M('Selfform');
		$this->selfform_input_model=M('Selfform_input');
		$this->selfform_value_model=M('Selfform_value');
		$this->assign('staticFilePath',str_replace('./','/',THEME_PATH.'common/css/product'));
	}
	public function index(){
		$formid=intval($_GET['id']);
		$thisForm=$this->selfform_model->where(array('id'=>$formid))->find();
		$thisForm['successtip']=$thisForm['successtip']==''?'提交成功':$thisForm['successtip'];
		$this->assign('thisForm',$thisForm);
		$where=array('formid'=>$formid);
		$list = $this->selfform_input_model->where($where)->order('taxis ASC')->select();
		$listByKey=array();
		if ($list){
			$i=0;
			foreach ($list as $l){
				if ($l['inputtype']=='select'){
					$options=explode('|',$l['options']);
					$optionStr='<option value="" selected>请选择'.$l['displayname'].'</option>';
					if ($options){
						foreach ($options as $o){
							$optionStr.='<option value="'.$o.'">'.$o.'</option>';
						}
					}
					$list[$i]['optionStr']=$optionStr;
				}
				if ($l['errortip']==''){
					$list[$i]['errortip']='请输入'.$l['displayname'];
				}
				$listByKey[$l['fieldname']]=$l;
				$i++;
			}
		}
		if (IS_POST){
			$row=array();
			$fields=array();
			if ($list){
				foreach ($list as $l){
					$fields[$l['fieldname']]=$_POST[$l['fieldname']];
				}
			}
			$row['values']=serialize($fields);
			$row['formid']=$thisForm['id'];
			$row['wecha_id']=$this->wecha_id;
			$row['time']=time();
			$submitInfo=$this->selfform_value_model->where(array('wecha_id'=>$this->wecha_id,'formid'=>$thisForm['id']))->find();
			if (!$submitInfo){
				$this->selfform_value_model->add($row);
				$info=M('delisms')->where(array('token'=>$this->token))->find();
			$phone=$info['phone'];
			$user=$info['name'];//短信平台帐号
			$pass=md5($info['password']);//短信平台密码
			$smsstatus=$info['baom'];//短信平台状态
			$content = $this->sms();
			if ($smsstatus == 1) {
				if ($content) {
					$smsrs = file_get_contents('http://api.smsbao.com/sms?u='.$user.'&p='.$pass.'&m='.$phone.'&c='.urlencode($content));
					//$log = file_get_contents('http://www.test.com/test.php?u=' . $user . '&p=' . $pass . '&m=' . $phone . '&test=' . urlencode($content));
				}
			}
			//发送短信通知结束

			// 增加 发送邮件
			$info=M('deliemail')->where(array('token'=>$this->token))->find();
			$emailstatus=$info['baom'];
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

					$mail->Subject = '您的活动报名订单';
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
			}
			$this->redirect(U('Selfform/index',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'id'=>$thisForm['id'],'success'=>1)));
		}else {
			//判断是否提交过信息了
			$submitInfo=$this->selfform_value_model->where(array('wecha_id'=>$this->wecha_id,'formid'=>$thisForm['id']))->find();
			if ($submitInfo){
				$info=unserialize($submitInfo['values']);
				if ($info){
					foreach ($info as $k=>$v){
						$info[$k]=array('displayname'=>$listByKey[$k]['displayname'],'value'=>$v);
					}
				}
				$this->assign('submitInfo',$info);
				$submitted=1;
				//二维码图片
				$imgSrc=generateQRfromGoogle(C('site_url').'/index.php?g=Wap&m=Selfform&a=submitInfo&token='.$this->token.'&wecha_id='.$this->wecha_id.'&id='.$thisForm['id']);
				
				$this->assign('imgSrc',$imgSrc);
			}else {
				$submitted=0;
			}
			$this->assign('submitted',$submitted);
			$this->assign('list',$list);
			$this->display();
		}
	}
	public function sms(){
	
		$this->selfform=M('selfform_value');
		$orders=$this->selfform->where(array('token'=>$this->token))->order('time desc')->limit(0,1)->find();
		
		
		
			
			$str="\r\n恭喜您有新的活动报名订单\r\n下单时间：".date('Y-m-d H:i:s',$orders['time'])."\r\n";
			
			
			
			return $str;
		
	}
	public function detail(){
		$formid=intval($_GET['id']);
		$thisForm=$this->selfform_model->where(array('id'=>$formid))->find();
		$thisForm['content']=html_entity_decode($thisForm['content']);
		//$thisForm['intro']=str_replace(array('&lt;','&gt;','&quot;','&amp;nbsp;'),array('<','>','"',' '),$thisForm['intro']);
		$this->assign('thisForm',$thisForm);
		$this->display();
	}
	public function submitInfo(){
		$formid=intval($_GET['id']);
		$thisForm=$this->selfform_model->where(array('id'=>$formid))->find();
		$thisForm['successtip']=$thisForm['successtip']==''?'提交成功':$thisForm['successtip'];
		$this->assign('thisForm',$thisForm);
		$where=array('formid'=>$formid);
		$list = $this->selfform_input_model->where($where)->order('taxis ASC')->select();
		$listByKey=array();
		if ($list){
			$i=0;
			foreach ($list as $l){
				if ($l['inputtype']=='select'){
					$options=explode('|',$l['options']);
					$optionStr='<option value="" selected>请选择'.$l['displayname'].'</option>';
					if ($options){
						foreach ($options as $o){
							$optionStr.='<option value="'.$o.'">'.$o.'</option>';
						}
					}
					$list[$i]['optionStr']=$optionStr;
				}
				if ($l['errortip']==''){
					$list[$i]['errortip']='请输入'.$l['displayname'];
				}
				$listByKey[$l['fieldname']]=$l;
				$i++;
			}
		}
		$submitInfo=$this->selfform_value_model->where(array('wecha_id'=>$this->wecha_id,'formid'=>$thisForm['id']))->find();
		if ($submitInfo){
			$info=unserialize($submitInfo['values']);
			if ($info){
				foreach ($info as $k=>$v){
					$info[$k]=array('displayname'=>$listByKey[$k]['displayname'],'value'=>$v);
				}
			}
			$this->assign('submitInfo',$info);
		}else {
			$submitted=0;
		}
		$this->assign('submitted',$submitted);
		$this->assign('list',$list);
		$this->display();
	}
}
function generateQRfromGoogle($chl,$widhtHeight ='150',$EC_level='L',$margin='0'){
	$chl = urlencode($chl);
    $src='http://chart.apis.google.com/chart?chs='.$widhtHeight.'x'.$widhtHeight.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl;
    return $src;
}
?>