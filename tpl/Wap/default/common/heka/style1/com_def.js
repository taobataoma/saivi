/**
author : zhupinglei
desc : n_com
**/
function com(){
	this.init();
}
com.prototype = {
	init : function(){
		var _this = this;
		_this.eff = $('body').attr('eff');
		_this.lid = parseInt(window.location.href.split('&lid=')[1]);
		_this.getDefault();
		_this.hideBottom();
		_this.music();
	},
	getDefault : function(){
		var _this = this;
		var cate = $('body').attr('cate'),
			subid = $('body').attr('subid');
		var cache = $.parseJSON(window.sessionStorage.getItem('gcardCache'));

		var hurl = window.location.href.split('&id='),
			idStr = hurl[1],
			msgId = parseInt(idStr);

		var key = decodeURIComponent(window.location.href.split('&key=')[1]).split('&')[0];
		$('.cardNum span').text(key);

		_this.active(_this.eff);
	},
	Forward : function(title,icon,link,desc,callback){
		$('body').attr('weiba-title',title);
		$('body').attr('weiba-icon',icon);
		$('body').attr('weiba-link',link);
		$('body').attr('weiba-desc',desc);
		if( typeof callback == 'function' ){
			callback();
		}
	},
	setPage : function(data){
		console.log(data);
		if( data ){
			$('.word .to').html(data.to);
			$('.word .say').html(data.msg);
			$('.word .from').html(data.from);
		}
	},
	hideBottom : function(){
		document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
			WeixinJSBridge.call('hideOptionMenu');
			WeixinJSBridge.call('hideToolbar');
		});
	},
	// gift : function(){
	// 	$.ajax({
	// 		url : '/data/gcard/info',
	// 		type : 'get',
	// 		dataType : 'json',
	// 		success : function(result){
	// 			if(result.ret == 0){
	// 				if( result.data.url ){
	// 					$('.gift a').attr('href',result.data.url);
	// 					$('.gift').show();
	// 					setInterval(function(){
	// 						$('.gift').css({'webkitTransform':'rotate(-30deg)'});
	// 						setTimeout(function(){
	// 							$('.gift').css({'webkitTransform':'rotate(30deg)'});
	// 						},200)
	// 					},400);
	// 				}
	// 			}else{
	// 				jDialog.alert(result.msg);
	// 			}
	// 		}
	// 	})
	// },
	active : function(eff){
		var _this = this;
		switch(eff){
			case 'wordAppear':
				_this.wordAppear();
				break;
			default:
				_this.def();
				break;
		}
	},
	def : function(){
		$('.word').show();
	},
	wordAppear : function(){
		$('.word').hide();
		var to = $('.word .to').text(),
			say = $('.word .say').text(),
			from = $('.word .from').text();
		$('.word .to').text('');
		$('.word .say').text('');
		$('.word .from').text('');
		$('.word').show();
		console.log(to,say,from);
		function appword(str,time,dom,callback){
			var i = 0,
				len = str.length;
			function go(){
				setTimeout(function(){
					if(i > len) return;
					dom.append(str[i]);
					i++;
					go();
				},time)
			}
			go();
			setTimeout(function(){
				callback();
			},len*time+500)
		}

		var t = 400;

		appword(to,t,$('.word .to'),function(){
			appword(say,t,$('.word .say'),function(){
				appword(from,t,$('.word .from'),function(){});
			})
		});
	},
	music : function(){
		if( $('#audio').size() ){
			$('.wrap').on('tap',function(){
				if( !$('#audio').hasClass('play') ){
					$('#audio').addClass('play');
					$('#audio')[0].play();
				}else{
					$('#audio').removeClass('play');
					$('#audio')[0].stop();
				}
			})
		}
	}
}
$(window).on('rendercomplete',function(){
	new com();
})