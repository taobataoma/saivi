<?php
class AlipaytypeAction extends BaseAction{
	public $token;
	public $wecha_id;
	public $alipayConfig;
	public function __construct(){
		$this->token = $this->_get('token');
		$this->wecha_id	= $this->_get('wecha_id');
		if (!$this->token){
			//
			$product_cart_model=M('product_cart');
			$out_trade_no = $this->_get('out_trade_no');
			$order=$product_cart_model->where(array('orderid'=>$out_trade_no))->find();
			if (!$order){
				$order=$product_cart_model->where(array('id'=>intval($this->_get('out_trade_no'))))->find();
			}
			$this->token=$order['token'];
		}
		//读取支付宝配置
		$alipay_config_db=M('Alipay_config');
		$this->alipayConfig=$alipay_config_db->where(array('token'=>$this->token))->find();
	}
	public function pay(){
		//参数数据
		$price=$_GET['price'];
		$orderName=$_GET['orderName'];
		if (!$orderName){
			$orderName=microtime();
		}
		$orderid=$_GET['orderid'];
		if (!$orderid){
			$orderid=$_GET['single_orderid'];//单个订单
		}
		
		//
		$alipayConfig=$this->alipayConfig;
		
		//
		if(!$price)exit('必须有价格才能支付');
		import("@.ORG.Alipay.AlipaySubmit");
		
		//支付类型
		$payment_type = "1";
		//必填，不能修改
		//服务器异步通知页面路径
		$notify_url = C('site_url').'/index.php?g=Wap&m=Alipaytype&a=notify_url';
		//需http://格式的完整路径，不能加?id=123这类自定义参数
		//页面跳转同步通知页面路径
		$return_url = C('site_url').'/index.php?g=Wap&m=Alipaytype&a=return_url&token='.$_GET['token'].'&wecha_id='.$_GET['wecha_id'].'&from='.$_GET['from'];
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
		//卖家支付宝帐户
		$seller_email =trim($alipayConfig['name']);
		//商户订单号
		$out_trade_no = $orderid;
		//商户网站订单系统中唯一订单号，必填
		//订单名称
		$subject =$orderName;
		//必填
		//付款金额
		$total_fee =floatval($price);

		$body = $orderName;
		//商品展示地址
		$show_url = C('site_url').U('Home/Index/price');
		//需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

		//防钓鱼时间戳
		$anti_phishing_key = "";
		//若要使用请调用类文件submit中的query_timestamp函数

		//客户端的IP地址
		$exter_invoke_ip = "";
		//非局域网的外网IP地址，如：221.0.0.1
		$body = $subject;
		$show_url = rtrim(C('site_url'),'/');
		//构造要请求的参数数组，无需改动
		$parameter = array(
		"service" => "create_direct_pay_by_user",
		"partner" =>trim($alipayConfig['pid']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"seller_email"	=> $seller_email,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,
		"_input_charset"	=>trim(strtolower('utf-8'))
		);

		//建立请求
		$alipaySubmit = new AlipaySubmit($this->setconfig());
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "进行支付");
		echo '正在跳转到支付宝进行支付...<div style="display:none">'.$html_text.'</div>';
	}
	public function setconfig(){
		$alipay_config['partner']		= trim($this->alipayConfig['pid']);
		//安全检验码，以数字和字母组成的32位字符
		$alipay_config['key']			= trim($this->alipayConfig['key']);
		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
		//签名方式 不需修改
		$alipay_config['sign_type']    = strtoupper('MD5');
		//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config['input_charset']= strtolower('utf-8');
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = getcwd().'\\Saivi\\Lib\\ORG\\Alipay\\cacert.pem';
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config['transport']    = 'http';		
		return $alipay_config;
	}
	//同步数据处理
	public function return_url (){
		import("@.ORG.Alipay.AlipayNotify");
		$alipayNotify = new AlipayNotify($this->setconfig());
		$verify_result = $alipayNotify->verifyReturn();	
		//if($verify_result) {
			$out_trade_no = $this->_get('out_trade_no');
			//支付宝交易号
			$trade_no =  $this->_get('trade_no');
			//交易状态
			$trade_status =  $this->_get('trade_status');
			if( $this->_get('trade_status') == 'TRADE_FINISHED' ||  $this->_get('trade_status') == 'TRADE_SUCCESS') {
				$member_card_create_db=M('Member_card_create');
				$userCard=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
				$member_card_set_db=M('Member_card_set');
				$thisCard=$member_card_set_db->where(array('id'=>intval($userCard['cardid'])))->find();
				$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
				//
				$arr['token']=$this->token;
				$arr['wecha_id']=$this->wecha_id;
				$arr['expense']=$this->_get('total_fee');
				$arr['time']=time();
				$arr['cat']=99;
				$arr['staffid']=0;
				$arr['score']=intval($set_exchange['reward'])*$arr['expense'];
				M('Member_card_use_record')->add($arr);
				$userinfo_db=M('Userinfo');
				$thisUser = $userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->find();
				$userArr=array();
				$userArr['total_score']=$thisUser['total_score']+$arr['score'];
				$userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
				$userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->save($userArr);
				//
				$from=$_GET['from'];
				$from=$from?$from:'Groupon';
				$from=$from!='groupon'?$from:'Groupon';
				switch (strtolower($from)){
					default:
					case 'groupon':
					case 'store':
						$order_model=M('product_cart');
						break;
					case 'repast':
						$order_model=M('Dish_order');
						break;
					case 'hotels':
						$order_model=M('Hotels_order');
						break;
					case 'business':
						$order_model=M('Reservebook');
						break;
				}
				$order_model->where(array('orderid'=>$out_trade_no))->setField('paid',1);
				$this->redirect('/index.php?g=Wap&m='.$from.'&a=payReturn&token='.$_GET['token'].'&wecha_id='.$_GET['wecha_id'].'&orderid='.$out_trade_no);
			}else {
			  exit('付款失败');
			}
		//}else {
			//exit('不存在的订单');
		//}
	}
	public function notify_url(){
		echo "success"; 
		eixt();
	}
}
?>