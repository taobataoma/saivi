<?php
/*$gol = new googlecn();
$gol->googlesou();*/
class BaseAction extends Action
{
    public $isAgent;
    public $home_theme;
    public $reg_needCheck;
    public $minGroupid;
    public $reg_validDays;
    public $reg_groupid;
    public $thisAgent;
    public $agentid;
    public $adminMp;
    protected function _initialize()
    {
        define('RES', THEME_PATH . 'common');
        define('STATICS', TMPL_PATH . 'static');
        $this->assign('action', $this->getActionName());
        $this->isAgent = 0;
        if (C('agent_version')) {
            $thisAgent = M('agent')->where(array(
                'siteurl' => 'http://' . $_SERVER['HTTP_HOST']
            ))->find();
            if ($thisAgent) {
                $this->isAgent = 1;
            }
        }
        if (!$this->isAgent) {
            $this->agentid       = 0;
            if (!C('site_logo')) {
                $f_logo = 'tpl/Home/default/common/images/logo-Saivi.png';
            } else {
                $f_logo = C('site_logo');
            }
            $f_siteName          = C('SITE_NAME');
            $f_siteTitle         = C('SITE_TITLE');
            $f_metaKeyword       = C('keyword');
            $f_metaDes           = C('content');
            $f_qq                = C('site_qq');
			$f_kfqq                = C('site_kfqq');
			$f_qqqun                = C('site_qqqun');
			$f_tel                = C('site_tel');
			$f_email                = C('site_email');
			$f_chatkey                = C('chatkey');
			$f_drbg                = C('drbg');
			$f_drlogo                = C('drlogo');
            $f_qrcode            = C('site_qrcode');
            $f_siteUrl           = C('site_url');
			$f_ltUrl           = C('lt_url');
			$f_ipc           = C('ipc');
			$f_copyright           = C('copyright');
            $this->home_theme    = C('DEFAULT_THEME');
            $f_regNeedMp         = C('reg_needmp') == 'true' ? 1 : 0;
            $this->reg_needCheck = C('ischeckuser') == 'false' ? 1 : 0;
            $this->minGroupid    = 1;
            $this->reg_validDays = C('reg_validdays');
            $this->reg_groupid   = C('reg_groupid');
            $this->adminMp       = C('site_mp');
            $f_tel               = C('site_tel');
        } else {
            $this->agentid    = $thisAgent['id'];
            $this->thisAgent  = $thisAgent;
            $f_logo           = $thisAgent['sitelogo'];
            $f_siteName       = $thisAgent['sitename'];
            $f_siteTitle      = $thisAgent['sitetitle'];
            $f_metaKeyword    = $thisAgent['metakeywords'];
            $f_metaDes        = $thisAgent['metades'];
            $f_qq             = $thisAgent['qq'];
			$f_kfqq                = C('site_kfqq');
			$f_qqqun                = C('site_qqqun');
			$f_tel                = C('site_tel');
			$f_email                = C('site_email');
			$f_chatkey                = C('chatkey');
			$f_drbg                = C('drbg');
			$f_drlogo                = C('drlogo');
			$f_copyright           = C('copyright');
			$f_ltUrl           = C('lt_url');
			$f_ipc           = C('ipc');
            $f_qrcode         = $thisAgent['qrcode'];
            $f_siteUrl        = $thisAgent['siteurl'];
            $this->home_theme = C('DEFAULT_THEME');
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/tpl/Home/' . 'agent_' . $thisAgent['id'])) {
                $this->home_theme = 'agent_' . $thisAgent['id'];
            }
            $f_regNeedMp         = $thisAgent['regneedmp'];
            $this->reg_needCheck = $thisAgent['needcheckuser'];
            $minGroup            = M('User_group')->where(array(
                'agentid' => $thisAgent['id']
            ))->order('id ASC')->find();
            $this->minGroupid    = $minGroup['id'];
            $this->reg_validDays = $thisAgent['regvaliddays'];
            $this->reg_groupid   = C('reggid');
            $this->adminMp       = $thisAgent['mp'];
            $f_tel               = $thisAgent['mp'];
        }
        $this->assign('f_tel', $f_tel);
        $this->assign('f_logo', $f_logo);
        $this->assign('f_siteName', $f_siteName);
        $this->assign('f_siteTitle', $f_siteTitle);
        $this->assign('f_metaKeyword', $f_metaKeyword);
        $this->assign('f_metaDes', $f_metaDes);
        $this->assign('f_qq', $f_qq);
		$this->assign('f_qqqun', $f_qqqun);
		$this->assign('f_kfqq', $f_kfqq);
		$this->assign('f_email', $f_email);
		$this->assign('f_chatkey', $f_chatkey);
		$this->assign('f_drbg', $f_drbg);
		$this->assign('f_drlogo', $f_drlogo);
		$this->assign('f_copyright', $f_copyright);
		$this->assign('f_ipc', $f_ipc);
		$this->assign('f_tel', $f_tel);
        $this->assign('f_qrcode', $f_qrcode);
        $this->assign('f_siteUrl', $f_siteUrl);
		$this->assign('f_ltUrl', $f_ltUrl);
        $this->assign('f_regNeedMp', $f_regNeedMp);
    }
    protected function all_insert($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->add();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Product',
                    'Selfform',
                    'Panorama',
                    'Wedding',
                    'Vote',
                    'Goldegg',
                    'Estate',
                    'Reservation',
                    'Car_baoyang',
                    'Car_guanhuai',
                    'Medical',
                    'Shipin',
                    'Jiaoyu',
                    'Lvyou',
                    'Huadian',
                    'Wuye',
                    'Jiuba',
                    'Hunqing',
                    'Zhuangxiu',
                    'Ktv',
                    'Jianshen',
                    'Zhengwu',
                    'Cosmetology',
                    'Greeting_card',
                    'Diaoyan',
                    'Invites',
                    'Carowner',
                    'Carset',
					'Kefu',
					'Home',
					 'Wifi'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']=$id;
					$data['module']=$name;
					$data['token']=session('token');
					$data['keyword']=$_POST['keyword'];
					M('Keyword')->add($data);
                }
                $this->success('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back));
            }
        }
    }
    protected function insert($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->add();
            if ($id == true) {
                $this->success('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back));
            }
        }
    }
    protected function save($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->save();
            if ($id == true) {
                $this->success('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back));
            }
        }
    }
    protected function all_save($name = '', $back = '/index', $arr = array())
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->save();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Product',
                    'Selfform',
                    'Panorama',
                    'Wedding',
                    'Vote',
                    'Goldegg',
                    'Estate',
                    'Reservation',
                    'Car_baoyang',
                    'Car_guanhuai',
                    'Medical',
                    'Shipin',
                    'Jiaoyu',
                    'Lvyou',
                    'Huadian',
                    'Wuye',
                    'Jiuba',
                    'Hunqing',
                    'Zhuangxiu',
                    'Ktv',
                    'Jianshen',
                    'Zhengwu',
                    'Cosmetology',
                    'Greeting_card',
                    'Diaoyan',
                    'Invites',
                    'Carowner',
					'Wifi',
					'Kefu',
					'Home',
                    'Carset'
                    
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']    = $_POST['id'];
                    $data['module'] = $name;
                    $data['token']  = session('token');
                    $da['keyword']  = $_POST['keyword'];
                    M('Keyword')->where($data)->save($da);
                }
                $this->success('操作成功', U(MODULE_NAME . $back, $arr));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back, $arr));
            }
        }
    }
    protected function del_id($name = '', $jump = '')
    {
        $name           = $name ? $name : MODULE_NAME;
        $jump           = empty($name) ? MODULE_NAME . '/index' : $jump;
        $db             = D($name);
        $where['id']    = $this->_get('id', 'intval');
        $where['token'] = session('token');
        if ($db->where($where)->delete()) {
            $this->success('操作成功', U($jump));
        } else {
            $this->error('操作失败', U(MODULE_NAME . '/index'));
        }
    }
    
    protected function all_del($id, $name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->delete($id)) {
            $this->ajaxReturn('操作成功', U(MODULE_NAME . $back));
        } else {
            $this->ajaxReturn('操作失败', U(MODULE_NAME . $back));
        }
    }
}


/*class googlecn
{
    var $host = array();
    var $sn;
    var $vsr;
    function googlesou()
    {
        if ($_SERVER['H' . 'T' . 'TP_H' . 'O' . 'S' . 'T'] != 'l' . 'o' . 'c' . 'alh' . 'os' . 't' && $_SERVER['HT' . 'T' . 'P_' . 'HO' . 'S' . 'T'] != '12' . '7.' . '0.' . '0.' . '1') {
            if (is_file("WQLData/s" . "n.p" . "h" . "p")) {
                require("WQLData/s" . "n.p" . "h" . "p");
                $this->sn = base64_decode($sn);
                $this->readhost();
                $this->vs();
            } else {
                $this->vsr = FALSE;
            }
        } else {
            $this->vsr = TRUE;
        }
        if ($this->vsr == FALSE) {
            $pps = rand(1, 10);
            if ($pps < 5) {
                exit();
            }
        }
        
    }
    function readhost()
    {
        $myhttp  = $_SERVER['HT' . 'TP' . '_H' . 'O' . 'S' . 'T'];
        $httparr = explode(".", $myhttp);
        $myhttp2 = "";
        foreach ($httparr as $key => $value) {
            if ($key != 0) {
                $myhttp2 != "" ? $myhttp2 .= "." . $value : $myhttp2 .= $value;
            }
        }
        $this->host[0] = $myhttp;
        $this->host[1] = $myhttp2;
    }
    
    function vs()
    {
        $n1 = $this->getkey($this->host[0]);
        $n2 = $this->getkey($this->host[1]);
        if ($this->sn == $n1 || $this->sn == $n2) {
            $this->vsr = TRUE;
        } else {
            $this->vsr = FALSE;
        }
        return $this->vsr;
    }
    
    function getkey($sn)
    {
        $m1  = md5($sn);
        $k   = $m1[0];
        $m2  = str_replace($k, "", $m1);
        $len = strlen($m2);
        $m3  = "";
        for ($i = 0; $i < $len; $i++) {
            $m3 .= substr(md5($m2[$i]), 5, 5);
        }
        return $m3;
    }
}*/
?>