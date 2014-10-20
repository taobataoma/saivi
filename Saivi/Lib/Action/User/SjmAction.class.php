<?php
class SjmAction extends UserAction{
		public $token;
	
	public function _initialize() {
		parent::_initialize();
		
		$this->token=session('token');
		$this->assign('token',$this->token);
	}
	public function index(){
		 $this->reply_info_model=M('Sjmreply_info');
		$thisInfo = $this->reply_info_model->where(array('token'=>$this->token))->find();
		
		if ($thisInfo&&$thisInfo['token']!=$this->token){
			exit();
		}

		if(IS_POST){
			$row['title']=$this->_post('title');
			$row['info']=$this->_post('info');
			$row['picurl']=$this->_post('picurl');
$row['sharepicurl']=$this->_post('sharepicurl');
			
			$row['token']=$this->_post('token');
			$row['url']=strip_tags(htmlspecialchars_decode($_POST['url']));
$row['shareurl']=strip_tags(htmlspecialchars_decode($_POST['shareurl']));
			
			$row['tip']=strip_tags(htmlspecialchars_decode($_POST['tip']));
			$row['wxname']=$this->_post('wxname');
			
			if ($thisInfo){
				$where=array('token'=>$this->token);
				$this->reply_info_model->where($where)->save($row);

				$keyword_model=M('Keyword');
				//$keyword_model->where(array('token'=>$this->token,'pid'=>$thisInfo['id'],'module'=>'Reply_info'))->save(array('keyword'=>$_POST['keyword']));
				$this->success('修改成功',U('Sjm/index',$where));
						
			}else {
				$where=array('token'=>$this->token);
				$this->reply_info_model->add($row);
				$this->success('设置成功',U('Sjm/index',$where));
			}
		}else{
			//
			
			
			//
			$this->assign('set',$thisInfo);
			
			$this->display();
		}
	}

}

	

?>