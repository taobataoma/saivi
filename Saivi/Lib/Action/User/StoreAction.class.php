<?php
class StoreAction extends UserAction{
	public $token;
	public $product_model;
	public $product_cat_model;
	public function _initialize() {
		parent::_initialize();
		$this->canUseFunction('shop');
		
		$this->assign('isDining', 0);
	}
	
	/**
	 * 分类列表
	 */
	public function index() {
		$data = M('Product_cat');
		$where = array('token' => session('token'));
        if (IS_POST) {
            $key = $this->_post('searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }

            $map['token'] = $this->get('token'); 
            $map['name|des'] = array('like',"%$key%"); 
            $list = $data->where($map)->select(); 
            $count      = $data->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        } else {
        	$count      = $data->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        }
		$this->assign('page',$show);		
		$this->assign('list',$list);
		if ($parentid){
			$parentCat = $data->where(array('id'=>$parentid))->find();
		}
		$this->assign('parentCat',$parentCat);
		$this->assign('parentid',$parentid);
		$this->display();		
	}
	
	/**
	 * 创建分类
	 */
	public function catAdd(){
		if(IS_POST){
			$this->insert('Product_cat','/index');
		}else{
			$parentid=intval($_GET['parentid']);
			$parentid=$parentid==''?0:$parentid;
			$this->assign('parentid',$parentid);
			$this->display('catSet');
		}
	}
	
	/**
	 * 分类修改
	 */
	public function catSet(){
        $id = $this->_get('id'); 
		$checkdata = M('Product_cat')->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Store/catAdd'));
        }
		if (IS_POST) {
            $data = D('Product_cat');
            $where=array('id'=>$this->_post('id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					if (!$this->isDining){
						$this->success('修改成功',U('Store/index',array('token'=>session('token'),'parentid'=>$this->_post('parentid'))));
					}else {
						$this->success('修改成功',U('Store/index',array('token'=>session('token'),'parentid'=>$this->_post('parentid'),'dining'=>1)));
					}
					
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{ 
			$this->assign('parentid',$checkdata['parentid']);
			$this->assign('set',$checkdata);
			$this->display();	
		
		}
	}
	
	/**
	 * 删除分类
	 */
	public function catDel() {
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
        $id = $this->_get('id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Product_cat');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $product_model=M('Product');
            $productsOfCat=$product_model->where(array('catid'=>$id))->select;
            if (count($productsOfCat)){
            	$this->error('本分类下有商品，请删除商品后再删除分类',U('Store/index',array('token'=>session('token'),'dining'=>$this->isDining)));
            }
            $back=$data->where($wehre)->delete();
            if($back==true){
            	if (!$this->isDining){
                $this->success('操作成功',U('Store/index',array('token'=>session('token'),'parentid'=>$check['parentid'])));
            	}else {
            		$this->success('操作成功',U('Store/index',array('token'=>session('token'),'parentid'=>$check['parentid'],'dining'=>1)));
            	}
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Store/index',array('token'=>session('token'))));
            }
        }        
	}
	
	/**
	 * 分类属性列表
	 */
	public function norms() {
		$type = isset($_GET['type']) ? intval($_GET['type']) : 0;
		$catid = intval($_GET['catid']);
		if($checkdata = M('Product_cat')->where(array('id' => $catid, 'token' => session('token')))->find()){
			$this->assign('catData', $checkdata);
        } else {
        	$this->error("没有选择相应的分类.",U('Store/index'));
        }
        
		$data = M('Norms');
		$where = array('catid' => $catid, 'type' => $type);
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page', $show);		
		$this->assign('list', $list);
		$this->assign('catid', $catid);
		$this->assign('type', $type);
		$this->display();		
	}
	
	/**
	 * 分类规格的操作
	 */
	public function normsAdd() {
		$type = intval($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
		if($data = M('Product_cat')->where(array('id' => $this->_get('catid'), 'token' => session('token')))->find()){
			$this->assign('catData', $data);
        } else {
        	$this->error("没有选择相应的分类.", U('Store/index'));
        }
		if (IS_POST) { 
            $data = D('Norms');
            $id = intval($this->_post('id'));
            if ($id) {
	            $where = array('id' => $id, 'type' => $type, 'catid' => $this->_get('catid'));
				$check = $data->where($where)->find();
				if ($check == false) $this->error('非法操作');
            }
			if ($data->create()) {
				if ($id) {
					if ($data->where($where)->save($_POST)) {
						$this->success('修改成功', U('Store/norms',array('token' => session('token'), 'catid' => $this->_post('catid'), 'type' => $type)));
					} else {
						$this->error('操作失败');
					}
				} else {
					if ($data->add($_POST)) {
						$this->success('添加成功', U('Store/norms',array('token' => session('token'), 'catid' => $this->_post('catid'), 'type' => $type)));
					} else {
						$this->error('操作失败');
					}
				}
			} else {
				$this->error($data->getError());
			}
		} else { 
			$data = M('Norms')->where(array('id' => $this->_get('id'), 'type' => $type, 'catid' => $this->_get('catid')))->find();
			//print_r($data);die;
			$this->assign('catid', $this->_get('catid'));
			$this->assign('type', $type);
			$this->assign('token', session('token'));
			$this->assign('set', $data);
			$this->display();	
		}
	}
	
	/**
	 *属性的删除 
	 */
	public function normsDel() {
		if($this->_get('token') != session('token')){$this->error('非法操作');}
        $id = intval($this->_get('id'));
        $catid = intval($this->_get('catid'));
        $type = intval($this->_get('type'));
        if(IS_GET){                              
            $where = array('id' => $id, 'type' => $type, 'catid' => $catid);
            $data = M('Norms');
            $check = $data->where($where)->find();
            if($check == false) $this->error('非法操作');
            if ($back = $data->where($wehre)->delete()) {
            	$this->success('操作成功',U('Store/norms', array('type' => $type, 'catid' => $check['catid'])));
            } else {
				$this->error('服务器繁忙,请稍后再试',U('Store/norms', array('type' => $type, 'catid' => $check['catid'])));
            }
        }        
	}
	
	/**
	 * 分类属性列表
	 */
	public function attributes() {
		$catid = intval($_GET['catid']);
		if($checkdata = M('Product_cat')->where(array('id' => $catid, 'token' => session('token')))->find()){
			$this->assign('catData', $checkdata);
        } else {
        	$this->error("没有选择相应的分类.",U('Store/index'));
        }
		$data = M('Attribute');
		$where = array('catid' => $catid, 'token' => session('token'));
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page', $show);		
		$this->assign('list', $list);
		$this->assign('catid', $catid);
		$this->display();		
	}
	
	/**
	 * 分类属性的操作
	 */
	public function attributeAdd() {
		if($checkdata = M('Product_cat')->where(array('id' => $this->_get('catid'), 'token' => session('token')))->find()){
			$this->assign('catData', $checkdata);
        } else {
        	$this->error("没有选择相应的分类.", U('Store/index'));
        }
		if (IS_POST) { 
            $data = D('Attribute');
            $id = intval($this->_post('id'));
            $catid = intval($this->_post('catid'));
            if ($id) {
	            $where = array('id' => $id, 'token' => session('token'), 'catid' => $catid);
				$check = $data->where($where)->find();
				if ($check == false) $this->error('非法操作');
            }
			if ($data->create()) {
				if ($id) {
					if ($data->where($where)->save($_POST)) {
						$this->success('修改成功', U('Store/attributes',array('token' => session('token'), 'catid' => $this->_post('catid'))));
					} else {
						$this->error('操作失败');
					}
				} else {
					if ($data->add($_POST)) {
						$this->success('添加成功', U('Store/attributes',array('token' => session('token'), 'catid' => $this->_post('catid'))));
					} else {
						$this->error('操作失败');
					}
				}
			} else {
				$this->error($data->getError());
			}
		} else { 
			$data = M('Attribute')->where(array('id' => $this->_get('id'), 'token' => session('token'), 'catid' => $this->_get('catid')))->find();
			$this->assign('catid', $this->_get('catid'));
			$this->assign('token', session('token'));
			$this->assign('set', $data);
			$this->display();	
		}
	}
	
	/**
	 *属性的删除 
	 */
	public function attributeDel() {
		if($this->_get('token') != session('token')){$this->error('非法操作');}
        $id = intval($this->_get('id'));
        $catid = intval($this->_get('catid'));
        if(IS_GET){                              
            $where = array('id' => $id, 'token' => session('token'), 'catid' => $catid);
            $data = M('Attribute');
            $check = $data->where($where)->find();
            if($check == false) $this->error('非法操作');
            if ($back = $data->where($wehre)->delete()) {
            	$this->success('操作成功',U('Store/attributes', array('token' => session('token'), 'catid' => $catid)));
            } else {
				$this->error('服务器繁忙,请稍后再试',U('Store/attributes', array('token' => session('token'), 'catid' => $catid)));
            }
        }        
	}
	
	/**
	 * 商品列表
	 */
	public function product() {		
		$catid = intval($_GET['catid']);
		$product_model = M('Product');
		$product_cat_model = M('Product_cat');
		$where = array('token' => session('token'), 'groupon' => 0);
		if ($catid){
			$where['catid'] = $catid;
		}
        if(IS_POST){
            $key = $this->_post('searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }

            $map['token'] = $this->get('token'); 
            $map['name|intro|keyword'] = array('like',"%$key%"); 
            $list = $product_model->where($map)->select(); 
            $count      = $product_model->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        } else {
        	$count      = $product_model->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $product_model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }
		$this->assign('page',$show);		
		$this->assign('list',$list);
		$this->assign('isProductPage',1);
		$this->assign('catid', $catid);
		$this->display();		
	}
	
	/**
	 * 添加商品
	 */
	public function addNew() {
		$catid = intval($_GET['catid']);
		$id = intval($_GET['id']);
		if($productCatData = M('Product_cat')->where(array('id' => $catid, 'token' => session('token')))->find()){
			$this->assign('catData', $productCatData);
        } else {
        	$this->error("没有选择相应的分类.", U('Store/index'));
        }
        //产品的规格
        $normsData = M("Norms")->where(array('catid' => $catid))->select();
        $colorData = $formatData = array();
        foreach ($normsData as $row) {
        	if ($row['type']) {
        		$colorData[] = $row;
        	} else {
        		$formatData[] = $row;
        	}
        	$normsList[$row['id']] = $row['value'];
        }
        if ($id && ($product = M('Product')->where(array('catid' => $catid, 'token' => session('token'), 'id' => $id))->find())) {
        	$attributeData = M("Product_attribute")->where(array('pid' => $id))->select();
        	$productDetailData = M("Product_detail")->where(array('pid' => $id))->select();
        	$productimage = M("Product_image")->where(array('pid' => $id))->select();
        	$colorList = $formatList = $pData = array();
        	foreach ($productDetailData as $p) {
        		$p['formatName'] = $normsList[$p['format']];
        		$p['colorName'] = $normsList[$p['color']];
        		$formatList[] = $p['format'];
        		$colorList[] = $p['color'];
        		$pData[] = $p;
        	}
        	$this->assign('set', $product);
        	$this->assign('formatList', $formatList);
        	$this->assign('colorList', $colorList);
        	$this->assign('imageList', $productimage);
        	//print_r($productimage);die;
        } else {
	        //分类产品的属性
	        $data = M("Attribute")->where(array('catid' => $catid))->select();
	        $attributeData = array();
	        foreach ($data as $row) {
	        	$row['aid'] = $row['id'];
	        	$row['id'] = 0;
	        	$attributeData[] = $row;
	        }
        }
		$this->assign('color', $this->color);
		$this->assign('attributeData', $attributeData);
		$this->assign('normsData', $normsData);
		$this->assign('colorData', $colorData);
		$this->assign('formatData', $formatData);
		$this->assign('productCatData', $productCatData);
		$this->assign('productDetailData', $pData);
		$this->assign('catid', $catid);
		$this->display('set_new');
	}
	
	/**
	 * 增加商品
	 */
	public function productSave() {
		$token = isset($_POST['token']) ? htmlspecialchars($_POST['token']) : '';
		$catid = isset($_POST['catid']) ? intval($_POST['catid']) : 0;
		$num = isset($_POST['num']) ? intval($_POST['num']) : 0;
		$pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
		$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
		$keyword = isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : '';
		$pic = isset($_POST['pic']) ? htmlspecialchars($_POST['pic']) : '';
		$price = isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '';
		$vprice = isset($_POST['vprice']) ? htmlspecialchars($_POST['vprice']) : '';
		$oprice = isset($_POST['oprice']) ? htmlspecialchars($_POST['oprice']) : '';
		$mailprice = isset($_POST['mailprice']) ? htmlspecialchars($_POST['mailprice']) : '';
		$intro = isset($_POST['intro']) ? $_POST['intro'] : '';
		$attribute = isset($_POST['attribute']) ? htmlspecialchars_decode($_POST['attribute']) : '';
		$norms = isset($_POST['norms']) ? htmlspecialchars_decode($_POST['norms']) : '';
		$images = isset($_POST['images']) ? htmlspecialchars_decode($_POST['images']) : '';
		$sort = isset($_POST['sort']) ? intval($_POST['sort']) : 100;
		if ($token != session('token')) {
			exit(json_encode(array('error_code' => true, 'msg' => '不合法的数据')));
		}
		if (empty($name)) {
			exit(json_encode(array('error_code' => true, 'msg' => '商品不能为空')));
		}
		if (empty($catid)) {
			exit(json_encode(array('error_code' => true, 'msg' => '商品分类不能为空')));
		}
		$data = array('token' => $token, 'num' => $num, 'sort' => $sort, 'catid' => $catid, 'name' => $name, 'price' => $price, 'mailprice' => $mailprice, 'vprice' => $vprice, 'oprice' => $oprice, 'intro' => $intro, 'logourl' => $pic, 'keyword' => $keyword, 'time' => time());
		$product = M('Product');
		if ($pid && $obj = $product->where(array('id' => $pid, 'token' => $token))->find()) {
			$product->where(array('id' => $pid, 'token' => $token))->save($data);
		} else {
			$pid = $product->add($data);
		}
		if (empty($pid)) {
			exit(json_encode(array('error_code' => false, 'msg' => '商品添加出错了')));
		}
		
		if (!empty($attribute)) {
			$product_attribute = M('Product_attribute');
			$attribute = json_decode($attribute, true);
			foreach ($attribute as $row) {
				$data_a = array('pid' => $pid, 'aid' => $row['aid'], 'name' => $row['name'], 'value' => $row['value']);
				if ($row['id']) {
					$product_attribute->where(array('id' => $row['id'], 'pid' => $pid))->save($data_a);
				} else {
					$product_attribute->add($data_a);
				}
			}
		}
		
		if (!empty($norms)) {
			$product_detail = M('Product_detail');
			$norms = json_decode($norms, true);
			$detailList = $product_detail->field('id')->where(array('pid' => $pid))->select();
			$oldDetailId = array();
			foreach ($detailList as $val) {
				$oldDetailId[$val['id']] = $val['id'];
			}
			foreach ($norms as $row) {
				$data_d = array('pid' => $pid, 'format' => $row['format'], 'color' => $row['color'], 'num' => $row['num'], 'price' => $row['price'], 'vprice' => $row['vprice']);
				if ($row['id']) {
					unset($oldDetailId[$row['id']]);
					$product_detail->where(array('id' => $row['id'], 'pid' => $pid))->save($data_d);
				} else {
					$product_detail->add($data_d);
				}
			}
			//删除上次剩余的库存
			foreach ($oldDetailId as $id) {
				$product_detail->where(array('id' => $id, 'pid' => $pid))->delete();
			}
		}
		if (!empty($images)) {
			$product_image = M('Product_image');
			$images = json_decode($images, true);
			$iamgelist = $product_image->field('id')->where(array('pid' => $pid))->select();
			$oldImageId = array();
			foreach ($iamgelist as $val) {
				$oldImageId[$val['id']] = $val['id'];
			}
			foreach ($images as $row) {
				if (empty($row['image'])) continue;
				$data_d = array('pid' => $pid, 'image' => $row['image']);
				if ($row['id']) {
					unset($oldImageId[$row['id']]);
					$product_image->where(array('id' => $row['id'], 'pid' => $pid))->save($data_d);
				} else {
					$product_image->add($data_d);
				}
			}
			//删除上次剩余的库存
			foreach ($oldImageId as $id) {
				$product_image->where(array('id' => $id, 'pid' => $pid))->delete();
			}
		}
		exit(json_encode(array('error_code' => false, 'msg' => '商品操作成功')));
	}
	
	/**
	 * 删除商品
	 */
	public function del(){
		$product_model=M('Product');
		if($this->_get('token')!=session('token')){$this->error('非法操作');}
        $id = $this->_get('id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false)   $this->error('非法操作');

            $back=$product_model->where($wehre)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>session('token'),'pid'=>$id,'module'=>'Product'))->delete();
                $this->success('操作成功',U('Store/product',array('token'=>session('token'),'dining'=>$this->isDining)));
            }else{
                 $this->error('服务器繁忙,请稍后再试',U('Store/product',array('token'=>session('token'))));
            }
        }        
	}
	
	public function orders(){
		$product_cart_model=M('product_cart');
		if (IS_POST){
			if ($_POST['token']!=$this->_session('token')){
				exit();
			}
			for ($i=0;$i<40;$i++){
				if (isset($_POST['id_'.$i])){
					$thiCartInfo=$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->find();
					if ($thiCartInfo['handled']){
					$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>0));
					}else {
						$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>1));
					}
				}
			}
			$this->success('操作成功',U('Store/orders',array('token'=>session('token'),'dining'=>$this->isDining)));
		} else {
			$where = array('token'=>$this->_session('token'), 'groupon' => 0, 'dining' => 0);
			if (IS_POST) {
				$key = $this->_post('searchkey');
				if(empty($key)){
					$this->error("关键词不能为空");
				}

				$where['truename|address'] = array('like',"%$key%");
				$orders = $product_cart_model->where($where)->select();
				$count      = $product_cart_model->where($where)->limit($Page->firstRow.','.$Page->listRows)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
			}else {
				if (isset($_GET['handled'])){
					$where['handled']=intval($_GET['handled']);
				}
				$count      = $product_cart_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
				$orders=$product_cart_model->where($where)->order('time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			}


			$unHandledCount=$product_cart_model->where(array('token'=>$this->_session('token'),'handled'=>0))->count();
			$this->assign('unhandledCount',$unHandledCount);


			$this->assign('orders',$orders);

			$this->assign('page',$show);
			$this->display();
		}
	}
	
	public function orderInfo() {
		$this->product_model = M('Product');
		$this->product_cat_model = M('Product_cat');
		$product_cart_model = M('product_cart');
		$thisOrder = $product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		if (strtolower($thisOrder['token'])!=strtolower($this->_session('token'))){
			exit();
		}
		if (IS_POST){
			if (intval($_POST['sent'])){
				$_POST['handled']=1;
			}
			$product_cart_model->where(array('id'=>$thisOrder['id']))->save(array('sent'=>intval($_POST['sent']),'paid'=>intval($_POST['paid']),'logistics'=>$_POST['logistics'],'logisticsid'=>$_POST['logisticsid'],'handled'=>1));
			//TODO 发货的短信提醒
			if ($_POST['sent']) {
				$company = D('Company')->where(array('token' => $thisOrder['token'], 'isbranch' => 0))->find();
				$userInfo = D('Userinfo')->where(array('token' => $thisOrder['token'], 'wecha_id' => $thisOrder['wecha_id']))->find();
				Sms::sendSms($this->token, "您在{$company['name']}商城购买的商品，商家已经给您发货了，请您注意查收", $userInfo['tel']);
			}
			
			//
			/************************************************/
			if (intval($_POST['paid'])&&intval($thisOrder['price'])){
				$member_card_create_db=M('Member_card_create');
				$wecha_id=$thisOrder['wecha_id'];
				$userCard=$member_card_create_db->where(array('token'=>$this->token,'wecha_id'=>$wecha_id))->find();
				$member_card_set_db=M('Member_card_set');
				$thisCard=$member_card_set_db->where(array('id'=>intval($userCard['cardid'])))->find();
				$set_exchange = M('Member_card_exchange')->where(array('cardid'=>intval($thisCard['id'])))->find();
				//
				
				$arr['token']=$this->token;
				$arr['wecha_id']=$wecha_id;
				$arr['expense']=$thisOrder['price'];
				$arr['time']=time();
				$arr['cat']=99;
				$arr['staffid']=0;
				$arr['score']=intval($set_exchange['reward'])*$order['price'];
				M('Member_card_use_record')->add($arr);
				$userinfo_db=M('Userinfo');
				$thisUser = $userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->find();
				$userArr=array();
				$userArr['total_score']=$thisUser['total_score']+$arr['score'];
				$userArr['expensetotal']=$thisUser['expensetotal']+$arr['expense'];
	
				$userinfo_db->where(array('token'=>$thisCard['token'],'wecha_id'=>$arr['wecha_id']))->save($userArr);
			}
			/************************************************/
			//
			
			$this->success('修改成功',U('Store/orderInfo',array('token'=>session('token'),'id'=>$thisOrder['id'])));
		}else {
			//订餐信息
			$product_diningtable_model = M('product_diningtable');
			if ($thisOrder['tableid']) {
				$thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
				$thisOrder['tableName']=$thisTable['name'];
			}
			//
			$this->assign('thisOrder',$thisOrder);
			$carts=unserialize($thisOrder['info']);

			//
			$totalFee=0;
			$totalCount=0;
			
			$data = $this->getCat($carts);
			if (isset($data[1])) {
				foreach ($data[1] as $pid => $row) {
					$totalCount += $row['total'];
					$totalFee += $row['totalPrice'];
					$listNum[$pid] = $row['total'];
				}
			}
			$list = $data[0];
//			print_r($list);die;
			$this->assign('products', $list);
			//
			$this->assign('totalFee',$totalFee);
			//
			$this->assign('totalCount',$totalCount);
			$this->display();
		}
	}
	
	/**
	 * 计算一次购物的总的价格与数量
	 * @param array $carts
	 */
	public function getCat($carts)
	{
		if (empty($carts)) {
			return array();
		}
		//邮费
		$mailPrice = 0;
		//商品的IDS
		$pids = array_keys($carts);
		
		//商品分类IDS
		$productList = $cartIds = array();
		if (empty($pids)) {
			return array(array(), array(), array());
		}
		
		$productdata = $this->product_model->where(array('id'=> array('in', $pids)))->select();
		foreach ($productdata as $p) {
			if (!in_array($p['catid'], $cartIds)) {
				$cartIds[] = $p['catid'];
			}
			$mailPrice = max($mailPrice, $p['mailprice']);
			$productList[$p['id']] = $p;
		}
	
		//商品规格参数值
		$catlist = $norms = array();
		if ($cartIds) {
			$normsdata = M('norms')->where(array('catid' => array('in', $cartIds)))->select();
			foreach ($normsdata as $r) {
				$norms[$r['id']] = $r['value'];
			}
			//商品分类
			$catdata = M('Product_cat')-> where(array('id' => array('in', $cartIds)))->select();
			foreach ($catdata as $cat) {
				$catlist[$cat['id']] = $cat;
			}
		}
		$dids = array();
		foreach ($carts as $pid => $rowset) {
			if (is_array($rowset)) {
				$dids = array_merge($dids, array_keys($rowset));
			}
		}
		//商品的详细
		$data = array();
		if ($dids) {
			$dids = array_unique($dids);
			$detail = M('Product_detail')->where(array('id'=> array('in', $dids)))->select();
			foreach ($detail as $row) {
				$row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
				$row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
				$row['count'] = isset($carts[$row['pid']][$row['id']]['count']) ? $carts[$row['pid']][$row['id']]['count'] : 0;
				$productList[$row['pid']]['detail'][] = $row;
				$data[$row['pid']]['total'] = isset($data[$row['pid']]['total']) ? intval($data[$row['pid']]['total'] + $row['count']) : $row['count'];
				$data[$row['pid']]['totalPrice'] = isset($data[$row['pid']]['totalPrice']) ? intval($data[$row['pid']]['totalPrice'] + $row['count'] * $row['price']) : $row['count'] * $row['price'];//array('total' => $totalCount, 'totalPrice' => $totalFee);
			}
		}
		//商品的详细列表
		$list = array();
		foreach ($productList as $pid => $row) {
			if (!isset($data[$pid]['total'])) {
				$data[$pid] = array();
				$row['count'] = $data[$pid]['total'] = isset($carts[$pid]['count']) ? $carts[$pid]['count'] : (isset($carts[$pid]) && is_int($carts[$pid]) ? $carts[$pid] : 0);
				$data[$pid]['totalPrice'] = $data[$pid]['total'] * $row['price'];
			}
			$row['formatTitle'] =  isset($catlist[$row['catid']]['norms']) ? $catlist[$row['catid']]['norms'] : '';
			$row['colorTitle'] =  isset($catlist[$row['catid']]['color']) ? $catlist[$row['catid']]['color'] : '';
			$list[] = $row;
		}
		return array($list, $data, $mailPrice);
		die;
		if (empty($carts)) {
			return array();
		}
		$pdata = $data = $list = $ids = $detail_list = $products = array();
		$mailPrice = 0;
		foreach ($carts as $pid => $rowset) {
			$totalCount = $totalFee = 0;
			$tmp = $this->product_model->where(array('id'=> $pid))->find();
			if (is_array($rowset)) {
				$norms = $cntArray = $dids = array();
				foreach ($rowset as $did => $count) {
					if (!in_array($did, $dids)){
						array_push($dids, $did);
						$cntArray[$did] = $count['count'];
					}
					$totalCount += $count['count'];
				}
				$normsdata = M('norms')->where(array('catid' => $tmp['catid']))->select();
				foreach ($normsdata as $r) {
					$norms[$r['id']] = $r['value'];
				}
				if ($dids) {
					$temp = M('Product_detail')->where(array('id'=> array('in', $dids), 'pid' => $pid))->select();
					foreach ($temp as $row) {
						if (isset($rowset[$row['id']])) {
							$row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
							$row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
							$row['count'] = $cntArray[$row['id']];
							$totalFee += $cntArray[$row['id']] * $row['price'];
							$detail_list[$pid][] = $row;
						}
					}
				}
			} else {
				$totalCount += $rowset;
				$totalFee += $rowset * $tmp['price'];
				$pdata[$pid] = $rowset;
			}
			$mailPrice = max($mailPrice, $tmp['mailprice']);
			$data[$pid] = array('total' => $totalCount, 'totalPrice' => $totalFee);
		}
		
		$ids = array_keys($carts);
		if (count($ids)) {
			$tmp = $this->product_model->where(array('id'=>array('in', $ids)))->select();
			foreach ($tmp as $row) {
				if (isset($detail_list[$row['id']])) {
					if ($catData = M('Product_cat')-> where(array('id' => $row['catid']))->find()) {
						$row['formatTitle'] =  $catData['norms'];
						$row['colorTitle'] =  $catData['color'];
					}
					$row['detail'] =  $detail_list[$row['id']];
				} else {
					$row['detail'] = '';
					$row['count'] = $pdata[$row['id']];
				}
				$list[] = $row;
			}
		}
		return array($list, $data, $mailPrice);
	
		$list = $ids = $detail_list = $products = array();
		$carts = empty($carts) ? $this->_getCart() : $carts;
		$pdata = $data = array();
		$mailPrice = 0;
		foreach ($carts as $pid => $rowset) {
			$totalCount = $totalFee = 0;
			$tmp = $this->product_model->where(array('id'=> $pid))->find();
			if (is_array($rowset)) {
				$norms = $cntArray = $dids = array();
				foreach ($rowset as $did => $count) {
					if (!in_array($did, $dids)){
						array_push($dids, $did);
						$cntArray[$did] = $count['count'];
					}
					$totalCount += $count['count'];
				}
				$normsdata = M('norms')->where(array('catid' => $tmp['catid']))->select();
				foreach ($normsdata as $r) {
					$norms[$r['id']] = $r['value'];
				}
				if ($dids) {
					$temp = M('Product_detail')->where(array('id'=> array('in', $dids), 'pid' => $pid))->select();
					foreach ($temp as $row) {
						if (isset($rowset[$row['id']])) {
							$row['colorName'] = isset($norms[$row['color']]) ? $norms[$row['color']] : '';
							$row['formatName'] = isset($norms[$row['format']]) ? $norms[$row['format']] : '';
							$row['count'] = $cntArray[$row['id']];
							$totalFee += $cntArray[$row['id']] * $row['price'];
							$detail_list[$pid][] = $row;
						}
					}
				}
			} else {
				$totalCount += $rowset;
				$totalFee += $rowset * $tmp['price'];
				$pdata[$pid] = $rowset;
			}
			$mailPrice = max($mailPrice, $tmp['mailprice']);
			$data[$pid] = array('total' => $totalCount, 'totalPrice' => $totalFee);
		}
		$ids = array_keys($carts);
		if (count($ids)) {
			$tmp = $this->product_model->where(array('id'=>array('in', $ids)))->select();
			foreach ($tmp as $row) {
				if (isset($detail_list[$row['id']])) {
					if ($catData = M('Product_cat')-> where(array('id' => $row['catid']))->find()) {
						$row['formatTitle'] = $catData['norms'];
						$row['colorTitle'] = $catData['color'];
					}
					$row['detail'] =  $detail_list[$row['id']];
				} else {
					$row['detail'] = '';
					$row['count'] = $pdata[$row['id']];
				}
				$list[] = $row;
			}
		}
		return array($list, $data, $mailPrice);
	}
	
	public function deleteOrder(){
		$product_model=M('product');
		$product_cart_model=M('product_cart');
		$product_cart_list_model=M('product_cart_list');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		$id=$thisOrder['id'];
		if ($thisOrder['token']!=$this->_session('token')){
			exit();
		}
		//
		//删除订单和订单列表
		$product_cart_model->where(array('id'=>$id))->delete();
		$product_cart_list_model->where(array('cartid'=>$id))->delete();
		
		//商品销量做相应的减少
		if (empty($thisOrder['paid'])) {
			$carts = unserialize($thisOrder['info']);
			//还原库存
			foreach ($carts as $pid => $rowset) {
				$total = 0;
				if (is_array($rowset)) {
					foreach ($rowset as $did => $row) {
						M('product_detail')->where(array('id' => $did, 'pid' => $pid))->setInc('num', $row['count']);
						$total += $row['count'];
					}
				} else {
					$total = $rowset;
				}
				$product_model->where(array('id' => $pid))->setInc('num', $total);
				$product_model->where(array('id' => $pid))->setDec('salecount', $total);
			}
//			foreach ($carts as $k => $c){
//				if (is_array($c)){
//					$productid=$k;
//					$price=$c['price'];
//					$count=$c['count'];
//					$product_model->where(array('id'=>$k))->setDec('salecount',$c['count']);
//				}
//			}
		}
		$this->success('操作成功',U('Store/orders', array('token' => session('token'))));
		//$this->success('操作成功',$_SERVER['HTTP_REFERER']);
	}
	
	
	/**
	 * 商城设置
	 */
	public function setting(){
		$setting = M('Product_setting');
		$obj = $setting->where(array('token' => session('token')))->find();
		if (IS_POST) {
			if ($obj) {
				$t = $setting->where(array('token' => session('token')))->save($_POST);
				if ($t) {
					$this->success('修改成功',U('Store/index',array('token' => session('token'))));
				} else {
					$this->error('操作失败');
				}
			} else {
				$pid = $setting->add($_POST);
				if ($pid) {
					$this->success('增加成功',U('Store/index',array('token' => session('token'))));
				} else {
					$this->error('操作失败');
				}
			}
		} else {
			$this->assign('setting', $obj);
			$this->display();	
		}
	}
}
?>