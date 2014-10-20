<?php
class GoldeggAction extends BaseAction
{
    public function index()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")&&!isset($_SESSION['token'])) {
            echo '此功能只能在微信浏览器中使用'; exit;
        }
        
        $token    = $this->_get('token');
        $wecha_id = $this->_get('wecha_id');
        if (!$wecha_id) {
            
        }
        $id     = $this->_get('id');
        $redata = M('Goldegg_record');
        $where  = array(
            'token' => $token,
            'wecha_id' => $wecha_id,
            'lid' => $id
        );
        $record = $redata->where($where)->find();
        if ($record == NULL) {
            $redata->add($where);
            $record = $redata->where($where)->find();
        }
        $snlist  = M('sncode')->where(array(
            'lid' => $id,
            'token' => $token,
            'wecha_id' => $wecha_id
        ))->order('id asc')->select();
        $Goldegg = M('Goldegg')->where(array(
            'id' => $id,
            'token' => $token,
            'status' => 1
        ))->find();
        $data    = array();
        if ($Goldegg['enddate'] < time()) {
            $data['end']     = 1;
            $data['endinfo'] = $Goldegg['endinfo'];
            $this->assign('Goldegg', $data);
            $this->display();
            exit();
        }
        
        if ($record['islucky'] == 1) {
            $data['end']   = 5;
            $data['sn']    = $record['sn'];
            $data['uname'] = $record['wecha_name'];
            $data['prize'] = $record['prize'];
            $data['phone'] = $record['phone'];
        }
        
        $data['on']            = 1;
        $data['token']         = $token;
        $data['wecha_id']      = $record['wecha_id'];
        $data['lid']           = $record['lid'];
        $data['id']            = $record['id'];
        $data['usenums']       = $record['usenums'];
        $data['canrqnums']     = $Goldegg['canrqnums'];
        $data['displayjpnums'] = $Goldegg['displayjpnums'];
        $data['first']         = $Goldegg['first'];
        $data['second']        = $Goldegg['second'];
        $data['third']         = $Goldegg['third'];
        $data['four']          = $Goldegg['four'];
        $data['five']          = $Goldegg['five'];
        $data['six']           = $Goldegg['six'];
        $data['firstnums']     = $Goldegg['firstnums'];
        $data['secondnums']    = $Goldegg['secondnums'];
        $data['thirdnums']     = $Goldegg['thirdnums'];
        $data['info']          = $Goldegg['info'];
        $data['txt']           = $Goldegg['txt'];
        $data['summary']       = $Goldegg['summary'];
        $data['title']         = $Goldegg['title'];
        $data['startdate']     = $Goldegg['startdate'];
        $data['enddate']       = $Goldegg['enddate'];
        $data['redo']          = $Goldegg['redo'];
        $data['prizeinfo']     = array(
            '0' => array(
                'tit' => '一等奖',
                'num' => $Goldegg['firstnums'],
                'info' => $Goldegg['first']
            ),
            '1' => array(
                'tit' => '二等奖',
                'num' => $Goldegg['secondnums'],
                'info' => $Goldegg['second']
            ),
            '2' => array(
                'tit' => '三等奖',
                'num' => $Goldegg['thirdnums'],
                'info' => $Goldegg['third']
            )
        );
        $this->assign('Goldegg', $data);
        $this->assign('snlist', $snlist);
        $this->display();
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $proArr
     * @param unknown_type $total 预计参与人数
     * @return unknown
     */
    protected function get_rand($proArr, $total)
    {
        $result  = '';
        $randNum = mt_rand(1, $total);
        foreach ($proArr as $k => $v) {
            if ($v['v'] > 0) {
                if ($randNum > $v['start'] && $randNum <= $v['end']) {
                    $result = $k;
                    break;
                }
            }
        }
        return $result;
    }
    
    protected function get_prize($id)
    {
        $Goldegg   = M('Goldegg')->where(array(
            'id' => $id
        ))->find();
        $record    = M('Goldegg_record')->where(array(
            'id' => $record['id']
        ))->find();
        $firstNum  = intval($Goldegg['firstnums']);
        $secondNum = intval($Goldegg['secondnums']);
        $thirdNum  = intval($Goldegg['thirdnums']);
        $multi     = intval($Goldegg['canrqnums']);
        $prize_arr = array(
            '0' => array(
                'id' => 1,
                'prize' => '一等奖',
                'v' => $firstNum,
                'start' => 0,
                'end' => $firstNum
            ),
            '1' => array(
                'id' => 2,
                'prize' => '二等奖',
                'v' => $secondNum,
                'start' => $firstNum,
                'end' => $firstNum + $secondNum
            ),
            '2' => array(
                'id' => 3,
                'prize' => '三等奖',
                'v' => $thirdNum,
                'start' => $firstNum + $secondNum,
                'end' => $firstNum + $secondNum + $thirdNum
            ),
            '3' => array(
                'id' => 4,
                'prize' => '谢谢参与',
                'v' => (intval($Goldegg['allpeople'])) * $multi - ($firstNum + $secondNum + $thirdNum),
                'start' => $firstNum + $secondNum + $thirdNum,
                'end' => intval($Goldegg['allpeople']) * $multi
            )
        );
        
        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val;
        }
        //-------------------------------	 
        //随机抽奖[如果预计活动的人数为1为各个奖项100%中奖]
        //-------------------------------
        if ($Goldegg['allpeople'] == 1) {
            if ($Goldegg['thirdnums'] > 0 && $Goldegg['thirdlucknums'] <= $Goldegg['thirdnums']) {
                $prizetype = 3;
            } elseif ($Goldegg['secondnums'] > 0 && $Goldegg['secondlucknums'] <= $Goldegg['secondnums']) {
                $prizetype = 2;
            } elseif ($Goldegg['firstnums'] > 0 && $Goldegg['firstlucknums'] <= $Goldegg['firstnums']) {
                $prizetype = 1;
            }
        } else {
            $prizetype = $this->get_rand($arr, intval($Goldegg['allpeople']) * $multi);
        }
        $winprize = $prize_arr[$prizetype - 1]['prize'];
        $zjl      = false;
        switch ($prizetype) {
            case 1:
                if ($Goldegg['firstlucknums'] >= $Goldegg['firstnums'] || $Goldegg['firstnums'] == 0) {
                    $zjl       = false;
                    $prizetype = '';
                    $typeprize = '';
                    $winprize  = '谢谢参与';
                } else {
                    $zjl       = true;
                    $prizetype = 1;
                    $typeprize = '一';
                    M('Goldegg')->where(array(
                        'id' => $id
                    ))->setInc('firstlucknums');
                }
                break;
            case 2:
                if ($Goldegg['secondlucknums'] >= $Goldegg['secondnums'] || $Goldegg['secondnums'] == 0) {
                    $zjl       = false;
                    $prizetype = '';
                    $typeprize = '';
                    $winprize  = '谢谢参与';
                } else {
                    $zjl       = true;
                    $prizetype = 2;
                    $typeprize = '二';
                    M('Goldegg')->where(array(
                        'id' => $id
                    ))->setInc('secondlucknums');
                }
                break;
            case 3:
                if ($Goldegg['thirdlucknums'] >= $Goldegg['thirdnums'] || $Goldegg['thirdnums'] == 0) {
                    $zjl       = false;
                    $prizetype = '';
                    $typeprize = '';
                    $winprize  = '谢谢参与';
                } else {
                    $zjl       = true;
                    $prizetype = 3;
                    $typeprize = '三';
                    M('Goldegg')->where(array(
                        'id' => $id
                    ))->setInc('thirdlucknums');
                }
                break;
            default:
                $zjl       = false;
                $prizetype = '';
                $typeprize = '';
                $winprize  = '谢谢参与';
                break;
        }
        $prizedat['prize'] = $prizetype;
        $prizedat['type']  = $typeprize;
        return $prizedat;
    }
    
    public function goodluck()
    {
        $token    = $this->_get('token');
        $wecha_id = $this->_get('wecha_id');
        $id       = $this->_get('id');
        $redata   = M('Goldegg_record');
        $where    = array(
            'token' => $token,
            'wecha_id' => $wecha_id,
            'lid' => $id
        );
        $record   = $redata->where($where)->find();
        $Goldegg  = M('Goldegg')->where(array(
            'id' => $id,
            'token' => $token,
            'status' => 1
        ))->find();
		if ($record['islucky'] == 1) {
			if ($Goldegg['redo'] == 0) {
				$res['norun']   = 1;
				$res['msgtype'] = 1;
				$res['msg']     = "您已中奖，请勿重复参与";
				echo json_encode($res);
				exit;
			}
		}
        //中过奖金
		/*
        if ($record['islucky'] == 1) {
            if ($Goldegg['redo'] == 0) {
                $norun          = 1;
                $sn             = $record['sn'];
                $uname          = $record['wecha_name'];
                $prize          = $record['prize'];
                $tel            = $record['phone'];
                $msg            = "尊敬的:<font color='red'>$uname</font>,您已经中过<font color='red'> $prize</font> 了,您的领奖序列号:<font color='red'> $sn </font>请您牢记及尽快与我们联系.";
                $res['norun']   = 1;
                $res['msgtype'] = 1;
                $res['msg']     = $msg;
                echo json_encode($res);
                exit;
            }
        }
		*/
        if ($record['usenums'] >= $Goldegg['canrqnums']) {
            $norun            = 2;
            $usenums          = $record['usenums'];
            $canrqnums        = $Goldegg['canrqnums'];
            $res['norun']     = $norun;
            $res['usenums']   = $usenums;
            $res['canrqnums'] = $canrqnums;
            $res['id']        = $id;
            $res['token']     = $token;
            $res['status']    = $status;
            $res['msgtype']   = 2;
            $res['msg']       = "您的抽奖机会已用完！";
            echo json_encode($res);
            exit;
        } else {
            M('Goldegg_record')->where($where)->setInc('usenums');
            $record    = M('Goldegg_record')->where($where)->find();
            $prizetype = $this->get_prize($id);
            if ($prizetype['prize'] >= 1 && $prizetype['prize'] <= 6) {
                $sn               = uniqid();
                $res['success']   = 1;
                $res['sn']        = $sn;
                $res['prizetype'] = $prizetype['type'];
                $res['usenums']   = $record['usenums'];
                $res['msgtype']   = 3;
                $res['msg']       = "恭喜，您中得" . $prizetype['type'] . "等奖！中奖编号为" . $sn . "，请妥善保管！";
                echo json_encode($res);
            } else {
                $res['success']   = 0;
                $res['sn']        = '';
                $res['prizetype'] = '';
                $res['usenums']   = $record['usenums'];
                $res['msgtype']   = 0;
                $res['msg']       = "很遗憾，您没能砸中，请再接再厉！";
                echo json_encode($res);
            }
            exit;
        }
    }
    
    //中奖后填写信息
    public function add()
    {
        if ($_POST['action'] == 'add') {
            $lid                = $this->_post('lid');
            $wechaid            = $this->_post('wechaid');
            $data['phone']      = $this->_post('tel');
            $data['wecha_name'] = $this->_post('wxname');
            $data['token']      = $this->_post('token');
            $data['time']       = time();
            $data['islucky']    = 1;
            $rollback           = M('Goldegg_record')->where(array(
                'lid' => $lid,
                'wecha_id' => $wechaid,
                'token' => $data['token']
            ))->save($data);
			$data['sn']         = $this->_post('sncode');
			$data['prize']      = $this->_post('prize') . "等奖";
            $data['lid']        = $lid;
            $data['wecha_id']   = $wechaid;
            $data['module']     = 'Goldegg';
            $data['createtime'] = time();
            $sndata  = M('Sncode');
            $snwhere = array(
                'lid' => $lid,
                'token' => $token,
                'wecha_id' => $wecha_id
            );
            $sncord  = $sndata->where($snwhere)->find();
            if ($sncord == NULL) {
                $snback = $sndata->add($data);
            }
            if ($rollback == true || $snback == true) {
                $res['success'] = 1;
				$res['msg'] = "恭喜！尊敬的 " . $data['wecha_name'] . ",请您保持手机通畅！你的领奖序号:" . $data['sn'];
			} else {
				$res['success'] = 0;
				$res['msg'] = "尊敬的 " . $data['wecha_name'] . ",数据添加失败，请您保持手机通畅并牢记您的领奖序号:" . $data['sn'];
			}
			echo json_encode($res);
            exit;
        }
    }
    
}
?>