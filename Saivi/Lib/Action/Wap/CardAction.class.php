<?php
class CardAction extends WapAction{
	public $wecha_id;
	public $thisUser;
	public function __construct(){
		parent::_initialize();
		if (!defined('RES')){
			define('RES',THEME_PATH.'common');
		}
		$this->wecha_id=$this->_get('wecha_id');
		$this->assign('wecha_id',$this->wecha_id);
		//
		$this->token=$this->_get('token');
		$this->thisUser = M('Userinfo')->where(array('token'=>$this->_get('token'),'wecha_id'=>$this->wecha_id))->find();
		if (!$this->wecha_id){
			$this->error('您没有权限使用会员卡，如需使用请关注微信“'.$this->wxuser['wxname'].'”并回复会员卡',U('Index/index',array('token'=>$this->token)));
		}
	}
	public function index(){
		//transfer start
		$data=M('Member_card_create');
		$cardByToken=M('Member_card_set')->where(array('token'=>$this->token))->order('id ASC')->find();
		$data->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
		M('Member_card_exchange')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
		M('Member_card_coupon')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
		M('Member_card_vip')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
		M('Member_card_integral')->where('token=\''.$this->token.'\' AND cardid=0')->save(array('cardid'=>$cardByToken['id']));
		//transfer end
		$member_create_db=M('Member_card_create');
		//
		$cards=$member_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
		$cardsByID=array();
		if ($cards){
			foreach ($cards as $c){
				$cardsByID[$c['cardid']]=$c;
			}
		}
		$cardsCount=count($cards);
		$this->assign('cards',$cards);
		$this->assign('memberCard',$cards[0]);
		if ($cardsCount&&isset($_GET['mycard'])){
			echo '<script>location.href="/index.php?g=Wap&m=Card&a=card&wecha_id='.$this->wecha_id.'&token='.$this->token.'&cardid='.$cards[0]['cardid'].'";</script>';
		}
		$this->assign('cardsCount',$cardsCount);
		//
		$userinfo_db=M('Userinfo');
		$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
		$userScore=0;
		if ($userInfos){
			$userScore=intval($userInfos[0]['total_score']);
		}
		$this->assign('userScore',$userScore);
		//
		$member_card_set_db=M('Member_card_set');
		$allCards=$member_card_set_db->where(array('token'=>$this->token))->order('miniscore ASC')->select();
		if ($allCards){
			$i=0;
			foreach ($allCards as $c){
				$allCards[$i]['applied']=$cardsByID[$c['id']]?1:0;
				if (isset($_GET['mycard'])&&!$allCards[$i]['applied']){
					unset($allCards[$i]);
				}
				$i++;
			}
		}
		$allCardsCount=count($allCards);
		$this->assign('allCards',$allCards);
		$this->assign('allCardsCount',$allCardsCount);
		//
		$thisCompany=M('Company')->where(array('token'=>$this->token,'isbranch'=>0,'display'=>1))->find();
		$this->assign('thisCompany',$thisCompany);
		//
		$infoType='memberCardHome';
		if (isset($_GET['mycard'])){
			$infoType='myCard';
		}
		
		$focus = M('Member_card_focus')->where(array('token'=>$this->_get('token')))->select();
		
		if($focus == NULL){
			$focus = array(
				array(
					"info" => "广告位描述",
					"img" => "/tpl/static/attachment/focus/tour/4.jpg",
					"url" => ""
				),
				array(
					"info" => "广告位描述",
					"img" => "/tpl/static/attachment/focus/tour/3.jpg",
					"url" => ""
				)
			);
		}
		$IndexModel = new IndexAction();
		$focus = $IndexModel->convertLinks($focus);
		$this->assign('flash',$focus);
		$this->assign('infoType',$infoType);
		//
		$this->display();
    }
    public function companyMap(){
    	//
    	$member_card_create_db=M('Member_card_create');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
    	//
    	$this->apikey=C('baidu_map_api');
		$this->assign('apikey',$this->apikey);
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		if (isset($_GET['companyid'])){
			$where['id']=intval($_GET['companyid']);
		}
		$thisCompany=$company_model->where($where)->find();
		$this->assign('thisCompany',$thisCompany);
		$infoType='companyDetail';
		$this->assign('infoType',$infoType);
		$this->display();
    }
    public function companyDetail(){
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token))->order('id ASC')->find();
    	$this->assign('thisCard',$thisCard);
    	//
    	$member_card_create_db=M('Member_card_create');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
    	//
		$company_model=M('Company');
		$where=array('token'=>$this->token,'display'=>1);
		$companies=$company_model->where($where)->order('taxis ASC')->select();
		$this->assign('companies',$companies);
		$infoType='companyDetail';
		$this->assign('infoType',$infoType);
		$this->display();
    }
    public function companyIntro(){
    	//
    	$member_card_create_db=M('Member_card_create');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
    	//
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		if (isset($_GET['companyid'])){
			$where['id']=intval($_GET['companyid']);
		}
		$thisCompany=$company_model->where($where)->find();
		$this->assign('thisCompany',$thisCompany);
		$infoType='companyDetail';
		$this->assign('infoType',$infoType);
		$this->display();
    }
    function card(){
    	$this->assign('infoType','card');
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	$this->assign('card',$thisCard);
    	$error=0;
    	if ($thisCard){
    		$userinfo_db=M('Userinfo');
    		$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    		$userScore=0;
    		if ($userInfos){
    			$userScore=intval($userInfos[0]['total_score']);
    			$userInfo=$userInfos[0];
    		}
    		$this->assign('userScore',$userScore);
    		//
    		$member_card_create_db=M('Member_card_create');
    		$thisMember=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'cardid'=>intval($_GET['cardid'])))->find();
    		$hasApplied=count($thisMember);
    		//
    		if (!$hasApplied){
    			//
    			$card=M('Member_card_create')->field('id,number')->where("token='".$this->token."' and cardid=".$thisCard['id']." and wecha_id = ''")->find();
    			if(!$card){
    				$error=-1;
    			}else {
    				//
    				if (intval($thisCard['miniscore'])==0||$userScore>intval($thisCard['miniscore'])){
    					$error=-4;
    					header('Location:/index.php?g=Wap&m=Userinfo&a=index&token='.$this->token.'&wecha_id='.$this->wecha_id.'&cardid='.$thisCard['id']);
    				}else {
    					$error=-3;
    				}
    			}
    			//
    		}else{
    			$this->assign('thisMember',$thisMember);
    			//
    			$now=time();
    			//
    			$noticeCount=M('Member_card_notice')->where('cardid='.$thisCard['id'].' AND endtime>'.$now)->count();
    			$this->assign('noticeCount',$noticeCount);
    			//
    			$member_card_vip_db=M('Member_card_vip');
    			$previlegeCount=$member_card_vip_db->where('cardid='.$thisCard['id'].' AND ((type=0 AND statdate<'.$now.' AND enddate>'.$now.') OR type=1)')->count();
    			$this->assign('previlegeCount',$previlegeCount);
    			//
    			$member_card_integral_db=M('Member_card_integral');
    			$integralCount=$member_card_integral_db->where('cardid='.$thisCard['id'].' AND statdate<'.$now.' AND enddate>'.$now)->count();
    			$this->assign('integralCount',$integralCount);
    			//
    			$member_card_coupon_db=M('Member_card_coupon');
    			$couponCount=$member_card_coupon_db->where('cardid='.$thisCard['id'].' AND statdate<'.$now.' AND enddate>'.$now)->count();
    			$this->assign('couponCount',$couponCount);
    			//
    			$todaySigned=$this->_todaySigned();
    			$this->assign('todaySigned',$todaySigned);
    			//
    			$this->assign('userInfo',$userInfo);
    		}
    	}else {
    		$error=-2;
    	}
    	$this->assign('error',$error);
    	$this->display();
    }
    public function cardIntro(){
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	//
    	$data=M('Member_card_exchange')->where(array('token'=>$this->token,'cardid'=>$_GET['cardid']))->find();
    	$this->assign('data',$data);
    	//
    	$company_model=M('Company');
		$where=array('token'=>$this->token);
		$thisCompany=$company_model->where($where)->order('isbranch ASC')->find();
		$thisCompany['intro']=str_replace(array('&lt;','&gt;','&quot;','&amp;nbsp;'),array('<','>','"',' '),$thisCompany['intro']);
		$this->assign('thisCompany',$thisCompany);
    	//
    	$this->display();
    }
    public function signscore(){
    	$userinfo_db=M('Userinfo');
    	$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    	$userScore=0;
    	if ($userInfos){
    		$userScore=intval($userInfos[0]['total_score']);
    		$userInfo=$userInfos[0];
    	}
    	$this->assign('userInfo',$userInfo);
    	$this->assign('userScore',$userScore);
    	//
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	//
    	$todaySigned=$this->_todaySigned();
    	$this->assign('todaySigned',$todaySigned);
    	//
    	
    	$cardsign_db   = M('Member_card_sign');
    	$now=time();
    	$day=date('d',$now);
    	$year=date('Y',$now);
    	$month=date('m',$now);
    	if (isset($_GET['month'])){
    		$month=intval($_GET['month']);
    	}
    	$firstday = date('Y-m-01', strtotime($now));
    	$lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    	$firstSecondOfMonth=mktime(0,0,0,$month,1,$year);
    	$lastSecondOfMonth=mktime(23,59,59,$month,$lastday,$year);
    	$signRecords=$cardsign_db->where('token=\''.$this->token.'\' AND wecha_id=\''.$this->wecha_id.'\' AND sign_time>'.$firstSecondOfMonth.' AND sign_time<'.$lastSecondOfMonth)->order('sign_time DESC')->select();
    	$this->assign('signRecords',$signRecords);
    	//
    	$this->display();
    }
    public function addSign(){
    	$signed=$this->_todaySigned();
    	if ($signed){
    		echo'{"success":1,"msg":"您今天已经签到了"}';
    		exit();
    	}
    	$cardsign_db   = M('Member_card_sign');
    	$where    = array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'score_type'=>1);
    	$sign = $cardsign_db->where($where)->order('sign_time desc')->find();
    	//
    	if($sign == null){
    		$cardsign_db->add($where);
    		$sign = $cardsign_db->where($where)->order('id desc')->find();
    	}
    	$get_card=M('member_card_create')->where(array('wecha_id'=>$this->wecha_id,'cardid'=>intval($_GET['cardid'])))->find();
    	//
    	if(empty($get_card)){
    		Header("Location: ".C('site_url').'/'.U('Wap/Card/card',array('token'=>$this->token,'wecha_id'=>$this->wecha_id)));
    		exit('领卡后才可以签到.');
    	}
    	//
    	$set_exchange = M('Member_card_exchange')->where(array('token'=>$this->token,'cardid'=>intval($_GET['cardid'])))->find();
        $this->assign('set_exchange',$set_exchange);
        //
        $userinfo = M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->find();
    	//
    	$data['sign_time']  = time();
    	$data['is_sign']    = 1;
    	$data['score_type'] = 1;
    	$data['token']      = $this->token;
    	$data['wecha_id']   = $this->wecha_id;
    	$data['expense']    = intval($set_exchange['everyday']);
    	$rt=$cardsign_db->where($where)->add($data);
    	//
    	if ($rt){
    		$da['total_score'] = $userinfo['total_score'] +  $data['expense'];
    		$da['sign_score']  = $userinfo['sign_score'] + $data['expense'];
    		$da['continuous']  =  1;
    		//
    		M('Userinfo')->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->save($da);
    		echo'{"success":1,"msg":"签到成功，成功获取了'.$set_exchange['everyday'].'个积分"}';
    	}else {
    		echo'{"success":1,"msg":"暂时无法签到"}';
    	}
    }
    function _todaySigned(){
    	$signined=0;
    	$now=time();
    	$member_card_sign_db   = M('Member_card_sign');
    	$where    = array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'score_type'=>1);
    	$sign = $member_card_sign_db->where($where)->order('sign_time desc')->find();
    	$today = date('Y-m-d',$now);
    	$itoday = date('Y-m-d',intval($sign['sign_time']));
    	if($sign&&$itoday == $today){
    		$signined = 1;
    	}
    	return $signined;
    }
    public function _thisCard(){
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	return $thisCard;
    }
    public function notice(){
    	$this->assign('infoType','notice');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	//
    	$member_card_notice_db=M('Member_card_notice');
    	$now=time();
    	//
    	$notices=$member_card_notice_db->where('cardid='.$thisCard['id'].' AND endtime>'.$now)->select();
    	if ($notices){
    		$i=0;
    		foreach ($notices as $n){
    			$notices[$i]['content']=html_entity_decode($n['content']);
    			$i++;
    		}
    	}
    	$this->assign('notices',$notices);
    	$this->assign('firstItemID',$notices[0]['id']);
    	$this->display();
    }
    public function previlege(){
    	$this->assign('infoType','privelege');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	//
    	$now=time();
    	//
    	$member_card_vip_db=M('Member_card_vip');
    	$list=$member_card_vip_db->where('cardid='.$thisCard['id'].' AND ((type=0 AND statdate<'.$now.' AND enddate>'.$now.') OR type=1)')->order('create_time desc')->select();
    	if ($list){
    		$i=0;
    		foreach ($list as $n){
    			$list[$i]['info']=html_entity_decode($n['info']);
    			$i++;
    		}
    	}
    	$this->assign('firstItemID',$list[0]['id']);
    	$this->assign('list',$list);
    	//
    	$this->display();
    }
    public function integral(){
    	$this->assign('infoType','integral');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	//
    	$now=time();
    	//
    	$member_card_integral_db=M('Member_card_integral');
    	$list=$member_card_integral_db->where('cardid='.$thisCard['id'].' AND statdate<'.$now.' AND enddate>'.$now)->order('create_time desc')->select();
    	$this->assign('integralCount',$integralCount);
    	if ($list){
    		$i=0;
    		foreach ($list as $n){
    			$list[$i]['info']=html_entity_decode($n['info']);
    			$i++;
    		}
    	}
    	$this->assign('firstItemID',$list[0]['id']);
    	$this->assign('list',$list);
    	//
    	$this->display();
    }
    public function coupon(){
    	$this->assign('infoType','coupon');
    	$thisCard=$this->_thisCard();
    	$this->assign('thisCard',$thisCard);
    	//
    	$now=time();
    	//
    	$member_card_coupon_db=M('Member_card_coupon');
    	$list=$member_card_coupon_db->where('cardid='.$thisCard['id'].' AND statdate<'.$now.' AND enddate>'.$now)->order('create_time desc')->select();
    	if ($list){
    		$i=0;
    		foreach ($list as $n){
    			$list[$i]['info']=html_entity_decode($n['info']);
    			$list[$i]['useCount']=M('Member_card_use_record')->where(array('itemid'=>$n['id'],'cat'=>3,'wecha_id'=>$this->wecha_id))->sum('usecount');
    			$list[$i]['nouseCount']=intval($n['people'])-$list[$i]['useCount'];
    			$i++;
    		}
    	}
    	$this->assign('firstItemID',$list[0]['id']);
    	$this->assign('list',$list);
    	//
    	$this->display();
    }
    public function action_usePrivelege(){
    	if (IS_POST){
		
			$paytype = intval($_POST['paytype']);
    		$itemid=intval($_POST['itemid']);
    		$db=M('Member_card_vip');
    		$thisItem=$db->where(array('id'=>$itemid))->find();
    		if (!$thisItem){
    			echo'{"success":-2,"msg":"不存在指定特权"}';
    			exit();
    		}
    		//
    		$member_card_set_db=M('Member_card_set');
    		$thisCard=$member_card_set_db->where(array('id'=>intval($thisItem['cardid'])))->find();
    		$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
    		if (!$thisCard){
    			echo'{"success":-3,"msg":"会员卡不存在"}';
    			exit();
    		}
    		//
			$userinfo_db=M('Userinfo');
    		$thisUser = $this->thisUser;
			
		if($paytype == 0){
    		$staff_db=M('Company_staff');
    		$thisStaff=$staff_db->where(array('username'=>$this->_post('username'),'token'=>$thisCard['token']))->find();
    		if (!$thisStaff){
    			echo'{"success":-4,"msg":"用户名和密码不匹配"}';
    			exit();
    		}else {
    			if (md5($this->_post('password'))!=$thisStaff['password']){
    				echo'{"success":-4,"msg":"用户名和密码不匹配"}';
    				exit();
    			}else {

						$now=time();
						//score
						$arr=array();
						$arr['itemid']=$this->_post('itemid');
						$arr['token']=$thisItem['token'];
						$arr['wecha_id']=$this->_post('wecha_id');
						$arr['expense']=$this->_post('money');
						$arr['time']=$now;
						$arr['cat']=$this->_post('cat');
						$arr['staffid']=$thisStaff['id'];
						//
						
						$arr['score']=intval($set_exchange['reward'])*$arr['expense'];
						//
						M('Member_card_use_record')->add($arr);
						$userinfo_db=M('Userinfo');
						$thisUser = $this->thisUser;
						$userArr=array();
						$userArr['total_score']=$thisUser['total_score']+$arr['score'];
						$userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
						$userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->save($userArr);
						//
						$useCount=intval($thisItem['usetime'])+1;
						$db->where(array('id'=>$itemid))->save(array('usetime'=>$useCount));
						echo'{"success":1,"msg":"数据提交成功"}';
					}
    			}
    		}else{
						$arr['itemid']=$this->_post('itemid');
						$arr['wecha_id']=$this->_post('wecha_id');
						$arr['expense']=$_POST['money'];
						$arr['time']=time();
						$arr['token']=$thisItem['token'];
						$arr['cat']=1;
						$arr['staffid']=0;
						$arr['usecount']=1;;
						$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
						$arr['score']=intval($set_exchange['reward'])*$arr['expense'];
						if($arr['expense'] <= 0){
							$this->error('请输入有效的金额');
						}
						$single_orderid = date('YmdHis',time()).mt_rand(1000,9999);
						
					$record['orderid'] = $single_orderid;
					$record['ordername'] = '支付除特权外多余款项';
					$record['paytype'] = 'CardPay';
					$record['createtime'] = time();
					$record['paid'] = 0;
					$record['price'] = $arr['expense'];
					$record['token'] = $this->token;
					$record['wecha_id'] = $this->wecha_id;
					$record['type'] = 0;
					$result = M('Member_card_pay_record')->add($record);
					if(!$result){
						$this->error('提交记录失败');
					}
						
				$this->redirect(U('CardPay/pay',array('from'=>'Card','token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id'),'price'=>$arr['expense'],'single_orderid'=>$single_orderid,'orderName'=>'支付除特权外多余款项','redirect'=>'Card/payReturn|itemid:'.$arr['itemid'].',usecount:'.$arr['usecount'].',score:'.$arr['score'].',type:privelege')));
			
			}
    		//
    	}else {
    		echo'{"success":-1,"msg":"不是post数据"}';
    	}
    }
    function action_useIntergral(){
    	$now=time();
    	if (IS_POST){
    		$itemid=intval($_POST['itemid']);
    		$db=M('Member_card_integral');
    		$thisItem=$db->where('id='.$itemid.' AND statdate<'.$now.' AND enddate>'.$now)->find();
    		if (!$thisItem){
    			echo'{"success":-2,"msg":"不存在指定信息"}';
    			exit();
    		}
    		
    		//
    		$member_card_set_db=M('Member_card_set');
    		$thisCard=$member_card_set_db->where(array('id'=>intval($thisItem['cardid'])))->find();
    		if (!$thisCard){
    			echo'{"success":-3,"msg":"会员卡不存在"}';
    			exit();
    		}
    		//
    		$userinfo_db=M('Userinfo');
    		$thisUser = $this->thisUser;
    		if ($thisUser['total_score']<$thisItem['integral']){
    			echo'{"success":-5,"msg":"您只有'.intval($thisUser['total_score']).'个积分，无法兑换"}';
    			exit();
    		}
    		//
    		$staff_db=M('Company_staff');
    		$thisStaff=$staff_db->where(array('username'=>$this->_post('username'),'token'=>$thisCard['token']))->find();
    		if (!$thisStaff){
    			echo'{"success":-4,"msg":"用户名和密码不匹配"}';
    			exit();
    		}else {
    			if (md5($this->_post('password'))!=$thisStaff['password']){
    				echo'{"success":-4,"msg":"用户名和密码不匹配"}';
    				exit();
    			}else {
    				//score
    				$arr=array();
    				$arr['itemid']=$this->_post('itemid');
    				$arr['wecha_id']=$this->_post('wecha_id');
    				$arr['expense']=0;
    				$arr['time']=$now;
    				$arr['token']=$thisItem['token'];
    				$arr['cat']=$this->_post('cat');
    				$arr['staffid']=$thisStaff['id'];
    				//
    				$arr['score']=0-intval($thisItem['integral']);
    				//
    				M('Member_card_use_record')->add($arr);
    				
    				$userArr=array();
    				$userArr['total_score']=$thisUser['total_score']+$arr['score'];
    				$userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
    				$userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->save($userArr);
    				//
    				$useCount=intval($thisItem['usetime'])+1;
    				$db->where(array('id'=>$itemid))->save(array('usetime'=>$useCount));
    				echo'{"success":1,"msg":"兑换成功"}';
    			}
    		}
    		//
    	}else {
    		echo'{"success":-1,"msg":"不是post数据"}';
    	}
    }
    function action_useCoupon(){
		
    	$now=time();
    	if (IS_POST){
    		$itemid=intval($_POST['itemid']);
			$paytype = intval($_POST['paytype']);
    		$db=M('Member_card_coupon');
    		$thisItem=$db->where('id='.$itemid.' AND statdate<'.$now.' AND enddate>'.$now)->find();
    		if (!$thisItem){
    			echo'{"success":-2,"msg":"不存在指定信息"}';
    			exit();
    		}
    		
    		//
    		$member_card_set_db=M('Member_card_set');
    		$thisCard=$member_card_set_db->where(array('id'=>intval($thisItem['cardid'])))->find();
    		if (!$thisCard){
    			echo'{"success":-3,"msg":"会员卡不存在"}';
    			exit();
    		}
    		//
    		$userinfo_db=M('Userinfo');
    		$thisUser = $this->thisUser;
    		//
    		$useTime=intval($_POST['usetime']);
    		$useCount=M('Member_card_use_record')->where(array('itemid'=>$itemid,'cat'=>3,'wecha_id'=>$this->_post('wecha_id')))->sum('usecount');

    		$useCount=$useCount+$useTime;
			
    		if (intval($useCount)>intval($thisItem['people'])){
    			echo'{"success":-5,"msg":"最多能用'.$thisItem['people'].'次"}';
    			exit();
    		}
    		//
		if($paytype == 0){
			
    		$staff_db=M('Company_staff');
    		$thisStaff=$staff_db->where(array('username'=>$this->_post('username'),'token'=>$thisCard['token']))->find();
    		if (!$thisStaff){
    			echo'{"success":-4,"msg":"用户名和密码不匹配"}';
    			exit();
    		}else {
    			if (md5($this->_post('password'))!=$thisStaff['password']){
    				echo'{"success":-4,"msg":"用户名和密码不匹配"}';
    				exit();
    			}else {

						$arr=array();
						$arr['itemid']=$this->_post('itemid');
						$arr['wecha_id']=$this->_post('wecha_id');
						$arr['expense']=$this->_post('money');
						$arr['time']=$now;
						$arr['token']=$thisItem['token'];
						$arr['cat']=$this->_post('cat');
						$arr['staffid']=$thisStaff['id'];
						$arr['usecount']=$useTime;
						//
						$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
						$arr['score']=intval($set_exchange['reward'])*$arr['expense'];
						//
						M('Member_card_use_record')->add($arr);
						$userArr=array();
						$userArr['total_score']=$thisUser['total_score']+$arr['score'];
						$userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
						$userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->save($userArr);
						//
						$thisuseCount=intval($thisItem['usetime'])+$useTime;
						$db->where(array('id'=>$itemid))->save(array('usetime'=>$thisuseCount));
						echo'{"success":1,"msg":"成功提交记录"}';
						
				
					}
    			}
		}else{
						$arr['itemid']=$this->_post('itemid');
						$arr['wecha_id']=$this->_post('wecha_id');
						$arr['expense']=$_POST['money'];
						$arr['time']=$now;
						$arr['token']=$thisItem['token'];
						$arr['cat']=3;
						$arr['staffid']=0;
						$arr['usecount']=$useTime;
						$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
						$arr['score']=intval($set_exchange['reward'])*$arr['expense'];
						
						if($arr['expense'] <= 0){
							$this->error('请输入有效的金额');
						}
						
					$single_orderid = date('YmdHis',time()).mt_rand(1000,9999);
				
					$record['orderid'] = $single_orderid;
					$record['ordername'] = '支付除优惠劵外多余款项';
					$record['paytype'] = 'CardPay';
					$record['createtime'] = time();
					$record['paid'] = 0;
					$record['price'] = $arr['expense'];
					$record['token'] = $this->token;
					$record['wecha_id'] = $this->wecha_id;
					$record['type'] = 0;
					$result = M('Member_card_pay_record')->add($record);
					if(!$result){
						$this->error('提交记录失败');
					}
					
				$this->redirect(U('CardPay/pay',array('from'=>'Card','token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id'),'price'=>$arr['expense'],'single_orderid'=>$single_orderid,'orderName'=>'支付除优惠劵外多余款项','redirect'=>'Card/payReturn|itemid:'.$arr['itemid'].',usecount:'.$arr['usecount'].',score:'.$arr['score'].',type:coupon')));
			
			}
    		//
    	}else {
    		echo'{"success":-1,"msg":"不是post数据"}';
    	}
    }
    public function expense(){
    	$userinfo_db=M('Userinfo');
    	$userInfos=$userinfo_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->select();
    	$userScore=0;
    	if ($userInfos){
    		$userScore=intval($userInfos[0]['total_score']);
    		$userInfo=$userInfos[0];
    	}
    	$this->assign('userInfo',$userInfo);
    	$this->assign('userScore',$userScore);
    	//
    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
    	$this->assign('thisCard',$thisCard);
    	//
    	
    	$db   = M('Member_card_use_record');
    	$now=time();
    	$day=date('d',$now);
    	$year=date('Y',$now);
    	$month=date('m',$now);
    	if (isset($_GET['month'])){
    		$month=intval($_GET['month']);
    	}
			$nowY = date('Y');
			$start = strtotime($nowY."-".$month."-01");
			$last = strtotime(date('Y-m-d',$start)." +1 month -1 day");
    	$records=$db->where('token=\''.$this->token.'\' AND wecha_id=\''.$this->wecha_id.'\' AND time>'.$start.' AND time<'.$last)->order('time DESC')->select();
    	$this->assign('records',$records);
    	//
    	$this->display();
    }
    
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////
	public function request(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			//卡号
			$card=M('member_card_create')->where(array('token'=>$token))->order('id asc')->find();
			//联系方式
			$contact=M('member_card_contact')->where(array('token'=>$token))->order('sort desc')->find();
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('info',$info);			
		}else{
			$this->error('无此信息');
		}
		$this->display();	
    }

	public function get_card(){
		$token=$this->_get('token');
		$wecha_id=$this->_get('wecha_id');
		$card=M('member_card_create')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->find();
		if($card){
			header('Location:'.rtrim(C('site_url'),'/').U('Wap/Card/vip',array('token'=>$token,'wecha_id'=>$wecha_id)));
		}
		
		
		
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		
		$get_card=M('member_card_create')->where(array('wecha_id'=>$wecha_id))->find();

		if($get_card!=false){
			Header("Location: ".C('site_url').'/'.U('Wap/Card/vip',array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id')))); 
		}
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			//联系方式
		
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$contact=M('company')->where(array('token'=>$token,'branch'=>0))->find();
			$this->assign('contact',$contact);
			$this->assign('info',$info);
		}else{
			$this->error('无此信息');
		}
		$this->display();	
    }

	public function info(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
		$token=$this->_get('token');
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			$info['description']=nl2br($info['description']);
			//联系方式
			$contact=M('member_card_contact')->where(array('token'=>$token))->order('sort desc')->find();
			//我的卡号
			$mycard=M('member_card_create')->where(array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id')))->find();
			$this->assign('mycard',$mycard);
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('info',$info);
		}else{
			$this->error('无此信息');
		}
		$this->display();	
    }

	public function vip(){
		$token=$this->_get('token');
		$wecha_id=$this->_get('wecha_id');
		$card=M('member_card_create')->where(array('token'=>$token,'wecha_id'=>$wecha_id))->find();
		if($card==false){
			header('Location:'.rtrim(C('site_url'),'/').U('Wap/Card/get_card',array('token'=>$token,'wecha_id'=>$wecha_id)));
		}
		//
	   $agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}

		 
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			$info=M('member_card_info')->where(array('token'=>$token))->find();
			//卡号
			$card=M('member_card_create')->where(array('token'=>$token,'wecha_id'=>$this->_get('wecha_id')))->find();
			//var_dump($card);exit;
			//dump(array('token'=>$token,'wecha_id'=>$this->get('wecha_id')));
			//联系方式
			$contact=M('company')->where(array('token'=>$token,'branch'=>0))->find();
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('info',$info);			
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//dump($data);
			$this->assign('card',$data);
			//特权服务
			$vip=M('member_card_vip')->where(array('token'=>$token))->order('id desc')->find();
			//dump($vip);
			$this->assign('vip',$vip);
			//优惠卷
			$coupon=M('member_card_coupon')->where(array('token'=>$token))->find();
			$this->assign('coupon',$coupon);
			//兑换
			$integral=M('member_card_integral')->where(array('token'=>$token))->find();
			$this->assign('integral',$integral);
		}else{
			$this->error('无此信息');
		}
	
		$this->display();
	
	}
	public function addr(){
	$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';exit;
		}
	
		$token=$this->_get('token');
		if($token!=false){
			//会员卡信息
			$data=M('member_card_set')->where(array('token'=>$token))->find();
			//商家信息
			//$addr=M('member_card_contact')->where(array('token'=>$token))->select();
			//if (!$addr){
			$addr=M('Company')->where(array('token'=>$token))->order('isbranch ASC')->select();
			if ($addr){
				$i=0;
				foreach ($addr as $a){
					$addr[$i]['info']=$a['address'];
					$addr[$i]['tel']=$a['tel'];
					$i++;
				}
			}
			//}
			//联系方式
			$contact=M('member_card_contact')->where(array('token'=>$token))->order('sort desc')->find();
			//我的卡号
			$mycard=M('member_card_create')->where(array('token'=>$this->_get('token'),'wecha_id'=>$this->_get('wecha_id')))->find();
			$this->assign('mycard',$mycard);
			$this->assign('card',$data);
			$this->assign('card_info',$card);
			$this->assign('contact',$contact);
			$this->assign('addr',$addr);
		}else{
			$this->error('无此信息');
		}
		$this->display();
	
	}
	//充值页面
	public function topay(){
		$config = M('Alipay_config')->where(array('token'=>$this->token))->find();
		
		$info['cardid'] = $this->_get('cardid','intval');
		$info['token'] = $this->_get('token');
		$info['wecha_id'] = $this->_get('wecha_id');
		$member_card_set_db=M('Member_card_set');
		$member_card_create_db=M('Member_card_create');
		$thisCard=$member_card_set_db->where(array('token'=>$this->token,'id'=>intval($_GET['cardid'])))->find();
		$card = $member_card_create_db->field('number')->where(array('token'=>$info['token'],'wecha_id'=>$info['wecha_id']))->find();
		$company_model=M('Company');
		
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$this->assign('cardsCount',$cardsCount);
		$token = $this->token;
		$thisCompany=$company_model->where("token = '$token'")->find();
		
		
		$this->assign('thisCompany',$thisCompany);
		$this->assign('info',$info);
		$this->assign('card',$card);
		$this->assign('thisCard',$thisCard);
		$this->display();
	}
	//充值处理
	public function payAction(){
		$price = $_POST['price'];
		$orderid = $this->_get('orderid');
		
		if($orderid == '' && $price <= 0){
			$this->error('请填写正确的充值金额');
		}
		$record = M('member_card_pay_record');
		
		
		$token = $this->_get('token');
		$wecha_id = $this->_get('wecha_id');
		if($orderid != ''){
			$res = $record->where("token = '$token' AND wecha_id = '$wecha_id' AND orderid = $orderid AND paid = 0")->find();
		
			if($res){
				$this->success('提交成功，正在跳转支付页面..',U('Alipay/pay',array('from'=>'Card','orderName'=>$res['ordername'],'single_orderid'=>$res['orderid'],'token'=>$res['token'],'wecha_id'=>$res['wecha_id'],'price'=>$res['price'])));
			}else{
				$this->error('无此订单');
			}
		}
	
		
		$_POST['wecha_id'] = $wecha_id;
		$_POST['token'] = $token;
		$_POST['createtime'] = time();
		$_POST['orderid'] = date('YmdHis',time()).mt_rand(1000,9999);
		$_POST['ordername'] = $_POST['number'].' 充值';
		

		if($record->create($_POST)){
			if($record->add($_POST)){
				$this->success('提交成功，正在跳转支付页面..',U('Alipay/pay',array('from'=>'Card','orderName'=>$_POST['ordername'],'single_orderid'=>$_POST['orderid'],'token'=>$_POST['token'],'wecha_id'=>$_POST['wecha_id'],'price'=>$price)));
			}
		}else{
			$this->error('系统错误');
		}
		
	}
	//支付返回
	public function payReturn(){
	
		$orderid = $_GET['orderid'];
		$token = $_GET['token'];
		$wecha_id = $_GET['wecha_id'];
		$record = M('member_card_pay_record');
		$order = $record->where("orderid = '$orderid' AND token = '$token' AND wecha_id = '$wecha_id'")->find();
		
		if($order){
			if($order['paid'] == 1){
				$record->where("orderid = '$orderid'")->setField('paytime',time());
				if($order['type'] == 1){
					M('Userinfo')->where("wecha_id = '$wecha_id' AND token = '$token'")->setInc('balance',$order['price']);
				}else{
					$lastid = M('Member_card_use_record')->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->order('id DESC')->getField('id');
					if($this->_get('type') == 'coupon'){
						M('Member_card_coupon')->where(array('token'=>$this->token,'id'=>(int)$this->_get('itemid')))->setInc('usetime',(int)$this->_get('usecount'));
						M('Member_card_use_record')->where(array('token'=>$this->token,'id'=>$lastid))->setField(array('usecount'=>(int)$this->_get('usecount'),'cat'=>3));
					}elseif($this->_get('type') == 'privelege'){
						M('Member_card_vip')->where(array('token'=>$this->token,'id'=>(int)$this->_get('itemid')))->setInc('usetime');
						M('Member_card_use_record')->where(array('token'=>$this->token,'id'=>$lastid))->setField('cat',1);
					}
					
				}
				
				$this->success('支付成功',U('Card/index',array('token'=>$token,'wecha_id'=>$wecha_id)));
			}else{
				exit('支付失败');
			}
		
		}else{
			exit('订单不存在');
		}
	
	}
	
	//充值消费记录
	public function payRecord(){

		
		$token = $this->token;
		$wecha_id = $this->wecha_id;

		$record = M('Member_card_pay_record');

    	$member_card_set_db=M('Member_card_set');
    	$thisCard=$member_card_set_db->where(array('token'=>$token,'id'=>intval($_GET['cardid'])))->find();

		$m = $this->_get('month','intval');
		
		if($m != ''){
			$nowY = date('Y');
			$start = strtotime($nowY."-".$m."-01");
			$last = strtotime(date('Y-m-d',$start)." +1 month -1 day");
			$list = $record->where("token = '$token' AND wecha_id = '$wecha_id' AND createtime < $last AND createtime > $start")->order('createtime DESC')->select();
		}else{
			$list = $record->where("token = '$token' AND wecha_id = '$wecha_id'")->order('createtime DESC')->select();
		}

		
		$balance = M('Userinfo')->field('balance')->where("token = '$token' AND wecha_id = '$wecha_id'")->find();
		

		
    	$member_card_create_db=M('Member_card_create');
		$company_model=M('Company');
		$cardsCount=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id))->count();
		$thisCompany=$company_model->where("token = '$token'")->find();
		
		$this->assign('thisCompany',$thisCompany);
		
		$this->assign('cardsCount',$cardsCount);
		
		$this->assign('balance',$balance['balance']);
		$this->assign('thisCard',$thisCard);
		$this->assign('list',$list);
		$this->assign('cardid',$this->_get('cardid','intval'));
		$this->display();
	}
}
?>