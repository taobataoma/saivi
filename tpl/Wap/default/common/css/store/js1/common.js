/************************
 * 描述：公共脚本
 * 作者：Gibil
 * 时间：2013-12-13
************************/

/************
* 公用方法
************/
var Common = {
    //图片加载完毕后触发callback事件
    imgLoaded: function (obj, callback, pars) {//obj-图片对象,callback-回调方法,pars-传递过来回调的参数(对象)
        //判断浏览器 
        var b = new Object();
        b.useragent = navigator.userAgent.toLocaleLowerCase();
        b.ie = /msie/.test(b.useragent);
        b.moz = /gecko/.test(b.useragent);
        if (b.ie) {
            obj.onreadystatechange = function () {
                if (obj.readystate == "complete" || obj.readystate == "loaded") {
                    if (pars)
                        callback(obj, pars);
                    else
                        callback(obj);
                }
            }
        } else if (b.moz) {
            obj.onload = function () {
                if (obj.complete == true) {
                    if (pars)
                        callback(obj, pars);
                    else
                        callback(obj);
                }
            }
        } else {
            if (pars)
                callback(obj, pars);
            else
                callback(obj);
        }
    },
    //保留N位小数
    //如：num为2,n为2(会在2后面补上00.即2.00)
    //num为数字，n为保留的位数
    toDecimal: function (num, n) {
        var f = parseInt(num * Math.pow(10, n) + 0.5) / Math.pow(10, n); //精度计算
        if (isNaN(f))
            return 0.00;
        var f = Math.round(num * n * 10) / (n * 10);
        var s = f.toString();
        var rs = s.indexOf('.');
        if (rs < 0) {
            rs = s.length;
            s += '.';
        }
        while (s.length <= rs + n) {
            s += '0';
        }
        return s;
    },
    //防止重复提交表单,注意此方法只对重复提交验证,不对表单验证(请独立验证)
    //使用示例：return Common.checkSubmit.submit(true);
    checkSubmit: {
        //提交次数统计
        submitCount: 0,
        //初始化统计量避免操作成功后无法继续操作
        initCount: function () {
            Common.checkSubmit.submitCount = 0;
        },
        //验证
        check: function () {
            if (Common.checkSubmit.submitCount > 0) {//已提交
                return false;
            }
            else {
                Common.checkSubmit.submitCount += 1;
                return true;
            }
        },
        //确认提交,表单验证在外部,此处只做重复验证
        submit: function (isSubmit) {//isSubmit为bool参数,表示表单是否已通过验证
            if (isSubmit) {//信息验证通过再验证是否重复提交
                if (!Common.checkSubmit.check())//防止重复提交表单
                    return false;
                else
                    return true;
            }
            else {
                Common.checkSubmit.submitCount = 0; //没通过必须将提交次数初始化，避免通过后无法提交
                return false;
            }
        }
    }
}

//更多点击事件
$(function(){
		//展示下拉分类菜单
	$(".productcats").click(function(){
		if($("#win").is(":hidden")){
			$("#win").show();
		}else{
			$("#win").hide();	
		}
	})
	 //更多
	 $("#more").click(function(){
		if($("#icondiv").is(":hidden"))
		{
			$("#icondiv").show();
		}
		else{
			$("#icondiv").hide();
		}
		if($("#show").is(":hidden"))
		{
			$("#show").show();
		}
		else{
			$("#show").hide();
		}
		if($("#mcolor").is(".bmore")){
			$("#mcolor").removeClass("bmore").addClass("redmore");
		}
		else{
			$("#mcolor").removeClass("redmore").addClass("bmore");
		} 
	});

	//二维码
	$("#erwei").click(function(){
		if($(".erwei").is(":hidden")){
			$(".erwei").show();
		}else{
			$(".erwei").hide();
		}	
	})
	//点击二维码隐藏
	$(".erwei").click(function(){
		$(this).hide();
	})
	//阴影层的点击事件
	$("#show").click(function(){
		$(this).css("display","none");
		$("#icondiv").hide();
	});	
	
		/*关闭分享朋友特效*/
	$("#sharemcover").click(function(){
		$(this).hide();
	});
	/*显示阴影层*/
	$(".btnshare").click(function(){
		$("#sharemcover").show();
	});
});

/************************************************************
* Cookie操作    											*
* 注意：													*
*   浏览器中Cookie有大小和个数限制							*
*   限制一般是数量50个，总大小4096字节（包括name和value）。	*
*************************************************************/
var CookieHelper = {
	//创建Cookie
	//name：Cookie名
	//value：Cookie值
	//second：有效时间，秒
	Set: function(name, value, second) {//添加cookie
		var str = name + "=" + escape(value);
		if (second != null && second > 0) {//为0时不设定过期时间，浏览器关闭时cookie自动消失
			var date = new Date();
			date.setTime(date.getTime() + second * 1000);
			str += ";expires=" + date.toGMTString();
		}
		document.cookie = str;
	},
	//获取Cookie
	//name：Cookie名
	Get: function(name) {//获取指定名称的cookie的值
		var arrStr = document.cookie.split("; ");
		for(var i = 0;i < arrStr.length;i ++) {
			var temp = arrStr[i].split("=");
			if(temp[0] == name) return unescape(temp[1]);
		}
		return "";
	},
	//删除Cookie
	//name：Cookie名
	Remove: function(name){//为了删除指定名称的cookie，可以将其过期时间设定为一个过去的时间
		var date = new Date();
		date.setTime(date.getTime() - 10000);
		document.cookie = name + "=a; expires=" + date.toGMTString();
	}
}

//隐藏微信中网页底部导航栏
;(function(){
	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
		WeixinJSBridge.call('hideToolbar');
	});
})();
