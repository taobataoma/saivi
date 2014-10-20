///////////////////////////////////////////////////////////////////////////////////////
// 确认框

/*
示例
confirmBox('示例', {
	'yes':{
		'title':'',
		'class':'',
		'callback':function(){
			
		}
	},
	'no':function(){
		
	}
});
*/

//
var confirmStatus = false;
var confirmCloseHide = false;

// func
function confirmBox(msg, callback, timer) {
	if (msg == '' || confirmStatus) {
		return;
	}
	// 确认框HTML加入到页面中
	var confirmHtml = '<div class="confirm-box" id="confirm-box" style="left:-1000px;top:-1000px;">'
					+ '	<div class="til"><img id="confirm-close" src="/themes/default/images/g-confirm-close.png" />温馨提示</div>'
					+ '	<div class="msg" id="confirm-msg"></div>'
					+ '	<em></em>'
					+ '	<div class="btn">'
					+ '		<a class="no" id="confirm-no">取消</a>'
					+ '		<a class="yes" id="confirm-yes">确定</a>'
					+ '		<em></em>'
					+ '	</div>'
					+ '</div>'
					+ '<div class="confirm-shadow" id="confirm-shadow"></div>'
					+ '<div class="confirm-model" id="confirm-model"></div>';
	$('body').append(confirmHtml);
	//
	if (callback == 'tip') {
		$('#confirm-box .til').hide();
		$('#confirm-box .btn').hide();
		$('#confirm-msg').addClass('tip');
		$('#confirm-shadow').addClass('tip');
		$('#confirm-model').hide();
	}
	//
	if (confirmCloseHide) {
		$('#confirm-close').hide();
	}
	//
	$('#confirm-msg').html(msg);
	//
	var cbox = $('#confirm-box');
	var cmsg = $('#confirm-msg');
	var cyes = $('#confirm-yes');
	var cno = $('#confirm-no');
	var cclose = $('#confirm-close');
	var cshadow = $('#confirm-shadow');
	var cmodel = $('#confirm-model');
	//
	if (callback != 'tip') {
		cyes.hide();
		cno.hide();
		cclose.click(function(){
			confirmBoxClose();
		});
		if (callback == undefined) {
			cyes.show();
			cyes.one('click', function(){
				confirmBoxClose();
			});
		} else {
			if (callback.yes) {
				cyes.show();
				if (callback.yes.title) {
					cyes.html(callback.yes.title);
				}
				if (callback.yes.class) {
					cyes.addClass(callback.yes.class);
				}
				cyes.one('click', function(){
					$(this).attr('disabled', true);
					$(this).addClass("disabled");
					if (callback.yes.callback) {
						callback.yes.callback();
					} else {
						callback.yes();
					}
					confirmBoxClose();
				});
			}
			if (callback.no) {
				cno.show();
				if (callback.no.title) {
					cno.html(callback.no.title);
				}
				if (callback.no.class) {
					cno.addClass(callback.no.class);
				}
				cno.one('click', function(){
					$(this).attr('disabled', true);
					$(this).addClass("disabled");
					if (callback.no.callback) {
						callback.no.callback();
					} else {
						callback.no();
					}
					confirmBoxClose();
				});
			}
		}
	}
	//
	confirmBoxCenter();
	//
	cbox.show();
	cshadow.show();
	if (callback != 'tip') {
		cmodel.show();
	}
	//
	if (callback == 'tip') {
		setTimeout(function(){
			confirmBoxClose();
		}, timer == undefined ? 1500 : timer);
	}
	//
	confirmStatus = true;
}
function confirmBoxClose() {
	confirmStatus = false;
	$('#confirm-box').hide();
	$('#confirm-box').remove();
	$('#confirm-shadow').hide();
	$('#confirm-shadow').remove();
	$('#confirm-model').hide();
	$('#confirm-model').remove();
}
function confirmBoxCenter() {
	var cbox = $('#confirm-box');
	var cyes = $('#confirm-yes');
	var cno = $('#confirm-no');
	var cshadow = $('#confirm-shadow');
	if (cbox.get(0) == undefined) {
		return;
	}
	var cboxHeight = cbox.get(0).offsetHeight;
	var cboxWidth = cbox.get(0).offsetWidth;
	var cboxTop = $(window).height() / 2 - cboxHeight / 2;
	var cboxLeft = $(window).width() / 2 - cboxWidth / 2;
	cbox.css('top', cboxTop);
	cbox.css('left', cboxLeft);
	cshadow.css('top', cboxTop - 4);
	cshadow.css('left', cboxLeft - 4);
	cshadow.css('width', cboxWidth + 8);
	cshadow.css('height', cboxHeight + 8);
}

// 屏幕大小发生变化时，重新居中提示框
$(function(){
	$(window).resize(function(){
		confirmBoxCenter();
	});
})