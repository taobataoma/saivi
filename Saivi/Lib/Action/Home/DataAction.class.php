<?php
class DataAction extends Action{
	private $token;
	private $fun;
	private $data=array();
	private $my='晨优汇';
	public function index(){
		 die("获取各种js调用的数据");
		
	}
	public function info(){
		//获取公众号所在的公司的信息
		$return = array(
			"ret"=>0,
			"data"=>array(
			"company"=>		C("site_name"),
			"is_deleted"=>	"0",
			"is_active"=>	"1",
			"dt_expire"=>	"2029-1-1",
			"mp_username"=>	"",
			"logo"=>		"",
			"web_name"=>	"",
			"contact_mobile"=>	"",
			"country"=>		"",
			"status"=>		0,
			"url"=>			0,
		)
		);
		die(json_encode($return));
	}
	public function footer(){
		$return = array(
			"ret"=>0,
			"data"=>array(
			"slogan"=>array(
			array("title"=>"","href"=>"","ajax"=>0),
			array("title"=>"","href"=>"","ajax"=>0),
			array("title"=>"","href"=>"","ajax"=>0),
		),
			"support"=>array(
			"title"	=>C("site_name"),
			"href"	=>C("site_url"),
			"ajax"	=>0,
		),
		)
		);
		die(json_encode($return));
	}
	public function userinfo(){
		$wxid = $this->_post("wxid");
		$token = $this->_post("token");
		//查询整个平台中，当前用户的信息，然后返回，这个信息全局共享的
		$return = array(
			"ret"=>0,
			"data"=>array(
			"id"=>			"",
			"fake_id"=>		"",
			"wid"=>			"1",
			"name"=>		"1",
			"mobile"=>		"1",
			"password"=>	"1",
			"invite_account_id"=>	"1",
			"money"=>		"1",
			"gift_money"=>	"1",
			"credit"=>		"1",
			"level_credit"=>	"1",
			"last_ip"=>		"1",
			"dt_login"=>	"1",
			"dt_add"=>		"1",
			"dt_update"=>	"1",
		)
		);
		die(json_encode($return));
	}

	public function account(){
	$wxid = $this->_get("wxid");
	$token = $this->_get("token");
	$return = array(
			"ret"=>0,
			"data"=>array(
			"wxid"=>		$wxid,
			"id"=>			$wxid,
			"fake_id"=>		"",
			"wid"=>			"",
			"name"=>		"",
			"mobile"=>		"",
			"password"=>	"",
			"invite_account_id"=>	"1",
			"money"=>		"1",
			"gift_money"=>	"1",
			"credit"=>		"1",
			"level_credit"=>	"1",
			"last_ip"=>		"1",
			"dt_login"=>	"1",
			"dt_add"=>		"1",
			"dt_update"=>	"1",
		)
		);
		die(json_encode($return));
	}




}