<?php

class EstateAction extends WapAction{
    public $token;
    public $wecha_id;
    private $tpl;
    private $info;
    public $weixinUser;
    public $homeInfo;
    public function _initialize() {
        parent::_initialize();
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $this->token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $this->wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $this->assign('token',$this->token);
        $this->assign('wecha_id',$this->wecha_id);
        $estat = M('Estate')->where(array('token'=>$this->token))->find();
        $this->assign('rid',$estat['res_id']);
        $this->assign('estatindex',$estat);
        $tpl=$this->wxuser;
        $this->tpl=$tpl;
    }

    public function index(){
        $data = M("Estate");
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $where      = array('token'=>$token);
        $es_data = $data->where($where)->find();

        include('./Saivi/Lib/ORG/index.Tpl.php');
        foreach($tpl as $k=>$v){
            if($v['tpltypeid'] == $es_data['tpid']){
                 $tplinfo = $v;
            }
        }

        $this->assign('estatindex',$es_data);

        $allflash   = M('Flash')->where(array('token'=>$token))->select();
        $flash   = array();
        $flashbg = array();

        foreach ($allflash as $af){
            if ($af['url']==''){
                $af['url']='javascript:void(0)';
            }
            if ($af['tip']==1){
                array_push($flash,$af);
            }elseif ($af['tip']==2) {
                array_push($flashbg,$af);
            }
            unset($af);

        }


$info = array();

$info[0]['url']  = "/index.php?g=Wap&m=Estate&a=index&token=$token&wecha_id=$wecha_id";
$info[0]['img']  = $es_data['picurl1'];
$info[0]['name'] = $es_data['menu1'];

$info[1]['url']  = "/index.php?g=Wap&m=Estate&a=introduce&token=$token&wecha_id=$wecha_id";
$info[1]['img']  = $es_data['picurl2'];
$info[1]['name'] = $es_data['menu2'];

$info[2]['url']  = "/index.php?g=Wap&m=Estate&a=housetype&token=$token&wecha_id=$wecha_id&sgssz=mp.weixin.qq.com";
$info[2]['img']  = $es_data['picurl3'];
$info[2]['name'] = $es_data['menu3'];

$info[3]['url']  = "/index.php?g=Wap&m=Estate&a=album&token=$token&wecha_id=$wecha_id";
$info[3]['img']  = $es_data['picurl4'];
$info[3]['name'] = $es_data['menu4'];

$info[4]['url']  = "/index.php?g=Wap&m=Estate&a=news&token=$token&wecha_id=$wecha_id";
$info[4]['img']  = $es_data['picurl5'];
$info[4]['name'] = $es_data['menu5'];

$info[5]['url']  = "/index.php?g=Wap&m=Estate&a=impress&token=$token&wecha_id=$wecha_id";
$info[5]['img']  = $es_data['picurl6'];
$info[5]['name'] = $es_data['menu6'];

$info[6]['url']  = "/index.php?g=Wap&m=Estate&a=reserlist&token=$token&wecha_id=$wecha_id";
$info[6]['img']  = $es_data['picurl7'];
$info[6]['name'] = $es_data['menu7'];

$info[7]['url']  = "/index.php?g=Wap&m=Estate&a=aboutus&token=$token&wecha_id=$wecha_id";
$info[7]['img']  = $es_data['picurl8'];
$info[7]['name'] = $es_data['menu8'];

        $homeInfo=M('home')->where(array('token'=>$token))->find();
        $this->assign('homeInfo',$homeInfo);
        $this->assign('flash',$flash);  //home view
        $this->assign('info',$info);   // 菜单相关,url(连接),img(菜单背景图),name(菜单名)
        $this->assign('flashbg',$flashbg);  //背景轮播图 img(图片地址)
        $this->assign('tpl',$this->tpl);
        if(!empty($tplinfo['tpltypename'])){
            $this->display('Index:'.$tplinfo['tpltypename']);
        }else{
            $this->display();
        }

    }


    public function introduce(){

        $this->display('index');
    }

    public function reserlist(){
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $data = M("Reservation");
        $where = array('token'=>$token,'addtype'=>'house_book');
        $count      = $data->where($where)->count();
        $Page       = new Page($count,12);
        $show       = $Page->show();
        $reslist = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('take desc')->select();
        $this->assign('count',$count);
        $this->assign('page',$show);
       // var_dump($reslist);
        $this->assign('info',$reslist);
        $this->display();
    }

    public function reservation(){
        $data = M("Reservation");
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $rid        = trim(filter_var($this->_get('rid'),FILTER_VALIDATE_INT));
        if(empty($rid) || empty($wecha_id)){
            exit($this->error('表单提交错误！如果是从分享进来的请先关注该微信公众号再来提交表单！',U('Estate/index',array('token'=>$token,'wecha_id'=>$wecha_id))));
        }
        $where = array('token'=>$token);
        $this->assign('rid',$rid);
        $where2 = array('token'=>$token,'id'=>$rid);
        $reslist =  $data->where($where2)->find();
        $this->assign('reslist',$reslist);
        $t_housetype = M('Estate_housetype');
        $housetype = $t_housetype->where($where)->order('sort desc')->field('id as hid,name')->select();
        $this->assign('housetype',$housetype);
        $where4 = array('token'=>$token,'wecha_id'=>$wecha_id,'type'=>'house_book','rid'=>$rid);
        $count = M('Reservebook')->where($where4)->count();
        $this->assign('count',$count);
        $this->display();
    }

    public function add(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
           // exit('此功能只能在微信浏览器中使用');
        }
if(IS_POST){

        $da['token']      = strval($this->_get('token'));
        $da['wecha_id']   = strval($this->_post('wecha_id'));
        $da['rid']        = (int)$this->_post('rid');
        $da['truename']   = strval($this->_post("truename"));
        $da['dateline']   = strval($this->_post("dateline"));
        $da['timepart']   = strval($this->_post("timepart"));
        $da['info']       = strval($this->_post("info"));
        $da['tel']        = strval($this->_post("tel"));
        $da['type']       = strval($this->_post('type'));
        $da['housetype']  = $this->_post('housetype');
        $da['booktime']   = time();
        $das['id']        = (int)$this->_post('id');
         $book   =   M('Reservebook');
         $token = strval($this->_get('token'));
         $wecha_id = strval($this->_get('wecha_id'));
         $url ='http://'.$_SERVER['HTTP_HOST'];
         $url .= U('Estate/mylist',array('token'=>$token,'wecha_id'=>$wecha_id,'rid'=>$da['rid']));

         $t_res = M("Reservation");
         $check = $t_res->where(array('id'=>$da['rid'],'addtype'=>'house_book','token'=>$da['token']))->find();

         if((int)$check['typename'] <= 0){
            $arr=array('errno'=>1,'msg'=>'预约失败，已经满员啦!','token'=>$token,'wecha_id'=>$wecha_id,'url'=>$url);
            echo json_encode($arr);
            exit;
         }else{
             if(!empty($check['typename2']) && strtotime($check['typename2']) > time()){
                $arr=array('errno'=>1,'msg'=>'预约失败，预约还没开始呢!','token'=>$token,'wecha_id'=>$wecha_id,'url'=>$url);
                echo json_encode($arr);
                exit;
             }elseif(!empty($check['typename3']) && strtotime($check['typename3']) < time()){
                $arr=array('errno'=>1,'msg'=>'预约失败，预约已经结束啦!','token'=>$token,'wecha_id'=>$wecha_id,'url'=>$url);
                echo json_encode($arr);
                exit;
             }
         }

        $ok = $book->data($da)->add();
        if(!empty($ok)){
            $t_res->where(array('id'=>$da['rid'],'addtype'=>'house_book','token'=>$da['token']))->setDec('typename');
            $arr=array('errno'=>0,'msg'=>'恭喜预约成功','token'=>$token,'wecha_id'=>$wecha_id,'url'=>$url);
            echo json_encode($arr);
            exit;
        }else{
             $arr=array('errno'=>1,'msg'=>'预约失败，请重新预约','token'=>$token,'wecha_id'=>$wecha_id,'url'=>$url);
            echo json_encode($arr);
            exit;
        }
      }
    }

    public function mylist(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(!strpos($agent,"icroMessenger")) {
            //exit('此功能只能在微信浏览器中使用');
        }
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $rid        = trim(filter_var($this->_get('rid'),FILTER_VALIDATE_INT));
        $book   =   M('Reservebook');

        $where = array('token'=>$token,'wecha_id'=>$wecha_id,'rid'=>$rid,'type'=>'house_book');
        //$books  = $book->where($where)->order('id DESC')->select();
       // $this->assign('books',$books);


         $count     = $book->where($where)->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $books      = $book->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('books',$books);

        $data = M("Reservation");
        $where3 = array('token'=>$token,'id'=>$rid);
        $headpic =  $data->where($where3)->getField('headpic');
        $this->assign('headpic',$headpic);
        $this->display();

    }

    public  function delOrder(){
        $token      = filter_var($this->_get('token'),FILTER_SANITIZE_STRING);
        $wecha_id   = filter_var($this->_get('wecha_id'),FILTER_SANITIZE_STRING);
        $rid        = trim(filter_var($this->_get('rid'),FILTER_VALIDATE_INT));
        $id         = trim(filter_var($this->_get('id'),FILTER_VALIDATE_INT));
        $t_book   =   M('Reservebook');
        $check = $t_book->where(array('id'=>$id,'rid'=>$rid, 'wecha_id'=>$wecha_id))->find();
        if($check){
            $t_book->where(array('id'=>$check['id'],'type'=>'house_book'))->delete();
            $this->success('删除成功',U('Estate/mylist',array('token'=>$token,'wecha_id'=>$wecha_id,'rid'=>$rid)));
             exit;
         }else{
            $this->error('非法操作！',U('Estate/mylist',array('token'=>$token,'wecha_id'=>$wecha_id,'rid'=>$rid)));
             exit;
         }
    }


    public function news(){
        $data = M("Estate");
        $this->token=$this->_get('token');
        $where = array('token'=>$this->token);
        $cid = $data->where($where)->getField('classify_id');
        if($cid != null){
            $t_classify = M('Classify');
            $where = array('token'=>$this->token,'id'=>$cid);
            $classify = $t_classify->where($where)->find();
        }
        $t_img = M('Img');
        $where = array('classid'=>$classify['id'],'token'=>$this->_get('token'));

        $count      = $t_img->where($where)->count();
        $Page       = new Page($count,5);
        $show       = $Page->show();
        $imgtxt     = $t_img->where($where)->field('id as mid,title,pic,createtime')->order('createtime desc,uptatetime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('imgtxt',$imgtxt);
        $this->assign('classify',$classify);
        $this->display();
    }

    public function  newlist(){
        $token = $this->_get('token');
        $mid = (int)$this->_get('mid');
        $t_img = M('Img');
        $where = array('id'=>$mid,'token'=>$token);
        $imgtxt = $t_img->where($where)->find();
        $this->assign('imgtxt',$imgtxt);
        $this->display();
    }


    public function housetype(){
        $t_housetype = M('Estate_housetype');
        $where       = array('token'=>$this->_get('token'));
        $count      = $t_housetype->where($where)->count();
        $Page       = new Page($count,5);
        $show       = $Page->show();
        $housetype  = $t_housetype->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        foreach ($housetype as $k => $v) {
            $son_type[] = M("Estate_son")->where(array('id'=>$v['son_id']))->field('id as sid,title,description as desc_son')->find();
        }
        foreach ($son_type as $key => $value) {
             foreach ($value as $k => $v) {
                  $housetype[$key][$k] = $v;
             }

        }
        $this->assign('housetype',$housetype);
        $data = M("Estate");
        $this->token=$this->_get('token');
        $where = array('token'=>$this->token);
        $es_data = $data->where($where)->field('title,house_banner,panorama_id')->find();
        $this->assign('es_data',$es_data);
        $this->display();
    }



    public function album(){
        $this->token=$this->_get('token');
        $reply_info_db=M('Reply_info');
        $config=$reply_info_db->where(array('token'=>$this->token,'infotype'=>'album'))->find();
        if ($config){
            $headpic=$config['picurl'];
        }else {
            $headpic='/tpl/Wap/default/common/css/Photo/banner.jpg';
        }
        $this->assign('headpic',$headpic);

        $Photo = M("Photo");
        $t_album = M('Estate_album');
        $album = $t_album->where(array('token'=>$this->_get('token')))->field('id,poid')->select();
        $list_photo = array();
        foreach ($album as $k => $v) {
             $list_photo[] = $Photo->where(array('token'=>$this->_get('token'),'id'=>$v['poid']))->find();
        }
        $this->assign('photo',$list_photo);
        $this->display('Photo:index');
    }

    public function show_album(){
        $this->token=$this->_get('token');
        $reply_info_db=M('Reply_info');
        $config=$reply_info_db->where(array('token'=>$this->token,'infotype'=>'album'))->find();
        if ($config){
            $headpic=$config['picurl'];
        }else {
            $headpic='/tpl/Wap/default/common/css/Photo/banner.jpg';
        }
        $this->assign('headpic',$headpic);
        $t_housetype = M('Estate_housetype');
        $id = (int)$this->_get('id');
        $where = array('token'=>$this->_get('token'),'id'=>$id);
        $housetype = $t_housetype->where($where)->order('sort desc')->find();
        $this->assign('shareid',$id);

        $data = M("Estate");
        $this->token=$this->_get('token');
        $where = array('token'=>$this->token);
        $es_data = $data->where($where)->field('id,title')->find();
        if(!empty($es_data)){
            $housetype = array_merge($housetype,$es_data);
        }
        $this->assign('housetype',$housetype);
        $this->display();
    }

    public function impress(){
        $t_impress = M('Estate_impress');
        $where     = array('token'=>$this->_get('token'));
        $where2    = array('token'=>$this->_get('token'),'is_show'=>1);
        $impress   = $t_impress->where($where2)->order('sort desc')->select();
        $count2    = $t_impress->where($where2)->count();
        $Page2     = new Page($count2,12);
        $show2     = $Page2->show();
        $impress   = $t_impress->where($where2)->limit($Page2->firstRow.','.$Page2->listRows)->order('sort desc')->select();
        $this->assign('impress',$impress);
        $this->assign('thiscount',$count2);
        $this->assign('page2',$show2);

        $t_expert = M('Estate_expert');
        $count      = $t_expert->where($where)->count();
        $Page       = new Page($count,5);
        $show       = $Page->show();
        $expert     = $t_expert->where($where)->order('sort desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('tcount',$count);
        $this->assign('expert',$expert);

        $this->display();
    }

    public function impress_add(){
        $t_impress  = M('Estate_impress'); //comment 统计数
        $t_imp_add  = M("Estate_impress_add");
        $imp_id     = (int)$this->_post('imp_id');
        $token      = $this->_post('token');
        $wecha_id   = $this->_post('wecha_id');
        $where      =  array('wecha_id'=>$wecha_id,'token'=>$token);
        $check      = $t_imp_add->where($where)->find();
        if($check != null){
            $imp  = $t_impress->where(array('token'=>$check['token'],'id'=>$check['imp_id']))->find();
            $data=array('errno'=>2,'msg'=>"您已经点赞:",'thiscom'=>$imp['title']);
            echo json_encode($data);
            exit;
        }

         if($id=$t_imp_add->add($_POST)){
            $t_impress->where(array('id'=>$imp_id,'token'=>$token))->setInc('comment');
             $data=array('errno'=>1,'msg'=>"谢谢您的赞。");
              echo json_encode($data);
             exit;
         }else{
            $data=array('errno'=>0,'msg'=>"点赞失败，请再来一次吧。");
            echo json_encode($data);
            exit;
         }

    }

    public function  aboutus(){
        $company = M('Company');
        $token=$this->_get('token');
        $about = $company->where(array('token'=>$token,'shortname'=>'loupan','isbranch'=>1))->find();
        $this->assign('about',$about);

        $this->display();
    }

}



?>