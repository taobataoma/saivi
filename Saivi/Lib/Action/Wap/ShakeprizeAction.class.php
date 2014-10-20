<?php
class ShakeprizeAction extends BaseAction{
	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"Mobile")) {
			//echo '此功能只能在微信浏览器中使用';exit;
		}
	 
		$token	  =  $this->_get('token');
		$wecha_id = $this->_get('wecha_id');
		if (!$wecha_id){
			//$wecha_id='null';
		}
		$id 	  = $this->_get('id');
		$Lottery =	M('Lottery')->where(array('id'=>$id,'token'=>$token,'type'=>6))->find(); 
		$this->assign('Shakeprize',$Lottery);
		$this->assign('token',$token);
		$this->display();
		
	}
	
	public function info() {
		$id = $this->_get("id");
		$token = $this->_get("token");
		$wecha_id = $this->_get("wxid");
		$where	  = array('token'=>$token,'id'=>$id);
		$shake = M("Lottery")->where($where)->find();
		if(!$shake){
			die(json_encode(array("ret"=>1,"msg"=>"活动不存在")));
		}

		$prize=array();
		if($shake['fist']!="" && $shake['fistnums']!=0){
			$prize[]=array("name"=>$shake['fist'],"number"=>$shake['fistnums'],"pic"=>"/assets/public/css/images/liwu.png","bigpic"=>"/assets/public/css/images/liwu.png");
		}
		if($shake['second']!="" && $shake['secondnums']!=0){
			$prize[]=array("name"=>$shake['second'],"number"=>$shake['secondnums'],"pic"=>"/assets/public/css/images/liwu.png","bigpic"=>"/assets/public/css/images/liwu.png");
		}
		if($shake['third']!="" && $shake['thirdnums']!=0){
			$prize[]=array("name"=>$shake['fist'],"number"=>$shake['thirdnums'],"pic"=>"/assets/public/css/images/liwu.png","bigpic"=>"/assets/public/css/images/liwu.png");
		}
		$myRecordList = array();
		$recordList = array();
		$where2	  = array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id);
		$shakerecord = M("Lottery_record")->where($where2)->find();
		$usechance = 0;
		if($shakerecord){
			if($shakerecord["islottery"]){
					$myRecordList[]=array("id"=>$shakerecord["id"],"prize_name"=>$shakerecord["prize"],"prize_type"=>$shakerecord["prize"],"status"=>0,"inputtime"=>$shakerecord["time"]);
					//status=1则是可以兑奖的状态
			}
			$usechance = $shakerecord["usenums"];
		}
		$chance = $shake['canrqnums']-$usechance;
		$return = array(
			"ret"=>0,
			"data"=>array(
			"id"=>			$shake['lid'],
			"cate_id"=>		"",
			"wid"=>			"",
			"title"=>		$shake['title'],
			"desc"=>		$shake['info'],
			"content"=>		$shake['info'],
			"start_time"=>	$shake['statdate'],
			"end_time"=>	$shake['enddate'],
			"rule"=>		array("number"=>$shake['canrqnums'],"type"=>1,"lotterynum"=>0),
			"prize"=>		$prize,
			"advset"=>		array("displaywinner"=>0,"showprizenum"=>1),
			"cover_img"=>	array("image_start"=>"/assets/game/shake_start.jpg","image_end"=>"/assets/game/shake_start.jpg"),
			"backgroundimage"=>		"",
			"tips"=>	array("wintips"=>$shake["sttxt"],"failtips"=>"再接再厉哟，大奖马上就来！","endtitle"=>$shake["endtite"]),
			"views"=>		$shake['click'],
			"joins"=>		$shake['joinnum'],
			"auth"=>		"0",
			"chance"=>		$chance,
			"type"=>		"5",
			"validatecode"=>		"g",
			"inputtime"=>		$shake['createtime'],
			"is_deleted"=>		0,
			"is_start"=>		1,
			"is_end"=>			1,
			"game_status"=>		1,
			"totalchance"=>		100,
			"recordList"=>		$recordList,
			"myRecordList"=>	$myRecordList,
		)
		);
		die(json_encode($return));
	}
	public function run() {
		$id = $this->_post("id");
		$token = $this->_post("token");
		$wecha_id = $this->_post("wxid");
		
		$redata	  = M('Lottery_record');
		$where	  = array('token'=>$token,'wecha_id'=>$wecha_id,'lid'=>$id);
		$record   = $redata->where($where)->find();		
		if($record == Null){
			// 还没有个人的记录，则应该增加下
			$redata->add($where);
			//sleep(1);
			$record =$redata->where($where)->find();
		} 
		
		$Lottery =	M('Lottery')->where(array('id'=>$id,'token'=>$token,'type'=>6))->find(); 
		$data = array();

		if ($Lottery['enddate'] < time()) {
			//活动结束
			$return = array("ret"=>1,"msg"=>"活动已经结束");
			die(json_encode($return));
		}

		if ($record['islottery'] == 1) {
			//已经中过奖
			$return = array("ret"=>1,"msg"=>"您已经中过一个奖，把机会留给别人吧");
			die(json_encode($return));
		}else{
		
			if ($record['usenums'] >= $Lottery['canrqnums'] ) {
				//次数已经达到限定
				$return = array("ret"=>1,"msg"=>"抽奖次数已用完");
				die(json_encode($return));

			}else{
				
				$redata->where(array('id'=>$record['id']))->setInc('usenums');
				$record = $redata->where(array('id'=>$record['id']))->find();
				$firstNum=intval($Lottery['fistnums']);
				$secondNum=intval($Lottery['secondnums']);
				$thirdNum=intval($Lottery['thirdnums']);
				$fourthNum=intval($Lottery['fournums']);
				$fifthNum=intval($Lottery['fivenums']);
				$sixthNum=intval($Lottery['sixnums']);
				$multi=intval($Lottery['canrqnums']);//最多抽奖次数
				$prize_arr = array(
				'0' => array('id'=>1,'prize'=>'一等奖:'.$Lottery['fist'],'v'=>$firstNum,'start'=>0,'end'=>$firstNum),
				'1' => array('id'=>2,'prize'=>'二等奖'.$Lottery['second'],'v'=>$secondNum,'start'=>$firstNum,'end'=>$firstNum+$secondNum),
				'2' => array('id'=>3,'prize'=>'三等奖'.$Lottery['third'],'v'=>$thirdNum,'start'=>$firstNum+$secondNum,'end'=>$firstNum+$secondNum+$thirdNum),
				'3' => array('id'=>4,'prize'=>'谢谢参与','v'=>(intval($Lottery['allpeople']))*$multi-($firstNum+$secondNum+$thirdNum),'start'=>$firstNum+$secondNum+$thirdNum,'end'=>intval($Lottery['allpeople'])*$multi)
				);


				foreach ($prize_arr as $key => $val) { 
					$arr[$val['id']] = $val; 
				} 
				if ($Lottery['allpeople'] == 1) {
 
					if ($Lottery['fistlucknums'] <= $Lottery['fistnums']) {
						$rid = 1;	
					}else{
						$rid = 4;	
					}			
				 
				}else{
					$rid = $this->get_rand($arr,intval($Lottery['allpeople'])*$multi); 
				}
				
			
				$winprize = $prize_arr[$rid-1]['prize'];
				$zjl = false;
				$prizetype = 0;//中奖的类型，标示中奖等级，扩展用，0是未中奖，或是4个未中奖
				switch($rid){
					case 1:
						if ($Lottery['fistlucknums'] > $Lottery['fistnums']) {
							 $zjl = false; 
							 $winprize = '谢谢参与';
						}else{
							$zjl	= true;
							$prizetype = 1;
							M('Lottery')->where(array('id'=>$id))->setInc('fistlucknums');
						}
					break;
						
					case 2:
						if ($Lottery['secondlucknums'] > $Lottery['secondnums']) {
								$zjl = false;
								$winprize = '谢谢参与';
						}else{
							//判断是否设置了2等奖&&数量
							if(empty($Lottery['second']) && empty($Lottery['secondnums'])){
								$zjl = false;
								$winprize = '谢谢参与';
							}else{ //输出中了二等奖
								$zjl	= true;
								$prizetype = 2;
								M('Lottery')->where(array('id'=>$id))->setInc('secondlucknums');
							}	 
							
						}
					break;
						
					case 3:
						if ($Lottery['thirdlucknums'] > $Lottery['thirdnums']) {
							 $zjl = false;
							 $winprize = '谢谢参与';
						}else{
							if(empty($Lottery['third']) && empty($Lottery['thirdnums'])){
								$zjl = false;
								$winprize = '谢谢参与';
							}else{  
								$zjl	= true;
								$prizetype = 3;
								M('Lottery')->where(array('id'=>$id))->setInc('thirdlucknums');
							}	 
							
						}
					break;
						
					default:
							$zjl = false;
							$winprize = '谢谢参与';
							break;
				}
			
			
		} //end if;
	} // end first if; 
		//{"ret":0,"data":{"data":{"type":0,"prize":"\u8c22\u8c22\u53c2\u4e0e","prize_type":0,"pic":"","tips":"\u518d\u63a5\u518d\u5389\u54df\uff01"}}}

		if($zjl){
			//先把中奖的信息添加到record表中
			$redata->where($where)->save(array("prize"=>$winprize,"prizetype"=>$prizetype,'islottery'=>1));
			$return = array(
			"ret"=>0,
			"data"=>array(
				"data"=>array(
				"gid"=>$id,
				"type"=>3,
				"prize"=>$winprize,
				"prize_type"=>$prizetype,
				"pic"=>"/assets/public/css/images/liwu.png",
				"tips"=>"恭喜您，中奖了！您的运气实在太好了！",
				"recordid"=>$record["id"],
				"mobile"=>$record["phone"],
				"address"=>$record["address"],
				"name"=>$record["name"],
				"inputtime"=>$record["createtime"]
			))
			);
		}else{
			$return = array(
			"ret"=>0,
			"data"=>array(
				"data"=>array(
				"type"=>0,
				"prize"=>$winprize,
				"prize_type"=>0,
				"pic"=>"",
				"tips"=>"再接再厉哟！",
			))
			);
		}
		die(json_encode($return));
	}
	public function giveUpPrize(){
		$id 				= $this->_post('id');
		$wecha_id 			= $this->_post('wxid');
		$redata = M('Lottery_record');
		$where = array('id'=> $id,'wecha_id'=>$wecha_id);
		$record = $redata->where($where)->find();
		if($record){
			$redata->where($where)->save(array("prize"=>"","prizetype"=>0,'islottery'=>0));
			die(json_encode(array("ret"=>0,"msg"=>"已经取消该次中奖")));
		}else{
			die(json_encode(array("ret"=>1,"msg"=>"记录不存在")));
		}
		
		
	}
	public function submit(){
		if(IS_POST){
			$lid 				= $this->_post('lid');
			$id 				= $this->_post('rid');
			$wecha_id 			= $this->_post('wxid');
			$data['phone'] 		= $this->_post('mobile');
			$data['name']		= $this->_post('username');
			$data['address']	= $this->_post('address');
			$data['time']		= time();
			$data['sn']			= uniqid();
			$rollback = M('Lottery_record')->where(array('id'=> $id,'wecha_id'=>$wecha_id))->save($data);
			die(json_encode(array("ret"=>0,"msg"=>"更新成功")));
		}

	}

	protected function get_rand($proArr,$total) { 
		    $result = 7; 
		    $randNum = mt_rand(1, $total); 
		    foreach ($proArr as $k => $v) {
		    	
		    	if ($v['v']>0){//奖项存在或者奖项之外
		    		if ($randNum>$v['start']&&$randNum<=$v['end']){
		    			$result=$k;
		    			break;
		    		}
		    	}
		    }
		    return $result; 
	}
		
}
?>