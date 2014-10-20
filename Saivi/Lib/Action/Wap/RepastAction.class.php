<?php
class RepastAction extends WapAction{
    public $token;
    public $wecha_id = 'gh_aab60b4c5a39';
    public $session_dish_info;
    public $session_dish_user;
    public $_cid = 0;
    private $_sms_auth_code = '';//下订单的短信验证
    public function _initialize(){ 
        parent :: _initialize();
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger")){
        }
        $this -> token = isset($_REQUEST['token']) ? $_REQUEST['token'] : session('token');
        $this -> assign('token', $this -> token);
        $this -> wecha_id = isset($_REQUEST['wecha_id']) ? $_REQUEST['wecha_id'] : '';
        if (!$this -> wecha_id){
            $this -> wecha_id = '';
        }
        $this -> assign('wecha_id', $this -> wecha_id);
        $this -> _cid = $_SESSION["session_company_{$this->token}"];
        $this -> assign('cid', $this -> _cid);
        $this -> session_dish_info = "session_dish_{$this->_cid}_info_{$this->token}";
        $this -> session_dish_user = "session_dish_{$this->_cid}_user_{$this->token}";
        $menu = $this -> getDishMenu();
        $count = count($menu);
        $this -> assign('totalDishCount', $count);
    }
    public function index(){       
        $data = M('dish_company');
        $list = $data->select();  
	$id_arr = array();
        foreach ($list as $row) {  
            $id_arr[] = $row['catid'];
        }   
        
        $company = M('Company') -> where("`token`='{$this->token}' AND ((`isbranch`=1 AND `display`=1) OR `isbranch`=0) ") -> select();

        $company_new = array();
        foreach ($company as $row) {
                if (in_array($row['id'], $id_arr)) {
                        $company_new[] = $row;
                }
        }		
	$company = $company_new;
        
        if (count($company) == 1){
            $this -> redirect(U('Repast/select', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id, 'cid' => $company[0]['id'])));
        }
        $this -> assign('company', $company);
        $this -> assign('metaTitle', '餐厅分布');
        $this -> display();
    }
    public function select(){
        $istakeaway = 0;
        $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
        if ($company = M('Company') -> where(array('token' => $this -> token, 'id' => $cid)) -> find()){
            $_SESSION["session_company_{$this->token}"] = $cid;
        }else{
            $this -> redirect(U('Repast/index', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id)));
        }
        if ($dishCompany = M('Dish_company') -> where(array('cid' => $cid)) -> find()){
            $istakeaway = $dishCompany['istakeaway'];
        }
        $this -> assign('istakeaway', $istakeaway);
        $this -> assign('metaTitle', '点餐选择');
        $this -> display();
    }
    public function virtual(){
        $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
        $company = M('Company') -> where(array('token' => $this -> token, 'id' => $cid)) -> find();
        $this -> assign('company', $company);
        $this -> assign('metaTitle', '餐厅介绍');
        $this -> display();
    }
    public function selectTable(){//to Repast/saveUser
        $thisUser = M('Userinfo') -> where(array('token' => $this -> token, 'wecha_id' => $this -> wecha_id)) -> find();
        $this -> assign('thisUser', $thisUser);
        $takeaway = isset($_GET['takeaway']) ? intval($_GET['takeaway']) : 0; //2-现场点餐 0-在线预订
        $_SESSION[$this -> session_dish_user] = null;
        unset($_SESSION[$this -> session_dish_user]);
        $time = time();
        $orderTable = M('Dish_table') -> where(array('reservetime' => array('elt', $time + 2 * 3600), 'reservetime' => array('egt', $time - 2 * 3600), 'cid' => $this -> _cid, 'isuse' => 0)) -> select();
        $tids = array();
        foreach ($orderTable as $row){
            $tids[] = $row['tableid'];
        }
        if ($tids){
            $table = M('Dining_table') -> where(array('id' => array('not in', $tids), 'cid' => $this -> _cid)) -> select();
        }else{
            $table = M('Dining_table') -> where(array('cid' => $this -> _cid)) -> select();
        }
        $dates = array();
        $dates[] = array('k' => date("Y-m-d"), 'v' => date("m月d日"));
        for ($i = 1; $i <= 90; $i ++){
            $dates[] = array('k' => date("Y-m-d", strtotime("+{$i} days")), 'v' => date("m月d日", strtotime("+{$i} days")));
        }
        $hours = array();
        for ($i = 0; $i < 24; $i ++){
            $hours[] = array('k' => $i, 'v' => $i . "时");
        }
        $seconds = array();
        for ($i = 0; $i < 60; $i ++){
            $seconds[] = array('k' => $i, 'v' => $i . "分");
        }
        $this -> assign('dates', $dates);
        $this -> assign('seconds', $seconds);
        $this -> assign('hours', $hours);
        $this -> assign('takeaway', $takeaway);
        $this -> assign('tables', $table);
        $this -> assign('metaTitle', '填写个人信息');
        $this -> assign('time', date("Y-m-d H:i:s"));
        $this -> display();
    }
    public function getTable(){
        $date = isset($_POST['redate']) ? htmlspecialchars($_POST['redate']) : '';
        $hour = isset($_POST['rehour']) ? htmlspecialchars($_POST['rehour']) : '';
        $second = isset($_POST['resecond']) ? htmlspecialchars($_POST['resecond']) : '';
        $time = strtotime($date . ' ' . $hour . ':' . $second . ':00');
        $orderTable = M('Dish_table') -> where(array('reservetime' => array('elt', $time + 2 * 3600), 'reservetime' => array('egt', $time - 2 * 3600), 'cid' => $this -> _cid, 'isuse' => 0)) -> select();
        $tids = array();
        foreach ($orderTable as $row){
            $tids[] = $row['tableid'];
        }
        if ($tids){
            $table = M('Dining_table') -> where(array('id' => array('not in', $tids), 'cid' => $this -> _cid)) -> select();
        }else{
            $table = M('Dining_table') -> where(array('cid' => $this -> _cid)) -> select();
        }
        exit(json_encode($table));
    }
    /**
     * 取短信验证码在下订单时
     */
    public function get_sms_auth_code() {
        if ($_POST['tel']) {
            $this->_sms_auth_code = rand(100000, 999999);
            $res = Sms :: sendSms($this -> token . "_" . $this -> _cid, "您的订餐短信验证码是". $this->_sms_auth_code ."请妥善保管", $_POST['tel']);            
            exit(json_encode(array('success' => 1, 'msg' => $this->_sms_auth_code)));
        } else {
            exit(json_encode(array('success' => 0, 'msg' => '电话号码不能为空')));
        }              
    }    
    public function saveUser(){//保存订单  //2-现场点餐 0-在线预订 1-外卖（店铺上设置）
        if ($_POST['tel_auth_code'] != $_POST['tel_auth_code_ajax']) {
            exit(json_encode(array('success' => 0, 'msg' => '您的手机短信验证码错误，不能订餐!')));            
        }        
        $takeaway = isset($_POST['takeaway']) ? intval($_POST['takeaway']) : 0;
        $tel = $table = $address = $des = $name = '';
        $sex = $nums = 1;
        $price = 0;
        if ($takeaway == 1){
            $dishCompany = M('Dish_company') -> where(array('cid' => $this -> _cid)) -> find();
            if (isset($dishCompany['istakeaway']) && $dishCompany['istakeaway']) $price = $dishCompany['price'];
        }
        if ($takeaway != 2){
            $tel = isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '';
            if (empty($tel)){
                exit(json_encode(array('success' => 0, 'msg' => '电话号码不能为空')));
            }
            $name = isset($_POST['guest_name']) ? $_POST['guest_name'] : '';
            if (empty($name)){
                exit(json_encode(array('success' => 0, 'msg' => '姓名不能为空')));
            }
            $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
            $sex = isset($_POST['sex']) ? intval($_POST['sex']) : 0;
            $date = isset($_POST['redate']) ? htmlspecialchars($_POST['redate']) : '';
            $hour = isset($_POST['rehour']) ? htmlspecialchars($_POST['rehour']) : '';
            $second = isset($_POST['resecond']) ? htmlspecialchars($_POST['resecond']) : '';
            $reservetime = strtotime($date . ' ' . $hour . ':' . $second . ':00');
            if ($reservetime < time()){
                exit(json_encode(array('success' => 0, 'msg' => '预约用餐时间不可以小于当前时间')));
            }
            $nums = isset($_POST['nums']) ? intval($_POST['nums']) : 1;
        }else{
            $reservetime = time() + 600;
        }
        $table = isset($_POST['table']) ? intval($_POST['table']) : 0;
        $des = isset($_POST['remark']) ? htmlspecialchars($_POST['remark']) : '';
        $data = array('tableid' => $table, 'tel' => $tel, 'takeaway' => $takeaway, 'address' => $address, 'name' => $name, 'sex' => $sex, 'reservetime' => $reservetime, 'price' => $price, 'nums' => $nums, 'des' => $des);
        $_SESSION[$this -> session_dish_user] = serialize($data);
        exit(json_encode(array('success' => 1, 'msg' => 'ok')));
        //repast_selecttable 成功后- window.location = "{pigcms::U('Repast/dish', array('token'=>$token, 'wecha_id' => $wecha_id, 'cid' => $cid))}";
    }
    public function dish(){
        $company = M('Company') -> where(array('token' => $this -> token, 'id' => $this -> _cid)) -> find();
        $userInfo = unserialize($_SESSION[$this -> session_dish_user]);
        if (empty($userInfo)){
            $this -> redirect(U('Repast/select', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id, 'cid' => $this -> _cid)));
        }
        $this -> assign('metaTitle', $company['name']);
        $this -> display();
    }
    public function GetDishList(){
        $company = M('Company') -> where(array('token' => $this -> token, 'id' => $this -> _cid)) -> find();
        $dish_sort = M('Dish_sort') -> where(array('cid' => $this -> _cid)) -> select();
        $dish = M('Dish') -> where(array('cid' => $this -> _cid)) -> select();
        $dish_like = M('Dish_like') -> where(array('cid' => $this -> _cid, 'wecha_id' => $this -> wecha_id)) -> select();
        $like = array();
        foreach ($dish_like as $dl){
            $like[$dl['did']] = 1;
        }
        $mymenu = $this -> getDishMenu();
        $list = array();
        foreach ($dish as $d){
            $t = array();
            $t['id'] = $d['id'];
            $t['aid'] = $d['cid'];
            $t['name'] = $d['name'];
            $t['price'] = $d['price'];
            $t['discount_name'] = '';
            $t['discount_price'] = '';
            $t['class_id'] = $d['sid'];
            $t['pic'] = $d['image'];
            $t['note'] = $d['des'];
            $t['unit'] = $d['unit'];
            $t['tag_name'] = $d['ishot'] ? '推荐' : '';
            $t['html_name'] = '';
            $t['check'] = isset($like[$d['id']]) ? $like[$d['id']] : 0;
            $t['select'] = isset($mymenu[$d['id']]) ? 1 : 0;
            $list[$d['sid']][] = $t;
        }
        $result = array();
        foreach ($dish_sort as $sort){
            $r = array();
            $r['id'] = $sort['id'];
            $r['aid'] = $sort['cid'];
            $r['name'] = $sort['name'];
            $r['dishes'] = isset($list[$sort['id']]) ? $list[$sort['id']] : '';
            $result[] = $r;
        }
        exit(json_encode($result));
    }
    public function dolike(){
        if (empty($this -> wecha_id)){
            exit(json_encode(array('status' => 0)));
        }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $check = isset($_POST['check']) ? intval($_POST['check']) : 0;
        if ($id){
            $dishLike = D('Dish_like');
            $data = array('did' => $id, 'cid' => $this -> _cid, 'wecha_id' => $this -> wecha_id);
            if ($check){
                $dishLike -> add($data);
            }else{
                $dishLike -> where($data) -> delete();
                exit(json_encode(array('status' => 1)));
            }
        }
        exit(json_encode(array('status' => 0)));
    }
    public function like(){
        if ($this -> wecha_id){
            $mymenu = $this -> getDishMenu();
            $dish_like = M('Dish_like') -> where(array('cid' => $this -> _cid, 'wecha_id' => $this -> wecha_id)) -> select();
            $dids = array();
            foreach ($dish_like as $like){
                $dids[] = $like['did'];
            }
            $dish = array();
            if ($dids){
                $list = M('Dish') -> where(array('id' => array('in', $dids), 'cid' => $this -> _cid)) -> select();
                foreach ($list as $row){
                    $row['select'] = isset($mymenu[$row['id']]) ? 1 : 0;
                    $dish[] = $row;
                }
            }
        }else{
            $dish = array();
        }
        $this -> assign('dishlist', $dish);
        $this -> assign('metaTitle', '我喜欢的菜');
        $this -> display();
    }
    public function editOrder(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $num = isset($_POST['num']) ? intval($_POST['num']) : 0;
        $des = isset($_POST['des']) ? htmlspecialchars($_POST['des']) : '';
        if ($id){
            $oldMenu = $this -> getDishMenu();
            if (isset($oldMenu[$id])){
                $oldMenu[$id]['des'] = $des ? $des : $oldMenu[$id]['des'];
                $oldMenu[$id]['num'] += $num;
                if ($oldMenu[$id]['num'] == 0){
                    unset($oldMenu[$id]);
                }
            }elseif ($num > 0){
                $oldMenu[$id]['des'] = $des ;
                $oldMenu[$id]['num'] = $num;
            }
            $_SESSION[$this -> session_dish_info] = serialize($oldMenu);
        }
    }
    public function mymenu(){
        $userInfo = unserialize($_SESSION[$this -> session_dish_user]);
        if (empty($userInfo)){
            $this -> error('没有填写用餐信息，先填写信息，再提交订单！', U('Repast/select', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id, 'cid' => $this -> _cid)));
        }
        $menu = $this -> getDishMenu();
        $data = array();
        $totalNum = $totalPrice = 0;
        if ($menu){
            $dids = array_keys($menu);
            $dishList = M('Dish') -> where(array('cid' => $this -> _cid, 'id' => array('in', $dids))) -> select();
            foreach ($dishList as $dish){
                if (isset($menu[$dish['id']])){
                    $totalNum += $menu[$dish['id']]['num'];
                    $totalPrice += $menu[$dish['id']]['num'] * $dish['price'];
                    $r = array();
                    $r['id'] = $dish['id'];
                    $r['name'] = $dish['name'];
                    $r['price'] = $dish['price'];
                    $r['nums'] = $menu[$dish['id']]['num'];
                    $r['des'] = $menu[$dish['id']]['des'];
                    $data[] = $r;
                }
            }
        }
        $tableName = '';
        if ($userInfo['tableid']){
            $diningTable = M('Dining_table') -> where(array('cid' => $this -> _cid, 'id' => $userInfo['tableid'])) -> find();
            $tableName = isset($diningTable['name']) && isset($diningTable['isbox']) ? ($diningTable['isbox'] ? $diningTable['name'] . '(包厢' . $diningTable['num'] . '座)' : $diningTable['name'] . '(大厅' . $diningTable['num'] . '座)') : '';
        }
        $this -> assign('tableName', $tableName);
        $this -> assign('userInfo', $userInfo);
        $this -> assign('totalNum', $totalNum);
        $this -> assign('totalPrice', $totalPrice);
        $this -> assign('my_dish', $data);
        $this -> assign('metaTitle', '我的订单');
        $this -> display();
    }
    public function getInfo(){
        if (empty($this -> wecha_id)){
            exit(json_encode(array('success' => 0, 'msg' => '无法获取您的微信身份，请关注“公众号”，然后回复“订餐”来使用此功能')));
        }
        exit(json_encode(array('success' => 1, 'msg' => 'ok')));
    }


    public function saveMyOrder(){
        if (empty($this -> wecha_id)){
            unset($_SESSION[$this -> session_dish_info]);
            $this -> error('您的微信账号为空，不能订餐!');
            exit(json_encode(array('success' => 0, 'msg' => '您的微信账号为空，不能订餐!')));
        }
        $dishs = $this -> getDishMenu();
        if (empty($dishs)){
            $this -> error('没有点餐，请去点餐吧!');
        }
        $userInfo = unserialize($_SESSION[$this -> session_dish_user]);//已有好多信息数组
        if (empty($userInfo)){
            $this -> error('您的个人信息有误，请重新下单!', U('Repast/selectTable', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id, 'cid' => $this -> _cid)));
        }
        $userInfo['cid'] = $this ->_cid;
        $userInfo['wecha_id'] = $this -> wecha_id;
        $userInfo['token'] = $this -> token;
        $total = $price = 0;
        $dids = array_keys($dishs);
        $dishList = M('Dish') -> where(array('cid' => $this ->_cid, 'id' => array('in', $dids))) -> select();
        $temp = array();
        foreach ($dishList as $r){
            if (isset($dishs[$r['id']])){
                $temp[$r['id']] = array('price' => $r['price'], 'num' => $dishs[$r['id']]['num'], 'name' => $r['name'], 'des' => $dishs[$r['id']]['des']);
                $total += $dishs[$r['id']]['num'];
                $price += $dishs[$r['id']]['num'] * $r['price'];
            }
        }
        $takeAwayPrice = 0;
        if (isset($userInfo['price']) && $userInfo['price']){
            $price += $userInfo['price'];
            $takeAwayPrice = $userInfo['price'];
        }
        $userInfo['total'] = $total;
        $userInfo['price'] = $price;
        $userInfo['info'] = serialize(array('takeAwayPrice' => $takeAwayPrice, 'list' => $temp));
        $userInfo['time'] = time();
        $userInfo['orderid'] = substr($this -> wecha_id, -1, 4) . date("YmdHis");
        $doid = D('Dish_order') -> add($userInfo);//send_email
        $dis_order_id = $doid;
        if ($doid){$info=M('delisms')->where(array('token'=>$this->token))->find();
			$phone=$info['phone'];
			$user=$info['name'];//短信平台帐号
			$pass=md5($info['password']);//短信平台密码
			$smsstatus=$info['dingcan'];//短信平台状态
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
			$emailstatus=$info['dingcan'];
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
					$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
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

					$mail->Subject = '您的微餐饮订单';
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
			
            
			
            if ($userInfo['tableid']){
                $table_order = array('cid' => $this -> _cid, 
                    'tableid' => $userInfo['tableid'], 
                    'orderid' => $doid, 
                    'wecha_id' => $this -> wecha_id, 
                    'reservetime' => $userInfo['reservetime'], 
                    'creattime' => time());
                $doid = D('Dish_table') -> add($table_order);
            }
            $_SESSION[$this -> session_dish_info] = $_SESSION[$this -> session_dish_user] = '';
            unset($_SESSION[$this -> session_dish_user], $_SESSION[$this -> session_dish_info]);
            $alipayConfig = M('Alipay_config') -> where(array('token' => $this -> token)) -> find();
            $dishCompany = M('Dish_company') -> where(array('cid' => $this -> _cid)) -> find();

            
            $dish_info = unserialize($userInfo['info']);
            $cai_arr_mail = array();
            $cai_arr = array();
            //print_r($dish_info['list']);exit;
            $all_money = 0;
            foreach ($dish_info['list'] as $cai) {
                $c_name  = $cai['name'] . str_repeat(' ', (10-strlen($cai['name'])/3)*2);
                $c_price = str_pad($cai['price'], 5, " ", STR_PAD_RIGHT);
                $cai_arr[] = $c_name . $c_price . $cai['num'];
                
                $c_name  = $cai['name'] . str_repeat(' ', (10-strlen($cai['name'])/3)*3);
                $cai_arr_mail[] = $c_name . $c_price . $cai['num'];
                
                $all_money += $cai['price'] * $cai['num'];
            }
            $email_tpl=
"订单编号：$userInfo[orderid]
联 系 人：$userInfo[name]
电    话：$userInfo[tel]
条目        单价（元）   数量
----------------------------\n" .join(chr(10).chr(13), $cai_arr_mail). "

备注：$userInfo[des]
----------------------------
订餐人数：$userInfo[nums]
总　　价：$all_money
送餐时间：" . date('Y-m-d H:i:s', $userInfo['reservetime']) . "
下单时间：".  date('Y-m-d H:i:s', $userInfo['time']);             
            //发邮件动作
            if ($dishCompany['email_status'] == 1 && $dishCompany['email']) {                                  
                    $to_email       = $dishCompany['email'];
                    $emailuser      = $info['emailuser'];
                    $emailpassword  = $info['emailpassword'];
                    $subject        = "您有新的订单，单号：".$userInfo['orderid']."，预定人：".$userInfo['name'];
                    $body           = $email_tpl;
                    //$this->send_email($subject,$body,$emailuser,$emailpassword,$to_email);
                    
                    $smtpserver = C('email_server'); 
                    $port = C('email_port');
                    $smtpuser = C('email_user');
                    $smtppwd = C('email_pwd');
                    $mailtype = "TXT";
                    $sender = C('email_user');
                    $smtp = new Smtp($smtpserver,$port,true,$smtpuser,$smtppwd,$sender); 
                $to = $to_email;//$list['email']; 
                $subject = $subject;//C('pwd_email_title');
                    //$body = iconv('UTF-8','gb2312',$fetchcontent);inv
                    
                    D('Dish_order')->save(array('send_email' => 1, 'id'=>$dis_order_id));//是否发过邮件
                    
            }

//短信
	    if ($dishCompany['phone_status'] == 1 && $userInfo['takeaway'] != 2){
                if ($userInfo['takeaway'] == 1){
                    Sms :: sendSms($this -> token . "_" . $this -> _cid, "顾客{$userInfo['name']}刚刚叫了一份外卖，订单号：{$userInfo['orderid']}，请您注意查看并处理【云信使】");
                }else{
                    Sms :: sendSms($this -> token . "_" . $this -> _cid, "顾客{$userInfo['name']}刚刚预约了一次用餐，订单号：{$userInfo['orderid']}，请您注意查看并处理【云信使】");
                }
            }
            //打印 
            //商户代码：0466550ef46d11e391ea00163e02163b
            //API：c4e011af
            //设备编码：4600108698566106
            if ($dishCompany['print_status'] == 1 && $dishCompany['memberCode'] && $dishCompany['feiyin_key'] && $dishCompany['deviceNo']) {
                $company_row = M('Company') -> where(array('id' => $userInfo['cid'])) -> find();
                //$this->printTxt($email_tpl, $dishCompany);
               echo $company_row[name];
		$str="
     $company_row[name]
	
条目         单价（元） 数量
----------------------------\n".join(chr(10).chr(8), $cai_arr)."

备注：$userInfo[des]
----------------------------
合计：{$all_money}元 

订餐人数：$userInfo[nums]
送货地址：$userInfo[address]
联系电话：$userInfo[tel]
送餐时间：" . date('Y-m-d H:i:s', $userInfo['reservetime']) . "
订购时间：".date("Y-m-d H:i:s");
		$msgInfo=array(
			'memberCode'=>$dishCompany['memberCode'],
			'msgDetail'=>$str,
			'deviceNo'=>$dishCompany['deviceNo'],
			'msgNo'=>time()+1,
			'reqTime' => number_format(1000*time(), 0, '', '')
		);
		$content = $msgInfo['memberCode'].$msgInfo['msgDetail'].$msgInfo['deviceNo'].$msgInfo['msgNo'].$msgInfo['reqTime'].$dishCompany['feiyin_key'];
		$msgInfo['securityCode'] = md5($content);
		$msgInfo['mode']=2;
		$client = new HttpClient('my.feyin.net');
		if($client->post('/api/sendMsg',$msgInfo)){
			$printstate=$client->getContent();
		}
		if($printstate==0){
                        //echo '打印成功';
			//$this->success('打印成功', U('Printer/index',array('token'=>$this->token)));
		}else{
                    //echo '打印失败';
                    //$this->error('打印失败，错误代码：'.$printstate);
		}                
            }
            
            
            if ($alipayConfig['open'] && $dishCompany['payonline']){
                $this -> success('正在提交中...', U('Alipay/pay', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id, 'success' => 1, 'from' => 'Repast', 'orderName' => $userInfo['orderid'], 'single_orderid' => $userInfo['orderid'], 'price' => $price)));
            }else{
                $this -> redirect(U('Repast/myOrder', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id, 'cid' => $this -> _cid, 'success' => 1)));
            }
   

            exit(json_encode(array('success' => 1, 'msg' => 'ok', 'orderid' => $userInfo['orderid'], 'orderName' => $userInfo['orderid'], 'price' => $price, 'isopen' => $alipayConfig['open'])));
        }else{
            $this -> error('订单出错，请重新下单');
            exit(json_encode(array('success' => 0, 'msg' => '订单出错，请重新下单')));
        }
    }
	//测试打印 $dishCompany['memberCode'] && $dishCompany['feiyin_key'] && $dishCompany['deviceNo']
	public function printTxt($email_tpl, $dishCompany){
               
		$str="
     宝微平台订餐打印
	
条目      单价（元）   数量
----------------------------
番茄炒粉     10.0       1
客家咸香鸡   20.0       1

备注：$userInfo[des]
----------------------------
合计：{$all_money}元 

送货地址：$userInfo[address]
联系电话：$userInfo[tel]
订购时间：".date("Y-m-d H:i:s");
		$msgInfo=array(
			'memberCode'=>$dishCompany['memberCode'],
			'msgDetail'=>$email_tpl,//$str,
			'deviceNo'=>$dishCompany['deviceNo'],
			'msgNo'=>time()+1,
			'reqTime' => number_format(1000*time(), 0, '', '')
		);
		$content = $msgInfo['memberCode'].$msgInfo['msgDetail'].$msgInfo['deviceNo'].$msgInfo['msgNo'].$msgInfo['reqTime'].$dishCompany['feiyin_key'];
		$msgInfo['securityCode'] = md5($content);
		$msgInfo['mode']=2;
		$client = new HttpClient('my.feyin.net');
		if($client->post('/api/sendMsg',$msgInfo)){
			$printstate=$client->getContent();
		}
		if($printstate==0){
                        //echo '打印成功';
			//$this->success('打印成功', U('Printer/index',array('token'=>$this->token)));
		}else{
                    //echo '打印失败';
                    //$this->error('打印失败，错误代码：'.$printstate);
		}
	}
    
//打印方法 $dishCompany['memberCode'] && $dishCompany['feiyin_key'] && $dishCompany['deviceNo']
	public function printTxt_a($email_tpl, $dishCompany){
            $email_tpl = str_replace(chr(13).chr(10), "\r\n", $email_tpl);
			$str=$email_tpl;
			$str .= "\r\n打印时间：".date('Y-m-d H:i:s')."\r\n--------------------------------\r\n";		
			$str="<1B40><1D2111><1B6101>订餐内容<0D0A><1B6100><1D2100><0D0A>".$str;  //初始化打印机加粗居中						
			//$str=iconv('utf-8','gbk',$str);
			//设置打印服务器开始
			$server="http://218.97.194.59:8088/Router/Rest/";  //打印API接口地址
			$appkey= $dishCompany['memberCode'];  //商户编码
                        $appsecret = $dishCompany['feiyin_key'];  // 商户密钥
                        $type = "addPrintContext"  ;//   打印类型
			$printerid = $dishCompany['deviceNo'];  //打印机编号
                        $isrun = "1";   //1为直接打印，非1等待打印
			$printcontext = $str ;    //打印内容
			$printcount= 1;//$printermodel['PrinterCount'];
                        $contentencode=urlencode("$printcontext");
			//$contentencode=$printcontext;
            $url = "$server/?appkey=$appkey&appsecret=$appsecret&type=$type&printerid=$printerid&$isrun=$isrun&printcount=$printcount&printcontext=$contentencode";
                        $content = file_get_contents($url);
			//print_r ('反馈结果'.$content);   //服务器返回结果，成功则返回此订单打印序列号，用于判断修改打印状态及处理状态。
         
			//设置打印服务器结束
			//设置为打印过了
			//$this->product_cart_model->where(array('id'=>$thisOrder['id']))->save(array('printed'=>1,'handled'=>1,'pcid'=>$content));
			//echo "CMD=01	FLAG=0	MESSAGE=成功	DATETIME=".date('YmdHis',$now)."	ORDERCOUNT=".$count."	ORDERID=".$thisOrder['id']."	PRINT=".$str;

    }    
     //发邮件函数
   public function sms(){
		 $userInfo = unserialize($_SESSION[$this -> session_dish_user]);
		$where['token']=$this->token;
		$where['wecha_id']=$this->wecha_id;
		$where['printed']=0;
		$this->dish_order_model=M('dish_order');
		$this->dining_table_model=M('dining_table');
		$count      = $this->dish_order_model->where($where)->count();
		$orders=$this->dish_order_model->where($where)->order('time DESC')->limit(0,1)->select();
		
		$now=time();
		if ($orders){
			$thisOrder=$orders[0];
			
			
			//订餐信息
			$product_diningtable_model=M('dish_order');
			if ($thisOrder['tableid']) {
				$thisTable=$this->dining_table_model->where(array('cid' => $this -> _cid,'id' => $userInfo['tableid']))->find();
				$thisOrder['tableid']=$thisTable['name'];
			}else{
				$thisOrder['tableid']='未指定';
			}
			$str="\r\n订单编号：".$thisOrder['id']."\r\n姓名：".$thisOrder['name']."\r\n电话：".$thisOrder['tel']."\r\n人数：".$thisOrder['nums']."\r\n预约时间：".$thisOrder['reservetime']= date("Y-m-d H:i:s",$thisOrder['reservetime'])."\r\n地址：".$thisOrder['address']."\r\n桌台：".$thisOrder['tableid']."\r\n下单时间：".date('Y-m-d H:i:s',$thisOrder['time'])."\r\n";
			//
			$carts=unserialize($thisOrder['info']);

			//
			$totalFee=0;
			$totalCount=0;
			$products=array();
			$ids=array();
			foreach ($carts as $k=>$c){
				if (is_array($c)){
					$productid=$k;
					$price=$c['price'];
					$count=$c['count'];
					//
					if (!in_array($productid,$ids)){
						array_push($ids,$productid);
					}
					$totalFee+=$price*$count;
					$totalCount+=$count;
				}
			}
			if (count($ids)){
				$products=$this->dish_order_model->where(array('id'=>array('in',$ids)))->select();
			}
			if ($products){
				$i=0;
				foreach ($products as $p){
					$products[$i]['count']=$carts[$p['id']]['count'];
					$str.=$p['name']."  ".$products[$i]['count']."份  单价：".$p['price']."元\r\n";
					$i++;
				}
			}
			$str.="合计：".$thisOrder['price']."元";
			return $str;
		}else {
			return '';
		}
	}

	//增加sms内容止//
    public function clearMyMenu(){
        $_SESSION[$this -> session_dish_info] = null;
        unset($_SESSION[$this -> session_dish_info]);
    }
    public function myOrder(){
        $status = isset($_GET['status']) ? intval($_GET['status']) : 0;
        $where = array('cid' => $this -> _cid, 'wecha_id' => $this -> wecha_id);
        if ($status == 4){
            $where['isuse'] = 1;
            $where['paid'] = 1;
        }elseif ($status == 3){
            $where['isuse'] = 0;
            $where['paid'] = 1;
        }elseif ($status == 2){
            $where['isuse'] = 1;
            $where['paid'] = 0;
        }elseif ($status == 1){
            $where['isuse'] = 0;
            $where['paid'] = 0;
        }
        $dish_order = M('Dish_order') -> where($where) -> order('id DESC') -> select();
        $list = array();
        foreach ($dish_order as $row){
            $row['info'] = unserialize($row['info']);
            $list[] = $row;
        }
        $this -> assign('orderList', $list);
        $this -> assign('status', $status);
        $this -> assign('metaTitle', '我的订单');
        $this -> display();
    }
    public function getDishMenu(){
        if (!isset($_SESSION[$this -> session_dish_info]) || !strlen($_SESSION[$this -> session_dish_info])){
            $dish = array();
        }else{
            $dish = unserialize($_SESSION[$this -> session_dish_info]);
        }
        return $dish;
    }
    public function payReturn(){
        $orderid = $_GET['orderid'];
        if ($order = M('dish_order') -> where(array('orderid' => $orderid, 'token' => $this -> token)) -> find()){
            if ($order['paid']){
                Sms :: sendSms($this -> token . "_" . $this -> _cid, "顾客{$order['name']}刚刚对订单号：{$orderid}的订单进行了支付，请您注意查看并处理【云信使】");
            }
            $this -> redirect(U('Repast/myOrder', array('token' => $this -> token, 'wecha_id' => $this -> wecha_id, 'cid' => $this -> _cid)));
        }else{
            exit('订单不存在');
        }
    }
}



//协议
class HttpClient {
    // Request vars
    var $host;
    var $port;
    var $path;
    var $method;
    var $postdata = '';
    var $cookies = array();
    var $referer;
    var $accept = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
    var $accept_encoding = 'gzip';
    var $accept_language = 'en-us';
    var $user_agent = 'Incutio HttpClient v0.9';
    // Options
    var $timeout = 20;
    var $use_gzip = true;
    var $persist_cookies = true;  // If true, received cookies are placed in the $this->cookies array ready for the next request
                                  // Note: This currently ignores the cookie path (and time) completely. Time is not important, 
                                  //       but path could possibly lead to security problems.
    var $persist_referers = true; // For each request, sends path of last request as referer
    var $debug = false;
    var $handle_redirects = true; // Auaomtically redirect if Location or URI header is found
    var $max_redirects = 5;
    var $headers_only = false;    // If true, stops receiving once headers have been read.
    // Basic authorization variables
    var $username;
    var $password;
    // Response vars
    var $status;
    var $headers = array();
    var $content = '';
    var $errormsg;
    // Tracker variables
    var $redirect_count = 0;
    var $cookie_host = '';
    function HttpClient($host, $port=80) {
        $this->host = $host;
        $this->port = $port;
    }
    function get($path, $data = false) {
        $this->path = $path;
        $this->method = 'GET';
        if ($data) {
            $this->path .= '?'.$this->buildQueryString($data);
        }
        return $this->doRequest();
    }
    function post($path, $data) {
        $this->path = $path;
        $this->method = 'POST';
        $this->postdata = $this->buildQueryString($data);
        return $this->doRequest();
    }
    function buildQueryString($data) {
        $querystring = '';
        if (is_array($data)) {
            // Change data in to postable data
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $val2) {
                        $querystring .= urlencode($key).'='.urlencode($val2).'&';
                    }
                } else {
                    $querystring .= urlencode($key).'='.urlencode($val).'&';
                }
            }
            $querystring = substr($querystring, 0, -1); // Eliminate unnecessary &
        } else {
            $querystring = $data;
        }
        return $querystring;
    }
    function doRequest() {
        // Performs the actual HTTP request, returning true or false depending on outcome
        if (!$fp = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout)) {
            // Set error message
            switch($errno) {
                case -3:
                    $this->errormsg = 'Socket creation failed (-3)';
                case -4:
                    $this->errormsg = 'DNS lookup failure (-4)';
                case -5:
                    $this->errormsg = 'Connection refused or timed out (-5)';
                default:
                    $this->errormsg = 'Connection failed ('.$errno.')';
                $this->errormsg .= ' '.$errstr;
                $this->debug($this->errormsg);
            }
            return false;
        }
        socket_set_timeout($fp, $this->timeout);
        $request = $this->buildRequest();
        $this->debug('Request', $request);
        fwrite($fp, $request);
        // Reset all the variables that should not persist between requests
        $this->headers = array();
        $this->content = '';
        $this->errormsg = '';
        // Set a couple of flags
        $inHeaders = true;
        $atStart = true;
        // Now start reading back the response
        while (!feof($fp)) {
            $line = fgets($fp, 4096);
            if ($atStart) {
                // Deal with first line of returned data
                $atStart = false;
                if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) {
                    $this->errormsg = "Status code line invalid: ".htmlentities($line);
                    $this->debug($this->errormsg);
                    return false;
                }
                $http_version = $m[1]; // not used
                $this->status = $m[2];
                $status_string = $m[3]; // not used
                $this->debug(trim($line));
                continue;
            }
            if ($inHeaders) {
                if (trim($line) == '') {
                    $inHeaders = false;
                    $this->debug('Received Headers', $this->headers);
                    if ($this->headers_only) {
                        break; // Skip the rest of the input
                    }
                    continue;
                }
                if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
                    // Skip to the next header
                    continue;
                }
                $key = strtolower(trim($m[1]));
                $val = trim($m[2]);
                // Deal with the possibility of multiple headers of same name
                if (isset($this->headers[$key])) {
                    if (is_array($this->headers[$key])) {
                        $this->headers[$key][] = $val;
                    } else {
                        $this->headers[$key] = array($this->headers[$key], $val);
                    }
                } else {
                    $this->headers[$key] = $val;
                }
                continue;
            }
            // We're not in the headers, so append the line to the contents
            $this->content .= $line;
        }
        fclose($fp);
        // If data is compressed, uncompress it
        if (isset($this->headers['content-encoding']) && $this->headers['content-encoding'] == 'gzip') {
            $this->debug('Content is gzip encoded, unzipping it');
            $this->content = substr($this->content, 10); // See http://www.php.net/manual/en/function.gzencode.php
            $this->content = gzinflate($this->content);
        }
        // If $persist_cookies, deal with any cookies
        if ($this->persist_cookies && isset($this->headers['set-cookie']) && $this->host == $this->cookie_host) {
            $cookies = $this->headers['set-cookie'];
            if (!is_array($cookies)) {
                $cookies = array($cookies);
            }
            foreach ($cookies as $cookie) {
                if (preg_match('/([^=]+)=([^;]+);/', $cookie, $m)) {
                    $this->cookies[$m[1]] = $m[2];
                }
            }
            // Record domain of cookies for security reasons
            $this->cookie_host = $this->host;
        }
        // If $persist_referers, set the referer ready for the next request
        if ($this->persist_referers) {
            $this->debug('Persisting referer: '.$this->getRequestURL());
            $this->referer = $this->getRequestURL();
        }
        // Finally, if handle_redirects and a redirect is sent, do that
        if ($this->handle_redirects) {
            if (++$this->redirect_count >= $this->max_redirects) {
                $this->errormsg = 'Number of redirects exceeded maximum ('.$this->max_redirects.')';
                $this->debug($this->errormsg);
                $this->redirect_count = 0;
                return false;
            }
            $location = isset($this->headers['location']) ? $this->headers['location'] : '';
            $uri = isset($this->headers['uri']) ? $this->headers['uri'] : '';
            if ($location || $uri) {
                $url = parse_url($location.$uri);
                // This will FAIL if redirect is to a different site
                return $this->get($url['path']);
            }
        }
        return true;
    }
    function buildRequest() {
        $headers = array();
        $headers[] = "{$this->method} {$this->path} HTTP/1.0"; // Using 1.1 leads to all manner of problems, such as "chunked" encoding
        $headers[] = "Host: {$this->host}";
        $headers[] = "User-Agent: {$this->user_agent}";
        $headers[] = "Accept: {$this->accept}";
        if ($this->use_gzip) {
            $headers[] = "Accept-encoding: {$this->accept_encoding}";
        }
        $headers[] = "Accept-language: {$this->accept_language}";
        if ($this->referer) {
            $headers[] = "Referer: {$this->referer}";
        }
        // Cookies
        if ($this->cookies) {
            $cookie = 'Cookie: ';
            foreach ($this->cookies as $key => $value) {
                $cookie .= "$key=$value; ";
            }
            $headers[] = $cookie;
        }
        // Basic authentication
        if ($this->username && $this->password) {
            $headers[] = 'Authorization: BASIC '.base64_encode($this->username.':'.$this->password);
        }
        // If this is a POST, set the content type and length
        if ($this->postdata) {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $headers[] = 'Content-Length: '.strlen($this->postdata);
        }
        $request = implode("\r\n", $headers)."\r\n\r\n".$this->postdata;
        return $request;
    }
    function getStatus() {
        return $this->status;
    }
    function getContent() {
        return $this->content;
    }
    function getHeaders() {
        return $this->headers;
    }
    function getHeader($header) {
        $header = strtolower($header);
        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        } else {
            return false;
        }
    }
    function getError() {
        return $this->errormsg;
    }
    function getCookies() {
        return $this->cookies;
    }
    function getRequestURL() {
        $url = 'http://'.$this->host;
        if ($this->port != 80) {
            $url .= ':'.$this->port;
        }            
        $url .= $this->path;
        return $url;
    }
    // Setter methods
    function setUserAgent($string) {
        $this->user_agent = $string;
    }
    function setAuthorization($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    function setCookies($array) {
        $this->cookies = $array;
    }
    // Option setting methods
    function useGzip($boolean) {
        $this->use_gzip = $boolean;
    }
    function setPersistCookies($boolean) {
        $this->persist_cookies = $boolean;
    }
    function setPersistReferers($boolean) {
        $this->persist_referers = $boolean;
    }
    function setHandleRedirects($boolean) {
        $this->handle_redirects = $boolean;
    }
    function setMaxRedirects($num) {
        $this->max_redirects = $num;
    }
    function setHeadersOnly($boolean) {
        $this->headers_only = $boolean;
    }
    function setDebug($boolean) {
        $this->debug = $boolean;
    }
    // "Quick" static methods
    function quickGet($url) {
        $bits = parse_url($url);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        if (isset($bits['query'])) {
            $path .= '?'.$bits['query'];
        }
        $client = new HttpClient($host, $port);
        if (!$client->get($path)) {
            return false;
        } else {
            return $client->getContent();
        }
    }
    function quickPost($url, $data) {
        $bits = parse_url($url);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        $client = new HttpClient($host, $port);
        if (!$client->post($path, $data)) {
            return false;
        } else {
            return $client->getContent();
        }
    }
    function debug($msg, $object = false) {
        if ($this->debug) {
            print '<div style="border: 1px solid red; padding: 0.5em; margin: 0.5em;"><strong>HttpClient Debug:</strong> '.$msg;
            if ($object) {
                ob_start();
                print_r($object);
                $content = htmlentities(ob_get_contents());
                ob_end_clean();
                print '<pre>'.$content.'</pre>';
            }
            print '</div>';
        }
    }   
}


//协议结束



?>