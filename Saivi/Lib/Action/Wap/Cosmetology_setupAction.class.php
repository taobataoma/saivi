<?php
class Cosmetology_setupAction extends BaseAction{

	    public function content(){
       $agent = $_SERVER['HTTP_USER_AGENT']; 
        if(!strpos($agent,"MicroMessenger")&&!isset($_SESSION['token'])) {
            echo '此功能只能在微信浏览器中使用';exit;
        }
   $where['token']= $this->_get('token'); 
 //echo M()->getLastSql();
//exit;
        $set=M('Cosmetology_setup')->where($where)->find();
		$pd=M('Cosmetology_setup_control')->where($where)->find();
		//echo M()->getLastSql();
//exit;

		$pt=M('Cosmetology')->where(array('token'=>$this->_GET('token')))->find();
	  $ks=M('Cosmetology_departments')->where(array('token'=>$this->_GET('token')))->find();
		$id=M('Cosmetology_departments')->where(array('token'=>$this->_GET('token')))->order('id desc')->select(); 
		$this->assign('ks',$ks);
		$this->assign('id',$id);
		$this->assign('pd',$pd);
		$this->assign('pt',$pt);
        $this->assign('set',$set);
        $this->display();
	
    }
	
	
	
	    public function orders(){
       $agent = $_SERVER['HTTP_USER_AGENT']; 
        if(!strpos($agent,"MicroMessenger")&&!isset($_SESSION['token'])) {
            echo '此功能只能在微信浏览器中使用';exit;
        }
   $where['token']= $this->_get('token'); 
 //echo M()->getLastSql();
//exit;
        $set=M('Cosmetology_setup')->where($where)->find();
		$pd=M('Cosmetology_setup_control')->where($where)->find();
		//echo M()->getLastSql();
//exit;
     //查看订单
        $ckdd=M('Cosmetology_setup')->where(array('token'=>$this->_GET('token'),'wecha_id'=>$this->_GET('wecha_id')))->find();

		$pt=M('Cosmetology')->where(array('token'=>$this->_GET('token')))->find();
	    $ks=M('Cosmetology_departments')->where(array('token'=>$this->_GET('token')))->find();
		$id=M('Cosmetology_departments')->where(array('token'=>$this->_GET('token')))->order('id desc')->select(); 
		$this->assign('ckdd',$ckdd);
		$this->assign('ks',$ks);
		$this->assign('id',$id);
		$this->assign('pd',$pd);
		$this->assign('pt',$pt);
        $this->assign('set',$set);
        $this->display();
	
    }
	
    
   public function book(){ 
        
        if($_POST['action'] == 'book'){           
$data['token']  =  $this->_get('token');
$data['wecha_id']  =  $this->_get('wecha_id');
$data['name']  =  $this->_post('name');
$data['sex']  =  $this->_post('sex');
$data['age']  =  $this->_post('age');
$data['phone']  =  $this->_post('phone');
$data['scheduled_date']  =  $this->_post('scheduled_date');
$data['address']  =  $this->_post('address');
$data['departments']  =  $this->_post('departments');
$data['expert']  =  $this->_post('expert');
$data['disease']  =  $this->_post('disease');
$data['process']  =  "未处理";

 $count = M('Cosmetology_setup')->where(array('token'=>$data['token'],'wecha_id'=>$data['wecha_id'],'status'=>0))->count();

if ($count<1) $order = M('Cosmetology_setup')->data($data)->add();       
          
        if($order){
                echo "下单成功";
            }else{
                echo "您已经下过此单";
            }            
 
        }    
            
        
    } 

}
    
?>