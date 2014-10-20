<?php
class DiymenAction extends UserAction{
	public $thisWxUser;
	public function _initialize() {
		parent::_initialize();
		$where=array('token'=>$this->token);
		$this->thisWxUser=M('Wxuser')->where($where)->find();
		if (!$this->thisWxUser['appid']||!$this->thisWxUser['appsecret']){
			$diyApiConfig=M('Diymen_set')->where($where)->find();
			if (!$diyApiConfig['appid']||!$diyApiConfig['appsecret']){
				$this->error('请先设置AppID和AppSecret再使用本功能，谢谢','?g=User&m=Index&a=edit&id='.$this->thisWxUser['id']);
			}else {
				$this->thisWxUser['appid']=$diyApiConfig['appid'];
				$this->thisWxUser['appsecret']=$diyApiConfig['appsecret'];
			}
		}
	}
	//自定义菜单配置
	public function index(){
		$data=M('Diymen_set')->where(array('token'=>$_SESSION['token']))->find();
		$this->assign('diymen',$data);
		if(IS_POST){
			$_POST['token']=$_SESSION['token'];
			if($data==false){
				$this->all_insert('Diymen_set');
			}else{
				$_POST['id']=$data['id'];
				$this->all_save('Diymen_set');
			}
			M('Wxuser')->where(array('token'=>$this->token))->save(array('appid'=>trim($this->_post('appid')),'appsecret'=>trim($this->_post('appsecret'))));
		}else{
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();//dump($class);
			foreach($class as $key=>$vo){
				$c=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id']))->order('sort desc')->select();
				$class[$key]['class']=$c;
			}
			//dump($class);
			$this->assign('class',$class);
			$this->display();
		}
	}


	public function  class_add(){
		if(IS_POST){
			$this->all_insert('Diymen_class','/index');
		}else{
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();
			$this->assign('class',$class);
			$this->display();
		}
	}
	public function  class_del(){
		$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$this->_get('id')))->order('sort desc')->find();
		//echo M('Diymen_class')->getLastSql();exit;
		if($class==false){
			$back=M('Diymen_class')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($back==true){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('请删除该分类下的子分类');
		}


	}
	public function  class_edit(){
		if(IS_POST){
			$_POST['id']=$this->_get('id');
			$this->all_save('Diymen_class','/index?id='.$this->_get('id'));
		}else{
			$data=M('Diymen_class')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->find();
			if($data==false){
				$this->error('您所操作的数据对象不存在！');
			}else{
				$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();//dump($class);
				$this->assign('class',$class);
				$this->assign('show',$data);
			}
			$this->display();
		}
	}
	public function  class_send(){
		if(IS_GET){
			//dump($api);
			$url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->thisWxUser['appid'].'&secret='.$this->thisWxUser['appsecret'];
			$json=json_decode($this->curlGet($url_get));
			if (!$json->errmsg){
				//return array('rt'=>true,'errorno'=>0);
			}else {
				$this->error('获取access_token发生错误：错误代码'.$json->errcode.',微信返回错误信息：'.$json->errmsg);
			}


			$data = '{"button":[';

			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0,'is_show'=>1))->limit(3)->order('sort desc')->select();//dump($class);
			$kcount=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0,'is_show'=>1))->limit(3)->order('sort desc')->count();
			$k=1;
			foreach($class as $key=>$vo){
				//主菜单

				$data.='{"name":"'.$vo['title'].'",';
				$c=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort desc')->select();
				$count=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort desc')->count();
				//子菜单
				$vo['url']=str_replace(array('&amp;'),array('&'),$vo['url']);
				if($c!=false){
					$data.='"sub_button":[';
				}else{
					if(!$vo['url']){
						$data.='"type":"click","key":"'.$vo['keyword'].'"';
					}else {
						$data.='"type":"view","url":"'.$vo['url'].'"';
					}
				}
				$i=1;
				foreach($c as $voo){
					$voo['url']=str_replace(array('&amp;'),array('&'),$voo['url']);
					if($i==$count){
						if($voo['url']){
							$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"}';
						}else{
							$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"}';
						}
					}else{
						if($voo['url']){
							$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"},';
						}else{
							$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"},';
						}
					}
					$i++;
				}
				if($c!=false){
					$data.=']';
				}

				if($k==$kcount){
					$data.='}';
				}else{
					$data.='},';
				}
				$k++;
			}
			$data.=']}';

			file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$json->access_token);

			$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$json->access_token;
			$rt=$this->api_notice_increment($url,$data);
			if($rt['rt']==false){
				$this->error('操作失败,curl_error:'.$rt['errorno']);
			}else{
				$this->success('操作成功');
			}
			exit;
		}else{
			$this->error('非法操作');
		}
	}
	
	function api_notice_increment($url, $data){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {
			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if ($js['errcode']=='0'){
				return array('rt'=>true,'errorno'=>0);
			}else {
				$this->error('发生错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
			}
		}
	}
	function curlGet($url){
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}

}
	?>