<?php
class Car_baoyangAction extends BaseAction
{
    public function index()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger")) {
            //  echo '此功能只能在微信浏览器中使用';exit;
        }
        $where['token'] = $this->_get('token');
        
        $set = M('Car_baoyang')->where($where)->select();
        $this->assign('set', $set);
        $this->display();
    }
    
    public function content()
    {
        $where['token'] = $this->_get('token');
        $where['id']    = $this->_get('id');
        $set            = M('Car_baoyang')->where($where)->find();
        $this->assign('set', $set);
        $this->display();
    }
    
    public function book()
    {
        if ($_POST['action'] == 'book') {
            $data['token']     = $this->_get('token');
            $data['wecha_id']  = $this->_get('wecha_id');
            $data['pname']     = $this->_post('pname');
            $data['name']      = $this->_post('name');
            $data['phone']     = $this->_post('phone');
            $data['carno']     = $this->_post('carno');
            $data['Kilometer'] = $this->_post('Kilometer');
            $data['starttime'] = $this->_post('starttime');
            $data['hid']       = $this->_get('hid');
            $data['status']    = "未处理";
            
            $count = M('Car_baoyang_input')->where(array(
                'token' => $data['token'],
                'wecha_id' => $data['wecha_id'],
                'status' => 0,
                'hid' => $data['hid']
            ))->count();
            
            if ($count < 1)
                $order = M('Car_baoyang_input')->data($data)->add();
            
            if ($order) {
                echo "下单成功";
            } else {
                echo "您已经下过此单";
            }
            
        }
        
        
    }
    
}

?>