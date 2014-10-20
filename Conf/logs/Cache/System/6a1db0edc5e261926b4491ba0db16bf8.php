<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
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
<style type="text/css">
  
</style>
<script>
  KindEditor.ready(function(K){
    var editor = K.editor({
      allowFileManager:true
    });
    K('#homeurl_upload').click(function() {
      editor.loadPlugin('image', function() {
        editor.plugin.imageDialog({
          fileUrl : K('#bg_pic').val(),
          clickFn : function(url, title) {
            if(url.indexOf("http") > -1){
              K('#bg_pic').val(url);
            }else{
              K('#bg_pic').val("<?php echo C('site_url');?>"+url);
            }
            editor.hideDialog();
          }
        });
      });
    });
    

    
  });
</script>
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
            <h3>站点设置 >> 基本设置 >> 基本信息设置</h3>
          </div> <!-- /widget-header -->
          
          <div class="widget-content">
            <style type="text/css">	
	.btn-warning>a{color: #fff;}
</style>
<div class="btn-group" style="margin-top:10px;">
	<?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="btn btn-large <?php if(ACTION_NAME == $vo['name']): ?>btn-warning<?php endif; ?>" ><a href="<?php echo U($action.'/'.$vo['name'],array('pid'=>6,'level'=>3));?>"><?php echo ($vo['title']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>	
</div>
            <form id="myform" action="<?php echo U('Site/insert');?>" method="post">
            <table class="table table-striped table-bordered" style="margin-top:10px;" id="set_table">

                  <tr>
                    <td><strong>网站名称：</strong></td>
                    <td>
                      <input type="text" name="site_name" value="<?php echo C('site_name');?>"  />                
                      <span>&nbsp;&nbsp;例：百合网</span>
                    </td>
                  </tr>

                  <tr>
                    <td><strong>网站标题：</strong></td>
                    <td>
                      <input type="text" name="site_title" value="<?php echo C('site_title');?>" size="45" />                
                      <span>&nbsp;&nbsp;一般不超过80个字符</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>网站地址：</strong>
                    </td>
                    <td>
                      <input type="text" name="site_url" value="<?php echo C('site_url');?>" size="45" />                
                      <span>&nbsp;&nbsp;例:http://Saivi.cn</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>网站LOGO上传：</strong>
                    </td>
                    <td>
                      <input type="text" name="site_logo" value="<?php echo C('site_logo');?>" id="bg_pic" class="px" readonly="readonly">                
                      <span class="ke-button-common" id="homeurl_upload" >
                        <input type="button" class="btn-info" value="Upload">
                      </span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>授权TOKEN：</strong>
                    </td>
                    <td>
                      <input type="text" name="site_Token" value="<?php echo C('site_Token');?>" size="45" />                
                      <span>&nbsp;&nbsp;联系作者</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>机器人名称：</strong>
                    </td>
                    <td>
                      <input type="text" name="site_my" value="<?php echo C('site_my');?>" size="45" />                
                      <span>&nbsp;&nbsp;例:http://Saivi.cn</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>审核用户：</strong>
                    </td>
                    <td>
                      <input type="radio" name="ischeckuser" value="true" <?php if(C('ischeckuser')==='true')echo checked; ?>/>注册时无需要审核
                      <input type="radio" name="ischeckuser" value="false" <?php if(C('ischeckuser')==='false')echo checked; ?>/>注册时需要审核
                    </td>
                  </tr>
                  
                  
                  <tr> 
				    <td>
				      <strong>新用户默认组：</strong>
				    </td>
					<td>
					   <select name="reg_groupid">
					   <?php if(is_array($groups)): $i = 0; $__LIST__ = $groups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$g): $mod = ($i % 2 );++$i;?><option value="<?php echo ($g["id"]); ?>" <?php if(C('reg_groupid') == $g['id']): ?>selected<?php endif; ?>><?php echo ($g["name"]); ?>
					    </option><?php endforeach; endif; else: echo "" ;endif; ?>
					   </select>
					   <span>&nbsp;&nbsp;仅注册不需要审核的时候有效</span>
				    </td>
				  </tr>
                  
 
                  
                  <tr>
                    <td>
                      <strong>注册试用时间：</strong>
                    </td>
                    <td>
                      <input type="text" name="reg_validdays" value="<?php echo C('reg_validdays');?>" size="45" />                
                      <span>&nbsp;&nbsp;注册后多少天过期(仅注册不需要审核的时候有效)</span>
                    </td>
                  </tr>
                  
                  
               
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                   <tr>
                    <td>
                      <strong>注册需要手机号：</strong>
                    </td>
                    <td>
                      <input type="radio" name="reg_needmp" value="true"  <?php if(C('reg_needmp')==='true')echo checked; ?> />需要填写手机号
                      <input type="radio" name="reg_needmp" value="false" <?php if(C('reg_needmp')==='false')echo checked; ?>/>不需要填写手机号
                    </td>
                  </tr>
                  
                  
                  
                
                
                  <tr>
                    <td>
                      <strong>售后服务电话：</strong>
                    </td>
                    <td>
                      <input type="text" name="ipc" value="<?php echo C('ipc');?>" size="45" />                
                      <span>&nbsp;&nbsp;例：400-668-6688</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>站长QQ：</strong>
                    </td>
                    <td>
                      <input type="text" name="site_qq" value="<?php echo C('site_qq');?>" size="45" />                
                      <span>&nbsp;&nbsp;例如:QQ:8888888</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>百度地图API：</strong>
                    </td>
                    <td>
                      <input type="text" name="baidu_map_api" value="<?php echo C('baidu_map_api');?>" size="45" />                
                      <span>
                        &nbsp;&nbsp;
                        <a href="http://lbsyun.baidu.com/apiconsole/key?application=key" target="_blank">点击获取</a>
                      </span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>站长Email：</strong>
                    </td>
                    <td>
                      <input type="text" name="site_email" value="<?php echo C('site_email');?>" size="45" />                
                      <span>&nbsp;&nbsp;例如:QQ:server@Saivi.cn</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>联系地址：</strong>
                    </td>
                    <td>
                      <input type="text" name="keyword" value="<?php echo C('keyword');?>" size="45" />
                      <span>&nbsp;&nbsp;一般不超过100个字符</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>网站描述：</strong>
                    </td>
                    <td>
                      <input type="text" name="content" value="<?php echo C('content');?>" size="45" />
                      <span>&nbsp;&nbsp;一般不超过200个字符</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>简短公告：</strong>
                    </td>
                    <td>
                      <input type="text" name="counts" value="<?php echo C('counts');?>" size="200" />
                      
                      <span>&nbsp;&nbsp;</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <strong>底部版权：</strong>
                    </td>
                    <td>
                      <input type="text" name="copyright" value="<?php echo C('copyright');?>" size="45" />
                      <span>&nbsp;&nbsp;例:Saivi版权所有</span>
                    </td>
                  </tr>

                  <input type="hidden" name="files" value="info.php" />  

                  <tr>
                    <td colspan="2">
                      <div id="addkey"></div>
                      
                      <input type="submit" value="保存设置" class="btn-large btn-info span7" style="margin-left: 100px;"/>                
                      
                    </td>
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