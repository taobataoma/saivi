<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
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
            <h3>用户管理 >> 前台用户 >> 用户修改</h3>
          </div> <!-- /widget-header -->
          
          <div class="widget-content">

<?php if(($info["id"]) > "0"): ?><script src="./tpl/User/default/common/js/date/WdatePicker.js"></script>
			<form action="<?php echo U('Users/edit');?>" method="post" name="form" id="myform">
			<input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
		<?php else: ?>
			<form action="<?php echo U('Users/add');?>" method="post" name="form" id="myform"><?php endif; ?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" id="addn">

				 <tr>
					<th colspan="4"><?php echo ($title); ?></th>
				</tr>
				<tr>
					<td height="48" align="right"><strong>用户名称：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="username" class="ipt" size="45" value="<?php echo ($info["username"]); ?>" <?php if(($info["username"]) == "admin"): ?>readonly="readonly"<?php endif; ?>>
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>手机号：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="tel" class="ipt" size="45" value="<?php echo ($info["tel"]); ?>" />
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>邮箱：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="email" class="ipt" size="45" value="<?php echo ($info["email"]); ?>" />
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>密　　码：</strong></td>
					<td colspan="3" class="lt">
						<input type="password" name="password" class="ipt" size="45" value="">
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>确认密码：</strong></td>
					<td colspan="3" class="lt">
						<input type="password" name="repassword" class="ipt" size="45"/>
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>用户角色：</strong></td>
					<td colspan="3" class="lt">
						<select name="gid" style="width:136px">
							<?php if(is_array($role)): $i = 0; $__LIST__ = $role;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($vo["id"]) == $info["gid"]): ?>selected=""<?php endif; ?> ><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>用户状态：</strong></td>
					<td colspan="3" class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="status" id="status1" <?php if(($info["status"] == 1) OR ($info['status'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="status" id="status2" <?php if(($info["status"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
				</tr>
				<!-- <tr>
					<td height="48" align="right"><strong>启用微路由：</strong></td>
					<td colspan="3" class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="apenable" id="apenable" <?php if(($info["apenable"] == 1) OR ($info['apenable'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="apenable" id="apenable" <?php if(($info["apenable"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>启用高级模板：</strong></td>
					<td colspan="3" class="lt">
						<input type="radio" class="radio" class="ipt" value="1" name="mpenable" id="mpenable" <?php if(($info["mpenable"] == 1) OR ($info['mpenable'] == '') ): ?>checked=""<?php endif; ?> >
							启用
							<input type="radio" class="radio" class="ipt"  value="0" name="mpenable" id="mpenable" <?php if(($info["mpenable"]) == "0"): ?>checked=""<?php endif; ?> >
							关闭
					</td>
				</tr>
          <tr>
            <td height="48" align="right"><strong>微行业权限：</strong></td>
            <td>
            <div class="controls ">
              <label class="checkbox inline">
                <input type="checkbox" name="cy" value="1" 
                <?php if(($home["cy"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微餐饮 </label>
              <label class="checkbox inline">
                <input type="checkbox" name="wm" value="1"  
                <?php if(($home["play_audio"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微外卖 </label>
              <label class="checkbox inline">
                <input type="checkbox" name="yy" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微预约 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="fc" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微房产 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="jd" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微酒店 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="yl" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微医疗 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="qc" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微汽车 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="dy" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微电影 </label>
               </br>
               <label class="checkbox inline">
                <input type="checkbox" name="mr" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微美容 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="jb" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微酒吧 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="jy" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微教育 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="hd" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微花店 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="zw" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微政务 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="js" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微健身 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="ly" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微旅游 </label>
                <label class="checkbox inline">
                <input type="checkbox" name="sp" value="1"  
                <?php if(($home["play_wifi"]) == "on"): ?>checked="checked"<?php endif; ?>
                >
                微食品 </label>
              </div>
              </td>
          <tr> -->
				<tr>
					<td height="48" align="right"><strong>公众号已创建数：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="wechat_card_num" class="ipt" size="45" value="<?php echo ($info["wechat_card_num"]); ?>">
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>会员卡数量：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="card_num" class="ipt" size="45" value="<?php echo ($info["card_num"]); ?>">
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>活动创建数：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="activitynum" class="ipt" size="45" value="<?php echo ($info["activitynum"]); ?>">
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>资金余额：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="money" class="ipt" size="45" value="<?php echo ($info["money"]); ?>">
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>附件大小：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="attachmentsize" class="ipt" size="45" value="<?php echo $info['attachmentsize'];?>"> <?php echo $info['attachmentsize']/(1024*1024);?> M
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>到期时间：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="viptime" onClick="WdatePicker()" class="ipt" size="45" value="<?php echo (date("Y-m-d",$info["viptime"])); ?>">
					</td>
				</tr>
				<tr>
					<td height="48" align="right"><strong>备注说明：</strong></td>
					<td colspan="3" class="lt">
						<input type="text" name="remark" class="ipt" size="45" value="<?php echo ($info["remark"]); ?>"/>
					</td>
				</tr>
				
				<tr>
					<td height="48" align="right"><strong>邀请列表：</strong></td>
					<td colspan="3" class="lt">
					<a href="?g=System&m=Users&a=index&inviter=<?php echo ($info["id"]); ?>">点击查看邀请列表（共<?php echo ($inviteCount); ?>人）</a>
					</td>
				</tr>
	<tr>
		<td colspan="4">
			<?php if(($info["id"]) > "0"): ?><input class="bginput" type="submit" name="dosubmit" value="修 改" >
				<?php else: ?>
				<input class="bginput" type="submit" name="dosubmit" value="添 加"><?php endif; ?>
			&nbsp;
			<input class="bginput" type="button" onclick="javascript:history.back(-1);" value="返 回" ></td>
	</tr>
</table>
</form>
			                     
          
          </div> <!-- /widget-content -->
          
        </div> <!-- /widget -->
      
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