<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>商城后台</title><link href="__STATIC__/css/admin/pancl01.css" type="text/css" rel="stylesheet" /><link href="__STATIC__/css/admin/pancl02.css" type="text/css" rel="stylesheet" /><link href="__STATIC__/weixin/colorbox.css" type="text/css" rel="stylesheet" /><script type="text/javascript" src="__STATIC__/js/jquery-1.8.3.min.js"></script><script type="text/javascript" src="__STATIC__/js/all.js"></script><script type="text/javascript" src="__STATIC__/weixin/js/colorbox.js"></script><script>			$(document).ready(function(){
				//Examples of how to assign the Colorbox event to elements
				$(".group1").colorbox({rel:'group1'});
				
				
			});
	
</script></head><body><div id="mainer"><div class="mainbox"><div class="mleft"><div class="mbar"><h4>提醒: 商城 团购 和秒杀的触发关键词分别为：微商城 微团购 微秒杀</h4><div class="mtishi"><p><strong>您需要立即处理:</strong></p><p><span>订单提醒:</span><a  href="<?php echo U('item_order/index',array('status'=>1,'menuid'=>296));?>">待付款订单 [<span class="count"><?php echo ($count["fukuan"]); ?></span>]</a><a href="<?php echo U('item_order/index',array('status'=>2,'menuid'=>296));?>">待发货订单 [<span class="count"><?php echo ($count["fahuo"]); ?></span>]</a><a href="<?php echo U('item_order/index',array('status'=>3,'menuid'=>296));?>">已发货订单 [<span class="count"><?php echo ($count["yfahuo"]); ?></span>]</a><!-- <a href="<?php echo U('item_order/index',array('status'=>1,'menuid'=>296));?>">退款中订单 [<span class="count">0</span>]</a>--></p><p><span>商品提醒:</span><a href="<?php echo U('item/index',array('status'=>0));?>">待上架商品 [<span class="count"><?php echo ($count["nobuycount"]); ?></span>]</a></p></div><p><span>商品管理:</span><a href="<?php echo U('item/index',array('status'=>1,));?>">出售中商品 [<span class="count"><?php echo ($count["buycount"]); ?></span>]</a></p></div></div><div class="mright"><div class="mbar"><h4>服务热线</h4><p style="padding:5px 0 2px 20px;"><a href="http://wpa.qq.com/msgrd?v=3&uin=123456&site=qq&menu=yes" target="_blank"><img border="0" title="点击这里给我发消息" alt="点击这里给我发消息" src="__STATIC__/images/admin/pancl/online2.jpg"></a></p><p><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=123456&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:123456:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>                sc/app/tpl/admin/index/panel.html修改
               </p><p><span class="tel">服务热线: 010-123456</span></p><p class="remark">（热线服务时间：周一至周五 8：30 - 17：30，其他时间段请选择QQ留言，谢谢合作！）</p></div></div></div></div><div id="footer"><div class="f-box"></div></div></body></html><script type="text/javascript">        (function(){
            var $=function(id){return "string" == typeof id ? document.getElementById(id) : id;};
            var start, end, obj, data;
            obj = $("txtShopLink");
            data = obj.value;
            end = data.length;
            $("btn-copy").onclick = function(){
                if(-[1,]){             //处理费IE浏览器
                    alert("您使用的浏览器不支持此复制功能，请使用Ctrl+C或鼠标右键。");
                    obj.setSelectionRange(0,end);
                    obj.focus();
                }else{
                    var flag = window.clipboardData.setData("text",data);
                    if(flag == true){
                        alert("复制成功。现在您可以粘贴（Ctrl+v）到。");
                    }else{
                        alert("复制失败。");
                    }
                    var range = obj.createTextRange();
                    range.moveEnd("character",end);
                    range.moveStart("character",0);
                    range.select();
                }

            }
        })()
    </script>