<?php
class BasicAction extends AgentAction{
	public function _initialize() {
		parent::_initialize();
	}
	public function index(){
		if (IS_POST){
			if($this->agent_db->create()){
				$this->agent_db->where(array('id'=>$this->thisAgent['id']))->save($_POST);
				$this->success('修改成功！',U('Basic/index'));
			}else{
				$this->error($this->agent_db->getError());
			}
		}else {
			$this->display();
		}
	}
	public function expenseRecords(){
		$agent_expenserecords_db=M('Agent_expenserecords');
		$count      = $agent_expenserecords_db->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$where=$this->agentWhere;
		$where['status']=1;
		$list=$agent_expenserecords_db->where($where)->order('id DESC')->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function recharge(){
		$this->display();
	}
	public function changePassword(){
		if (IS_POST){
			if (trim($_POST['password'])!=trim($_POST['repassword'])){
				$this->error('两次输入的密码不一致');
			}
			$password=md5(md5(trim($_POST['password'])).$this->thisAgent['salt']);
			$this->agent_db->where(array('id'=>$this->thisAgent['id']))->save(array('password'=>$password));
			$this->success('修改成功！',U('Basic/changePassword'));
		}else {
			$this->display();
		}
	}
}

?>