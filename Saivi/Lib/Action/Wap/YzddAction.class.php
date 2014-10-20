<?php

class YzddAction extends BaseAction
{
    public $wecha_id;
    public $token;
    public function _initialize(){
    	parent::_initialize();
        defined('RES') 	or  define('RES', THEME_PATH . 'common');
        $this->wecha_id = $this->_get('wecha_id');
        $this->assign('wecha_id', $this->wecha_id);
        $this->token = $this->_get('token');
        
        if(empty($this->wecha_id)){
        	die('非法操作');
        }
    }
    
    /**
     * 一站到底进入页面逻辑
     *  根据活动id超找活动信息，存在活动信息
     *  判定活动开始时间和结束时间，  
     * 
     */
    public function index()
    {
    	$id = $this->_get('id');
    	
    	$where = array('id'=>$id,'token'=>$this->token);
        $info =  M('Yzdd')->where($where)->find();
        
        if(!$info){
        	die('非法参数');
        }
         
        if($info['kssj']>time()){

		 
             $this->display('activitynotscratch');
              exit;
	     }elseif($info['jssj']<time()){
	     	 $this->display('activityend');
             exit;
	     }
	    
	     $record_db =  M('yzdd_record');
	     $record_info = $record_db->where(array('wecha_id'=>$this->wecha_id,'yid'=>$id))->order('hdrq desc')->find();
	    
		 if($record_info && $record_info['hdrq']==date('Y-m-d')){
		 	
		 	
        
			  $rid = $record_info['id'];
			 //每天限定的题目数已经结束
			 if($record_info['tms']<=$record_info['jrtms']){
			 	//进入提示页面 当天的成绩页面
			 	
			 	  
			 	
			 	
			 	 $this->assign('jrjf', $info['limit']*$record_info['zqs']);
			 	 $this->display('jrjs');
			 	 exit;
			 }else{
			 	 
			 	//否则可以进入答题首页
			 	//判定答题数，当天题目数完成那么跳转到结束页面
		         $this->redirect(U('Yzdd/kz',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'id'=>$id,'rid'=>$record_info['id'])));
			 	 exit;
			 }
			 
           
		 }else{
				//初次进入	
		    //插入对应的一站到底记录表
		    $lasttmid = $record_info?$record_info['lasttmid']:0;
		    if(empty($lasttmid))$lasttmid = 0;
			$data = array(
			  'token'=>$this->token,
			  'wecha_id'=>$this->wecha_id,
			  'yid'=>$id,
			  'tms'=>$info['mrtm'],
			  'ctime'=>time(),
			  'hdrq'=>date('Y-m-d'),
			  'lasttmid'=>$lasttmid//最后一次答题的题目号 
			 );
			$rid = $record_db->add($data);
		 }
		 
        $this->assign('rid', $rid);
        $this->assign('info', $info);
        
        $this->display();
    }
    
   
   //答题控制逻辑
    public function kz(){
    	
    	$id = $this->_get('id');
    	$rid = $this->_get('rid');
    	if(empty($this->wecha_id) || empty($this->token) || empty($id) ||  empty($rid)){
    		die('非法操作');
    	}
    	
    	$where = array('id'=>$id,'token'=>$this->token);
        $info =  M('Yzdd')->where($where)->find();
        
        if(!$info){
        	die('非法参数');
        }
        
    	//需要简单判定一些情况
         $record_db = M('yzdd_record');
    	 $record_info = $record_db->where(array('wecha_id'=>$this->wecha_id,'id'=>$rid,'hdrq'=>date('Y-m-d')))->find();
    	 
    	 //没有当天的记录，自动跳转到首页进行一个 规则的查看
    	if(!$record_info){
			 $this->redirect(U('Yzdd/index',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'id'=>$id)));
			 exit;
    	}
    	
    	//数据非法的情况
    	if($record_info['yid'] != $id){
    		 $this->redirect(U('Yzdd/index',array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'id'=>$id)));
			 exit;
    	}
    	 $signle_score = $info['limit'];
    	if($record_info['tms']<=$record_info['jrtms']){
			 	//进入提示页面 当天的成绩页面
			  /**
			   * 判定是否已经给会员卡增加积分，如果增加那么就不再增加
			   */
			 
			   $cardsign   = M('Member_card_sign'); 
				   
			   $cardsign_record = $cardsign ->where(array('token'=>$this->token,'wecha_id'=>$this->wecha_id,'eventid'=>444444))->order('create_time desc')->find();
				   
		       if($cardsign_record){
			    	 $today = date('Y-m-d',time());
                     $itoday = date('Y-m-d',$cardsign_record['create_time']); 
                     if($today == $itoday){
                     	
                     }else{
                     	$cardsign->data(array(
		        		   'token' => $this->token,
		                   'wecha_id' =>$this->wecha_id,
		                   'eventid' =>444444,
		                   'eventname' =>'一站到底',
		                   'create_time' =>time(),
		                   'score' =>$signle_score*$record_info['zqs']
        		            ))->add();
                     }
			    }else{
			    	$cardsign->data(array(
		        		   'token' => $this->token,
		                   'wecha_id' => $this->wecha_id,
		                   'eventid' =>444444,
		                   'eventname' =>'一站到底',
		                   'create_time' =>time(),
		                   'score' =>$signle_score*$record_info['zqs']
        		            ))->add();
			    }
				
			 	 
			 
			  $this->assign('jrjf', $signle_score*$record_info['zqs']);
			  $this->assign('hdxx', $info);
			  $this->display('jrjs');
			  exit;
	     }
    	
    	
    	//查找对应的题目
    	 $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
         $tkinfo = $Model->query("SELECT * FROM `tp_yzddtk` AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `tp_yzddtk`)-(SELECT MIN(id) FROM `tp_yzddtk`))+(SELECT MIN(id) FROM `tp_yzddtk`)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT 1");
    	
    	/*`tp_yzddtk`
    	$where['token']=$this->token;
    	$where['id']=array('gt',$record_info['lasttmid']);
    	
    	$tkinfo = M('yzddtk')->where($where)->order('id asc')->find();
    	
    	if(!$tkinfo){
    		$where['id']=array('gt',0);
    		$tkinfo = M('yzddtk')->where($where)->order('id asc')->find();
    	}
    	
    	if(!$tkinfo){
    		die('没有题目信息');
    	}
    	*/
    	$this->assign('recordinfo', $record_info);
    	$this->assign('score', $signle_score);
    	//$this->assign('nextnum',$record_info['jrtms']+1);
    	$this->assign('info',$tkinfo[0]);
    	//生成每个题目的唯一值，控制重复提交相关判定
    	$uuid = uniqid(mt_rand(),1);
    	
    	/**
    	 * 生成答题数据 放到对应的数据表中
    	 */
    	$user_tm_info = array(
    	  'token'=>$this->token,
    	  'wecha_id'=>$this->wecha_id,
    	  'yid'=>$id,
    	  'rid'=>$rid,
    	  'tmid'=>$tkinfo[0]['id'],
    	  'uuid'=>$uuid,
    	  'zd'=>$tkinfo[0]['zd'],
    	  'ctime'=>time(),
    	  'htime'=>0,
    	);
    	M('yzdd_record_data')->add($user_tm_info);
    	
    	//今日题目数  加1
    	$record_db->where("id=".$rid)->setInc('jrtms');
    	$this->assign('uuid',$uuid);
    	//var_dump($tkinfo);
    	$this->display();
		
	}
    
    /**
     * 检查题目答案
     *   如果回答正确 题目数+1，积分加上去
     * 防止重复提交的问题
     */
    public function check(){
    	/**
    	 * 防止重复提交的问题
    	 */
    	$uuid = $this->_get('uuid');
    	$tmid = $this->_get('tmid');
    	$zqda = $this->_get('zqda');
    	$rid  =  $this->_get('rid');
    	
    	$curtminfo_db = M('yzdd_record_data');
    	$curtminfo = $curtminfo_db->where(array('uuid'=>$uuid))->find();
    	
    	//$tminfo = M('yzddtk')->where(array('id'=>$tmid))->find();
    	//$record_db = M('yzdd_record');
    	
    	//$recordinfo = $record_db->where(array('id'=>$rid))->find();
    	
    	$res = 'invalid';
    	if($curtminfo && $curtminfo['htime']==0){
    		
    		
    		
    		if($zqda == '0'){
    			$res = 'out';
    		}elseif($zqda == $curtminfo['zd']){
    			$res = 'ok';
    			//没做一次回答，今日题目数增加1
    		   //$zqs = ' ,zqs= zqs+1 ';
    		   M('yzdd_record')->where(array('id'=>$curtminfo['rid']))->setInc('zqs');
    		}else{
    			$res = 'no';
    		}
           
           
    		$curtminfo_db->where("id=".$curtminfo[id])->save(array('htime'=>time()));
    		
    	}
    	
    	
    	die($res);
    	
    }
   
}
?>