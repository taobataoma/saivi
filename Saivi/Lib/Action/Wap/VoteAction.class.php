<?php
class VoteAction extends BaseAction{


	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"icroMessenger")) {
			// echo '此功能只能在微信浏览器中使用';exit;
		}

        if($this->_get('wecha_id')){
            $cover = 0;
        }else{
            $cover = 1;
        }
        $this->assign('cover',$cover);

        if($this->_get('token') && $this->_get('id')){
            $token      = $this->_get('token');
            $wecha_id   = $this->_get('wecha_id');
            $id         = $this->_get('id');
            session('token',$token);
            session('wecha_id',$wecha_id);
            session('id',$id);           
        }else{
            $token = session('token');
            $wecha_id = session('wecha_id');
            $id = session('id');
        }


        $this->assign('token',$token);
        $this->assign('wecha_id',$wecha_id);
        $this->assign('id',$id);

		$t_vote		= M('Vote');
        $t_record  = M('Vote_record');
		$where 		= array('token'=>$token,'id'=>$id);
		$vote 	= $t_vote->where($where)->find();
        if(empty($vote)){
            exit('非法操作');
        }
        $condition['vid'] = $vote['id'];
        if($_POST['search']!=null && is_numeric($_POST['search'])){
            $condition['id'] = intval(htmlspecialchars($_POST['search']));
        }

        if($_POST['search'] != null){
            if(is_numeric($_POST['search'])){
               $condition['id'] = intval(htmlspecialchars($_POST['search'])); 
           }else{
                $condition['item'] = htmlspecialchars($_POST['search']);
           }
        }
       
        import('./Saivi/Lib/ORG/Page.class.php');
        $count = M('Vote_item')->where($condition)->count();
        $page = new Page($count,20);
        $page->setConfig('theme', '<li><a>%totalRow% %header%</a></li> %upPage%  %linkPage% %downPage% ');
        $show = $page->show();
        
        $vote_item = M('Vote_item')->where($condition)->order('vcount DESC')->limit($page->firstRow.','.$page->listRows)->select();
        // dump(M('Vote_item')->getLastSql()); 
        $vcount =  M('Vote_item')->where(array('vid'=>$vote['id']))->sum("vcount");
        $this->assign('count',$vcount);
        //检查是否投票过
        $t_item = M('Vote_item');
        $where = array('wecha_id'=>$wecha_id,'vid'=>$id);
        $vote_record  = $t_record->where($where)->find();
        if($vote_record && $vote_record != NULL){
            $arritem = trim($vote_record['item_id'],',');
            $map['id'] = array('in',$arritem);
            $hasitems = $t_item->where($map)->field('item')->select();
            $this->assign('hasitems',$hasitems);
            $this->assign('vote_record',1);
        }else{
            $this->assign('vote_record',0);
        }

        $item_count = M('Vote_item')->where($condition)->order('vcount DESC')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($item_count as $k=>$value) {
           $vote_item[$k]['per']=(number_format(($value['vcount'] / $vcount),2))*100;
           $vote_item[$k]['pro']=$value['vcount'];
        } 
        // dump($vote_item);
        $this->assign('page',$show);
        $this->assign('total',$total);
        $this->assign('vote_item', $vote_item);
        $this->assign('vote',$vote);
		$this->display();
	}

	public function add_vote(){	
	

		$token 		=	$this->_post('token');
		$wecha_id	=	$this->_post('wecha_id');
		$tid 		=	$this->_post('tid');
		$chid 		= 	rtrim($this->_post('chid'),',');	
		$recdata 	=	M('Vote_record');
        $where   = array('vid'=>$tid,'wecha_id'=>$wecha_id,'token'=>$token);  
        $recode =  $recdata->where($where)->find();
        if($recode != '' || $wecha_id ==''){
            $arr=array('success'=>0);
		
            echo json_encode($arr);
			
            exit;
        }else{
		
        $data = array('item_id'=>$chid,'token'=>$token,'vid'=>$tid,'wecha_id'=>$wecha_id,'touch_time'=>time(),'touched'=>1);     
		$ok = $recdata->add($data);

		
        $map['id'] = array('in',$chid);
        $t_item = M('Vote_item');
        $t_item->where($map)->setInc('vcount');       
        $arr=array('success'=>1,'token'=>$token,'wecha_id'=>$wecha_id,'tid'=>$tid,'chid'=>$chid,'arrpre'=>$per);
        echo json_encode($arr); 
		       
       exit;}
	}


    public function show(){
        $agent = $_SERVER['HTTP_USER_AGENT']; 
        if(!strpos($agent,"icroMessenger")) {
            // echo '此功能只能在微信浏览器中使用';exit;
        }
        $vote_item = M('Vote_item');
        $condition['id'] = htmlspecialchars($this->_get('id'));
        $data = $vote_item->where($condition)->find();
        // dump($data);
        if($_GET['wecha_id']){
            $cover = 0;
        }else{
            $cover = 1;
        }
        $this->assign('cover',$cover);
        $this->assign('data',$data);
        $this->display();
    }

    public function vote(){
        $data['item_id'] = htmlspecialchars($this->_post('id'));
        $data['vid'] = htmlspecialchars($this->_post('vid'));
        $data['token'] = htmlspecialchars($this->_post('token'));
        $data['wecha_id'] = htmlspecialchars($this->_post('wecha_id'));
        $data['touch_time'] = time();
        $data['touched'] = 1;
        $condition['vid'] = $data['vid'];
        $condition['wecha_id'] = $data['wecha_id'];

        $vote_record = M('Vote_record');
        if($vote_record->where($condition)->find()){
            //已经投过票了
            $this->ajaxReturn('','',1,'json');
        }else{
            //没有投过票
            $vote_record->add($data);
            $map['id'] = array('in',$data['item_id']);
            $t_item = M('Vote_item');
            $t_item->where($map)->setInc('vcount'); 
            $this->ajaxReturn('','',2,'json');            
        }
    }
	
}?>