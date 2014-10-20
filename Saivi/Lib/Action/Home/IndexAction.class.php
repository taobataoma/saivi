<?php
class IndexAction extends BaseAction{
	public $includePath;
	protected function _initialize(){
		parent::_initialize();
		$this->home_theme=$this->home_theme?$this->home_theme:'default';
		$this->includePath='./tpl/Home/'.$this->home_theme.'/';
		
		$this->assign('includeHeaderPath',$this->includePath.'Public_header.html');
		$this->assign('includeFooterPath',$this->includePath.'Public_footer.html');
		
	}
	public function clogin()
	{
		$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
		$k = isset($_GET['k']) ? $_GET['k'] : '';
		$this->assign('cid', $cid);
		$this->assign('k', $k);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	//关注回复
	public function index(){
		$where['status']=1;
		if (C('agent_version')){
			$where['agentid']=$this->agentid;
		}
		$links=D('Links')->where($where)->select();
		$info=M('artical')->order('date DESC')->limit(10)->select();
		
		$this->assign('info',$info);
		
		$this->assign('links',$links);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function news(){
		
	$info=M('artical')->order('date DESC')->select();
			
			
		
		
		$this->assign('info',$info);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function news1(){
		
	$info=M('artical')->where(array('type'=>'公司新闻'))->order('date DESC')->select();
			
			
		
		
		$this->assign('info',$info);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function news2(){
		
$info=M('artical')->where(array('type'=>'行业新闻'))->order('date DESC')->select();
			
			
		
		
		$this->assign('info',$info);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function newss(){
		$info=M('artical')->where(array('id'=>$_GET['id']))->find();
		//dump($info);exit;
		
		$this->assign('info',$info);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
    // 用户登出
    public function logout() {
		session(null);
		session_destroy();
		unset($_SESSION);
        redirect(U('Home/Index/index'));
		
    }
	public function verify(){
		Image::buildImageVerify(4,1,'png',0,28,'verify');
	}
	public function verifyLogin(){
		Image::buildImageVerify(4,1,'png',0,28,'loginverify');
	}
	public function resetpwd(){
		$uid=$this->_get('uid','intval');
		$code=$this->_get('code','trim');
		$rtime=$this->_get('resettime','intval');
		$info=M('Users')->find($uid);
		if( (md5($info['uid'].$info['password'].$info['email'])!==$code) || ($rtime<time()) ){
			$this->error('非法操作',U('Index/index'));
		}
		$this->assign('uid',$uid);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function fc(){
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function about(){
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function price(){
		$groupWhere=array();
		$groupWhere['status']=1;
		if (C('agent_version')){
			$groupWhere['agentid']=$this->agentid;
		}
		$groups=M('User_group')->where($groupWhere)->order('id ASC')->select();
		$this->assign('groups',$groups);
		$count=count($groups);
		$this->assign('count',$count);
		//
		$prices=array();
		$isCopyright=array();
		$wechatNums=array();
		$diynums=array();
		$connectnums=array();
		$activitynums=array();
		$create_card_nums=array();
		if ($groups){
			foreach ($groups as $g){
				array_push($prices,$g['price']);
				array_push($isCopyright,$g['copyright']);
				array_push($wechatNums,$g['wechat_card_num']);
				array_push($diynums,$g['diynum']);
				array_push($connectnums,$g['connectnum']);
				array_push($activitynums,$g['activitynum']);
				array_push($create_card_nums,$g['create_card_num']);
			}
		}
		$this->assign('prices',$prices);
		$this->assign('copyrights',$isCopyright);
		$this->assign('wechatNums',$wechatNums);
		$this->assign('diynums',$diynums);
		$this->assign('connectnums',$connectnums);
		$this->assign('activitynums',$activitynums);
		$this->assign('create_card_nums',$create_card_nums);
		//
		if (C('agent_version')&&$this->agentid){
			$funs=M('Agent_function')->where(array('status'=>1,'agentid'=>$this->agentid))->order('gid DESC')->select();
		}else {
			$funs=M('Function')->where(array('status'=>1))->order('gid DESC')->select();
		}
		if ($funs){
			$i=0;
			foreach ($funs as $f){
				$funs[$i]['access']=array();
				if ($groups){
					foreach ($groups as $g){
						if ($f['gid']>$g['id']){
							$canUse=0;
						}else {
							$canUse=1;
						}
						array_push($funs[$i]['access'],$canUse);
					}
				}
				$i++;
			}
		}
		$this->assign('funs',$funs);
		//
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	public function help(){
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
	function think_encrypt($data, $key = '', $expire = 0) {
		$key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
		$data = base64_encode($data);
		$x    = 0;
		$len  = strlen($data);
		$l    = strlen($key);
		$char = '';

		for ($i = 0; $i < $len; $i++) {
			if ($x == $l) $x = 0;
			$char .= substr($key, $x, 1);
			$x++;
		}

		$str = sprintf('%010d', $expire ? $expire + time():0);

		for ($i = 0; $i < $len; $i++) {
			$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
		}
		return str_replace('=', '',base64_encode($str));
	}
	function text(){
		$domain=$_GET['domain'];
		$domains=explode('.',$domain);

		echo '<a href="http://'.$domain.'/index.php?g=Home&m=T&a=test&n='.$this->think_encrypt($domains[1].'.'.$domains[2]).'" target="_blank">http://'.$domain.'/index.php?g=Home&m=T&a=test&n='.$this->think_encrypt($domains[1].'.'.$domains[2]).'</a><br>';
		echo '<a href="http://'.$domain.'/index.php?g=User&m=Create&a=index" target="_blank">http://'.$domain.'/index.php?g=User&m=Create&a=index</a><br>';
	}
	function common(){
		$where['status']=1;
		if (C('agent_version')){
			$where['agentid']=$this->agentid;
		}
		$cases=M('Case')->where($where)->order('id DESC')->select();
		$this->assign('cases',$cases);
		$this->display($this->home_theme.':Index:'.ACTION_NAME);
	}
}