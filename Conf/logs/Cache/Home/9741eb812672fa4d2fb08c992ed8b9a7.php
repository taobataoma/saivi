<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo C('site_title');?></title>
        <meta name="Keywords" content="<?php echo C('site_name');?>" />
        <link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/home/summer/css/reset.css" />
        <link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/home/summer/css/style.css" />
        <script src="<?php echo STATICS;?>/home/summer/js/jquery-1.8.3.min.js"></script>
        <script src="<?php echo STATICS;?>/home/summer/js/common.js"></script>
        <script src="<?php echo STATICS;?>/home/summer/js/jquery.slider.js"></script>
        <script src="<?php echo STATICS;?>/home/summer/js/jquery-runbanner.js"></script>
        <script src="<?php echo STATICS;?>/home/summer/js/turn4.1.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/index.css">
        <!--        防止恶意点击 -->
        <script type="text/javascript">
            var _tsa = _tsa || [];
            _tsa.id = 9434;
            _tsa.protocol = document.location.protocol == "https:" ? "https://" : "http://";
            (function() {
                var obj = document.createElement('script');
                obj.src = _tsa.protocol + 's.topsem.com/safe.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(obj, s);
            })();
        </script>
        <script>
$(document).ready(function(){
    var b = (function(){
        var ua= navigator.userAgent, 
            N= navigator.appName, tem, 
            M= ua.match(/(opera|chrome|safari|firefox|msie|trident)\/?\s*([\d\.]+)/i) || [];
            M= M[2]? [M[1], M[2]]:[N, navigator.appVersion, '-?'];
            if(M && (tem= ua.match(/version\/([\.\d]+)/i))!= null) M[2]= tem[1];
            // return M.join(' ');
            return M;
    })();
    if ((b[0]=='msie' || b[0]=='MSIE') && (parseInt(b[1])<9)){


        $('#ie9version').css('display', 'inline');
    }
});
</script>
<style type="text/css">
#ie9version{
    font-family:Microsoft Yahei;
    position:fixed;
    display: none;
    padding-top:0px;
    height:32px;
    width:100%;
    background-color:#E1E871;
    z-index:600;
    float:left;
    text-align:center;
    line-height:31px;
    font-size:15px;
}
#ie9version ul{
    
}
#ie9version a{
    padding-left:20px;
    color:#0099CC;
    background:none;
}
#ie9version li{
    display:inline;
}
#firefoxfont{
    padding-left:10px;
}
</style>

</head>


    <body>
        
        <div class="wrap_header">
    <div class="header">
        <div class="navigation">
            <div class="logo"><img src="<?php echo C('site_logo');?>" style="height:50px;">
                </img>
            </div>
            <ul><li class="current"><a href="/">首页</a></li><li><a href="<?php echo U('Index/index');?>#func">功能</a></li><li><a href="<?php echo U('Index/case');?>">案例</a></li><li><a href="<?php echo U('Index/agency');?>">代理</a></li><li><a href="<?php echo U('Index/reg');?>">入驻</a></li><li><a href="<?php echo U('Index/contact');?>">联系</a></li></ul>
            <div class="login">
                <?php if($_SESSION[uid]==false): ?><a href="<?php echo U('Index/login');?>">登录</a>
                  <a href="<?php echo U('Index/reg');?>" <?php if($curr == 'reg' ): ?>class="current"<?php endif; ?>>注册</a>
                <?php else: ?>
                  <a href="<?php echo U('System/Adminsaivi/logout');?>">注销</a><a style="color: #F00" href="<?php echo U('User/Index/index');?>">管理中心</a><?php endif; ?>
            </div>
          
        </div>
    </div>
</div>
<div class="header_login">
	<div class="header_login_main">
    	
  </div>
</div>

<div class="bg_login_main">
	<div class="login_main">
      <div class="img">
        <img src="<?php echo STATICS;?>/home/summer/images/login/img.png" width="485" height="485"/>
      </div>
      <div class="img02">
        <img src="<?php echo STATICS;?>/home/summer/images/login/img02.png" width="127" height="127"/>
      </div>
      
      <form action="<?php echo U('Users/checklogin');?>" method="post">
   	  <input name="username" type="text" class="txt_name" placeholder="平台账号" />
      <input name="password" type="password" class="txt_pwd" placeholder="密码" />
    
      	<div class="op">
        	<span><input name="" type="checkbox" value="" />记住密码</span>
            <a href="/">测试用户名：test  密码：test</a>
        </div>
      <input type="submit" class="btn_login" value="" />
      <a href="<?php echo U('Index/reg');?>" class="btn_reg">新用户注册</a>
      </form>
    </div>
</div>

<script type="text/javascript">
$(function(){
	$(".img").addClass("active");
	$(".btn_reg").animate({right:"22px"},"slow");

    //刷新验证码
    $('#J_captcha_img').click(function(){
        var timenow = new Date().getTime(),
            url = $(this).attr('data-url').replace(/js_rand/g,timenow);
        $(this).attr("src", url);
    });
    $('#J_captcha_change').click(function(){
        $('#J_captcha_img').trigger('click');
    }); 

})
</script>



         
<div class="joinLine">全国招商热线：<?php echo C('ipc');?> </div>
<div class="footer">
    <div class="footer_con">
        <div class="logo">
            <a href="/"><?php echo C('site_title');?></a>
        </div>
        <div class="link">
            <p>
                
<a href="/">返回首页</a> | 
<a href="<?php echo U('Index/reg');?>">申请入驻</a> |
<a href="<?php echo U('Index/agency');?>"> 加盟代理</a> | 
<a href="<?php echo U('Index/case');?>">成功案例</a> | 
<a href="<?php echo U('Index/contact');?>">关于我们</a>
            </p>
            <p>
                客服专线：<?php echo C('ipc');?>   QQ：<?php echo C('site_qq');?>   邮箱：<?php echo C('site_email');?>
            </p>
            <p>地址：<?php echo C('keyword');?></p>
        </div>
        <div class="code"><img src="<?php echo STATICS;?>/home/summer/images/code.jpg" width="124" height="124" /></div>
    </div>
</div>
<div class="copyright">
    Copyright © 2014 All Rights Reserved <?php echo C('site_name');?> 版权所有 
</div>

<script type="text/javascript" src="http://tajs.qq.com/stats?sId=16443606" charset="UTF-8"></script>

<!--右侧悬浮 begin-->

<div id="daiyanbao_com_content" style="position: fixed;_position: absolute;text-align: left;overflow: visible;bottom :0;right:0;display:block; z-index:999;">
<!--<script src="http://res.daiyanbao.com/freevideojs/301/1/18910682112.js"></script>-->
</div>


    </body>
</html>