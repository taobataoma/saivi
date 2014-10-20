<?php if (!defined('THINK_PATH')) exit();?><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<title>友情提示</title> 
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta charset="utf-8">
<link href="/tpl/static/tip.css" rel="stylesheet" type="text/css">
</head> 
<body>
	<div class="errorbg"><img src="/tpl/static/tip.jpg"></div>
	<div class="tu"><img src="<?php if(isset($message)): ?>/tpl/static/success.png<?php else: ?>/tpl/static/error.png<?php endif; ?>"><div class="msg" style="margin-top:15px"><?php if(isset($message)): echo($message); else: echo($error); endif; ?></div><div class="msg" style="color:#999999;font-size:12px"><span id="wait"><?php echo($waitSecond); ?></span>秒后自动跳转<a id="href" style="display:block; color:red" href="<?php echo($jumpUrl); ?>">点击手动跳转</a></div></div>
<script type="text/javascript">

(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time == 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 2000);
})();

</script>
</body></html>