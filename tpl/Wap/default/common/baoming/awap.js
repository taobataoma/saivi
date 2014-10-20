// JavaScript Document
//页面加载开始
$(document).ready(function() {

var visibleW=$(window).width();

/*左侧出来的分类模块*/
$('#top_rightXJs,#top_rightXJs2').click(function(){
	$('.pr_list').css('left',visibleW+"px");
	$('.pr_list').animate({left:'0'},500);
});

$('.pr_listBtn,.pr_listBtn a').click(function(){
	$('.pr_list').animate({left:'100%'},500);
});
$('.pr_listTip').click(function(){
	//var pr_listTip2=$(this).find('.pr_listTip');
	var class2=$(this).parents('.pr_listConModn').find('.class2');
	if(class2.is(":hidden")){
		$('.pr_listTip').removeClass('pr_listTip2');		
		$(this).addClass('pr_listTip2');
	}else{
		$(this).removeClass('pr_listTip2');
		
	}
	
});

/*点击灰色背景 或 取消按钮 的操作*/
$('.J_Shade_tip_img,.reset,.vote_tip_operate div.yl,.vote_tip_operate div.fq').click(function(){
	hideBg();
	$('.J_Shade_tip').css('display','none');	
	$('.apply_box').css('display','none');	
});



$('.apply_add2 input,.apply_add2 textarea').focus(function(){
$(".footer2").css("display","none");

$(this).blur(function() {
    $(".footer2").css("display","block");
});
});


/*处理有边框的文本框的100%宽度
text_W();
function text_W(){
	var text_w=$('.apply_warp').width()-22;
	$('.apply_warp input').css('width',text_w+'px');
	$('.apply_warp textarea').css('width',text_w+'px');
}
*/
});
//页面加载结束



/*点击 分享给朋友圈 按钮的操作--> .btn_general*/
function share_to(){
	var display=$('.J_Shade_tip').css('display');
	if(display=='none'){
		showBg();
		$('.J_Shade_tip').css('display','block');
	}else{
		hideBg();
		$('.J_Shade_tip').css('display','none');	
	}
}


/*查看报名人员*/
function apply_user_info(a1){
	var c1="remid_mob1_"+a1;
	
	if($("#"+c1).siblings('.remid_mob2').is(":hidden")){//判断是否隐藏
	    $('.remid_mob').removeClass('show');	
	    $('.remid_mob2').slideUp(200,function(){});
		//alert(12);
		$("#"+c1).parents('.remid_mob').addClass('show');
		$("#"+c1).siblings('.remid_mob2').slideDown(200,function(){});
	}else{		
		$("#"+c1).siblings('.remid_mob2').slideUp(200,function(){
			$("#"+c1).parents('.remid_mob').removeClass('show');
		});
	}
}

/*点击 我要报名 按钮的操作--> .apply*/
function apply1(){
	var display=$('.apply_box1').css('display');
	if(display=='none'){
		showBg();
		$('.apply_box1').css('display','block');
	}else{
		hideBg();
		$('.apply_box1').css('display','none');	
	}
}

function apply2(){
	var display=$('.apply_box2').css('display');
	if(display=='none'){
		showBg();
		$('.apply_box2').css('display','block');
	}else{
		hideBg();
		$('.apply_box2').css('display','none');	
	}
}

/*隐藏灰色背景*/
function hideBg(){
	$('.J_Shade').removeClass('J_ShadeShow');
	$('.J_Shade').addClass('J_ShadeHide');
}

/*显示灰色背景*/
function showBg(){
	$('.J_Shade').removeClass('J_ShadeHide');
	$('.J_Shade').addClass('J_ShadeShow');
}



/*姓名检测开始*/
function isChn(str){ 

   var name = /^[\u4E00-\u9FA5]+$/; 

   if(!name.test(str)){ 
      alert("提示：请正确输入真实姓名！"); 
      return false;  
   }

} 
/*姓名检测结束*/


/*身份证检测开始*/
function checkCardId(socialNo){

	  if(socialNo == "")
	  {
	    alert("输入身份证号码不能为空!");
	    return (false);
	  }

	  if (socialNo.length != 15 && socialNo.length != 18)
	  {
	    alert("提示：请正确输入身份证！!");
	    return (false);
	  }
		
	 var area={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"}; 
	   
	   if(area[parseInt(socialNo.substr(0,2))]==null) {
	   	alert("身份证号码不正确!");
	    	return (false);
	   } 
	    	
	  if (socialNo.length == 15)
	  {
	     pattern= /^\d{15}$/;
	     if (pattern.exec(socialNo)==null){
			alert("15位身份证号码必须为数字！");
			return (false);
	    }
		var birth = parseInt("19" + socialNo.substr(6,2));
		var month = socialNo.substr(8,2);
		var day = parseInt(socialNo.substr(10,2));
		switch(month) {
			case '01':
			case '03':
			case '05':
			case '07':
			case '08':
			case '10':
			case '12':
				if(day>31) {
					alert('输入身份证号码不格式正确!');
					return false;
				}
				break;
			case '04':
			case '06':
			case '09':
			case '11':
				if(day>30) {
					alert('输入身份证号码不格式正确!');
					return false;
				}
				break;
			case '02':
				if((birth % 4 == 0 && birth % 100 != 0) || birth % 400 == 0) {
					if(day>29) {
						alert('输入身份证号码不格式正确!');
						return false;
					}
				} else {
					if(day>28) {
						alert('输入身份证号码不格式正确!');
						return false;
					}
				}
				break;
			default:
				alert('输入身份证号码不格式正确!');
				return false;
		}
		var nowYear = new Date().getYear();
		if(nowYear - parseInt(birth)<15 || nowYear - parseInt(birth)>100) {
			alert('输入身份证号码不格式正确!');
			return false;
		}
	    return (true);
	  }
	  
	  var Wi = new Array(
	            7,9,10,5,8,4,2,1,6,
	            3,7,9,10,5,8,4,2,1
	            );
	  var   lSum        = 0;
	  var   nNum        = 0;
	  var   nCheckSum   = 0;
	  
	    for (i = 0; i < 17; ++i)
	    {
	        

	        if ( socialNo.charAt(i) < '0' || socialNo.charAt(i) > '9' )
	        {
	            alert("提示：请正确输入身份证！!");
	            return (false);
	        }
	        else
	        {
	            nNum = socialNo.charAt(i) - '0';
	        }
	         lSum += nNum * Wi[i];
	    }

	  
	    if( socialNo.charAt(17) == 'X' || socialNo.charAt(17) == 'x')
	    {
	        lSum += 10*Wi[17];
	    }
	    else if ( socialNo.charAt(17) < '0' || socialNo.charAt(17) > '9' )
	    {
	        alert("提示：请正确输入身份证！!");
	        return (false);
	    }
	    else
	    {
	        lSum += ( socialNo.charAt(17) - '0' ) * Wi[17];
	    }

	    
	    
	    if ( (lSum % 11) == 1 )
	    {
	        return true;
	    }
	    else
	    {
	        alert("提示：请正确输入身份证！!");
	        return (false);
	    }
		
}
/*身份证检测结束*/


/*手机检测开始*/
function checkPhone(tel) {
	
if ( /^13\d{9}$/g.test(tel) || /^15\d{9}$/g.test(tel) || /^18\d{9}$/g.test(tel) ){
     return true;
}else{
	 alert("提示：请正确输入手机！");
     return (false);
}

}
/*手机检测结束*/


/*邮箱检测开始*/
function checkEmail(email){
    var temp = email;
    //对电子邮件的验证
    var myreg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+[\.][a-zA-Z]{2,3}$/;
    if (!myreg.test(temp)){
        alert("提示：请正确输入的E-Mail！");
        return false;
    }
}
/*邮箱检测结束*/

/*邮箱检测开始*/
function checkUser2(user){
    var reg = /^[A-Za-z0-9]+$/;   
   if (arr=user.match(reg)) {  
      //return ture;   
   }else{
      alert("提示：密码只允许输入英文、数字！");   
      return false;   
   }   
}
/*邮箱检测结束*/

/*用户名检测开始*/
function checkUser(user){
   var reg = /^(\w|[\u4E00-\u9FA5])*$/;   
   if (arr=user.match(reg)) {  
      //return ture;   
   }else{
      alert("提示：用户名只允许输入英文、数字、汉字！");   
      return false;   
   }   
}
/*用户名检测结束*/


/*保存报名数据开始*/



/*保存报名数据结束*/


/*验证发布人开始*/
function apply_user_check() {
	var aid=$("#aid").val();
	var pwd=$("#pwd").val();
	
	if (aid=="" || isNaN(aid)) {
       alert("数据异常！");
       return (false);
    }
	
	if (pwd == "" || pwd.length > 16 || pwd.length < 6) {
       alert("提示：请正确输入管理密码！");
       return (false);
    }
	
	$("#BtnOn4").css("display","none");
	$("#BtnOn5").css("display","inline");
	//$("#BtnOn6").css("display","none");
	
	$.post("apply_user_check.do", {aid:aid,pwd:pwd},function(txt){tb_remove(),$("#net_re").html(txt);});
}



function apply_user_check_re(a1) {
   if (a1=="no"){
	  $("#BtnOn4").css("display","inline");
	  $("#BtnOn5").css("display","none");
	  $("#BtnOn6").css("display","inline");
   }
   
   if (a1=="yes"){
      apply2();	
	  $("#BtnOn4").css("display","inline");
	  $("#BtnOn5").css("display","none");
	  $("#BtnOn6").css("display","inline");
   }
}
/*验证发布人结束*/


/*查看报名人页开始*/
function apply_user_del(a1,a2){
	
	var v1=$("#aid").val();;

	if (a1=="" || isNaN(a1) || v1=="" || isNaN(v1)) {
       alert("数据异常！");
       return (false);
    }
	
	var jstxt="删除用户“"+a2+"”，确认操作？"
	
	if (confirm(jstxt)){
	   var b1="remid_mob_s_"+a1;
	   var b2="remid_"+a1;
	
       $("#"+b1).slideUp(300);
	   $.post("apply_user_del.do", {uid:a1,aid:v1},function(txt){tb_remove(),$("#net_re").html(txt);});
	}   
}


function apply_user_page(){
	var k=$("#k").val();
	var aid=$("#aid").val();
	var page=$("#page").val();
	var d1="remid_more_"+page;
	
	if (page=="" || isNaN(page) || aid=="" || isNaN(aid)) {
       alert("数据异常！");
       return (false);
    }
	
	var nextpage=page*1+1;
	var b1="rem_load1_"+page;
	var b2="rem_load2_"+page;
	
	$("#"+b1).css("display","none");
	$("#"+b2).css("display","block");
	
	$.post("apply_user_page.do", {page:nextpage,aid:aid,k:k},function(txt){tb_remove(),$("#"+d1).html(txt);});
}


function apply_user_search(){
	var search_text=$("#search_text").val();
	var aid=$("#aid").val();
	
	location.href="apply-user-"+aid+"-"+search_text+".html";
}
/*查看报名人页结束*/


/*报名列表开始*/
function apply_list_page(){
	var k=$("#k").val();
	var page=$("#page").val();
	var netid=$("#netid").val();
	var d1="remid_more_"+page;
	
	if (page=="" || isNaN(page)) {
       alert("数据异常！");
       return (false);
    }
	
	var nextpage=page*1+1;
	var b1="rem_load1_"+page;
	var b2="rem_load2_"+page;
	
	$("#"+b1).css("display","none");
	$("#"+b2).css("display","block");
	
	$.post("apply_list_page.do", {page:nextpage,k:k,netid:netid},function(txt){tb_remove(),$("#"+d1).html(txt);});
}


function apply_list_search(){
	var search_text=$("#search_text").val();
	var netid=$("#netid").val();
	
	if (netid!="" && isNaN(netid)) {
       alert("数据异常！");
       return (false);
    }
	
	if (netid=="") netid="0";
	
	location.href="apply-list-"+netid+"-"+search_text+".html";
}
/*报名列表结束*/


/*发布报名页切换开始*/
function apply_add_site(a1) {

if (a1 != "1" && a1 != "2" && a1 != "3"){
    alert("数据异常！");
    return (false);	
}

if (a1=="1" || a1=="2") {
   $(".apply_add1 div").removeClass("apply_add1_on");
   $("#psite").val(a1);
}

if (a1 == "1") {
   $(".apply_add3").css("display","none");
   $(".apply_add1_1").addClass("apply_add1_on");
}

if (a1 == "2") {
   $(".apply_add3").css("display","block");
   $(".apply_add1_2").addClass("apply_add1_on");
}

if (a1 == "3") alert("VIP版正在内测中");

}
/*发布报名页切换结束*/


/*封面图片上传开始*/
function FpicUp(){
  $("#FpicUpDiv2").css("display","block");
  $("#FpicUpDiv1").css("display","none");
  
  $("#apply_up_on").trigger("click");
}


function FpicDel(){
  $("#FpicUpDiv4").css("display","block");
  $("#FpicUpDiv3").css("display","none");
  
  $.post("fpic_up_del.do", function(txt){tb_remove(),$("#net_re").html(txt);});
}
/*封面图片上传结束*/


/*发布报名开始*/
function apply_add_save() {
	var title=$("#title").val();
	var timeTxt=$("#timeTxt").val();
	var priceTxt=$("#priceTxt").val();
	var info=$("#info").val();
	var fromUser=$("#fromUser").val();
	var fromPwd=$("#fromPwd").val();
	var psite=$("#psite").val();
	
	if (psite != "1" && psite != "2") {
       alert("数据异常！");
       return (false);
    }
	
	if (title == "" || title == " " || title == "　") {
       alert("提示：请正确输入活动主题！");
       return (false);
    }
	
	if (title.length > 30) {
       alert("提示：活动主题不能超过30字符！");
       return (false);
    }
	
	if (timeTxt != "" && (timeTxt == " " || timeTxt == "　")) {
       alert("提示：请正确输入活动时间！");
       return (false);
    }
	
	if (priceTxt != "" && (priceTxt == " " || priceTxt == "　")) {
       alert("提示：请正确输入活动费用！");
       return (false);
    }
	
	if (info == "" || info == " " || info == "　") {
       alert("提示：请正确输入活动详细！");
       return (false);
    }
	
	if (info.length > 100000) {
       alert("提示：活动详细不能超过10万字符！");
       return (false);
    }
	
	if ( fromUser== "" || fromUser == " " || fromUser == "　") {
       alert("提示：请正确输入姓名！");
       return (false);
    }
	
	if (fromUser.length > 15 || fromUser.length < 2) {
       alert("提示：姓名长度限制2-15位！");
       return (false);
    }
	
	if (fromPwd== "" || fromPwd == " " || fromPwd == "　") {
       alert("提示：请正确输入密码！");
       return (false);
    }
	
	if (fromPwd.length > 16 || fromPwd.length < 6) {
       alert("提示：密码长度限制6-16位！");
       return (false);
    }
	
	if (checkUser2(fromPwd)==false) return (false);
	
	$("#apply_add_on2").css("display","block");
	$("#apply_add_on1").css("display","none");
	
	$.post("apply_add_save.do", {title:title,timeTxt:timeTxt,priceTxt:priceTxt,info:info,fromUser:fromUser,fromPwd:fromPwd,psite:psite},function(txt){tb_remove(),$("#net_re").html(txt);});
}



function apply_add_save_re(a1) {
   if (a1=="no"){
	  $("#BtnOn1").css("display","block");
	  $("#BtnOn2").css("display","none");
	  $("#BtnOn3").css("display","block");
   }
   
   if (a1=="yes"){
      apply1();	
	  $("#BtnOn1").css("display","block");
	  $("#BtnOn2").css("display","none");
	  $("#BtnOn3").css("display","block");
   }
}


function apply_add_on(){

$("#foot").css("display","none");

$("#this").blur(function() {
    $("#foot").css("display","block");
  })

}
/*发布报名结束*/



/*用户登录开始*/
function user_login() {
	var username=$("#username").val();
	var sfz=$("#sfz").val();
	var prid=$("#prid").val();
	
	if (prid=="" || isNaN(prid)) {
       alert("数据异常！");
       return (false);
    }
	
	if (username == "" || username.length > 4 || username.length < 2) {
       alert("提示：请正确输入真实姓名！");
       return (false);
    }
	
	if (isChn(username)==false) return (false);
	
	
	if (sfz == "" || sfz.length != 11 || isNaN(sfz)) {
       alert("提示：请正确输入手机！");
       return (false);
    }
	
	if (checkPhone(sfz)==false) return (false);
	
	//$("#BtnOn1").css("display","none");
	//$("#BtnOn2").css("display","inline");
	
	//$.post("go.do", {username:username,sfz:sfz,prid:prid},function(txt){tb_remove(),$("#net_re").html(txt);});
	document.form.submit();   
    return true;	
}


function user_login_re(a1) {
   if (a1=="no"){
	  $("#BtnOn1").css("display","inline");
	  $("#BtnOn2").css("display","none");
   }
}
/*用户登录结束*/


//分享开始
function shareWeb(a1) {
if (a1=="weixin"){	
   $(".WeiXinShareShow").css("display","block");
}

}


function shareWebClose() {
   $(".WeiXinShareShow").css("display","none");
}
//分享结束



/*以下引用第三方js，格式压缩，无需更改*/
(function($){$.fn.artZoom=function(){var loading='position:absolute;left:6px;top:6px;width:16px;height:16px;background:url(js/images/loading.gif) no-repeat',max='url(js/images/zoomin.cur), pointer',min='url(js/images/zoomout.cur), pointer';$(this).live('mouseover',function(){this.style.cursor=max});$(this).live('click',function(){var maxImg=$(this).attr('href');if($(this).find('.loading').length==0)$(this).append('<span class="loading" style="'+loading+'" title="Loading.."></span>');imgTool($(this),maxImg);return false});var loadImg=function(url,fn){var img=new Image();img.src=url;if(img.complete){fn.call(img)}else{img.onload=function(){fn.call(img)}}};var imgTool=function(on,maxImg){var width=0,height=0,tool=function(){on.find('.loading').remove();on.hide();if(on.next('.artZoomBox').length!=0){return on.next('.artZoomBox').show()};var maxWidth=on.parent().innerWidth()-12;if(width>maxWidth){height=maxWidth/width*height;width=maxWidth};var html='<div class="artZoomBox"><a href="'+maxImg+'" class="maxImgLink" style="cursor:'+min+'"> <img class="maxImg" width="'+width+'" height="'+height+'" src="'+maxImg+'" /></a></div>';on.after(html);var box=on.next('.artZoomBox');box.find('.maxImgLink').bind('click',function(){box.hide();box.prev().show();return false})};loadImg(maxImg,function(){width=this.width;height=this.height;tool()})}};$('a.artZoom').artZoom()})(jQuery);
/* Thickbox 3.1*/
var tb_pathToImage="/js/images/loadingAnimation.gif";$(document).ready(function(){tb_init('a.thickbox')});function tb_init(domChunk){$(domChunk).click(function(){var t=this.title||this.name||null;var a=this.href||this.alt;if (window.parent.frames[window.name] && (top.document.getElementsByTagName('frameset').length <= 0)) { self.top.tb_show(t,a); }else{ tb_show(t,a); }this.blur();return false})}function tb_show(caption,url){try{if(typeof document.body.style.maxHeight==="undefined"){$("body","html").css({height:"100%",width:"100%"});$("html").css("overflow","hidden");if(document.getElementById("TB_HideSelect")===null){$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");$("#TB_overlay").click(tb_remove)}}else{if(document.getElementById("TB_overlay")===null){$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");$("#TB_overlay").click(tb_remove)}}if(tb_detectMacXFF()){$("#TB_overlay").addClass("TB_overlayMacFFBGHack")}else{$("#TB_overlay").addClass("TB_overlayBG")}if(caption===null){caption=""}$("body").append("<div id='TB_load'><img src='"+tb_pathToImage+"' /></div>");$('#TB_load').show();var baseURL;if(url.indexOf("?")!==-1){baseURL=url.substr(0,url.indexOf("?"))}else{baseURL=url}var queryString=url.replace(/^[^\?]+\??/,'');var params=tb_parseQuery(queryString);TB_WIDTH=(params['width']*1)+30||580;TB_HEIGHT=(params['height']*1)+40||300;ajaxContentW=TB_WIDTH-30;ajaxContentH=TB_HEIGHT-45;if(url.indexOf('TB_iframe')!=-1){urlNoQuery=url.split('TB_');$("#TB_iframeContent").remove();if(params['modal']!="true"){$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='关闭'><img src='/js/images/divclose.gif'></a></div></div><iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW+29)+"px;height:"+(ajaxContentH+17)+"px;' > </iframe>")}else{$("#TB_overlay").unbind();$("#TB_window").append("<iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW+29)+"px;height:"+(ajaxContentH+17)+"px;'> </iframe>")}}else{if($("#TB_window").css("display")!="block"){if(params['modal']!="true"){$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton'><img src='/js/images/divclose.gif'></a></div></div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>")}else{$("#TB_overlay").unbind();$("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>")}}else{$("#TB_ajaxContent")[0].style.width=ajaxContentW+"px";$("#TB_ajaxContent")[0].style.height=ajaxContentH+"px";$("#TB_ajaxContent")[0].scrollTop=0;$("#TB_ajaxWindowTitle").html(caption)}}$("#TB_closeWindowButton").click(tb_remove);if(url.indexOf('TB_inline')!=-1){$("#TB_ajaxContent").append($('#'+params['inlineId']).children());$("#TB_window").unload(function(){$('#'+params['inlineId']).append($("#TB_ajaxContent").children())});tb_position();$("#TB_load").remove();$("#TB_window").css({display:"block"})}else if(url.indexOf('TB_iframe')!=-1){tb_position();if($.browser.safari){$("#TB_load").remove();$("#TB_window").css({display:"block"})}}else{$("#TB_ajaxContent").load(url+="&random="+(new Date().getTime()),function(){tb_position();$("#TB_load").remove();tb_init("#TB_ajaxContent a.thickbox");$("#TB_window").css({display:"block"})})}if(!params['modal']){document.onkeyup=function(e){if(e==null){keycode=event.keyCode}else{keycode=e.which}if(keycode==27){tb_remove()}}}}catch(e){}}function tb_showIframe(){$("#TB_load").remove();$("#TB_window").css({display:"block"})}function tb_remove(){$("#TB_imageOff").unbind("click");$("#TB_closeWindowButton").unbind("click");$("#TB_window").fadeOut("fast",function(){$('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove()});$("#TB_load").remove();if(typeof document.body.style.maxHeight=="undefined"){$("body","html").css({height:"auto",width:"auto"});$("html").css("overflow","")}document.onkeydown="";document.onkeyup="";return false}function tb_position(){$("#TB_window").css({marginLeft:'-'+parseInt((TB_WIDTH/2),10)+'px',width:TB_WIDTH+'px'});if(!(jQuery.browser.msie&&jQuery.browser.version<7)){$("#TB_window").css({marginTop:'-'+parseInt((TB_HEIGHT/2),10)+'px'})}}function tb_parseQuery(query){var Params={};if(!query){return Params}var Pairs=query.split(/[;&]/);for(var i=0;i<Pairs.length;i++){var KeyVal=Pairs[i].split('=');if(!KeyVal||KeyVal.length!=2){continue}var key=unescape(KeyVal[0]);var val=unescape(KeyVal[1]);val=val.replace(/\+/g,' ');Params[key]=val}return Params}function tb_getPageSize(){var de=document.documentElement;var w=window.innerWidth||self.innerWidth||(de&&de.clientWidth)||document.body.clientWidth;var h=window.innerHeight||self.innerHeight||(de&&de.clientHeight)||document.body.clientHeight;arrayPageSize=[w,h];return arrayPageSize}function tb_detectMacXFF(){var userAgent=navigator.userAgent.toLowerCase();if(userAgent.indexOf('mac')!=-1&&userAgent.indexOf('firefox')!=-1){return true}}