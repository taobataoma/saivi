<?php
class WxqAction extends BaseAction{
	public function register(){
		
        $con = array();
        $con['from_user'] = array('eq',$this->_get('wecha_id'));
        $con['wxq_id'] = array('eq',$this->_get('id'));
		if (!empty($_POST['submit'])) {
			$data = array(
				'nickname' => $_POST['nickname'],
			);
			if (empty($data['nickname'])) {
				echo '<script>alert("请填写您的昵称！");</script>';
			}
			$data['avatar'] = $_POST['avatar_radio'];
//			if(!empty($_FILES['avatar']['tmp_name'])){
//                $upload = new UploadFile();
//                $upload->maxSize = 1024*1024; // 设置头像上传大小
//                $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置头像上传类型
//                $upload->savePath = RES.'/images/up_avatar/'; // 设置头像上传目录
//                if(!$upload->upload()){// 上传错误提示错误信息
//                    echo '<script>alert("上传出错");</script>';
//                }else{// 上传成功 获取上传文件信息
//                    $info = $upload->getUploadFileInfo();
//                }
//                $data['avatar'] = $info[0]['savepath'].$info[0]["savename"];
//			}
            if(empty($data['avatar'])){
                //如果不选择图像 使用默认图像
                $data['avatar']='./tpl/Wap/default/common/images/avatar/noavatar.jpg';
            }
            $data['lastupdate'] = strtotime("now");
            $data['isjoin']=1;
            $members = M('WxwallMembers')->where($con)->save($data);
            if($members){
                $memberData = M('WxwallMembers')->where($con)->find();
                $valId = $memberData['wxq_id'];
                $wxqData = M('Wxq')->where("id=$valId")->find();
                if(S($memberData['from_user'] . 'wxq')){
                    S($memberData['from_user'] . 'wxq', NULL);
                }
                S($memberData['from_user'] . 'wxq', $memberData, $wxqData['timeout']);
                echo '<script>alert("登记成功！现在进入话题发表内容！");</script>';
            }else{
                 echo '<script>alert("登记失败！重新登记！");</script>' ;
            }
        }
        $member = M('WxwallMembers')->where($con)->find();
		$wall = M('Wxq')->where(array('id' => $this->_get('id')))->find();
        $this->assign('data',$wall);
        $this->assign('member',$member);
		$this->display();
	}
}

?>