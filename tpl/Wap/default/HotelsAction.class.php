<?php
class HotelsAction extends WapAction {
	
	public $token;
	
	public $wecha_id = 'gh_aab60b4c5a39';
	
	public $session_dish_info;//
	public $session_dish_user;
	public $_cid = 0;
	
	public $offset = 8;
	
	public function _initialize(){
		parent::_initialize();
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if (!strpos($agent, "MicroMessenger")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
		
		$this->token = isset($_REQUEST['token']) ? $_REQUEST['token'] : session('token');//$this->_get('token');
		
		$this->assign('token', $this->token);
		$this->wecha_id	= isset($_REQUEST['wecha_id']) ? $_REQUEST['wecha_id'] : '';
		if (!$this->wecha_id){
			$this->wecha_id='';
		}
		//$this->wecha_id = 'gh_aab60b4c5a39';
		$this->assign('wecha_id', $this->wecha_id);
		
		$this->_cid = $_SESSION["session_hotel_{$this->token}"];
		$this->assign('cid', $this->_cid);
		
		$this->session_dish_info = "session_hotel_{$this->_cid}_info_{$this->token}";
		$this->session_dish_user = "session_hotel_{$this->_cid}_user_{$this->token}";
		
		$this->assign('totalDishCount', $count);
	}
	
	/**
	 * 酒店分布
	 */
	public function index() {
		$company = M('Company')->where("`token`='{$this->token}' AND ((`isbranch`=1 AND `display`=1) OR `isbranch`=0)")->select();
		if (count($company) == 1) {
			$this->redirect(U('Hotels/selectdate',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'cid' => $company[0]['id'])));
		}
		$price = M('Hotels_house_sort')->field('min(vprice) as price, cid')->group('cid')->where(array('token' => $this->token))->select();
		$t = array();
		foreach ($price as $row) {
			$t[$row['cid']]	= $row['price'];
		}
		$list = array();
		foreach ($company as $c) {
			if (isset($t[$c['id']])) {
				$c['price'] = $t[$c['id']];
			} else {
				$c['price'] = 0;
			}
			$list[] = $c;
		}
		$this->assign('company', $list);
		$this->assign('metaTitle', '酒店分布');
		$this->display();
	}
	
	public function selectdate()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		if ($company = M('Company')->where(array('token' => $this->token, 'id' => $cid))->find()) {
			$_SESSION["session_hotel_{$this->token}"] = $cid;
		} else {
			$this->redirect(U('Hotels/index',array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
		}
		$dates = array();
		$dates[] = array('k' => date("Y-m-d"), 'v' => date("m月d日"));
		for ($i = 1; $i <= 90; $i ++) {
			$dates[] = array('k' => date("Y-m-d", strtotime("+{$i} days")), 'v' => date("m月d日", strtotime("+{$i} days")));
		}
		
		$this->assign('dates', $dates);
		$this->assign('metaTitle', '在线预订客房');
		$this->display();
	}
	
	public function hotel()
	{
		$in = isset($_GET['check_in_date']) ? htmlspecialchars($_GET['check_in_date']) : '';
		$out = isset($_GET['check_out_date']) ? htmlspecialchars($_GET['check_out_date']) : '';
		
		$days = (strtotime($out) - strtotime($in)) / 86400;
		if ($days < 1) {
			$this->redirect(U('Hotels/selectdate',array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
		}
		
		$in = date("Ymd", strtotime($in));
		$out = date("Ymd", strtotime($out));
		
		$company = M('Company')->where(array('id' => $this->_cid))->find();
		
		$sorts = M('Hotels_house_sort')->where(array('cid' => $this->_cid, 'token' => $this->token))->select();
		
		$order = M('Hotels_order')->field('sum(nums) as num, sid')->group('sid')->where(array('startdate' => array('ELT', $in), 'enddate' => array('GT', $in), 'token' => $this->token, 'cid' => $this->_cid))->select();
		$t = array();
		foreach ($order as $o) {
			$t[$o['sid']] = $o['num'];
		}
		$list = array();
		foreach ($sorts as $s) {
			$s['useHouse'] = isset($t[$s['id']]) ? $t[$s['id']] : 0;
			$list[] = $s;
		}
		$this->assign('company', $company);
		$this->assign('sday', date("m月d日", strtotime($in)));
		$this->assign('eday', date("m月d日", strtotime($out)));
		$this->assign('startdate', $in);
		$this->assign('enddate', $out);
		$this->assign('days', $days);
		$this->assign('list', $list);
		$this->assign('metaTitle', '在线预订客房');
		$this->display();
		
	}
	
	
	public function order()
	{
		$in = isset($_GET['startdate']) ? htmlspecialchars($_GET['startdate']) : '';
		$out = isset($_GET['enddate']) ? htmlspecialchars($_GET['enddate']) : '';
		$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
		$days = (strtotime($out) - strtotime($in)) / 86400;
		if ($days < 1) {
			$this->redirect(U('Hotels/selectdate',array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
		}
		if ($sort = M('Hotels_house')->where(array('cid' => $this->_cid, 'token' => $this->token, 'id' => $sid))->find()) {
			if ($this->fans['getcardtime'] > 0) {
				$sort['price'] = $sort['vprice'] ? $sort['vprice'] : $sort['price'];
			}
			$company = M('Company')->where(array('id' => $this->_cid))->find();
			$this->assign('company', $company);
			$this->assign('sort', $sort);
			$this->assign('sday', date("m月d日", strtotime($in)));
			$this->assign('eday', date("m月d日", strtotime($out)));
			$this->assign('startdate', $in);
			$this->assign('enddate', $out);
			$this->assign('days', $days);
			$this->assign('total', $days * $sort['price']);
			$this->assign('metaTitle', '在线预订客房');
			$this->display();
		}
	}
	
	/**
	 * 提交订单
	 */
	public function saveorder()
	{
		$dataBase = D('Hotels_order');
		if (IS_POST) {
			$price = 0;
			if ($sort = M('Hotels_house')->where(array('cid' => $this->_cid, 'token' => $this->token, 'id' => $_POST['sid']))->find()) {
				if ($this->fans['getcardtime'] > 0) {
					$price = $sort['vprice'] ? $sort['vprice'] : $sort['price'];
				} else {
					$price = $sort['price'];
				}
			}
			$days = (strtotime($_POST['enddate']) - strtotime($_POST['startdate'])) / 86400;
			$sday = date("Y年m月d日", strtotime($_POST['startdate']));
			$eday = date("Y年m月d日", strtotime($_POST['enddate']));
			if ($_POST['startdate'] < date("Ymd") || $days < 1) {
				$this->error('您预定的时间不正确');
			}
			$_POST['orderid'] = $orderid = substr($this->wecha_id, -1, 4) . date("YmdHis");
			$_POST['price'] = $_POST['nums'] * $days * $price;
			if ($dataBase->create() !== false) {
				$action = $dataBase->add();
				if ($action != false ) {
					Sms::sendSms($this->token . "_" . $this->_cid, "顾客{$_POST['name']}刚刚预定了{$sday}到{$eday}，{$days}天的{$sort['name']}，请您注意查看并处理");
					$alipayConfig = M('Alipay_config')->where(array('token' => $this->token))->find();
					if ($alipayConfig['open']) {
						$this->success('添加成功', U('Alipay/pay',array('token' => $this->token, 'wecha_id' => $this->wecha_id, 'from'=> 'Hotels', 'orderName' => $orderid, 'single_orderid' => $orderid, 'price' => $_POST['price'])));
					} else {
						$this->redirect(U('Hotels/my',array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
					}
				} else {
					$this->error('操作失败');
				}
			} else {
				$this->error($dataBase->getError());
			}
		}
	}
	
	/**
	 * 我的订单
	 */
	public function my()
	{
		$company = M('Company')->where(array('id' => $this->_cid, 'token' => $this->token))->find();
		$orders = M('Hotels_order')->where(array('cid' => $this->_cid, 'token' => $this->token, 'wecha_id' => $this->wecha_id))->order('id desc')->limit($this->offset)->select();
		$list = array();
		foreach ($orders as $o) {
			$o['day'] = (strtotime($o['enddate']) - strtotime($o['startdate'])) / 86400;
			$o['startdate'] = date("m月d日", strtotime($o['startdate']));
			$o['enddate'] = date("m月d日", strtotime($o['enddate']));
			$list[] = $o;
		}
		$count = M('Hotels_order')->where(array('cid' => $this->_cid, 'token' => $this->token, 'wecha_id' => $this->wecha_id))->count();
		$totalpage = ceil($count / $this->offset);
		$this->assign('totalpage', $totalpage);
		$this->assign('company', $company);
		$this->assign('list', $list);
		$this->assign('metaTitle', '我的订单');
		$this->display();
	}
	
	public function ajaxorder()
	{
		$company = M('Company')->where(array('id' => $this->_cid, 'token' => $this->token))->find();
		$page = isset($_GET['page']) && intval($_GET['page']) > 1 ? intval($_GET['page']) : 2;
		$start =($page-1) * $this->offset;
		$orders = M('Hotels_order')->where(array('cid' => $this->_cid, 'token' => $this->token, 'wecha_id' => $this->wecha_id))->order('id desc')->limit($start . ', ' . $this->offset)->select();
		$list = array();
		foreach ($orders as $o) {
			$o['day'] = (strtotime($o['enddate']) - strtotime($o['startdate'])) / 86400;
			$o['startdate'] = date("m月d日", strtotime($o['startdate']));
			$o['enddate'] = date("m月d日", strtotime($o['enddate']));
			$o['hotelname'] = $company['name'];
			$list[] = $o;
		}
		
		$count = M('Hotels_order')->where(array('cid' => $this->_cid, 'token' => $this->token, 'wecha_id' => $this->wecha_id))->count();
		
		$totalpage = ceil($count / $this->offset);
		$page = $totalpage > $page ? intval($page + 1) : 0;
		exit(json_encode(array('page' => $page, 'data' => $list)));
	}
	
	/**
	 * 订单详情
	 */
	public function detail()
	{
		$id = isset($_GET['oid']) ? intval($_GET['oid']) : 0;
		if ($order = M('Hotels_order')->where(array('cid' => $this->_cid, 'token' => $this->token, 'id' => $id))->find()) {
			$company = M('Company')->where(array('id' => $this->_cid))->find();
			$order['startdate'] = date("m月d日", strtotime($order['startdate']));
			$order['enddate'] = date("m月d日", strtotime($order['enddate']));
			$sort = M('Hotels_house')->where(array('cid' => $this->_cid, 'token' => $this->token, 'id' => $order['sid']))->find();
			$order['housename'] = isset($sort['name']) ? $sort['name'] : '';
			$this->assign('company', $company);
			$this->assign('order', $order);
			$this->assign('metaTitle', '订单详情');
			$this->display();
		} else {
			$this->redirect(U('Hotels/my',array('token' => $this->token, 'wecha_id' => $this->wecha_id)));
		}
	}
	
	/**
	 * 支付成功后的回调函数
	 */
	public function payReturn() {
	   $orderid = $_GET['orderid'];
	   if ($order = M('Hotels_order')->where(array('orderid' => $orderid, 'token' => $this->token))->find()) {
			//TODO 发货的短信提醒
			if ($order['paid']) {
				//Sms::sendSms($this->token, "您刚刚对订单号：{$orderid}的订单进行了支付，欢迎您的入住！", $order['tel']);
				Sms::sendSms($this->token . "_" . $order['cid'], "顾客{$order['name']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理");
			}
			$this->redirect(U('Hotels/my', array('token'=>$this->token, 'wecha_id' => $this->wecha_id)));
	   }else{
	      exit('订单不存在');
	    }
	}
}
?>