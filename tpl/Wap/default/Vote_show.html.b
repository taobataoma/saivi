<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<title>我是{Saivi:$data.item}，来给我投一票吧</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<link rel="stylesheet" type="text/css" href="{Saivi::RES}/bootstrap/css/bootstrap.min.css">
	<script src="http://libs.baidu.com/jquery/1.9.0/jquery.js"></script>
	<style type="text/css">
		body{width: 94%;margin: 0 auto;padding: 0;}
		img{width:100%;}
		#pic{margin-top: 10px;}
		.name{background: rgba(0,0,0,0.3);height: 30px;width: 94%;position: absolute;z-index: 2;overflow: hidden;color: white;text-align: center;line-height: 30px;font-size: 16px;font-family: microsoft yahei;}
		.click{padding-bottom: 20px;}
		.click button{width: 100%;}
        #cover{z-index: 1000;background: rgba(0,0,0,0.7);width: 100%;height: 100%;position: fixed;left: 0px;top: 0px;}
        #cover p{text-align:left;margin: 0 auto;font-family: microsoft yahei;color: white;font-size: 20px;margin-top: 180px;line-height: 30px;width: 90%;border: 1px dashed yellow;}
	</style>

</head>
<body>
    <div id="cover" style="display:none;">
        <p>您可能是从朋友分享过的页面打开的连接，无法直接投票<br><br>您可以点击右上角→查看公众号，关注公众号后，回复投票即可参与投票</p>

    </div>
	<div id="pic">
		<img src="{Saivi:$data.startpicurl}">
		<div class="name">{Saivi:$data.item}  编号：{Saivi:$data.id}</div>
	</div>
	<h3>我的个性宣言</h3>
	<pre>
		{Saivi:$data.description}
	</pre>
	<div class="click">
		<button class="btn btn-large btn-success" id="click" style="margin-bottom:10px;">投我一票</button>
		<a href="{Saivi::U('index',array('token'=>$_GET['token'],'wecha_id'=>$_GET['wecha_id'],'id'=>$_GET['vid']))}">
            <button class="btn btn-large btn-warning" style="margin-bottom:10px;">查看最新投票结果</button>
			
		</a>
        
        
	</div>
	<script type="text/javascript">
		$(function(){
            
			
			$('.name').css('top',10);

			$('#click').click(function(){

				var vid = '{Saivi:$_GET['vid']}';
				var token = '{Saivi:$_GET['token']}';
				var wecha_id = '{Saivi:$_GET['wecha_id']}';
				var id = '{Saivi:$_GET['id']}';
				var sgssz = '{Saivi:$_GET['sgssz']}';
				if(!wecha_id){
                    $('#cover').show();
                    return false;
                }
				
				$.ajax({
					type:'post',
					url:'{Saivi::U('vote')}',
					data:{vid:vid,id:id,token:token,wecha_id:wecha_id},
					dataType:'json',
					success:function(data){
						if(data.data == 1){
							if(confirm('你已经投过票了')){
								window.location.href="index.php?g=Wap&m=Vote&a=index&token="+token+"&wecha_id="+wecha_id+"&id="+vid+"&sgssz="+sgssz;
							}
						}

                        if(data.data == 2){
                            if(confirm('投票成功\n点击确定，立刻参加《中国好身材》大赛\n点击取消，查看最新排名')){
                                
                                window.location.href="index.php?g=Wap&m=Coupon&a=index&token="+token+"&type=3&wecha_id="+wecha_id+"&id=129&sgssz="+sgssz;
                            }else{
                                window.location.href="index.php?g=Wap&m=Vote&a=index&token="+token+"&wecha_id="+wecha_id+"&id="+vid+"&sgssz="+sgssz;
                            }
                            
                        }
					}
				})
			})
		})
	</script>

	<script type="text/javascript">

var imgUrl = "{saivi:$data.startpicurl}";
        var lineLink = "{saivi:$Think.server.http_host}{Saivi::U('show',array('token'=>$_GET['token'],'id'=>$_GET['id'],'vid'=>$_GET['vid'],'sgssz'=>$_GET['sgssz']))}";
        var descContent = "亲爱的，我在参加聚美汇成《中国\n好身材》大赛，请为我投上一票吧。";
        var shareTitle = '{Saivi:$data.item}';
        var appid = 'wxa84796a6ad7591fc';
        
         
        function shareFriend() {
            WeixinJSBridge.invoke('sendAppMessage',{
                "appid": appid,
                "img_url": imgUrl,
                "img_width": "200",
                "img_height": "200",
                "link": lineLink,
                "desc": descContent,
                "title": shareTitle
            }, function(res) {
                //_report('send_msg', res.err_msg);
            })
        }
        function shareTimeline() {
            WeixinJSBridge.invoke('shareTimeline',{
                "img_url": imgUrl,
                "img_width": "200",
                "img_height": "200",
                "link": lineLink,
                "desc": descContent,
                "title": shareTitle
            }, function(res) {
                   //_report('timeline', res.err_msg);
            });
        }
        function shareWeibo() {
            WeixinJSBridge.invoke('shareWeibo',{
                "content": descContent,
                "url": lineLink,
            }, function(res) {
                //_report('weibo', res.err_msg);
            });
        }

        
        // 当微信内置浏览器完成内部初始化后会触发WeixinJSBridgeReady事件。
        document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
            // 发送给好友
            WeixinJSBridge.on('menu:share:appmessage', function(argv){
                shareFriend();
            });
            // 分享到朋友圈
            WeixinJSBridge.on('menu:share:timeline', function(argv){
                shareTimeline();
            });
            // 分享到微博
            WeixinJSBridge.on('menu:share:weibo', function(argv){
                shareWeibo();
            });
        }, false);
    </script>
</body>
</html>