<?php
class Token_openAction extends UserAction{

	public function add(){
		if ($this->isAgent){
			$fun=M('Agent_function')->where(array('id'=>$this->_get('id')))->find();
		}else {
			$fun=M('Function')->where(array('id'=>$this->_get('id')))->find();
		}
		
		$openwhere=array('uid'=>session('uid'),'token'=>session('token'));
		//删除掉重复的token
		$deleteWhere=array();
		$deleteWhere['uid']=array('neq',session('uid'));
		$deleteWhere['token']=session('token');
		M('Token_open')->where($deleteWhere)->delete();
		//
		$open=M('Token_open')->where($openwhere)->find();		
		$str['queryname']=str_replace(',,',',',$open['queryname'].','.$fun['funname']);	
		//
		if (!$open){
			M('Token_open')->add(array('uid'=>session('uid'),'token'=>session('token')));
		}
		//
		$back=M('Token_open')->where($openwhere)->save($str);
		if($back){
			echo 1;
		}else{
			echo 2;
		}
	
	}
	public function del(){
		if ($this->isAgent){
			$fun=M('Agent_function')->where(array('id'=>$this->_get('id')))->find();
		}else {
			$fun=M('Function')->where(array('id'=>$this->_get('id')))->find();
		}
		$openwhere=array('uid'=>session('uid'),'token'=>session('token'));
		$open=M('Token_open')->where($openwhere)->find();		
		//删除掉重复的token
		$deleteWhere=array();
		$deleteWhere['uid']=array('neq',session('uid'));
		$deleteWhere['token']=session('token');
		M('Token_open')->where($deleteWhere)->delete();
		//
		$str['queryname']=ltrim(str_replace(',,',',',str_replace($fun['funname'],'',$open['queryname'])),',');	
		$back=M('Token_open')->where($openwhere)->save($str);
		if($back){
			echo 1;
		}else{
			echo 2;
		}
	}




}



?>