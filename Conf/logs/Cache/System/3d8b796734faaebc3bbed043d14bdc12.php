<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title><?php echo C('site_name');?>后台管理系统</title>
    <meta name="keywords" content="<?php echo ($f_siteName); ?>-Saivi后台管理系统" />
    <meta name="description" content="<?php echo ($f_siteName); ?>-Saivi后台管理系统" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />    
    
    <link href="<?php echo RES;?>/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo RES;?>/css/bootstrap-responsive.min.css" rel="stylesheet" />
    
    <link href="<?php echo RES;?>/css/font-awesome.css" rel="stylesheet" />
    
    <link href="<?php echo RES;?>/css/adminia.css" rel="stylesheet" /> 
    <link href="<?php echo RES;?>/css/adminia-responsive.css" rel="stylesheet" /> 
    
    <link href="<?php echo RES;?>/css/pages/dashboard.css" rel="stylesheet" /> 
    <link href="<?php echo RES;?>/css/pages/faq.css" rel="stylesheet" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="<?php echo RES;?>/js/html5.js"></script>
    <![endif]-->
	<script src="<?php echo RES;?>/js/jquery-1.7.2.min.js"></script>
	<script src="<?php echo STATICS;?>/kindeditor/kindeditor.js"></script>
	<script src="<?php echo STATICS;?>/kindeditor/lang/zh_CN.js"></script>
	<script src="<?php echo STATICS;?>/kindeditor/plugins/code/prettify.js"></script>
	<link rel="stylesheet" href="<?php echo STATICS;?>/kindeditor/themes/default/default.css" />
	<link rel="stylesheet" href="<?php echo STATICS;?>/kindeditor/plugins/code/prettify.css" /> 

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">
	$(function(){

		var str = $(".widget-header h3").html();
		// alert(str.indexOf("&gt;"));
		var hstr = $.trim(str.substr(0, str.indexOf("&gt;")));
		var num = '';
		if(hstr == "站点设置")
			num = '1';
		else if(hstr == '用户管理')
			num = '2';
		else if(hstr == '内容管理')
			num = '3';
		else if(hstr == '公众号管理')
			num = '4';
		else if(hstr == '功能管理')
			num = '5'
		else if(hstr == '扩展管理')
			num = '6';

		var current = '#collapse' + num;
		$(current).css('height','auto').removeClass('collapse').addClass('in');

	})
</script>
</head>

<body>
	
<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span> 				
			</a>
			
			<a class="brand" href="<?php echo U('System/System/index');?>"><?php echo C('site_name');?></a>
			
			<div class="nav-collapse">
			
				<ul class="nav pull-right">

					
					<li class="divider-vertical"></li>
					
					<li class="dropdown">
						
						<a data-toggle="dropdown" class="dropdown-toggle " href="#">
							管理 <b class="caret"></b>							
						</a>
						
						<ul class="dropdown-menu">
<!-- 							
							<li>
								<a href="./change_password.html"><i class="icon-lock"></i> 密码修改</a>
							</li> -->
							
							<li class="divider"></li>
							
							<li>
								<a href="<?php echo U('System/Adminsaivi/logout');?>"><i class="icon-off"></i> 退出系统</a>
							</li>
						</ul>
					</li>
				</ul>
				
			</div> <!-- /nav-collapse -->
			
		</div> <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->


<div id="content">
  
  <div class="container">
    
    <div class="row">
      
      <div class="span3">
				
				<ul id="main-nav" class="nav nav-tabs nav-stacked">
					
					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse1">
		                <i class="icon-home"></i>
		                站点设置
		              </a>

		              <div id="collapse1" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" href="" onclick="javascript:window.location.href = '<?php echo U('System/Site/index', array('pid'=>6, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  基本设置
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Node/index', array('pid'=>11, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  节点管理
		                </a>
		              </div>
					</li>

					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse2">
		                <i class="icon-user"></i>
		                用户管理
		              </a>

		              <div id="collapse2" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/User/index', array('pid'=>18, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  用户中心
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Group/index', array('pid'=>25, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  管理组
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/User_group/index', array('pid'=>48, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  会员组
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Users/index', array('pid'=>50, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  前台用户
		                </a>
		              </div>
					</li>

					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse3">
		                <i class="icon-file"></i>
		                内容管理
		              </a>

		              <div id="collapse3" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Article/index', array('pid'=>38, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  微信图文
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Text/index', array('pid'=>57, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  微信文本
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Custom/index', array('pid'=>60, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  自定义页面
		                </a>
		              </div>
					</li>

					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse4">
		                <i class="icon-qrcode"></i>
		                公众号管理
		              </a>

		              <div id="collapse4" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Token/index', array('pid'=>81, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  公众号管理
		                </a>
		              </div>
					</li>	

					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse5">
		                <i class="icon-th"></i>
		                功能管理
		              </a>

		              <div id="collapse5" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Function/index', array('pid'=>46, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  功能模块
		                </a>
		              </div>
					</li>	

					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse6">
		                <i class="icon-share"></i>
		                扩展管理
		              </a>

		              <div id="collapse6" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Records/index', array('pid'=>64, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  充值记录
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Case/index', array('pid'=>66, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  用户案例
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Links/index', array('pid'=>73, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  友情链接
		                </a>

		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Seo/index', array('pid'=>88, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  文章管理
		                </a>
		              </div>
					</li>

					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse7">
		                <i class="icon-th"></i>
		                代理商管理
		              </a>

		              <div id="collapse7" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/Agent/index', array('pid'=>85, 'level'=>3));?>'">
		                  <i class="icon-share-alt"></i>
		                  代理商管理
		                </a>
		              </div>
					</li>


					<li class="active accordion-group">
		              <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#collapse8">
		                <i class="icon-th"></i>
		                系统管理
		              </a>

		              <div id="collapse8" class="accordion-body collapse" style="height: 0px; ">
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/System/clear');?>'">
		                  <i class="icon-share-alt"></i>
		                  清除缓存
		                </a>
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="" href="" onclick="javascript:window.location.href = '<?php echo U('System/System/updateMysql');?>'">
		                  <i class="icon-share-alt"></i>
		                  获取更新
		                </a>
		              </div>

					</li>						

				</ul>	
			
				<br />
		
			</div> <!-- /span3 -->
        
      <div class="span9">

        <div class="widget widget-table">
                    
          <div class="widget-header">
            <i class="icon-th-list"></i>
            <h3>用户管理 >> 前台用户 >> 用户权限</h3>
          </div> <!-- /widget-header -->
          
          <div class="widget-content">

<?php if(($info["id"]) > "0"): ?><script src="./tpl/User/default/common/js/date/WdatePicker.js"></script>
			<form action="<?php echo U('Users/edit');?>" method="post" name="form" id="myform">
			<input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
		<?php else: ?>
			<form action="<?php echo U('Users/add');?>" method="post" name="form" id="myform"><?php endif; ?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" id="addn">

				 <tr>
					<th colspan="5"><?php echo ($title); ?></th>
				</tr>
				<tr>
					<td height="48" align="right"><strong>用户名称：</strong></td>
					<td colspan="4" class="lt">
						<input type="text" name="username" class="ipt" size="45" value="<?php echo ($info["username"]); ?>" <?php if(($info["username"]) == "admin"): ?>readonly="readonly"<?php endif; ?>>
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>手机号：</strong></td>
					<td colspan="4" class="lt">
						<input type="text" name="mp" class="ipt" size="45" value="<?php echo ($info["mp"]); ?>" />
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>邮箱：</strong></td>
					<td colspan="4" class="lt">
						<input type="text" name="email" class="ipt" size="45" value="<?php echo ($info["email"]); ?>" />
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>用户角色：</strong></td>
					<td colspan="4" class="lt">
						<select name="gid" style="width:136px">
							<?php if(is_array($role)): $i = 0; $__LIST__ = $role;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($vo["id"]) == $info["gid"]): ?>selected=""<?php endif; ?> ><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>到期时间：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="viptime" onClick="WdatePicker()" class="ipt" size="45" value="<?php echo (date("Y-m-d",$info["viptime"])); ?>">
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>用户状态：</strong></td>
					<td colspan="4" class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="status" id="status1" <?php if(($info["status"] == 1) OR ($info['status'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="status" id="status2" <?php if(($info["status"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
				</tr>
				<tr><td><a href="<?php echo U('changeAllStatus',array('id'=>$_GET['id']));?>" class='btn btn-success'>全部开启</a><td></tr>
				<tr><td><a href="<?php echo U('quanbuguanbi',array('id'=>$_GET['id']));?>" class='btn btn-success'>全部关闭</a><td></tr>
		<!--这里是独立功能控制 -->		
			<tr><td  style="font-size:14px; font-weight:bolder; color: #666; background: #F0F0F0; height:40px; line-height:40px; text-indent:30px;"  colspan="4">主模块权限控制(一级模块关闭后 各模块功能独立权限将不起作用)</td></tr>
				<tr>
					<td height="48" align="right"><strong>微路由模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wifi" id="wifi" <?php if(($info["wifi"] == 1) OR ($info['wifi'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wifi" id="wifi" <?php if(($info["wifi"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>


				
					<td height="48" align="right"><strong>高级模板：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="mb" id="mb" <?php if(($info["mb"] == 1) OR ($info['mb'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="mb" id="mb" <?php if(($info["mb"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
					
				</tr>
                
                
                
                <tr>
					<td height="48" align="right"><strong>微电商模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="weidianshang" id="weidianshang" <?php if(($info["weidianshang"] == 1) OR ($info['weidianshang'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="weidianshang" id="weidianshang" <?php if(($info["weidianshang"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>


				
					<td height="48" align="right"><strong>微促销模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wcx" id="wcx" <?php if(($info["wcx"] == 1) OR ($info['wcx'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wcx" id="wcx" <?php if(($info["wcx"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
					
				</tr>
                
                
                             <tr>
					<td height="48" align="right"><strong>微互动模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="whd" id="whd" <?php if(($info["whd"] == 1) OR ($info['whd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="whd" id="whd" <?php if(($info["whd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>


				
					<td height="48" align="right"><strong>微游戏模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wyx" id="wyx" <?php if(($info["wyx"] == 1) OR ($info['wyx'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wyx" id="wyx" <?php if(($info["wyx"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
					
				</tr>
                   
                
                                             <tr>
					<td height="48" align="right"><strong>微行业模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="why" id="why" <?php if(($info["why"] == 1) OR ($info['why'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="why" id="why" <?php if(($info["why"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>

					<td height="48" align="right"><strong>微应用模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="weiyuyue" id="weiyuyue" <?php if(($info["weiyuyue"] == 1) OR ($info['wyy'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="weiyuyue" id="weiyuyue" <?php if(($info["weiyuyue"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
				

					
				</tr>
                   
                
                
                
                                                           <tr>
					<td height="48" align="right"><strong>在线客服模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="kf" id="kf" <?php if(($info["kf"] == 1) OR ($info['kf'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="kf" id="kf" <?php if(($info["kf"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>


				
					<td height="48" align="right"><strong>会员管理模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="huiyuan" id="huiyuan" <?php if(($info["huiyuan"] == 1) OR ($info['huiyuan'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="huiyuan" id="huiyuan" <?php if(($info["huiyuan"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
					
				</tr>
                   
                
                
                
                
                

				<tr>
					<td height="48" align="right"><strong>独立商城功能：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="csshop" id="csshop" <?php if(($info["csshop"] == 1) OR ($info['csshop'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="csshop" id="csshop" <?php if(($info["csshop"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
		            <td height="48" align="right"><strong>微生活模块：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wsh" id="wsh" <?php if(($info["wsh"] == 1) OR ($info['wsh'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wsh" id="wsh" <?php if(($info["wsh"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>


				</tr>

 <!--这里是行业权限控制 -->  
 
 
 <tr><td  style="font-size:14px; font-weight:bolder; color: #666; background: #F0F0F0; height:40px; line-height:40px; text-indent:30px;"  colspan="4">行业模块权限控制</td></tr>
 
 
 
 
             
				<tr>



					<td height="48" align="right"><strong>微餐饮：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="cy" id="cy" <?php if(($info["cy"] == 1) OR ($info['cy'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="cy" id="cy" <?php if(($info["cy"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微租借：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="zujie" id="zujie" <?php if(($info["zujie"] == 1) OR ($info['zujie'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zujie" id="zujie" <?php if(($info["zujie"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>微预约：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="yy" id="yy" <?php if(($info["yy"] == 1) OR ($info['yy'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="yy" id="yy" <?php if(($info["yy"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微房产：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="fc" id="fc" <?php if(($info["fc"] == 1) OR ($info['fc'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="fc" id="fc" <?php if(($info["fc"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>微酒店：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="jd" id="jd" <?php if(($info["jd"] == 1) OR ($info['jd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="jd" id="jd" <?php if(($info["jd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微医疗：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="yl" id="yl" <?php if(($info["yl"] == 1) OR ($info['yl'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="yl" id="yl" <?php if(($info["yl"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>微汽车：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="qc" id="qc" <?php if(($info["qc"] == 1) OR ($info['qc'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="qc" id="qc" <?php if(($info["qc"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微装修：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="zhuangxiu" id="zhuangxiu" <?php if(($info["zhuangxiu"] == 1) OR ($info['zhuangxiu'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zhuangxiu" id="zhuangxiu" <?php if(($info["zhuangxiu"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>微美容：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="mr" id="mr" <?php if(($info["mr"] == 1) OR ($info['mr'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="mr" id="mr" <?php if(($info["mr"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微酒吧：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="jb" id="jb" <?php if(($info["jb"] == 1) OR ($info['jb'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="jb" id="jb" <?php if(($info["jb"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>微教育：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="jy" id="jy" <?php if(($info["jy"] == 1) OR ($info['jy'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="jy" id="jy" <?php if(($info["jy"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微花店：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="hd" id="hd" <?php if(($info["hd"] == 1) OR ($info['hd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="hd" id="hd" <?php if(($info["hd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>微政务：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="zw" id="zw" <?php if(($info["zw"] == 1) OR ($info['zw'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zw" id="zw" <?php if(($info["zw"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微健身：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="js" id="js" <?php if(($info["js"] == 1) OR ($info['js'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="js" id="js" <?php if(($info["js"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>微旅游：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="ly" id="ly" <?php if(($info["ly"] == 1) OR ($info['ly'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="ly" id="ly" <?php if(($info["ly"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微食品：</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="sp" id="sp" <?php if(($info["sp"] == 1) OR ($info['sp'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="sp" id="sp" <?php if(($info["sp"]) == "0"): ?>checked=""<?php endif; ?> >
							

						关闭</td>
				</tr>




			<tr>
					<td height="48" align="right"><strong>微物业：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wuye" id="wuye" <?php if(($info["wuye"] == 1) OR ($info['wuye'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wuye" id="wuye" <?php if(($info["wuye"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    <td height="48" align="right"><strong>微KTV</strong></td>
					<td class="lt"><input type="radio" class="radio" class="ipt" value="1" name="ktv" id="ktv" <?php if(($info["ktv"] == 1) OR ($info['ktv'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="ktv" name="ktv" id="kvt" <?php if(($info["ktv"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭</td>
				</tr> 

<tr>
					<td height="48" align="right"><strong>微婚庆：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="hunqing" id="wuye" <?php if(($info["hunqing"] == 1) OR ($info['hunqing'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="hunqing" id="wuye" <?php if(($info["hunqing"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    					<td height="48" align="right"><strong>房产中介：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="fczj" id="fczj" <?php if(($info["fczj"] == 1) OR ($info['fczj'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="fczj" id="fczj" <?php if(($info["fczj"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>

				</tr> 
<!-- 
互动模块独立功能控制开始 -->
 <tr><td  style="font-size:14px; font-weight:bolder; color: #666; background: #F0F0F0; height:40px; line-height:40px; text-indent:30px;"  colspan="4">微促销模块独立功能控制</td></tr>


<tr>
					<td height="48" align="right"><strong>刮刮卡：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="ggk" id="wuye" <?php if(($info["ggk"] == 1) OR ($info['ggk'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="ggk" id="wuye" <?php if(($info["ggk"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>大转盘：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="dzp" id="wuye" <?php if(($info["dzp"] == 1) OR ($info['dzp'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="dzp" id="wuye" <?php if(($info["dzp"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
				</tr> 
<tr>
					<td height="48" align="right"><strong>水果机：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="sgj" id="sgj" <?php if(($info["sgj"] == 1) OR ($info['sgj'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="sgj" id="sgj" <?php if(($info["sgj"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>优惠券：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="yhq" id="wuye" <?php if(($info["yhq"] == 1) OR ($info['yhq'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="yhq" id="wuye" <?php if(($info["yhq"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 		

<tr>

					<td height="48" align="right"><strong>砸金蛋：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="zjd" id="zjd" <?php if(($info["zjd"] == 1) OR ($info['zjd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zjd" id="zjd" <?php if(($info["zjd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 	


<!-- 这里是微互动模块控制开始	 -->
<tr><td  style="font-size:14px; font-weight:bolder; color: #666; background: #F0F0F0; height:40px; line-height:40px; text-indent:30px;"  colspan="4">互动模块独立功能控制</td></tr>


<tr>
<tr>

					<td height="48" align="right"><strong>微信签到：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wxqd" id="wxqd" <?php if(($info["wxqd"] == 1) OR ($info['wxqd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wxqd" id="wxqd" <?php if(($info["wxqd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>祝福贺卡：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="zfhk" id="zfhk" <?php if(($info["zfhk"] == 1) OR ($info['zfhk'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zfhk" id="zfhk" <?php if(($info["zfhk"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 	
<tr>

					<td height="48" align="right"><strong>摇一摇：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="yiy" id="yiy" <?php if(($info["yiy"] == 1) OR ($info['yiy'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="yiy" id="yiy" <?php if(($info["yiy"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>照片墙：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="zpq" id="zpq" <?php if(($info["zpq"] == 1) OR ($info['zpq'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zpq" id="zpq" <?php if(($info["zpq"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
<tr>

					<td height="48" align="right"><strong>微信墙：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wxq" id="wxq" <?php if(($info["wxq"] == 1) OR ($info['wxq'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wxq" id="wxq" <?php if(($info["wxq"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>微名片：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wmp" id="wmp" <?php if(($info["wmp"] == 1) OR ($info['wmp'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wmp" id="wmp" <?php if(($info["wmp"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
<tr>

					<td height="48" align="right"><strong>主题活动报名：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="zthdbm" id="zthdbm" <?php if(($info["zthdbm"] == 1) OR ($info['zthdbm'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zthdbm" id="zthdbm" <?php if(($info["zthdbm"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>一站到底：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="yzdd" id="yzdd" <?php if(($info["yzdd"] == 1) OR ($info['yzdd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="yzdd" id="yzdd" <?php if(($info["yzdd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
<!-- 这里是微应用模块控制开始	 -->
<tr><td  style="font-size:14px; font-weight:bolder; color: #666; background: #F0F0F0; height:40px; line-height:40px; text-indent:30px;"  colspan="4">微应用模块独立功能控制</td></tr>

<tr>

					<td height="48" align="right"><strong>微调研：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wdy" id="wdy" <?php if(($info["wdy"] == 1) OR ($info['wdy'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wdy" id="wdy" <?php if(($info["wdy"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>微相册：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wxc" id="wxc" <?php if(($info["wxc"] == 1) OR ($info['wxc'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wxc" id="wxc" <?php if(($info["wxc"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
<tr>

					<td height="48" align="right"><strong>微投票：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wtp" id="wtp" <?php if(($info["wtp"] == 1) OR ($info['wtp'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wtp" id="wtp" <?php if(($info["wtp"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>360全景：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="qj" id="qj" <?php if(($info["qj"] == 1) OR ($info['qj'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="qj" id="qj" <?php if(($info["qj"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr>  
<tr>

					<td height="48" align="right"><strong>万能表单：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wnbd" id="wnbd" <?php if(($info["wnbd"] == 1) OR ($info['wnbd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wnbd" id="wnbd" <?php if(($info["wnbd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>微场景：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="zxyd" id="zxyd" <?php if(($info["zxyd"] == 1) OR ($info['zxyd'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zxyd" id="zxyd" <?php if(($info["zxyd"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
<tr>

					<td height="48" align="right"><strong>微论坛：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wlt" id="wlt" <?php if(($info["wlt"] == 1) OR ($info['wlt'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wlt" id="wlt" <?php if(($info["wlt"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>微邀请：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wyq" id="wyq" <?php if(($info["wyq"] == 1) OR ($info['wyq'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wyq" id="wyq" <?php if(($info["wyq"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
<tr>

					<td height="48" align="right"><strong>微商盟：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wsm" id="wsm" <?php if(($info["wsm"] == 1) OR ($info['wsm'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wsm" id="wsm" <?php if(($info["wsm"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
					<td height="48" align="right"><strong>微喜帖：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wxt" id="wxt" <?php if(($info["wxt"] == 1) OR ($info['wxt'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wxt" id="wxt" <?php if(($info["wxt"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
<tr>

					<td height="48" align="right"><strong>微预约：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="wyy" id="wyy" <?php if(($info["wyy"] == 1) OR ($info['wyy'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="wyy" id="wyy" <?php if(($info["wyy"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
										<td height="48" align="right"><strong>微招聘：</strong></td>
					<td class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="zhaopin" id="zhaopin" <?php if(($info["zhaopin"] == 1) OR ($info['zhaopin'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="zhaopin" id="zhaopin" <?php if(($info["zhaopin"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
                    
</tr> 
	<tr>
		<td colspan="5" style=" border-top:1px solid #EBEBEB; padding-top:10px;">
        <div style="padding-left:360px;">
			<?php if(($info["id"]) > "0"): ?><input class="bginput" style="background: #0058B0; color:#fff; padding:5px 20px;" type="submit" name="dosubmit" value="修 改" >
				<?php else: ?>
				<input class="bginput" type="submit" name="dosubmit" value="添 加"><?php endif; ?>
			&nbsp;
			<input style="background: #0058B0; color:#fff; padding:5px 20px;"  class="bginput" type="button" onclick="javascript:history.back(-1);" value="返 回" ></div></td>
	</tr>
</table>
</form>                     
          
          </div> <!-- /widget-content -->
          
        </div> <!-- /widget -->;
      
      </div> <!-- /span9 -->
      
      
    </div> <!-- /row -->
    
  </div> <!-- /container -->
  
</div> <!-- /content -->

<div class="navbar navbar-fixed-bottom">
	<div class="navbar-inner" style="text-align: right;color:#fff;">
		Saivi 版权所有 2014
	</div>
</div>


    

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="<?php echo RES;?>/js/excanvas.min.js"></script>
<script src="<?php echo RES;?>/js/jquery.flot.js"></script>
<script src="<?php echo RES;?>/js/jquery.flot.pie.js"></script>
<script src="<?php echo RES;?>/js/jquery.flot.orderBars.js"></script>
<script src="<?php echo RES;?>/js/jquery.flot.resize.js"></script>
<script src="<?php echo STATICS;?>/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="<?php echo STATICS;?>/artDialog/plugins/iframeTools.js"></script>


<script src="<?php echo RES;?>/js/bootstrap.js"></script>
<script src="<?php echo RES;?>/js/charts/bar.js"></script>
  </body>
</html>