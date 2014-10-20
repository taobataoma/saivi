<?php

class WxqAction extends UserAction{
	public function _initialize() {
		parent::_initialize();
		$function=M('Function')->where(array('funname'=>'Wxq'))->find();
		$this->canUseFunction('Wxq');
	}
    public function index(){

        $db = D('Wxq');
        $where['token'] = session('token');
        $count = $db->where($where)->count();
        $page = new Page($count, 10);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $page->show());
        $this->assign('info', $info);
        $this->display();
    }

    public function add(){
        $this->display();
    }

    public function insert(){
        $db = D('Wxq');
        $insertData = $db->create();
        if($insertData === false){
            $this->error($db->getError());
        }else{
            $id = $db->add();
            if($id){
                $data['pid'] = $id;
                $data['module'] = "Wxq";
                $data['token'] = session('token');
                $data['keyword'] = $_POST['keyword'];
                M('Keyword')->add($data);
                if(S('wxq' . $id)){
                    S('wxq' . $id, null);
                }
                S('wxq' . $id, $insertData); // 缓存
               $this->success('操作成功', U('Wxq/index'));
            }else{
                $this->error('操作失败', U('Wxq/index'));
            }
        }
    }

    public function edit(){
        $where['id'] = $this->_get('id', 'intval');
        $where['uid'] = session('uid');
        $where['token'] = session('token');
        $res = D('Wxq')->where($where)->find();
        $this->assign('info', $res);
        $this->display();
    }

    public function del(){
        $where['id'] = $this->_get('id', 'intval');
        $where['uid'] = session('uid');
        if(D('Wxq')->where($where)->delete()){
            M('Keyword')->where(array('pid' => $this->_get('id', 'intval'), 'token' => session('token'), 'module' => 'Wxq'))->delete();
            $con = array();
            $con['wxq_id'] = $where['id'];
            M('wxwall_message')->where($con)->delete();
            M('wxwall_members')->where($con)->delete();
            M('wxwall_award')->where($con)->delete();
            //清缓存
            S('wxq' . $where['id'], NULL); // 缓存
            $this->success('操作成功', U('Wxq/index'));
        }else{
            $this->error('操作失败', U('Wxq/index'));
        }
    }

    public function upsave(){
        $db = D('Wxq');
        $updateData = $db->create();
        if($updateData === false){
            $this->error($db->getError());
        }else{
            $id = $_POST['id'];
            $updateStatus = $db->where("id=$id")->save($updateData);
            if($updateStatus){
                $data['pid'] = $id;
                $data['module'] = "Wxq";
                $data['token'] = session('token');
                $da['keyword'] = $_POST['keyword'];
                M('Keyword')->where($data)->save($da);
                if(S('wxq' . $id)){
                    S('wxq' . $id, null);
                }
                S('wxq' . $id, $updateData); // 缓存
                $this->success('操作成功');
            }else{
                $this->error('操作失败', U("Wxq/index"));
            }
        }
    }

    public function detail(){
        $wxq_id = $this->_get('id', 'intval');
        $wall = $this->getWall($wxq_id);
        $con = array();
        $con['wxq_id'] = $wxq_id;
        $con['isshow'] = 1; //不需审核 或者是审核过得并没有显示的
        $list = M('Wxwall_message')->field(array('id', 'wxq_id', 'from_user', 'type','content', 'createtime'))->where($con)->order("createtime desc")->select();
        $list = json_encode($this->formatMsg($list));
        $this->assign('list', $list);
        $this->assign('wall', $wall);
        $this->assign('id',$wxq_id);
        $this->display();
    }

    private function getWall($id){
        $wall = M('wxq')->where("id=$id")->find();
        $wall['onlinenum'] = M('wxwall_members')->where("wxq_id=$id AND isjoin=1")->count();
        $con = array();
        $con['token'] = array('eq', $wall['token']);
        $wall['account'] = M('wxuser')->field(array('wxname', 'weixin'))->where($con)->find();
        return $wall;
    }

    private function formatMsg(&$list){
        if(empty($list)){
            return false;
        }
        $uids = $members = array();
        foreach($list as &$row){
            $uids[$row['from_user']] = $row['from_user'];
			if ($row['type'] == 'link') {
				$row['content'] = $this->iunserializer($row['content']);
				$row['content'] = '<a href="'.$row['content']['link'].'" target="_blank" title="'.$row['content']['description'].'">'.$row['content']['title'].'</a>'.'hit it';
			} elseif ($row['type'] == 'image') {
				$row['content'] = '<img width=100% height=100% src="'.$row['content'].'" />';
			}else{
            $row['content'] = $this->emotion($row['content'], '48px');
            }
        }
        unset($row);
        if(!empty($uids)){
            $db = M('Wxwall_members');
            for($i = 0; $i < count($list); $i++){
                $condition = array();
                $condition['from_user'] = $list[$i]['from_user'];
                $condition['wxq_id'] = $list[$i]['wxq_id'];
                $rs = $db->field(array('nickname', 'avatar', 'isblacklist'))->where($condition)->find();
                $list[$i]['nickname'] = $rs['nickname'];
                $list[$i]['avatar'] = $rs['avatar'];
                $list[$i]['isblacklist'] = $rs['isblacklist'];
                $rs = array();
            }
        }
        return $list;
    }

    /*
     * 增量数据调用
     */
    public  function iunserializer($value){
        if(empty($value)){
            return '';
        }
        if(is_array($value)){
            return $value;
        }
        $result = unserialize($value);
        return empty($result) ? $value : $result;
    }


    public function incoming(){
        $id = $this->_get('id', 'intval');
        $lastmsgtime = $this->_get('lastmsgtime', 'intval');
        $sql = "SELECT id, content, from_user, type, createtime FROM tp_wxwall_message WHERE wxq_id = '{$id}'";
        if(!empty($lastmsgtime)){
            $sql .= " AND createtime >= '$lastmsgtime' AND isshow = 1 AND isshowed=0 ORDER BY createtime ASC LIMIT 1";
        }else{
            $sql .= " AND isshow = 1 AND isshowed=0  ORDER BY createtime ASC  LIMIT 1";
        }
        $db = new Model();
        $rowBefore = array();
        $rowBefore = $db->query($sql);
        if(!empty($rowBefore)){
            $row = array();
            $row = $rowBefore[0];
            $condition = array();
            $condition['from_user'] = array('eq', $row['from_user']);
            $condition['wxq_id'] = array('eq', $id);
            $member = M('wxwall_members')->field(array('nickname', 'avatar'))->where($condition)->find();
            $row['avatar'] = $member['avatar'];
            $row['nickname'] = $member['nickname'];
            if ($row['type'] == 'link') {
				$row['content'] = $this->iunserializer($row['content']);
				$row['content'] = '<a href="'.$row['content']['link'].'" target="_blank" title="'.$row['content']['description'].'">'.$row['content']['title'].'</a>'.'hit it';
			} elseif ($row['type'] == 'image') {
				$row['content'] = '<img width=100% height=100% src="'.$row['content'].'" />';
			}else{
                $row['content'] = $this->emotion($row['content'], '48px');
            }
            M('wxwall_message')->where(array('id' => $row['id']))->save(array('isshowed' => 1)); //显示过得isshow
            $this->ajaxReturn($row, "1", 1);
        }
    }

    //审核
    public function examineInfo(){
        $wxq_id = $this->_get('id', 'intval');
        $db = M('Wxwall_message');
        if(($this->_get('isshow', 'intval') == 0)){
            $where['isshow'] = array('eq', 0);
            $where['wxq_id'] = array('eq', $wxq_id);
        }else if(($this->_get('isshow', 'intval') == 1)){
            $where['isshow'] = array('eq', 1);
            $where['wxq_id'] = array('eq', $wxq_id);
        }else{
            $where['wxq_id'] = array('eq', $wxq_id);
        }
        $count = $db->where($where)->count();
        $page = new Page($count, 3);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order("createtime ASC")->select();
        $info = $this->formatMsg($info);
        $wall = $this->getWall($wxq_id);
        $this->assign('wall', $wall);
        $this->assign('page', $page->show());
        $this->assign('list', $info);
        $this->assign('max', 3); //与分页数目一致
        $this->display();
    }

    //操作一条数据
    public function operateOne(){
        $id = $this->_get('id', 'intval');
        $db = M('Wxwall_message');
        if($_GET['type'] == 'shenhe'){
            $z = $db->where("id=$id")->save(array('isshow' => 1));
        }
        if($_GET['type'] == 'del'){
            $z = $db->where("id=$id")->delete();
        }
        if($_GET['type'] == 'bushen'){
            $z = $db->where("id=$id")->save(array('isshow' => 0));
        }
        if($z){
            $this->ajaxReturn($id, 'ok', 1);
        }else{
            $this->ajaxReturn($id, 'fail', 0);
        }
    }

    public function examine(){
        if(!empty($_POST)){
            if($_POST['verify'] == '一键审核'){
                if($_POST['max'] >= 0){
                    $db = M('Wxwall_message');
                    for($i = 1; $i <= $_POST['max']; $i++){
                        $id = $_POST['yes' . $i];
                        if($id > 0){
                            $db->where("id=$id")->save(array('isshow' => 1));
                        }
                    }
                    $message = '成功审批';
                }
            }
            if($_POST['delete'] == '一键删除'){
                if($_POST['max'] >= 0){
                    $db = M('Wxwall_message');
                    for($i = 1; $i <= $_POST['max']; $i++){
                        $id = $_POST['yes' . $i];
                        if($id){
                            $db->where("id=$id")->delete();
                        }
                    }
                    $message = '删除成功';
                }
            }
            $this->success($message);
        }
    }

    //抽奖页
    public function lottery(){
        $id = $this->_get('id', 'intval');
        $type = $this->_get('type', 'intval');
        $wall = $this->getWall($id);
        if($type == 1){
            $list = M('wxwall_message')->where("wxq_id={$wall['id']} AND isshowed=1 AND from_user <> ''")->order("createtime ASC")->select();
        }else{
            $list = M('wxwall_message')->where("wxq_id={$wall['id']} AND isshowed=1 AND  from_user <> ''")->order("createtime ASC")->group("from_user")->select();
        }
//        exit(dump($list));
        $list = json_encode($this->formatMsg($list));
        $this->formatMsg($list);
        $this->assign('wall', $wall);
        $this->assign('list', $list);
        $this->display();
    }

    public function award(){
        $message = M('wxwall_message')->where("id={$_GET['mid']}")->find();
        if(empty($message)){
            $this->ajaxReturn(0, "参数不正确", 0);
        }
        $data = array(
            'wxq_id' => $message['wxq_id'],
            'from_user' => $message['from_user'],
            'createtime' => time(),
            'status' => 0,
        );
        $rs = M('wxwall_award')->add($data);
        if($rs){
            $this->ajaxReturn($rs, "成功", 1);
        }else{
            $this->ajaxReturn(0, "失败", 0);
        }
    }

    //中奖名单
    public function awardList(){
        if(!empty($_GET['wid'])){
            $wid = $this->_get('wid', 'intval');
            if($_GET['type'] == 'del'){
                $rss = M('wxwall_award')->where(array('id' => $wid))->delete();
            }else{
                $rss = M('wxwall_award')->where(array('id' => $wid))->save(array('status' => $_GET['status']));
            }
            if($rss){
                exit($this->success('操作成功'));
            }else{
                exit($this->success('操作失败'));
            }
        }
        $wxq_id = $this->_get('id', 'intval');
        $where['wxq_id'] = $wxq_id;
        $db = M('wxwall_award');
        $count = $db->where($where)->count();
        $page = new Page($count, 5);
        $info = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order("createtime ASC")->select();
        $info = $this->formatMsg($info);
        $wall = $this->getWall($wxq_id);
        $this->assign('wall', $wall);
        $this->assign('page', $page->show());
        $this->assign('list', $info);
        $this->display();
    }

    //二维码
    public function qrcode(){
        $id = $this->_get('id','intval');
        $wall = $this->getWall($id);
        $this->assign('wall',$wall);
        $this->display();
    }

    public function emotion($message = '', $size = '24px'){
        $emotions = array(
            "/::)", "/::~", "/::B", "/::|", "/:8-)", "/::<", "/::$", "/::X", "/::Z", "/::'(",
            "/::-|", "/::@", "/::P", "/::D", "/::O", "/::(", "/::+", "/:--b", "/::Q", "/::T",
            "/:,@P", "/:,@-D", "/::d", "/:,@o", "/::g", "/:|-)", "/::!", "/::L", "/::>", "/::,@",
            "/:,@f", "/::-S", "/:?", "/:,@x", "/:,@@", "/::8", "/:,@!", "/:!!!", "/:xx", "/:bye",
            "/:wipe", "/:dig", "/:handclap", "/:&-(", "/:B-)", "/:<@", "/:@>", "/::-O", "/:>-|",
            "/:P-(", "/::'|", "/:X-)", "/::*", "/:@x", "/:8*", "/:pd", "/:<W>", "/:beer", "/:basketb",
            "/:oo", "/:coffee", "/:eat", "/:pig", "/:rose", "/:fade", "/:showlove", "/:heart",
            "/:break", "/:cake", "/:li", "/:bome", "/:kn", "/:footb", "/:ladybug", "/:shit", "/:moon",
            "/:sun", "/:gift", "/:hug", "/:strong", "/:weak", "/:share", "/:v", "/:@)", "/:jj", "/:@@",
            "/:bad", "/:lvu", "/:no", "/:ok", "/:love", "/:<L>", "/:jump", "/:shake", "/:<O>", "/:circle",
            "/:kotow", "/:turn", "/:skip", "/:oY", "/:#-0", "/:hiphot", "/:kiss", "/:<&", "/:&>"
        );
        foreach($emotions as $index => $emotion){
            $message = str_replace($emotion, '<img style="width:' . $size . ';vertical-align:middle;" src="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/' . $index . '.gif" />', $message);
        }
        return $message;
    }

}

?>
