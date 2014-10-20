//初始化幻灯片
var initSlider = function(_json, sliderContainerId){
	if (_json.AdsList && _json.AdsList.length > 0) {		
		var p = new Image(); //商品图片对象
		p.src = _json.AdsList[0].Source;
		mjquery.imgLoaded(p, function () {//图片加载完毕
			$('.slider-loading').hide();//隐藏加载效果
			for (var i = 0; i < _json.AdsList.length; i++) {
				$('#' + sliderContainerId + ' .swiper-wrapper').append('<div class="swiper-slide"><a href="' + _json.AdsList[i].LinkUrl + '"><img src="' +  _json.AdsList[i].Source + '" alt=""/></a></div>');
			}

			//初始化容器宽高
			var initContainerSize = function(_p){
				var _h = 0;
				var _w = mjquery.winWidth();
				_w = _w > _p.width ? _p.width : _w;//屏幕宽度大于图片实际宽度时将宽度设置为图片的实际宽度
				_h = _w * _p.height / _p.width;
				$('#' + sliderContainerId).css({'width':_w,'height':_h});
			}
			
			initContainerSize(p);
			//创建幻灯片
			var mySwiper = new Swiper('.swiper-container',{
				pagination: '.pagination',
				paginationClickable: true,
				autoplay: 4000,
				speed: 500,
				loop: true,
				onTouchEnd: function(_swiper){//触屏结束事件
					_swiper.startAutoplay();//触屏开始时停止自动播放，所以触屏结束后需要重新开始自动播放
				}
			})
			$(window).resize(function(){//屏幕大小变动时重新计算容器宽高
				initContainerSize(p);
			});
		});	
	} else {
		$('#' + sliderContainerId).hide();//没有幻灯片数据时隐藏幻灯片容器
	}
}