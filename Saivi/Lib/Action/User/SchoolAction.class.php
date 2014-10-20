<?php
class SchoolAction extends UserAction{
    public function _initialize() {
        parent::_initialize();
        $this->canUseFunction('school');
    }


    public  function getage($birthday){

                if(function_exists('date_diff')){
                     $now = date_create("now");
                     $birthday = date_create($birthday);
                    if($now < $birthday){
                        return "* 岁";
                    }else{
                      $interval = date_diff($now,$birthday);
                      return $interval->format("%y")." 岁";
                    }

                }else{
                    $year_diff = '';
                    $time      = strtotime($birthday);
                    if(FALSE === $time){
                      return '*';
                    }
                    $birthday   = date('Y-m-d', $time);
                    list($year,$month,$day) = explode("-",$birthday);
                    $year_diff  = date("Y") - $year;
                    $month_diff = date("m") - $month;
                    $day_diff   = date("d") - $day;
                    if ($day_diff < 0 || $month_diff < 0) $year_diff--;
                    return $year_diff.' 岁';
                }
    }

    public function index(){
        $arrType = array('semester','theclass','score','subject','timeframe','week');
        $type = trim(filter_var($this->_get('type'),FILTER_SANITIZE_STRING));
        if(!in_array($type,$arrType)){
            exit($this->error('参数非法',U('Function/index',array('token'=>session('token')))));
        }
        $t_s_classify = M("school_classify");
        if(IS_POST){

            $items = empty($_REQUEST['add']['sname'][0]) ? $this->error('选项至少填写一项') : $_REQUEST['add'];
            $new_item = array();
            foreach($items as $key=>$value){
                foreach($value as $k=>$v){
                    if($v != ''){
                        $new_item[$k][$key] = $v;
                    }
                }
            }
            foreach ($new_item as $k => $v) {
                $data['sname']  =   filter_var(trim($v['sname']),FILTER_SANITIZE_STRING) ? $v['sname'] :$this->error('检查是否有特殊符号或者留空项,请不要重复提交',U('School/index',array('token'=>session('token'),'type'=>$type)));
                $data['ssort']  = filter_var($v['ssort'],FILTER_VALIDATE_INT) ? $v['ssort'] : $this->error('检查是否是整数',U('School/index',array('token'=>session('token'),'type'=>$type)));
                if(empty($data['ssort'])){$data['ssort'] = 1;}

                $data['type']   = $type;
                $data['token']  = session('token');
                $chang['sid']   = filter_var($v['sid'],FILTER_VALIDATE_INT) ? $v['sid'] : '';
                if(isset($chang['sid']) && !empty($chang['sid'])){
                    $t_s_classify->where(array('token'=>session('token'),'sid'=>$chang['sid'],'type'=>$type))->save($data);
                }else{
                    $ok = $t_s_classify->add($data);
                }
                unset($data);
            }
            unset($_REQUEST);
            if($ok){
                $this->success('添加成功',U('School/index',array('token'=>session('token'),'type'=>$type)));
                exit;
            }else{
                $this->error('添加失败',U('School/index',array('token'=>session('token'),'type'=>$type)));
                exit;
            }
        }
         $where = array('token'=>session('token'),'type'=>$type);
         $semester = $t_s_classify->where($where)->select();
         $this->assign('semester',$semester);
         $this->assign('type',$type);
         $this->display();
    }

    public function del_item(){
        $arrType = array('students','assess','curriculum','scoresearch','semester','theclass','score','subject','timeframe','week');
        $type = trim(filter_var($this->_get('deltype'),FILTER_SANITIZE_STRING));
        if(!in_array($type,$arrType)){
            exit($this->error('参数非法',U('Function/index',array('token'=>session('token')))));
        }
         if(IS_POST){
            $sid    = trim(filter_var($this->_post('sid'),FILTER_VALIDATE_INT));
            //$type   = trim(filter_var($this->_get('deltype'),FILTER_SANITIZE_STRING));
            M('school_classify')->where(array('token'=>session('token'),'sid'=>$sid,'type'=>$type))->delete();
            echo  json_encode(array('errno'=>1));exit;
            //$this->success('删除成功',U("School/index",array('token'=>session('token'))));exit;
         }
         $id     =   trim(filter_var($this->_get('id'),FILTER_VALIDATE_INT));
         //$type   = filter_var($this->_get('deltype'),FILTER_SANITIZE_STRING);
          if($type == 'students'){
             M('school_students')->where(array('token'=>session('token'),'id'=>$id))->delete();
             M('school_score')->where(array('token'=>session('token'),'sid'=>$id))->delete();
             //$this->success('删除成功',U('School/students',array('token'=>session('token'))));exit;
          }elseif($type == 'assess'){
            M('school_teachers')->where(array('token'=>session('token'),'tid'=>$id))->delete();
            M('school_tcourse')->where(array('token'=>session('token'),'tid'=>$id))->delete();
            // $this->success('删除成功',U('School/assess',array('token'=>session('token'))));exit;
          }elseif($type == 'curriculum'){
            M('school_tcourse')->where(array('token'=>session('token'),'id'=>$id))->delete();

          }elseif('scoresearch' == $type){
            M('school_score')->where(array('token'=>session('token'),'id'=>$id))->delete();
          }
          $this->success('删除成功',U("School/$type",array('token'=>session('token'))));exit;
    }

    public function student_score(){
        $t_s_classify = M('school_classify');
        $t_s_score   = M('school_score');
        $type   =   trim(filter_var($this->_get('type'),FILTER_SANITIZE_STRING));
        $scid   =   intval(trim(filter_var($this->_get('scid'),FILTER_VALIDATE_INT)));
        if('edit' == $type && '' != $scid){
            $swhere    = array('token'=>session('token'),'id'=>$scid);
            $scores    = $t_s_score->where($swhere)->find();
            $this->assign('scores',$scores);
        }

        $where1     = array('token'=>session('token'),'type'=>'score');
        $li_score   = $t_s_classify->where($where1)->order('ssort DESC')->select();
        $where2     = array('token'=>session('token'),'type'=>'subject');
        $li_subject = $t_s_classify->where($where2)->order('ssort DESC')->select();
        $_sid       = intval(trim(filter_var($this->_get('id'),FILTER_VALIDATE_INT)));
        $t_s_students = M('school_students');
        $where4     = array('token'=>session('token'),'id'=>$_sid);
        $student    = $t_s_students->where($where4)->field('id sid,s_name,xq_id,token')->find();

        if(IS_POST){
            $_POST['token'] = session('token');
            $_id     = intval(filter_var($this->_post('id'),FILTER_VALIDATE_INT));
            $istoken = trim(filter_var($this->_post('istoken'),FILTER_SANITIZE_STRING));
            if($t_s_score->create()!=false){
                if(session('token') == $istoken && $_id != ''){
                    $where5 = array('token'=>session('token'),'id'=>$_id);
                    if($t_s_score->where($where5)->save($_POST)){
                        $this->success('修改成功',U('School/scoresearch',array('token'=>session('token'))));exit;
                    }else{
                        $this->error('服务器繁忙,修改失败,请稍候再试');exit;
                    }

                }else{
                    if($id=$t_s_score->data($_POST)->add()){
                        $this->success('添加成功',U('School/students',array('token'=>session('token'))));exit;
                    }else{
                        $this->error('服务器繁忙,添加失败,请稍候再试');exit;
                    }
                }
            }else{
                $this->error($t_s_score->getError());exit;
            }
        }
        $this->assign('student',$student);
        $xq = $t_s_classify->where(array('token'=>session('token'),'sid'=>$student['xq_id']))->getField('sname');
        $this->assign('xq',$xq);

        $this->assign('li_score',$li_score);
        $this->assign('li_subject',$li_subject);
        $this->display();
    }

    public function paycost(){
        $this->display();
    }

    public function curriculum(){
        $t_s_tcourse = M('school_tcourse');
        $where  =  array('token'=>session('token'));
        //$arrids =  $t_s_tcourse->where($where)->select();
        $count      = $t_s_tcourse->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $arrids = $t_s_tcourse->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $t_s_teachers = M('school_teachers');
        $t_s_classify = M('school_classify');
        $market = array();
        foreach($arrids as $k=>$val){
            $market[$k]['id']      = $val['id'];
            $market[$k]['tid']     = $val['tid'];
            $market[$k]['tname']   = $t_s_teachers->where(array('tid'=>$val['tid'],'token'=>session('token')))->getField('tname');
            $market[$k]['bj_name'] = $t_s_classify->where(array('sid'=>$val['bj_id'],'token'=>session('token')))->getField('sname');
            $market[$k]['km_name'] = $t_s_classify->where(array('sid'=>$val['km_id'],'token'=>session('token')))->getField('sname');
            $market[$k]['xq_name'] = $t_s_classify->where(array('sid'=>$val['xq_id'],'token'=>session('token')))->getField('sname');
            $market[$k]['sd_name'] = $t_s_classify->where(array('sid'=>$val['sd_id'],'token'=>session('token')))->getField('sname');
        }
        $this->assign('market',$market);
        $this->display();
    }

    public function subscribe(){
        $data       = M("Reservation");
        $where      = array('token'=>session('token'),'addtype'=>'course');
        $count      = $data->where($where)->count();
        $Page       = new Page($count,15);
        $show       = $Page->show();
        $reslist    = $data->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $course_count = $data->where(array('addtype' => 'course','token'=>session('token')))->count();
        $this->assign('course_count',$course_count);
        $this->assign('page',$show);
        $this->assign('reslist',$reslist);
        $this->display();
    }

    public function subscribe_add(){
        $id     =   trim(filter_var($this->_get('id'),FILTER_VALIDATE_INT));
        $type   =   trim(filter_var($this->_get('type'),FILTER_SANITIZE_STRING));
        $addtype=   trim(filter_var($this->_get('addtype'),FILTER_SANITIZE_STRING));
        $this->assign('addtype',$addtype);
        $data  =  D('reservation');
        if(isset($id) && $id != '' && $type =='edit'){
            $where = array('token'=>session('token'),'id'=>(int)$id,'addtype'=>$addtype);
            $reslist = $data->where($where)->find();
            $this->assign('reslist',$reslist);
        }
        if(IS_POST){
            $_POST['token']=session('token');
            $id     =   trim(filter_var($this->_post('id'),FILTER_VALIDATE_INT));
            $type   =   trim(filter_var($this->_post('type'),FILTER_SANITIZE_STRING));
            $addtype=   trim(filter_var($this->_post('addtype'),FILTER_SANITIZE_STRING));
            if($addtype == 'course' && $id != '' && $type =='editsave'){
                    if($data->create()){
                        if($data->where($where)->save($_POST)){
                            $this->success('修改成功',U('School/subscribe',array('token'=>session('token'),'addtype'=>'course')));
                        }else{
                            $this->error('操作失败',U('School/subscribe',array('token'=>session('token'),'addtype'=>'course')));
                        }
                }else{
                    $this->error($data->getError());
                }
            }else{
                if($data->create()!=false){
                    if($id=$data->data($_POST)->add()){
                        $this->success('添加成功',U('School/subscribe',array('token'=>session('token'),'addtype'=>'course')));
                    }else{
                        $this->error('服务器繁忙,请稍候再试',U('School/subscribe',array('token'=>session('token'),'addtype'=>'course')));
                    }
              }else{
                    $this->error($data->getError());
              }
            }


        }else{

            $this->display();
        }
    }

    public function subscribe_del(){
        $id     =   trim(filter_var($this->_get('id'),FILTER_VALIDATE_INT));
        $addtype=   trim(filter_var($this->_get('addtype'),FILTER_SANITIZE_STRING));
        $res    = M('Reservation');
        $find   = array('id'=>$id,'token'=>session('token'),'addtype'=>$addtype);
        $result = $res->where($find)->find();
         if($result){
            $res->where('id='.$result['id'])->delete();
            $where = array('rid'=>$result['id'],'token'=>session('token'));
            M('reservebook')->where($where)->delete();
            $this->success('删除成功',U('School/subscribe',array('token'=>session('token'),'addtype'=>'course')));
             exit;
         }else{
            $this->error('非法操作！',U('School/subscribe',array('token'=>session('token'),'addtype'=>'course')));
             exit;
         }
    }

    public function res_manage(){
        $t_reservebook = M('reservebook');
        $rid     =   trim(filter_var($this->_get('id'),FILTER_VALIDATE_INT));
        $addtype=   trim(filter_var($this->_get('addtype'),FILTER_SANITIZE_STRING));
        $this->assign('addtype',$addtype);

        $where = array('token'=>session('token'),'rid'=>$rid,'type'=>$addtype);
        $count      = $t_reservebook->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $books = $t_reservebook->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('books',$books);
        $this->assign('count',$t_reservebook->where($where)->count());
        $where2 = array('token'=>session('token'),'rid'=>$rid,'type'=>$addtype,'remate'=>1);
        $where3 = array('token'=>session('token'),'rid'=>$rid,'type'=>$addtype,'remate'=>2);
        $where4 = array('token'=>session('token'),'rid'=>$rid,'type'=>$addtype,'remate'=>0);
        $this->assign('ok_count',$t_reservebook->where($where2)->count());
        $this->assign('lose_count',$t_reservebook->where($where3)->count());
        $this->assign('call_count',$t_reservebook->where($where4)->count());
        $this->display();
    }

    public function reservation_uinfo(){

        $id    = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $token = trim(filter_var($this->_get('token'),FILTER_SANITIZE_STRING));
        $where = array('id'=>$id,'token'=>$token);
        $t_reservebook = M('reservebook');
        $userinfo = $t_reservebook->where($where)->find();
        $this->assign('userinfo',$userinfo);
        if(IS_POST){
            $id = $this->_post('id');
            $token = session('token');
            $where =  array('id'=>$id,'token'=>$token);
            $ok = $t_reservebook->where($where)->save($_POST);
            if($ok){
                $this->assign('ok',1);
            }else{
                $this->assign('ok',2);
            }

        }
        $this->display();
    }

    public function manage_del(){
        $id     = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $type   = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $res    = M('Reservebook');
        $find   = array('id'=>$id,'token'=>session('token'),'type'=>$type);
        $result = $res->where($find)->find();
         if($result){
            $res->where(array('id'=>$result['id'],'token'=>session('token'),'type'=>$type))->delete();
            $this->success('删除成功',U('School/res_manage',array('token'=>session('token'),'type'=>$type)));
             exit;
         }else{
            $this->error('非法操作');
             exit;
         }
    }


    public function scoresearch(){
        $t_s_score = M('school_score');
        $where  =  array('token'=>session('token'));
        $count      = $t_s_score->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $arrids = $t_s_score->where($where)->order('my_score DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $t_s_students = M('school_students');
        $t_s_classify = M('school_classify');
        $market = array();
        foreach($arrids as $k=>$val){
            $market[$k]['id']      = $val['id'];
            $market[$k]['sid']     = $val['sid'];
            $market[$k]['my_score']     = $val['my_score'];
            $market[$k]['s_name']   = $t_s_students->where(array('id'=>$val['sid'],'token'=>session('token')))->getField('s_name');
            $market[$k]['age']   = $this->getage($t_s_students->where(array('id'=>$val['sid'],'token'=>session('token')))->getField('birthdate'));
            $market[$k]['sex']   = $t_s_students->where(array('id'=>$val['sid'],'token'=>session('token')))->getField('sex');
            $market[$k]['bj_name'] = $t_s_classify->where(array('sid'=>$t_s_students->where(array('id'=>$val['sid'],'token'=>session('token')))->getField('bj_id'),'token'=>session('token')))->getField('sname');
            $market[$k]['km_name'] = $t_s_classify->where(array('sid'=>$val['km_id'],'token'=>session('token')))->getField('sname');
            $market[$k]['qh_name'] = $t_s_classify->where(array('sid'=>$val['qh_id'],'token'=>session('token')))->getField('sname');
        }
        $this->assign('market',$market);
        $this->display();
    }

    public function campusnews(){
        $this->display();
    }

    public function introduction(){
        $Photo = M("Photo");
        $photo = $Photo->where(array('token'=>session('token'),'status'=>1))->field('id,title')->order('id desc')->select();
        $this->assign('photo',$photo);
        $t_s_setindex = D('school_set_index');
        $where = array('token'=>session('token'));
        $classify = M('Classify')->where(array('token'=>session('token')))->field('id as cid,name')->order('id DESC')->select();
        $recipe   = M('recipe')->where(array('token'=>session('token'),'type'=>'school'))->field('id as rid,title')->order('sort desc')->select();
        $this->assign('recipe',$recipe);
        $this->assign('classify',$classify);

        if(IS_POST){
            $filters = array(
                'keyword'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'title'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'head_url'=>array(
                    'filter'=>FILTER_VALIDATE_URL
                ),
                'info'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
            );

            $check = filter_var_array($_POST,$filters);
            if(!$check){
                exit($this->error('表单包含敏感字符!'));
            }else{

                if(!$t_s_setindex->create()){
                    exit($this->error($t_s_setindex->getError()));
                }else{
                    $setid = filter_var($this->_post('setid'),FILTER_VALIDATE_INT);
                    $type = filter_var($this->_post('type'),FILTER_SANITIZE_STRING);
                    if('eidt'==$type && $setid != ''){

                   $o =  $t_s_setindex->where(array('setid'=>$setid, 'token'=>session('token')))->save($_POST);
                        if($o){
                            $data2['keyword'] = filter_var($this->_post('keyword'),FILTER_SANITIZE_STRING);
                            M('Keyword')->where(array('pid'=>$setid,'token'=>session('token'),'module'=>'Schoolset'))->data($data2)->save();
                            exit($this->success('修改成功',U('School/introduction',array('token'=>session('token')))));
                        }else{
                            exit($this->error('修改失败',U('School/introduction',array('token'=>session('token')))));
                        }
                    }else{
                        if($id=$t_s_setindex->data($_POST)->add()){
                            $data1['pid']=$id;
                            $data1['module']='Schoolset';
                            $data1['token']=session('token');
                            $data1['keyword']=filter_var($this->_post('keyword'),FILTER_SANITIZE_STRING);
                            M('Keyword')->add($data1);
                            $this->success('添加成功',U('School/introduction',array('token'=>session('token'))));exit;
                        }else{
                            $this->error('服务器繁忙,添加失败,请稍候再试');exit;
                        }
                    }
                }

            }
        }
        $schoolSet = $t_s_setindex->where(array('token'=>session('token')))->find();
        $this->assign('schoolSet',$schoolSet);
        $this->display();
    }

    public function assess(){
        $t_s_teachers = M('school_teachers');
        $where =  array('token'=>session('token'));
        $count      = $t_s_teachers->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $teachers = $t_s_teachers->where($where)->order('tid DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        foreach($teachers as &$val){
            $val['age'] = $this->getage($val['birthdate']);
        }
        $this->assign('teachers',$teachers);
        $this->display();
    }

    public function assess_add(){
        date_default_timezone_set('PRC');
        $t_s_teachers = M('school_teachers');
        $where = array('token'=>session('token'));
        $s_teachers = $t_s_teachers->where($where)->select();
        if(IS_POST){
            $_POST['token'] = session('token');
            $_tid =filter_var($this->_post('tid'),FILTER_VALIDATE_INT);
            if(strtotime($_POST['birthdate']) >= strtotime("-10 Year")){
                exit($this->error('亲,才10来岁就当老师啦?'));
            }
            $info = filter_var($this->_post('info'),FILTER_SANITIZE_STRING);
            if(empty($info)){
               exit($this->error('亲,教师简介不能为空!',U('School/assess',array('token'=>session('token')))));
            }

            if($t_s_teachers->create()!=false){
                if(isset($_tid) && $_tid != ''){
                    $where3 = array('token'=>session('token'),'tid'=>$_tid);
                    if($t_s_teachers->where($where3)->save($_POST)){
                        $this->success('修改成功',U('School/assess',array('token'=>session('token'))));exit;
                    }else{
                        $this->error('服务器繁忙,修改失败,请稍候再试',U('School/assess',array('token'=>session('token'))));exit;
                    }

                }else{
                    $_POST['createtime'] = time();
                    if($id=$t_s_teachers->data($_POST)->add()){
                        $this->success('添加成功',U('School/assess',array('token'=>session('token'))));exit;
                    }else{
                        $this->error('服务器繁忙,添加失败,请稍候再试',U('School/assess',array('token'=>session('token'))));exit;
                    }
                }
            }else{
                $this->error($t_s_teachers->getError());exit;
            }
        }
        $get_tid =filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        if(isset($get_tid) && $get_tid != '' && $type == 'edit'){
             $where_1 = array('token'=>session('token'),'tid'=>$get_tid);
             $s_teachers = $t_s_teachers->where($where_1)->find();
             $this->assign('type','edit');
        }
        $this->assign('s_teachers',$s_teachers);
        $this->display();
    }

    public function assess_course(){
        $t_s_classify = M('school_classify');
        $where1 = array('token'=>session('token'),'type'=>'theclass');
        $li_theclass = $t_s_classify->where($where1)->order('ssort DESC')->select();
        $where2 = array('token'=>session('token'),'type'=>'subject');
        $li_subject = $t_s_classify->where($where2)->order('ssort DESC')->select();
        $where_s = array('token'=>session('token'),'type'=>'week');
        $li_week = $t_s_classify->where($where_s)->order('ssort DESC')->select();
        $where_t = array('token'=>session('token'),'type'=>'timeframe');
        $li_timeframe = $t_s_classify->where($where_t)->order('ssort DESC')->select();

        $_tid =filter_var($this->_get('tid'),FILTER_VALIDATE_INT);
        $t_s_teachers = M('school_teachers');
        $where4 = array('token'=>session('token'),'tid'=>$_tid);
        $teachers = $t_s_teachers->where($where4)->field('tid,tname')->find();
        $t_s_tcourse = M('school_tcourse');
        $getid = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        if(isset($getid) && $type == 'edit' && $getid != ''){
            $editData = $t_s_tcourse->where(array('id'=>$getid,'token'=>session('token')))->find();
            $this->assign('tcourse',$editData);
        }
        if(IS_POST){
            $_POST['token'] = session('token');
            $_id =filter_var($this->_post('id'),FILTER_VALIDATE_INT);
            if($t_s_tcourse->create()!=false){
                if($type == 'edit' && $getid != ''){
                    $where5 = array('token'=>session('token'),'id'=>$_id);
                    if($t_s_tcourse->where($where5)->save($_POST)){
                        $this->success('修改成功',U('School/assess',array('token'=>session('token'))));exit;
                    }else{
                        $this->error('服务器繁忙,修改失败,请稍候再试');exit;
                    }

                }else{
                    if($id=$t_s_tcourse->data($_POST)->add()){
                        $this->success('添加成功',U('School/assess',array('token'=>session('token'))));exit;
                    }else{
                        exit($this->error('服务器繁忙,添加失败,请稍候再试'));
                    }
                }
            }else{
               exit($this->error($t_s_tcourse->getError()));
            }
        }

        $this->assign('teachers',$teachers);
        $this->assign('li_theclass',$li_theclass);
        $this->assign('li_week',$li_week);
        $this->assign('li_timeframe',$li_timeframe);
        $this->assign('li_subject',$li_subject);
        $this->display();
    }

    public function students(){
        $t_s_students = M('school_students');
        $where =  array('token'=>session('token'));
        $count      = $t_s_students->where($where)->count();
        $Page       = new Page($count,30);
        $show       = $Page->show();
        $students = $t_s_students->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $t_s_classify = M('school_classify');
        $bjArr = array();
        $this->assign('page',$show);
        foreach($students as &$val){
            $val['age'] = $this->getage($val['birthdate']);
            $bjArr['xq'] = $t_s_classify->where(array('sid'=>$val['bj_id'],'token'=>session('token')))->getField('sname');
            $val['bj_nmae'] = $bjArr['xq'];
            unset($bjArr['xq']);
        }
        $this->assign('students',$students);
        $this->display();
    }


    public function search(){
        $t_s_students = M('school_students');
        if(IS_POST){
            $where['token'] = session('token');
            $key        = filter_var($_REQUEST['searchkey']['key'],FILTER_SANITIZE_STRING);
            $name       = trim(filter_var($_REQUEST['searchkey']['name'],FILTER_SANITIZE_STRING));
            $phone      = trim(filter_var($_REQUEST['searchkey']['phone'],FILTER_SANITIZE_STRING));
            $jiontime   = trim(filter_var($_REQUEST['searchkey']['time'],FILTER_SANITIZE_STRING));
            $email      = trim(filter_var($_REQUEST['searchkey']['email'],FILTER_SANITIZE_STRING));
            if($key == 'students'){

                if(isset($name) && $name != ''){
                    $where['s_name'] = array('like',array("%{$name}%"));
                }
                if(isset($phone) && $phone != ''){
                    $where['mobile'] = array('like',array("%{$phone}%"));
                   // $where['mobile'] = $phone;
                }

                $count      = $t_s_students->where($where)->count();
                $Page       = new Page($count,30);
                $show       = $Page->show();
                $students = $t_s_students->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
                $t_s_classify = M('school_classify');
                $bjArr = array();
                $this->assign('page',$show);
                foreach($students as &$val){
                    $val['age'] = $this->getage($val['birthdate']);
                    $bjArr['xq'] = $t_s_classify->where(array('sid'=>$val['bj_id'],'token'=>session('token')))->getField('sname');
                    $val['bj_nmae'] = $bjArr['xq'];
                    unset($bjArr['xq']);
                }
                $this->assign('students',$students);
                $this->display('students');exit;
            }elseif($key == 'assess'){
                $t_s_teachers = M('school_teachers');
                if(isset($name) && $name != ''){
                    $where['tname'] = array('like',array("%{$name}%"));
                }
                if(isset($phone) && $phone != ''){
                    $where['mobile'] = array('like',array("%{$phone}%"));
                }
                if(isset($jiontime) && $jiontime != ''){
                    $where['jiontime'] = array('like',array("%{$jiontime}%"));
                }
                if(isset($email) && $jiontime != ''){
                    $where['email'] = array('like',array("%{$email}%"));
                   // $where['mobile'] = $phone;
                }
                $count      = $t_s_teachers->where($where)->count();
                $Page       = new Page($count,20);
                $show       = $Page->show();
                $teachers = $t_s_teachers->where($where)->order('tid DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
                $this->assign('page',$show);
                foreach($teachers as &$val){
                    $val['age'] = $this->getage($val['birthdate']);
                }
                $this->assign('teachers',$teachers);
                $this->display('assess');exit;
              }

        }
    }


    public function student_add(){
        date_default_timezone_set('PRC');
        $t_s_classify = M('school_classify');
        $where = array('token'=>session('token'),'type'=>'semester');
        $li_semester = $t_s_classify->where($where)->order('ssort DESC')->select();
        $where1 = array('token'=>session('token'),'type'=>'theclass');
        $li_theclass = $t_s_classify->where($where1)->order('ssort DESC')->select();
        $t_s_students = M('school_students');
        if(IS_POST){
            $_POST['token'] = session('token');
            $_id =filter_var($this->_post('id'),FILTER_VALIDATE_INT);
            if(strtotime($_POST['seffectivetime']) > strtotime($_POST['stheendtime'])){
                exit($this->error('生效时间不能大于终止时间'));
            }
            if(strtotime($_POST['birthdate']) >= strtotime("last Year")){
                exit($this->error('太坑爹了,人家才1岁就让上小学啦.'));
            }
            if($t_s_students->create()!=false){
                if(isset($_id) && $_id != ''){
                    //var_dump($_POST);exit;
                    $where3 = array('token'=>session('token'),'id'=>$_id);
                    if($t_s_students->where($where3)->save($_POST)){
                        $this->success('修改成功',U('School/students',array('token'=>session('token'))));exit;
                    }else{
                        $this->error('服务器繁忙,修改失败,请稍候再试');exit;
                    }

                }else{
                    $_POST['createdate'] = time();
                    if($id=$t_s_students->data($_POST)->add()){
                        $this->success('添加成功',U('School/students',array('token'=>session('token'))));exit;
                    }else{
                        $this->error('服务器繁忙,添加失败,请稍候再试');exit;
                    }
                }
            }else{
                $this->error($t_s_students->getError());exit;
            }
        }
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        if(isset($type) && $type === 'edit'){
            $id = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
            $where2 = array('token'=>session('token'),'id'=>$id);
            $student = $t_s_students->where($where2)->find();
            $this->assign('student',$student);
        }
        $this->assign('li_semester',$li_semester);
        $this->assign('li_theclass',$li_theclass);
        $this->display();
    }


    public function semester(){
        $this->display();
    }

    public function cookbook(){
        $data       = D('recipe');
        //$type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>session('token'),'type'=>'school');
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $recipe     = $data->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('recipe',$recipe);
        $this->display();
    }

    public function cookbook_add(){
        $t_recipe   = D('recipe');
        $id         = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $type       = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>session('token'),'id'=>$id,'type'=>$type);
        $recipe     = $t_recipe->where($where)->find();

        if(IS_POST){
            $filters = array(
                'keyword'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
                'title'=>array(
                    'filter'=>FILTER_SANITIZE_STRIPPED,
                    'flags'=>FILTER_SANITIZE_STRING,
                    'options'=>FILTER_SANITIZE_ENCODED
                ),
            );
            $_POST['begintime']  = strtotime(filter_var($this->_post('begintime'),FILTER_SANITIZE_STRING));
            $_POST['endtime']    = strtotime(filter_var($this->_post('endtime'),FILTER_SANITIZE_STRING));
            $_POST['type']       = filter_var($this->_post('type'),FILTER_SANITIZE_STRING);
            if($_POST['begintime'] > $_POST['endtime']){
                 exit($this->error('您好,开始时间不能大于结束时间.',U("School/cookbook",array('token'=>session('token'),'type'=>$type))));
            }
            $check = filter_var_array($_POST,$filters);
            if(!$check){
                exit($this->error('您好,包含敏感字符,或者是不允许字串!',U("School/cookbook",array('token'=>session('token'),'type'=>$type))));
            }
            $_POST['monday']    =   serialize($_REQUEST['monday']);
            $_POST['tuesday']   =   serialize($_REQUEST['tuesday']);
            $_POST['wednesday'] =   serialize($_REQUEST['wednesday']);
            $_POST['thursday']  =   serialize($_REQUEST['thursday']);
            $_POST['friday']    =   serialize($_REQUEST['friday']);
            $_POST['saturday']  =   serialize($_REQUEST['saturday']);
            $_POST['sunday']    =   serialize($_REQUEST['sunday']);
            $_POST['token']     =   session('token');
                if(!$t_recipe->create()){
                    exit($this->error($t_recipe->getError()));
                }else{
                    $id = filter_var($this->_post('id'),FILTER_VALIDATE_INT);
                    $status = filter_var($this->_post('status'),FILTER_SANITIZE_STRING);

                    if('edit'==$status && $id != ''){
                        $o =  $t_recipe->where(array('id'=>$id, 'token'=>session('token')))->save($_POST);
                        if($o){
                            $data2['keyword'] = filter_var($this->_post('keyword'),FILTER_SANITIZE_STRING);
                            M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Recipe'))->data($data2)->save();
                            exit($this->success('修改成功',U("School/cookbook",array('token'=>session('token'),'type'=>$_POST['type']))));
                        }else{
                            exit($this->error('修改失败',U("School/cookbook",array('token'=>session('token'),'type'=>$_POST['type']))));
                        }
                    }else{
                        if($id=$t_recipe->data($_POST)->add()){
                            $data1['pid']=$id;
                            $data1['module']='Recipe';
                            $data1['token']=session('token');
                            $data1['keyword']=filter_var($this->_post('keyword'),FILTER_SANITIZE_STRING);
                            M('Keyword')->add($data1);
                            $this->success('添加成功',U("School/cookbook",array('token'=>session('token'),'type'=>$_POST['type'])));exit;
                        }else{
                            exit($this->error('务器繁忙,添加失败,请稍候再试',U("Recipe/cookbook",array('token'=>session('token'),'type'=>$_POST['type']))));
                        }
                    }//edit & add
                }
        }
        $this->assign('recipe',$recipe);
        $this->display();
    }

    public function  cookbook_del(){
        $type = filter_var($this->_get('type'),FILTER_SANITIZE_STRING);
        $id  = filter_var($this->_get('id'),FILTER_VALIDATE_INT);
        $t_recipe = M('recipe');
        $find = array('id'=>$id,'type'=>$type,'token'=>session('token'));
        $result = $t_recipe->where($find)->find();
         if($result){
            $t_recipe->where(array('id'=>$result['id'],'type'=>$result['type'],'token'=>session('token')))->delete();
            exit($this->success('删除成功',U("School/cookbook",array('token'=>session('token'),'type'=>$result['type']))));
         }else{
            exit($this->error('非法操作,请稍候再试',U("School/cookbook",array('token'=>session('token'),'type'=>$type))));
         }
    }

    public function theclass(){
        $this->display();
    }

    public function score(){
        $this->display();
    }

    public function subject(){
        $this->display();
    }

    public function timeframe(){
        $this->display();
    }

    public function week(){
        $this->display();
    }

    public function area(){
        $this->display();
    }

}