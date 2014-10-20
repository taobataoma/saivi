<?php
class BusinessAction extends WapAction{

    public $token;
    public $wecha_id;
    public $type;
    public $bid;
    private $tpl;
    private $info;
    public $weixinUser;
    public $homeInfo;
    public function _initialize() {
        parent::_initialize();
        $this->token    = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $this->wecha_id = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $this->type     = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $this->bid      = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $arrAllow       = array('fitness','gover','food','travel','flower','property','ktv','bar','fitment','wedding','affections','housekeeper','lease','beauty');
        $orderid    =   filter_var($this->_get('orderid'),FILTER_SANITIZE_STRING);
        //if(!in_array($this->type,$arrAllow) || $this->type == '' || $this->bid =='' ){
        if(!in_array($this->type,$arrAllow) ){
            $this->error('抱歉,您的参数不合法!',U('Index/index',array('token'=>$this->token,'wecha_id'=>$this->wecha_id)));
        }
        $where   = array('token'=>$this->token,'type'=>$this->type,'bid'=>$this->bid);
        $busines = M('busines')->where($where)->find();
        $this->assign('busines',$busines);
        $this->assign('token',$this->token);
        $this->assign('wecha_id',$this->wecha_id);
        $this->assign('type',$this->type);
        $this->assign('bid',$this->bid);
        $tpl=$this->wxuser;
        $this->tpl=$tpl;

    }


    public function index(){
        $data   = D('busines');
        $type   = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $token  = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $bid    = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $wecha_id = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);

        if($bid == '' || $type == ''){
            $this->error('抱歉,您访问的URL路径出错,马上带您到首页...',U('Index/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
        }
        $where      = array('token'=>$token,'type'=>$type,'bid'=>$bid);

        $busines = $data->where($where)->find();
        if($busines == null){
            $this->error('抱歉,没有该记录,马上带您到首页...',U('Index/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
        }
        // 背景轮播图
        $pic     = M('busines_pic')->where(array('bid_id'=>$busines['bid'],'token'=>$token,'type'=>$type))->find();
        $flashbg[0]['img'] = $pic['picurl_1'];
        $flash[0]['img'] = $pic['picurl_1'];
        if($pic['picurl_2'] <> ''){
            $flashbg[1]['img'] = $pic['picurl_2'];
            $flash[1]['img'] = $pic['picurl_2'];
        }
        if($pic['picurl_3'] <> ''){
            $flashbg[2]['img'] = $pic['picurl_3'];
            $flash[2]['img'] = $pic['picurl_3'];
        }
        if($pic['picurl_4'] <> ''){
            $flashbg[3]['img'] = $pic['picurl_4'];
            $flash[3]['img'] = $pic['picurl_4'];
        }
        if($pic['picurl_5'] <> ''){
            $flashbg[4]['img'] = $pic['picurl_5'];
            $flash[4]['img'] = $pic['picurl_5'];
        }

        $this->assign('busines',$busines);
        $show    = filter_var($this->_get('show'),FILTER_SANITIZE_STRING);
        if($show == 'intro' && $show != ''){
            $where_2 = array('token'=>$token,'type'=>$type,'bid_id'=>$busines['bid']);
            $b_main = D('busines_main')->where($where_2)->select();
            $this->assign('b_main',$b_main);
            $this->display('intro');
            exit;
        }

        include('./WQLCms/Lib/ORG/index.Tpl.php');
        foreach($tpl as $k=>$v){
            if($v['tpltypeid'] == $busines['tpid']){
                 $tplinfo = $v;
            }
        }

$info = array();

$info[0]['url']  = "/index.php?g=Wap&m=Business&a=index&token=$token&wecha_id=$wecha_id&type=$type&bid=$bid";
$info[0]['img']  = '/tpl/static/attachment/icon/white/1.png';

$info[1]['url']  = "/index.php?g=Wap&m=Business&a=index&token=$token&wecha_id=$wecha_id&type=$type&bid=$bid&show=intro";
$info[1]['img']  = '/tpl/static/attachment/icon/white/5.png';

$info[2]['url']  = "/index.php?g=Wap&m=Business&a=classify&token=$token&wecha_id=$wecha_id&type=$type&bid=$bid";
$info[2]['img']  = '/tpl/static/attachment/icon/white/4.png';

$info[3]['url']  = "/index.php?g=Wap&m=Business&a=plist&token=$token&wecha_id=$wecha_id&type=$type&bid=$bid";
$info[3]['img']  = '/tpl/static/attachment/icon/white/13.png';

$info[4]['url']  = "/index.php?g=Wap&m=Business&a=comments&token=$token&wecha_id=$wecha_id&type=$type&bid=$bid";
$info[4]['img']  = '/tpl/static/attachment/icon/white/9.png';

$info[5]['url']  = "/index.php?g=Wap&m=Business&a=mylist&token=$token&wecha_id=$wecha_id&type=$type&bid=$bid";
$info[5]['img']  = '/tpl/static/attachment/icon/white/15.png';

switch($busines['type']){
    case 'fitness':
        $info[0]['name'] = '健身首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '健身房间';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

    case 'gover':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '部门简介';
        $info[2]['name'] = '服务窗口';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '领导点评';
        $info[5]['name'] = '我的订单';
        break;
    case 'food':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '销售门店';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;
    case 'travel':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '健身房间';
        $info[3]['name'] = '景区景点';
        $info[4]['name'] = '专家点评';
        $info[5]['name'] = '我的订单';
        break;
    case 'flower':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '花店分店';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

    case 'property':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '小区管理';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '专家点评';
        $info[5]['name'] = '我的订单';
        break;

    case 'ktv':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = 'KTV简介';
        $info[2]['name'] = 'KTV分店';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

    case 'bar':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '酒吧简介';
        $info[2]['name'] = '酒吧分店';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

    case 'fitment':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '装修分店';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

    case 'wedding':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '分店服务';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;


    case 'affections':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '宠物分店';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

     case 'housekeeper':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '分店服务';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;


    case 'lease':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '分店服务';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

    case 'beauty':
        $info[0]['name'] = '宣传首页';
        $info[1]['name'] = '公司简介';
        $info[2]['name'] = '分店服务';
        $info[3]['name'] = '相册展示';
        $info[4]['name'] = '客户点评';
        $info[5]['name'] = '我的订单';
        break;

}


        $this->assign('flash',$flash);
        $this->assign('info',$info);   // 菜单相关,url(连接),img(菜单背景图),name(菜单名)
        //$this->assign('num',$count);
        $this->assign('flashbg',$flashbg);  //背景轮播图 img(图片地址)
        $this->assign('tpl',$this->tpl);
        if($busines['tpid'] == 36){
            $this->assign('html36',$HTMLCSS);
        }
        if($busines['tpid'] == 117 ){
            $this->display('Index:1117_index_35y5buss');
        }elseif($busines['tpid'] == 36){
            $this->display('Index:136_index_esfsd344buss');
        }else{
            $this->display('Index:'.$tplinfo['tpltypename']);
        }

    }



    public function classify(){
        //Load('extend');
        $data       = D('busines_main');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $where      = array('token'=>$token,'type'=>$type,'bid_id'=>$bid);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,6);
        $show       = $Page->show();
        $classify   = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('count',6);
        $this->assign('page',$show);
        $this->assign('classify',$classify);
        $this->display();
    }

    public function classify_item(){
        $data       = D('busines_main');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $mid        = filter_var($this->_get('mid'),FILTER_VALIDATE_INT);
        $where_2    = array('token'=>$token,'type'=>$type,'mid'=>$mid);
        $classify   = $data->where($where_2)->find();
        $b_second   = M('busines_second');
        $where_3    = array('token'=>$token,'type'=>$type,'mid_id'=>$classify['mid']);

        //$sec_item   = $b_second->where($where_3)->order('sort desc')->select();
        $count      = $b_second->where($where_3)->count();
        $Page       = new Page($count,5);
        $show       = $Page->show();
        $sec_item   = $b_second->where($where_3)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('bid',$bid);
        $this->assign('page',$show);
        $this->assign('sec_item',$sec_item);
        $this->assign('classify',$classify);
        $this->display();
    }


    public function project(){
        $data       = D('busines_second');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $sid        = filter_var($this->_get('sid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'sid'=>$sid);
        $t_second   = $data->where($where)->find();
        $this->assign('sec_item',$t_second);
        $this->display();
    }

    public function goCart(){
        $data       = D('busines_second');
        $t_busines  = D('busines');
        $tb_resbook = D('reservebook');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $sid        = filter_var($this->_get('sid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'sid'=>$sid);
        $second     = $data->where($where)->find();
        $where_2    = array('token'=>$token,'type'=>$type,'bid'=>$bid);
        $busines    = $t_busines->where($where_2)->field('bid,roompicurl,address,businesphone,orderInfo,compyphone')->find();
        $maindata   = M('busines_main')->where(array('mid'=>$second['mid_id'],'token'=>$token,'type'=>$type))->field('mid,name as title')->find();
        if(!empty($second) && !empty($busines)){
            $oput   = array_merge($second,$busines,$maindata);
        }
        $count      = $tb_resbook->where(array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type))->count();
        if(IS_POST){
             $_POST['type']       = filter_var($this->_post('type'),FILTER_SANITIZE_STRING);
             $_POST['bid']        = filter_var($this->_post('bid'),FILTER_VALIDATE_INT);
             $_POST['sid']        = filter_var($this->_post('sid'),FILTER_VALIDATE_INT);
             $_POST['token']      = filter_var($this->_post('token'),FILTER_SANITIZE_STRING);
             $_POST['wecha_id']   = filter_var($this->_post('wecha_id'),FILTER_SANITIZE_STRING);
             $_POST['truename']   = trim(filter_var($this->_post('truename'),FILTER_SANITIZE_STRING));
             $_POST['tel']        = filter_var($this->_post('tel'),FILTER_SANITIZE_STRING);
             $_POST['address']    = filter_var($this->_post('address'),FILTER_SANITIZE_STRING);
             $_POST['info']       = filter_var($this->_post('info'),FILTER_SANITIZE_STRING);
             $_POST['productName']= filter_var($this->_post('productName'),FILTER_SANITIZE_STRING);
             $_POST['orderid']    = self::generateOrderSn();
             $_POST['paid']       = 0;
             $_POST['booktime']   = time();
			 $_POST['date']= date("Y-m-d H:i:s",time());
            //库存
            $where_stork          = array('token'=>$_POST['token'],'type'=>$_POST['type'],'sid'=>$_POST['sid']);
            $checkdata            = $data->where($where_stork)->find();
            if($_POST['wecha_id'] == '' || $_POST['token'] =='' || $_POST['truename'] == ''){
                exit($this->error('抱歉,请先关注我们的公众号.',
                    U('Index/index',array('token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],
                                        'bid'=>$_POST['bid'],'type'=>$_POST['type']))));
            }
            if($_POST['type'] == 'property' || $_POST['type'] =='gover'){

            }else{
                if(intval($checkdata['googsnumber']) <= 0){
                    exit($this->error('非常遗憾,您来晚了一步.',
                                U('Business/index',array('token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],
                                                    'bid'=>$_POST['bid'],'type'=>$_POST['type']))));
                }

            }

             //如果有库存->下单,减库存,->付款 ==> 付款成功()|付款失败() ]go to payReturn[
            $_POST['orderid']     = self::generateOrderSn();
            $_POST['orderName']   = $checkdata['name'];
            $_POST['payprice']    = $checkdata['oneprice'];
            $_POST['rid']         = filter_var($this->_post('sid'),FILTER_VALIDATE_INT);
            $insertdata           = $tb_resbook->data($_POST)->add();
            if($insertdata){ 
			//邮件发送
			$info=M('deliemail')->where(array('token'=>$_POST['token']))->find();
			$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
			$emailstatus=$info[$_POST['type']];
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

					$mail->Subject = '您有新的订单';
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
			
			
			//减库存
                $data->where(array('sid'=>$_POST['sid'],'type'=>$_POST['type'],'token'=>$_POST['token']))->setDec('googsnumber');
                if(intval($checkdata['oneprice']) == 0){
                    $this->assign('type',$_POST['type']);
                    $this->assign('token',$_POST['token']);
                    $this->assign('wecha_id',$_POST['wecha_id']);
                    $savedata['paid'] = 1;
                    $tb_resbook->where(array('id'=>$insertdata ,'token'=>$token))->save($savedata);
                    //发送给商家
                    Sms::sendSms($checkOrder['token'], "亲爱的,您的会员 {$checkOrder['truename']},已经购买了{$checkOrder['orderName']} 并付款成功,金额为{$checkOrder['payprice']},订单号为{$checkOrder['orderid']}. ". date('Y-m-d H:i:s',time()));
                    //发送给粉丝
                    Sms::sendSms($checkOrder['token'], "亲爱的 {$checkOrder['truename']},您购买的{$checkOrder['orderName']} 已经付款成功,金额为{$checkOrder['payprice']},订单号为{$checkOrder['orderid']},感谢您惠顾! ". date('Y-m-d H:i:s',time()),$checkOrder['tel']);

                    self::mylist();
                    exit;
                }else{
                    header("Content-type: text/html; charset=utf-8");
                    $this->redirect('Alipay/pay/', array('from' => 'Business','orderName'=>$checkdata['name'],
                                                'price'=>$checkdata['oneprice'],'token'=>$_POST['token'],
                                                'wecha_id'=>$_POST['wecha_id'],'type'=>$_POST['type'],'bid'=>$_POST['bid'],
                                                'sid'=>$_POST['sid']), 3, '您好,准备跳转到支付页面,请不要重复刷新页面,请耐心等待...');
                }


            }else{
                exit($this->error('Sorry,请重新下单.',
                    U('Business/index',array('token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],
                                        'bid'=>$_POST['bid'],'type'=>$_POST['type']))));
            }

        }
        $this->assign('oput',$oput);
        $this->assign('count',$count);
        $this->display();
    }

    private function  generateOrderSn(){
        date_default_timezone_set('PRC');
        list($msec, $sec) = explode(' ',microtime());
        return date('ymdHis',$sec).substr($msec,2,6);
    }

    protected function payReturn(){
        // $data       = D('busines_second');
        // $t_busines  = D('busines');
        $tb_resbook = D('reservebook');
        $orderid    =   filter_var($this->_get('orderid'),FILTER_SANITIZE_STRING);
        $token      =   filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $checkOrder = $tb_resbook->where(array('orderid'=>$orderid,'token'=>$token))->find();
       //根据订单号查出$order
       if($checkOrder){//如果订单存在
            if($checkOrder['paid'] === 1){ //支付成功,发信息,跳转到订单别表页面
                $this->assign('type',$checkOrder['type']);
                $this->assign('token',$checkOrder['token']);
                $this->assign('wecha_id',$checkOrder['wecha_id']);
                //发送给商家
                Sms::sendSms($checkOrder['token'], "您的会员 {$checkOrder['truename']},已经购买了{$checkOrder['orderName']} 并付款成功,金额为{$checkOrder['payprice']},订单号为{$checkOrder['orderid']}. ". date('Y-m-d H:i:s',time()));
                //发给单个连锁商家
               // Sms::sendSms(token_商家ID, 短信内容);
                //发送给粉丝
                Sms::sendSms($checkOrder['token'], " {$checkOrder['truename']},您购买的{$checkOrder['orderName']} 已经付款成功,金额为{$checkOrder['payprice']},订单号为{$checkOrder['orderid']},感谢您惠顾! ". date('Y-m-d H:i:s',time()),$checkOrder['tel']);

                self::mylist();
                exit;
            }else{
                //如果没支付，则进入另外一个判断,如果订单没有支付,这里应该回滚库存.setInc()
            M('busines_second')->where(array('sid'=>$checkOrder['rid'],'type'=>$checkOrder['type'],'token'=>$checkOrder['token']))->setInc('googsnumber');
            }

       }else{
        // $this->redirect('Business/index', array('token'=>$this->_get('token'),
        //                                         'wecha_id'=>$this->_get('wecha_id'),'type'=>$this->_get('type'),'bid'=>$this->_get('bid'),
        //                                         'sid'=>$this->_get('sid')), 2, '您好,系统里没有该订单...');
          exit('订单不存在!');
        }

    }
	public function sms(){
	
		$this->Business_order=M('reservebook');
		$this->Business_info=M('busines_second');
		$this->Business_dian=M('busines_main');
		$orders=$this->Business_order->where(array('token'=>$_GET['token'],'type'=> $_GET['type']))->order('date desc')->limit(0,1)->find();
		$info=$this->Business_info->where(array('token'=>$_GET['token'],'type'=> $_GET['type'],'sid'=>$this->_get('sid')))->find();
		$dian=$this->Business_dian->where(array('token'=>$_GET['token'],'type'=> $_GET['type'],'bid'=>$this->_get('bid')))->find();
		

		 if ($orders){
			$thisOrder=$_GET['type'];
			switch ($_GET['type']){
				case 'beauty':
					$orderType='微美容';
					break;
				case 'fitness':
					$orderType='健微身';
					break;
				case 'gover':
					$orderType='微政务';
					break;
				case 'food':
					$orderType='微食品';
					break;
				case 'travel':
					$orderType='微旅游';
					break;
				case 'flower':
					$orderType='微花店';
					break;
				case 'property':
					$orderType='微物业';
					break;	
				case 'bar':
					$orderType='微酒吧';
					break;	
				case 'fitment':
					$orderType='微装修';
					break;
				case 'wedding':
					$orderType='微婚庆';
					break;	
				case 'affections':
					$orderType='微宠物';
					break;		
				case 'housekeeper':
					$orderType='微家政';
					break;	
				case 'lease':
					$orderType='微租赁';
					break;				
			}}
			
			$str="\r\n订单类型：".$orderType."\r\n所属门店：".$dian['name']."\r\n价格：".$info['oneprice']."\r\n姓名：".$orders['truename']."\r\n电话：".$orders['tel']."\r\n地址：".$orders['address']."\r\n备注：".$orders['info']."\r\n下单时间：".$orders['date']."\r\n";
			
			
			
			return $str;
		
	}

    public function mylist(){
        $tb_resbook = D('reservebook');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'wecha_id'=>$wecha_id);

        $count      = $tb_resbook->where($where)->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $books      = $tb_resbook->where($where)->order('booktime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('books',$books);
        $this->display();
    }

    public function delOrder(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //echo '此功能只能在微信浏览器中使用';exit;
        }
        $id         = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $tb_resbook =   M('reservebook');
        $check      = $tb_resbook->where(array('id'=>$id,'token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type))->find();
        if($check){
            $tb_resbook->where(array('id'=>$check['id'],'wecha_id'=>$check['wecha_id'],'type'=>$check['type'],'token'=>$check['token']))->delete();
            $this->success('删除成功',U('Business/mylist',array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type)));
             exit;
         }else{
            $this->error('非法操作',U('Business/mylist',array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>$type)));
             exit;
         }
    }

    public function plist(){
        $this->token=$this->_get('token');
        $reply_info_db=M('Reply_info');
        $config=$reply_info_db->where(array('token'=>$this->token,'infotype'=>'album'))->find();
        if ($config){
            $headpic=$config['picurl'];
        }else {
            $headpic='/tpl/Wap/default/common/css/Photo/banner.jpg';
        }
        $this->assign('headpic',$headpic);

        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $get_id     = M('busines_pic')->field('bid_id,type,ablum_id')->where(array('bid_id'=>$bid,'token'=>$token,'type'=>$type))->find();
        $info=M('Photo')->field('title,picurl,id')->where(array('token'=>$token,'id'=>$get_id['ablum_id']))->find();
    $photo_list=M('Photo_list')->where(array('token'=>$token,'pid'=>$get_id['ablum_id'],'status'=>1))->order('sort desc')->select();
        $this->assign('info',$info);
        $this->assign('photo',$photo_list);
        $this->display();
    }


    public function comments(){
        $data       = D('busines_comment');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'bid_id'=>$bid);
        $count      = $data->where($where)->count();
        $Page       = new Page($count,6);
        $show       = $Page->show();
        $comments= $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('count',6);
        $this->assign('comments',$comments);
        $this->display();

    }

    public function comlist(){
        $data       = D('busines_comment');
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $bid        = filter_var($this->_get('bid'),FILTER_VALIDATE_INT);
        $cid        = filter_var($this->_get('cid'),FILTER_VALIDATE_INT);
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token,'type'=>$type,'cid'=>$cid);
        $comments= $data->where($where)->order('sort desc')->find();
        $this->assign('classify',$comments);
        $this->display();
    }


}