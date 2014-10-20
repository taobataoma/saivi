<?php
class WallAction extends UserAction{
	public $wall_model;
	public $token_where;
	public $keyword_model;
	public $stringFields;
	public $intFields;
	public function _initialize() {
		parent::_initialize();
		$this->canUseFunction('wall');
		$this->wall_model=M('Wall');
		$this->token_where['token']=$this->token;
		$this->keyword_model=M('Keyword');
		$this->stringFields=array('title','keyword','background','startbackground','logo','qrcode','endbackground','firstprizename','secondprizename','thirdprizename','fourthprizename','fifthprizename','sixthprizename','firstprizepic','secondprizepic','thirdprizepic','fourthprizepic','fifthprizepic','sixthprizepic');
		$this->intFields=array('isopen','firstprizecount','secondprizecount','thirdprizecount','fourthprizecount','fifthprizecount','sixthprizecount');
	}
	public function index(){
		$count=$this->wall_model->where($this->token_where)->count();
		$page=new Page($count,20);
		$info=$this->wall_model->where($this->token_where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function add(){
		if (IS_POST){
			if (!trim($_POST['title'])){
				$this->error('请填写标题');
			}
			$fileds=$this->stringFields;
			$row=array();
			foreach ($fileds as $f){
				$row[$f]=$this->_post($f);
			}
			$intFields=$this->intFields;
			foreach ($intFields as $f){
				$row[$f]=intval($this->_post($f));
			}
			$row['token']=$this->token;
			$row['time']=time();
			$id=$this->wall_model->add($row);
			if ($id){
				$this->keyword_model->add(array('module'=>'Wall','pid'=>$id,'token'=>$this->token,'keyword'=>$row['keyword']));
				if ($row['isopen']){
					$this->setOtherClose($id);
				}
			}
			$this->success('添加成功',U('Wall/index',array('token'=>session('token'))));
		}else {
			$info=array();
			$info['isopen']=1;
			$this->assign('info',$info);
			$this->display('set');
		}
	}
	public function edit(){
		if (IS_POST){
			if (!trim($_POST['title'])){
				$this->error('请填写标题');
			}
			$fileds=$this->stringFields;
			$row=array();
			foreach ($fileds as $f){
				$row[$f]=$this->_post($f);
			}
			$intFields=$this->intFields;
			foreach ($intFields as $f){
				$row[$f]=intval($this->_post($f));
			}
	
			$updateWhere=array();
			$updateWhere['token']=$this->token;
			$updateWhere['id']=intval($_POST['id']);
			$rt=$this->wall_model->where($updateWhere)->save($row);
			if ($rt){
				$this->keyword_model->where(array('module'=>'Wall','pid'=>$updateWhere['id']))->save(array('keyword'=>$row['keyword']));
				if ($row['isopen']){
					$this->setOtherClose($id);
				}
			}
			$this->success('修改成功',U('Wall/index',array('token'=>session('token'))));
		}else {
			$where['token']=$this->token;
			$where['id']=$this->_get('id','intval');
			$info=$this->wall_model->where($where)->find();
			$this->assign('info',$info);
			$this->display('set');
		}
	}
	public function prizeRecords(){
		$where['token']=$this->token;
		$where['id']=$this->_get('id','intval');
		$info=$this->wall_model->where($where)->find();
		$this->assign('info',$info);
		//
		$db=M('Wall_prize_record');
		$records=$db->where(array('wallid'=>$info['id']))->order('prize ASC')->select();
		$uids=array();
		$recordsArr=array();
		if ($records){
			foreach ($records as $m){
				if (!in_array($m['uid'],$uids)){
					array_push($uids,$m['uid']);
				}
			}
			$membersArr=array();
			if ($uids){
				$memberWhere=array();
				$memberWhere['id']=array('in',$uids);
				$members=M('Wall_member')->where($memberWhere)->select();
				if ($members){
					foreach ($members as $me){
						$membersArr[$me['id']]=$me;
					}
				}
			}
			
			foreach ($records as $m){
				$m['nickname']=$membersArr[$m['uid']]['nickname'];
				$m['mp']=$membersArr[$m['uid']]['mp'];
				array_push($recordsArr,$m);
			}
		}
		$this->assign('records',$recordsArr);
		//
		$this->display();
	}
	public function del(){
		$this->token_where['id']=intval($_GET['id']);
		$rt=$this->wall_model->where($this->token_where)->delete();
		if ($rt){
			$this->keyword_model->where(array('module'=>'Wall','pid'=>$this->token_where['id']))->delete();
			M('Wall_member')->where(array('wallid'=>$this->token_where['id']))->delete();
			M('Wall_message')->where(array('wallid'=>$this->token_where['id']))->delete();
			M('Wall_prize_record')->where(array('wallid'=>$this->token_where['id']))->delete();
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}
	}
	public function setOtherClose($id){
		$where=array();
		$where['token']=$this->token;
		$where['id']=array('neq',$id);
		$this->wall_model->where($where)->save(array('isopen'=>0));
	}
	public function screen(){
		$this->token_where['isopen']=1;
		$info=$this->wall_model->where($this->token_where)->find();
		$this->assign('info',$info);
		//
		$members=M('Wall_member')->where(array('wallid'=>$info['id']))->select();
		$this->assign('members',$members);
		$this->display();
	}
	public function pullScreen(){
		$where=array();
		$where['token']=$this->token;
		$where['wallid']=intval($_GET['id']);
		$where['time']=array('gt',intval($_GET['dapingmu']));
		$choujiangTime=intval($_GET['choujiang']);
		$shujubaobiaoTime=intval($_GET['shujubaobiao']);
		$messages=M('Wall_message')->where($where)->order('id ASC')->select();
		$messageArr=array();
		$uids=array();
		if ($messages){
			foreach ($messages as $m){
				if (!in_array($m['uid'],$uids)){
					array_push($uids,$m['uid']);
				}
			}
			$membersArr=array();
			if ($uids){
				$memberWhere=array();
				$memberWhere['id']=array('in',$uids);
				$members=M('Wall_member')->where($memberWhere)->select();
				if ($members){
					foreach ($members as $me){
						$membersArr[$me['id']]=$me;
					}
				}
			}
			$maxTime=0;
			foreach ($messages as $m){
				$m['caudit']=$m['time'];
				$m['cmedia']=0;
				$m['cid']=$m['id'];
				if ($membersArr[$m['uid']]){
					$m['avatar']=$membersArr[$m['uid']]['portrait'];
					$m['nickname']=$membersArr[$m['uid']]['nickname'];
				}else {
					$m['avatar']='';
					$m['nickname']='';
				}
				$m['from_mark']='<i></i>';
				$m['remove']=0;
				if ($maxTime<$m['time']){
					$maxTime=$m['time'];
				}
				array_push($messageArr,$m);
			}
		}
		//
		$infoCount=M('Wall_message')->where(array('wallid'=>$where['wallid']))->count();
		$userCount=M('Wall_member')->where(array('wallid'=>$where['wallid']))->count();
		//
		$memberWhere=array();
		$memberWhere['wallid']=$where['wallid'];
		$memberWhere['time']=array('gt',time());
		$members=M('Wall_member')->where($memberWhere)->select();
		$membersArr=array();
		$maxMemberTime=$choujiangTime;
		if ($members){
			foreach ($members as $m){
				$m['avatar']=$m['portrait'];
				$m['awards']=0;
				$m['uid']=$m['id'];
				$m['from_mark']='<i></i>';
				if ($maxMemberTime<$m['time']){
					$maxMemberTime=$m['time'];
				}
				array_push($membersArr,$m);
			}
		}else {
			$membersArr='';
		}
		//
		$arr=array(
		'dapingmu'=>array('type'=>'dapingmu','update'=>$messageArr,'remove'=>0,'time'=>$maxTime?$maxTime:intval($_GET['dapingmu'])),
		'shujubaobiao'=>array('type'=>'shujubaobiao','update'=>array('info_all'=>$infoCount,'user_all'=>$userCount),'remove'=>0,'time'=>$maxMemberTime>$maxTime?$maxMemberTime:$maxTime),
		'choujiang'=>array('type'=>'choujiang','update'=>$membersArr,'remove'=>'','time'=>$maxMemberTime),
		);
		echo json_encode($arr);
	}
	public function test(){

	}
	public function insertPrizeRecord(){
		$db=M('Wall_prize_record');
		$wallid=intval($_GET['id']);
		$uids=explode(',',$_GET['uids']);
		if ($uids){
			foreach ($uids as $uid){
				if (intval($uid)){
					$uid=intval($uid);
					$check=$db->where(array('wallid'=>$wallid,'uid'=>$uid))->find();
					if (!$check){
						$db->add(array('wallid'=>$wallid,'uid'=>$uid,'time'=>time(),'prize'=>intval($_GET['prize'])));
					}
				}
			}
		}
	}
}
?>