$(function() {

	///////////////////////////////////////////////////////////////////////////////////////
	// 截取内容
	//cutContent();
	$(window).resize(function() {
		cutContent();
	});

	///////////////////////////////////////////////////////////////////////////////////////
	// 对通用内容区的特殊控制
	$('.g-text img').each(function() {
		$(this).parent('p').addClass('img');
	});

	///////////////////////////////////////////////////////////////////////////////////////
	// 所有链接的触动效果
	$('.g-link-item').live(globalTouchStart, function(e) {
		$(this).addClass('g-link-on');
	});
	$('.g-link-item').live(globalTouchMove, function(e) {
		$(this).removeClass('g-link-on');
	});
	$('.g-link-item').live(globalTouchEnd, function(e) {
		if ($(this).hasClass('g-link-on')) {
			if ($(this).hasClass('g-link-remove')) {
				$(this).removeClass('g-link-on');
			}
			if ($(this).attr('href') && !$(e.target).hasClass('g-link-un')) {
				window.location = $(this).attr('href');
			}
		}
	});

	displayNavLocation();
});

function formatImageUrl(url, width, height) {
	var suffix = /\.[^\.]+/.exec(url).toString().toLowerCase();
	var urlfix = url.substr(0, url.length - suffix.length);
	return urlfix + '_' + width + 'x' + height + suffix;
}

function cutContent(className) {
	$('.' + (className ? className : 'cut')).each(function() {
		var tisWidth = $(this).width();
		tisWidth -= parseInt($(this).css('paddingLeft'));
		tisWidth -= parseInt($(this).css('paddingRight'));
		tisWidth -= parseInt($(this).css('marginLeft'));
		tisWidth -= parseInt($(this).css('marginRight'));
		tisWidth -= parseInt($(this).css('textIndent'));
		var tisContent = $(this).html();
		var tisContentBak = $(this).attr('bak');
		var tisContentLine = parseInt($(this).attr('line')) || 1;
		var tisFontSize = parseInt($(this).css('fontSize'));
		var tisWordTrimming = parseInt($(this).attr('word')) || 0;
		var tisWordTotal = parseInt(tisWidth / tisFontSize) * tisContentLine + tisWordTrimming;
		if (tisContentLine > 1) {
			tisWordTotal--;
		}
		if (!tisContentBak) {
			$(this).attr('bak', tisContent);
			tisContentBak = tisContent;
		}
		if (parseInt(tisContentBak.length) > tisWordTotal) {
			$(this).html(cutContentFormat(tisContentBak.substr(0, tisWordTotal)) + '…');
		} else {
			$(this).html(cutContentFormat(tisContentBak));
		}
	});
}

function cutContentFormat(string) {
	string = string.replace(new RegExp(/&/g), '&amp;');
	string = string.replace(new RegExp(/"/g), '&quot;');
	string = string.replace(new RegExp(/'/g), '&#039;');
	string = string.replace(new RegExp(/</g), '&lt;');
	string = string.replace(new RegExp(/>/g), '&gt;');
	return string;
}


function getItem(name, value) {
	var v = localStorage.getItem(name);
	return isNaN(v) && !v ? value : v;
}

function setItem(name, value) {
	localStorage.setItem(name, value);
}

//
var navStatus = true;
function displayNav() {
	if (navStatus) {
		navStatus = false;
		$('.home_nav').stop(true, false).animate({'bottom': -$('.home_nav').height()}, 100, function() {
			$('.home_nav').hide();
		});
	} else {
		navStatus = true;
		$('.home_nav').show();
		$('.home_nav').stop(true, false).animate({'bottom': 0}, 100);
	}
}
function displayNavShow() {
	$('.home_nav').show();
}
function displayNavHide() {
	$('.home_nav').hide();
}
function displayNavLocation() {
	if (parseInt($('#undisplayNav').val()) !== 1) {
	//	$('body .tj').before('<div><br /></div>');
	}
}


// 事件
var globalTouchStart = null;
var globalTouchMove = null;
var globalTouchEnd = null;
if (is_touch_device()) {
	if (is_ie_mobile()) {
		globalTouchStart = 'MSPointerDown';
		globalTouchMove = 'MSPointerMove';
		globalTouchEnd = 'MSPointerUp';
	} else {
		globalTouchStart = 'touchstart';
		globalTouchMove = 'touchmove';
		globalTouchEnd = 'touchend';
	}
} else {
	globalTouchStart = 'click';
	globalTouchMove = 'mousemove';
	globalTouchEnd = 'click';
}
//
function is_touch_device() {
	return !!('ontouchstart' in window) // works on most browsers 
			|| !!('onmsgesturechange' in window); // works on ie10 
}
function is_ie_mobile() {
	return !!(window.navigator.msPointerEnabled);
}

function showLoading() {
	if (!$('#global-loading').get(0)) {
		$('body').append('<div id="global-loading" class="global-loading"><div><img src="/themes/black/images/post-insert-loading.gif" /></div></div>');
	}
	$('#global-loading').show();
}
function hideLoading() {
	$('#global-loading').hide();
}