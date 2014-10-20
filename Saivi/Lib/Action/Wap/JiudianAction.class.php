<?php

//wap

class JiudianAction extends BaseAction{

	public $token;

	public $wecha_id;

	public $Yuyue_model;

	public $yuyue_order;

	public function __construct(){

		

		parent::__construct();

		$this->token=session('token');

		// $this->token = $this->_get('token');

		$this->assign('token',$this->token);

		$this->wecha_id	= $this->_get('wecha_id');

		if (!$this->wecha_id){

			$this->wecha_id='null';

		}

		$this->assign('wecha_id',$this->wecha_id);

		$this->Yuyue_model=M('yuyue');

		$this->yuyue_order=M('yuyue_order');

		$this->type='Jiudian';



	}



	

	//预约列表

	public function index(){

		$pid = $this->_get('id');

		$wecha_id = $this->_get('wecha_id');

		$where = array('token'=> $this->_get('token'),'id'=>$pid);

		$data = $this->Yuyue_model->where($where)->find();

		$info = M('yuyue_setcin')->where(array('pid'=>$pid,'type'=>$this->type))->select();

		//print_r($info);die;

		$data['count'] = $this->yuyue_order->where(array('wecha_id'=> $wecha_id,'pid'=>$pid))->count();

		$data['token'] = $this->_get('token');

		$data['wecha_id'] = $wecha_id;

		//print_r($str);die;

		$this->assign('data', $data);

		$this->assign('info', $info);

		$this->display();

	}

	

	public function info(){

		$pid = $this->_get('id');

		$id = $this->_get('aid');

		$where = array('token'=> $this->_get('token'),'id'=>$pid);

		

		$cast = array(

			'token'=> $this->_get('token'),

			'wecha_id'=> $this->_get('wecha_id')

		);

		$info = M('yuyue_setcin')->where(array('id'=>$id))->find();

		$info['sheng']=$info['yuanjia']-$info['youhui'];

		$data = $this->Yuyue_model->where($where)->find();

		for($i=1;$i<6;$i++){

			if(!empty($info['pic'.$i])){

				$info['pic'][]=$info['pic'.$i];

				unset($info['pic'.$i]);

			}

		}

		//print_r($data);print_r($info);die;

		$data['token'] = $this->_get('token');

		$data['wecha_id'] = $this->_get('wecha_id');

		$wap= M('setinfo')->where(array('pid'=>$pid))->select();

		$str=array();

		//print_r($wap);die;

		foreach($wap as $v){



			if($v['kind']==5){

				$str["message"]=$v["name"];

			}else{

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

		//print_r($list);die;

		

		$this->assign('str', $str);

		$this->assign('arr',$arr);

		$this->assign('list',$list);

		$this->assign('data', $data);

		$this->assign('info', $info);

		$this->display();

	}

	

	//添加订单

	public function add(){

		$info=M('Wxuser')->where(array('token'=>$this->token))->find();
		$email=$info['email'];
        $emailuser=$info['emailuser'];
        $emailpassword=$info['emailpassword'];
        $jiudianstatus=$info['jiudianstatus'];
		$content = $this->sms();
		
		$phone=$info['phone'];
        $user=$info['smsuser'];//短信平台帐号
        $pass=md5($info['smspassword']);//短信平台密码
        $jiudiansmsstatus=$info['jiudiansmsstatus'];//短信平台状态
        $contentt = $this->smss();

		if(IS_POST){

			$url = U($this->type.'/order',array('token'=>$_POST['token'], 'wecha_id'=>$_POST['wecha_id'],'id'=>$_POST['pid'],));

			$url = substr($url,1);

			//$url = U($this->type.'/order',array('token'=>$this->$_POST['token'], 'wecha_id'=>$_POST['wecha_id'],$id=>$_POST['pid']));

			$_POST['date']= date("Y-m-d H:i:s",time());
			$_POST['type']=$this->type;


			if($this->yuyue_order->add($_POST)){
					//短线发送
				if ($jiudiansmsstatus == 1) {
                  if ($contentt) {
		
        $smsrs = file_get_contents('http://api.smsbao.com/sms?u='.$user.'&p='.$pass.'&m='.$phone.'&c='.urlencode($contentt)); }}
               //短信结束
			   
			   
			   //邮件发送
				if ($jiudianstatus == 1) {
                if ($content) {
		
        date_default_timezone_set('PRC');
        require_once 'class.phpmailer.php';
        //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
        $mail = new PHPMailer();
        $body = $content;
        $mail->IsSMTP();
        // telling the class to use SMTP
        $mail->Host = 'smtp.163.com';
        // SMTP server
        $mail->SMTPDebug = '1';
        // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true;
        // enable SMTP authentication
        $mail->Host = 'smtp.163.com';
        // sets the SMTP server
        $mail->Port = 25;
        // set the SMTP port for the GMAIL server
        $mail->Username = $emailuser;
        // SMTP account username
        $mail->Password = $emailpassword;
        // SMTP account password
        $mail->SetFrom('18267720632@163.com', '微最强微信系统');
        $mail->AddReplyTo($email, '微最强微信系统');
        $mail->Subject = '恭喜你有新的客户订单，立刻查看';
        $mail->AltBody = '';
        // optional, comment out and test
        $mail->MsgHTML($body);
        $address = $email;
        $mail->AddAddress($address, '商户');
        $emailrs = $mail->Send(); }}
		//邮件结束

				$json = array(

					'error'=> 1,

					'msg'=> '添加成功！',

					'url'=> $url

				);

				echo  json_encode($json);

			}else{

				$json = array(

					'error'=> 0,

					'msg'=> '添加失败！',

					'url'=> U($this->type.'/index',array('token'=>$this->$_POST['token'], 'wecha_id'=>$_POST['wecha_id'],$id=>$_POST['pid']))

				);

				echo  json_encode($json);

			}

		}

	}
	public function sms(){
	
		$this->yuyue_order=M('yuyue_order');
		$orders=$this->yuyue_order->where(array('token'=>$this->token,'type'=>$this->type))->order('date desc')->limit(0,1)->find();
		
		
		
			
			$str="<includetail><div style='border-bottom:3px solid #d9d9d9; background:url(http://exmail.qq.com/zh_CN/htmledition/images/domainmail/bizmail_bg.gif) repeat-x 0 1px;'><div style='border:1px solid #c8cfda; padding:10px;'><div align='right'><img src='http://www.wall.wtoken.com/tpl/User/default/common/images/logo.png'></div><p style='margin:0 0 35px;'>亲爱的微最强网商户：<br>以下是客户在你微最强平台下的新酒店预约订单哦：</p>订单类型：".$orders['kind']."\r\n<br>姓名：".$orders['name']."\r\n<br>电话：".$orders['phone']."\r\n<br>预约日期：".$orders['or_date']."\r\n<br>预约时间：".$orders['time']."\r\n<br>下单时间：".$orders['date']."\r\n<h3 style='font-weight:bold;font-size:14px;'>";
			//
			
			
			return $str;
		
	}
	public function smss(){
	
		$this->yuyue_order=M('yuyue_order');
		$orders=$this->yuyue_order->where(array('token'=>$this->token,'type'=>$this->type))->order('date desc')->limit(0,1)->find();
		
		
		
			
			$str="亲爱的微最强网商户：以下是客户在你微最强平台下的新酒店预约订单哦：订单类型：".$orders['kind']."姓名：".$orders['name']."电话：".$orders['phone']."预约日期：".$orders['or_date']."预约时间：".$orders['time']."下单时间：".$orders['date'];
			//
			
			
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

		//print_r($data);die;

		$this->assign('data',$data);

		$this->assign('info',$info);

		$this->display();

	}

	

	//修改订单视图

	public function set(){

		$id = $this->_get('id');

		$pid = $this->_get('pid');

		

		$cast = array(

			'token'=> $this->_get('token'),

			'wecha_id'=> $this->_get('wecha_id')

		);

		$data = M('yuyue_order')->where(array('id'=>$id))->find();

		$info = M('yuyue_setcin')->where(array('name'=>$data['kind']))->find();

		$info['sheng']=$info['yuanjia']-$info['youhui'];

		

		//print_r($data);print_r($info);die;

		$copyright=$this->Yuyue_model->where(array('token'=> $this->_get('token'),'id'=>$pid))->find();

		$data['copyright']=$copyright['copyright'];

		//print_r($copyright);die;

		$data['token'] = $this->_get('token');

		$data['wecha_id'] = $this->_get('wecha_id');

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

		$list_arr =array();

		$i=0;

		foreach($list as $v){

			$list[$i]['value']= explode("|",$v['value']);

			$i++;

		}

		//print_r($list);die;



		$text=$data['fieldsigle'];

		$down=$data['fielddownload'];

		$text=substr($text,1);

		$down=substr($down,1);

		$text=explode('$',$text);

		$down=explode('$',$down);

		$detail=array();

		$i=1;

		foreach($text as $v){

			$detail['text'][$i]=explode('#',$v);

			$i++;

		}

		$i=1;

		foreach($down as $v){

			$detail['down'][$i]=explode('#',$v);	

		}

		//print_r($detail);die;



		$this->assign('detail', $detail);

		

		$this->assign('str', $str);

		$this->assign('arr',$arr);

		$this->assign('list',$list);

		$this->assign('list_arr',$list);

		$this->assign('data', $data);

		$this->assign('info', $info);

		$this->display();

	}

	

	//修改订单

	public function runSet(){

	

		$id = $_GET['id']; 

		if(IS_POST){

			$url = U($this->type.'/order',array('token'=>$_POST['token'], 'wecha_id'=>$_POST['wecha_id'],'id'=>$_POST['pid'],));

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

			$url = U($this->type.'/order',array('token'=>$_POST['token'], 'wecha_id'=>$_POST['wecha_id'],'id'=>$_POST['pid'],));

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