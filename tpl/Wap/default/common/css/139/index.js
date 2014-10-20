/**
 * @author Administrator
 */


   function windowResize() { 
   	var  w=$(".c-left").find("img").width();
   	 var w1=$(".ktvcontent").width()-w-15;	
   	 var w3=$(".review-text").width()-136;
   	 var h1=$(window).height();
   	 $(".bg").height($(window).height());
   	 $("textarea.text").css("width",w3);
   	 $(".c-right").width(w1);
	setTimeout("windowResize()", 100);
    }

$(document).ready(function(){
		windowResize();
		
		
//全局变量，触摸开始位置
		//document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
    // 通过下面这个API隐藏底部导航栏
		    //WeixinJSBridge.call('hideToolbar');
		//});
		 var H6=$(document).height();
		 $(".song-pz").find(".p").click(function()
		 	{
		 		$(".review-text").show();
		 		$('body,html').animate({scrollTop:H6},2000);
		 	}
		 ); 
		    //查询焦点事件
		 $("textarea.text").focus(function()
		 {
		 	$(".con").hide();
		 	$(".mejs-container").hide();
		 	$(".noticelist").css("margin-top","50px");
		 	$(".review-text").css({position:"absolute",top:"0",left:"0"});
		 	$(".shop").parent("li").css("border-top","0px");
		 	
		 }).blur(function(){
		 	$(".con").show();
		 	$(".mejs-container").show();
		 	$(".review-text").css("position","static");
		 	$(".shop").parent("li").css("border-top","1px solid #EA222F");
		 	
		 });
		 
		 
	    $(".trans10").on("touchstart",function()
	    {	$(this).find("a").addClass("trans8");})
	    .on("touchend",function()
	    {	$(this).find("a").removeClass("trans8");}
	    );
	    /********************************************/
	    /*包房选项卡切换*/
	    $(".appointment-con").eq(0).show();
	    $(".appointment-tab li").click(function(){
	    	index=$(this).index();
	    	$(this).siblings().find("a").removeClass("click");
	    	$(this).find("a").addClass("click");
	    	$(".appointment-con").hide();
	    	$(".appointment-con").eq(index).show();
	    	
	    });
	    
	  //日历
		 
		var t1=$("input[name='year']").val()+"年"+$("input[name='month']").val()+"月"+$(".DayNow").text()+"日";
		$(".Day,.DaySat,.DaySun,.DayNow,.CalendarTD").on("click",function()
		    {
		    	if($(this).text()!="")
		    	{
		    	var t=$("input[name='year']").val()+"/"+$("input[name='month']").val()+"/"+$(this).text();
		    	$("td").removeClass("td_color");
				$(this).addClass("td_color");
				$("#schedueltime").val(t);
				}
				
		    });
		      
	    
	    
		 
});

