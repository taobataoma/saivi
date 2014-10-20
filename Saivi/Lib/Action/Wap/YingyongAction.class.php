<?php

//wap

class YingyongAction extends WapAction{

	public $token;

	public $wecha_id;

	public $Yingyong_model;

	

	public function __construct(){

		

		parent::__construct();

		$this->token=session('token');

		// $this->token = $this->_get('token');

		$this->assign('token',$this->token);

		$this->wecha_id	= $this->_get('wecha_id');

		if (!$this->wecha_id){

			$this->wecha_id='null';

		}
			$where['token']=$this->token;
		
		$this->assign('wecha_id',$this->wecha_id);

		$this->Yingyong_model=M('Yingyong');

	
		

		



	}



	


	public function index(){
 $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            echo '此功能只能在微信浏览器中使用';exit;
        }

		
		$where = array('token'=> $this->_get('token'));
		
		
		
		

		$info = M('Yingyong')->where($where)->find();
		$date = M('Yingyong_reply')->where($where)->find();
                $info[tipurl]=str_replace('{siteUrl}','',$info[tipurl]);
$info[tip1url]=str_replace('{siteUrl}','',$info[tip1url]);
		
		
		$this->assign('info', $info);
		$this->assign('date', $date);
		if($info[type]==1){
			$this->display('index_sdasd2');
			
			}elseif($info[type]==2){$this->display('index_dsadd89');}else{$this->display('index_cjk');}
		
	}
	

	




	

}





?>