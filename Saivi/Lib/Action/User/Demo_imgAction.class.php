<?php
class Demo_imgAction extends UserAction
{
	public function index()
	{
		$act=$this->_get('act','string');
		
		switch ($act) 
		{
        	case 'home':
				$where['token']=session('token');
				$info=D('home')->field('id,title,picurl as pic,info as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=Index&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
                break;
			case 'Goldegg':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
				$info=D('Goldegg')->field('id,title,startpicurl as pic,txt as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=Goldegg&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;
			case 'Panorama':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
				$info=D('Panorama')->field('id,title,picurl as pic,intro as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=Panorama&a=item&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;
			case 'Lottery':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'),'type' => 1);
				$info=D('Lottery')->field('id,title,starpicurl as pic,info as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=Lottery&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;
			case 'Guajiang':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'),'type' => 2);
				$info=D('Lottery')->field('id,title,starpicurl as pic,info as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=Guajiang&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;
			case 'GoldenEgg':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'),'type' => 5);
				$info=D('Lottery')->field('id,title,starpicurl as pic,info as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=GoldenEgg&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;
			case 'LuckyFruit':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'),'type' => 4);
				$info=D('Lottery')->field('id,title,starpicurl as pic,info as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=LuckyFruit&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;
			case 'Greeting_card':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'));
				$info=D('greeting_card')->field('id,title,picurl as pic,info as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=Greeting_card&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;

			case 'Coupon':
				$where=array('id'=>$this->_get('id','intval'),'token'=>session('token'),'type' => 3);
				$info=D('Lottery')->field('id,title,starpicurl as pic,info as text')->where($where)->find();
				$info['url']='index.php?g=Wap&m=Coupon&a=index&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				break;
			default:
				$where['id']=$this->_get('id','intval');
				$where['uid']=session('uid');
				$info=D('Img')->field('id,title,pic,text,url as url1')->where($where)->find();
				if($info['url1'] == false)
				{
					$info['url']='index.php?g=Wap&m=Index&a=content&wecha_id='.session('uname').'&token='.session('token').'&id='.$info['id'];
				}
				else
				{
					$info['url'] = str_replace(array('{wechat_id}', '{siteUrl}', '&amp;'), array(session('uname'), C('site_url'), '&'), $info['url1']);
				}
				//print_r($info);die();
		}
		$this->assign('info',$info);
		$this->display();
		
	}
	

}
?>