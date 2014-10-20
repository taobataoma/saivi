$(document).bind("mobileinit", function(){
	$.mobile.defaultPageTransition = "slide";
});

function loadFloatMenu(tickets) {
	var floatMenu = $('#float-menu');
	if (floatMenu.length > 0) {
		floatMenu.show();
	} else {
		var menu = '<div id="float-menu"><!--input type="checkbox" id="menu" class="menuControl"--><label><div class="circle-container"><button class="circle"></button></div></label><ul class="items">';
		menu += '<li><a href="./?tickets=' + tickets + '&a=map"></a></li>';
		menu += '<li><a href="./?tickets=' + tickets + '&a=event_list"></a></li>';
		menu += '<li><a href="./?tickets=' + tickets + '&a=shijia"></a></li>';
		menu += '<li><a href="./?tickets=' + tickets + '&a=car_list"></a></li>';
		menu += '<li><a href="./?tickets=' + tickets + '"></a></li>';
		menu += '<li><a href="javascript:;" data-rel="back" data-direction="reverse"></a></li>';
		menu += '</ul></div>';
		$('body').append(menu);
		floatMenu = $('#float-menu');
		floatMenu.click(function() {
			floatMenu.find('.circle-container, ul.items').toggleClass('active');
		})
	}
}
function hideFloatMenu() {
	$('#float-menu').hide();
}

function loadSlider(imgs, container) {
	var PhotoSwipe = Code.PhotoSwipe;
	var instance, indicators;
	instance = PhotoSwipe.attach(
		imgs,
		{
			target: window.document.querySelectorAll('#' + container + '-container')[0],
			preventHide: true,
			captionAndToolbarHide: true,
			//autoStartSlideshow: true,
			imageScaleMethod: 'zoom',
			allowUserZoom: false,
			enableKeyboard: false,
			getImageSource: function(obj){
				return obj.url;
			},
			getImageCaption: function(obj){
				return obj.caption;
			},
			getImageMetaData: function(obj){
				return {
					relatedUrl: obj.link
				};
			}
		}
	);
	indicators = window.document.querySelectorAll('#' + container + '-indicators span');
	if (indicators.length > 0) {
		instance.addEventHandler(PhotoSwipe.EventTypes.onDisplayImage, function(event){
			var i, len;
			for (i = 0, len = indicators.length; i < len; i++){
				indicators[i].setAttribute('class', '');
			}
			indicators[event.index].setAttribute('class', 'current');
		});
	}
	instance.addEventHandler(PhotoSwipe.EventTypes.onTouch, function(e){
		if (e.action === 'tap'){
			var currentImage = instance.getCurrentImage();
			if (currentImage.metaData.relatedUrl) {
				$.mobile.changePage(currentImage.metaData.relatedUrl, {
					allowSamePageTransition:true,
					reloadPage: true,
					transition: 'slide'
				});
			}
		}
	});
	instance.show(0);
}


function checkForm(form, callback) {
	form = $(form);
	var ok = 0, requiredElement = 0;
	var result = false;
	var formElement = form.find('input, textarea, select'),
			button = form.find('.ui-btn[type="submit"]'),
			msgBox = $('#message');
	formElement.each(function() {
		var _this = $(this),
				item = _this.parents('.control-group');
		if (_this.attr('required') == 'required') {
			requiredElement++;
			if (_this.val() != '') {
				item.removeClass('error');
				ok++;
			} else {
				_this.focus();
				item.addClass('error');
				msgBox.addClass('error').html('请填写完整').popup('open');
				return false;
			}
		}
	});
	if (ok >= requiredElement) {
		if (callback) {
			result = false;
			callback(form);
		} else {
			result = true;
		}
		button.attr('disabled', 'disabled');
	}
	return result;
}
function ajaxEvent(form) {
	var url = form.attr('action'),
		type = form.attr('data-ajaxevent'),
		method = form.attr('method') ? form.attr('method') : 'post',
		data = {};
	var formElement = form.find('input, textarea, select'),
		button = form.find('.ui-btn[type="submit"]'),
		msgBox = form.find('.alert');
	msgBox.hide();
	formElement.each(function() {
		var _this = $(this),
			paramName = _this.attr('name'),
			elementType = _this.attr('type');
		if (paramName) {
			if (elementType == 'checkbox' || elementType == 'radio') {
				if (_this.is(':checked')) {
					if (data[paramName]) {
						data[paramName] += ',' + _this.val();
					} else {
						data[paramName] = _this.val();
					}
				}
			} else {
				data[paramName] = _this.val();
			}
		}
	});
	$.ajax({
		dataType: 'json',
		type: method,
		url: url,
		cache: false,
		data: data,
		success: function(d) {
			setTimeout(function() {
				button.removeAttr('disabled');
			}, 1000);
			ajaxAccess[type](d);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			button.removeAttr('disabled');
			msgBox.addClass('alert-error').html(jqXHR.responseText).show();
		}
	});
}
var ajaxAccess = {
	'sendorderTicketsmail': function(data) {
		var form = $('#form-order-tickets'),
				msgBox = form.find(' > .alert'),
				formElement = form.find('input, textarea, select');
		msgBox.removeClass('alert-error').addClass('alert-success').html('发送成功').show();
		setTimeout(function() {
			msgBox.fadeOut();
		}, 3000);
		formElement.val('');
	}
}
function changeNumberInput(obj, action) {
	event.preventDefault();
	obj = $(obj);
	var numberBox = obj.find('span.quantity-number'),
		numberInput = $('#quantity'),
		num = parseInt(numberBox.html());
	var max = parseInt(numberBox.attr('data-max')),
		min = numberBox.attr('data-min') ? parseInt(numberBox.attr('data-min')) : 1;
	if (action == '+') {
		num++;
		if (max && num > max) num = max;
	} else if (action == '-') {
		num--;
		if (min && num < min) num = min;
	}
	numberBox.html(num);
	numberInput.val(num);
	updateTotalPrice(num);
}
function updateTotalPrice(quantity) {
	quantity = quantity ? quantity : parseInt($('#quantity').val());
	var totalPrice = quantity * parseFloat($('#single-price').val());
	$('#total-price').html(totalPrice);
}
function initCalendar(calendar, target) {
	if ($('#' + calendar + ' .fc-view-month > table').length == 0) {
		$('#' + calendar).fullCalendar({
			header: {
				left: 'prev',
				center: 'title',
				right: 'next'
			},
			buttonIcons: {
				prev: 'circle-arrow-l',
				next: 'circle-arrow-r'
			},
			dayClick: function(date, allDay, jsEvent, view) {
				var d = new Date(date);
				d = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate();
				$(this).parents('table.fc-border-separate').find('td').removeClass('fc-current');
				$(this).addClass('fc-current');
				if ($(this).hasClass('fc-future')) {
					$('#' + target).html(d);
					$('input[name="' + target + '"]').val(d);
					$('#' + calendar).popup('close');
				}
			}
		});
	}
}
function selectPayment() {
	var pay1 = $('#payment-type-1'),
		pay2 = $('#payment-type-2'),
		payOnlineSelect = $('#payment-online'),
		singlePrice = $('#single-price');
	if (pay1.is(':checked')) {
		singlePrice.val(pay1.attr('data-price'));
		updateTotalPrice();
		payOnlineSelect.hide();
	} else if (pay2.is(':checked')) {
		singlePrice.val(pay2.attr('data-price'));
		updateTotalPrice();
		payOnlineSelect.show();
	}
}



var gua = {
	init: function(callback) {
		this.load();
		this.bind(callback);
	},
	load: function() {
		this.canvas = document.getElementById('canvas-gua');
		this.wrapper = document.getElementById('gua-container');
		this.result = document.getElementById('gua-result');
		this.w = this.canvas.width;
		this.h = this.canvas.height;
		this.touch = ('createTouch' in document);
		this.StartEvent = this.touch ? 'touchstart' : 'mousedown';
		this.MoveEvent = this.touch ? 'touchmove' : 'mousemove';
		this.EndEvent = this.touch ? 'touchend' : 'mouseup';
		this.ctx = this.canvas.getContext('2d');
		this.ctx.fillStyle = '#a7a7a7';
		this.ctx.fillRect(0, 0, this.w, this.h);
		this.eraserRadius = 10;
		this.result.style.visibility = 'visible';
	},
	bind: function(callback) {
		var t = this;
		this.canvas.parentNode['on'+this.StartEvent] = function(e) {
			e.preventDefault();
		}
		this.canvas['on'+this.StartEvent] = function(e) {
			var touch = t.touch ? e.touches[0] : e,
				_x = touch.clientX - touch.target.offsetLeft - t.wrapper.offsetLeft,
				_y = touch.clientY - touch.target.offsetTop - t.wrapper.offsetTop + document.body.scrollTop;
			t.drawEraser(_x, _y, touch);
		}
		this.canvas['on'+this.MoveEvent] = function(e) {
			var touch = t.touch ? e.touches[0] : e,
				_x = touch.clientX - touch.target.offsetLeft - t.wrapper.offsetLeft,
				_y = touch.clientY - touch.target.offsetTop - t.wrapper.offsetTop + document.body.scrollTop;
			t.drawEraser(_x, _y, touch);
		}
		this.done = false;
		this.canvas['on'+this.EndEvent] = function(e) {
			if (!this.done) {
				var data = t.ctx.getImageData(0,0,t.w,t.h).data;
				for(var i = 0,j = 0; i < data.length; i += 4){
					if (data[i] && data[i+1] && data[i+2] && data[i+3]) {
						j++;
					}
				}
				if (j <= t.w * t.h * 0.5) {
					this.done = true;
					if (typeof(callback) == 'function' ) callback();
				}
			}
		}
	},
	drawEraser: function(_x, _y, touch) {
		var t = this;
		t.ctx.globalCompositeOperation = 'destination-out';
		t.ctx.beginPath();
		t.ctx.arc(_x, _y, t.eraserRadius, 0, Math.PI * 2);
		t.ctx.strokeStyle = 'rgba(250,250,250,0)';
		t.ctx.fill();
		t.ctx.globalCompositeOperation = 'source-over';
	}
}

var rouletteWheel = {
	init: function(tickets, image, callback) {
		var t = this;

		t.startAngle = 0;
		t.spinAngleStart = 0;
		t.spinTimeout = null;
		t.circles = 3;
		t.spinTime = 0;
		t.spinTimeTotal = 100;
		t.spinning = false;
		t.wheelWidth = 200;
		t.wheelHeight = 200;
		t.ctx = null;
		t.wheel = new Image();
		t.callback = callback;
		t.tickets = tickets;
		t.image = image;

		t.drawRouletteWheel(true);

	},
	getData: function(callback) {
		var t = this;
		$.ajax({
			dataType: 'json',
			url: 'api.php?a=zhuan&tickets=' + t.tickets,
			cache: false,
			success: function(d) {
				if (d.error == 1) {
					t.prize = d.prize;
					t.result = d.result;
					t.arcd = 360 / t.prize.length; //每个奖所占角度
					if (callback) callback();
				} else {
					t.callback(0, d.text);
				}
			},
			error: function() {
				t.callback(0, '网络异常，请重试');
			}
		});
	},
	drawRouletteWheel: function(once) {
		var t = this;
		var canvas = document.getElementById("canvas-zhuan");
		if (canvas.getContext) {
			t.ctx = canvas.getContext("2d");
			function draw(){
				t.ctx.clearRect(0,0,t.wheelWidth,t.wheelHeight);
				t.ctx.save();
				t.ctx.translate(t.wheelWidth/2, t.wheelHeight/2);
				//console.log(t.startAngle)
				t.ctx.rotate(t.startAngle);
				t.ctx.drawImage(t.wheel, -t.wheelWidth/2, -t.wheelHeight/2, t.wheelWidth, t.wheelHeight);
				t.ctx.restore();
			}
			if (once) {
				$(t.wheel).load(draw);
				t.wheel.src = t.image;
			} else {
				draw();
			}
		}
	},
	getSpinAngle: function() {
		var t = this;
		var targetAngle1 = 360 - t.result * t.arcd  + 360 * t.circles,
			targetAngle2 = 360 - (t.result + 1) * t.arcd  + 360 * t.circles,
			targetAngle3 = 360 - (t.result + 0.5) * t.arcd  + 360 * t.circles;
		//目标角度为 targetAngle1 和 targetAngle2之间
		var __startAngle = 0, __spinAngleStart;
		//console.log(targetAngle3)
		for (var i = 1; i <= t.spinTimeTotal; i++) {
			__startAngle += (1 - i / t.spinTimeTotal);
		}
		__spinAngleStart = targetAngle3 / __startAngle;
		return __spinAngleStart;
	},
	spin: function() {
		var t = this;
		if (!t.spinning) {
			t.startAngle = 0;
			t.spinTime = 0;
			t.getData(function() {
				t.startAngle = 0;
				t.spinTime = 0;
				t.spinAngleStart = t.getSpinAngle();
				t.rotateWheel();
			});
		}
	},
	rotateWheel: function() {
		var t = this;
		t.spinning = true;
		t.spinTime += 1;
		if( t.spinTime >= t.spinTimeTotal ) {
			t.stopRotateWheel();
			return;
		}
		var spinAngle = t.spinAngleStart - Tween.Linear(t.spinTime, 0, t.spinAngleStart, t.spinTimeTotal);
		t.startAngle += (spinAngle * Math.PI / 180);
		t.drawRouletteWheel();
		t.spinTimeout = setTimeout(function() {
			t.rotateWheel();
		}, 30);
	},
	stopRotateWheel: function() {
		var t = this;
		clearTimeout(t.spinTimeout);
		var degrees = t.startAngle * 180 / Math.PI,
			index = Math.floor((360 - degrees % 360) / t.arcd);
		console.log(t.result + ' ' + index);
		if (t.callback) {
			t.callback(1, t.prize[index]);
		}
		t.spinning = false;
	}
};

var Tween = {
	EaseOut: function(t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	Linear: function(t, b, c, d) {
		return c*t/d + b;
	}
};



function dailySignin(btn, tickets, step) {
	step = step || 10;
	if (!$(btn).hasClass('disabled')) {
		var now = Number($('#score').html());
		$.ajax({
			url: 'api.php?a=qd&tickets=' + tickets,
			cache: false,
			success: function(d) {
				$(btn).addClass('disabled').html('已签到<span class="score-plus">+1</span>');
				$(btn).find('span.score-plus').show();
				setTimeout(function() { $(btn).find('span.score-plus').addClass('active') }, 1);
				$('#score').html(now + step);
			}
		});
	}
}

function getLocation(callback) {
	if (navigator.geolocation) {
		var latitude, longitude;
		navigator.geolocation.getCurrentPosition(function(position) {
			latitude = position.coords.latitude;
			longitude = position.coords.longitude;
			callback(latitude, longitude);
		}, function(error) {
			callback('error');
		}, {
			timeout: 5000
		});
	} else {
		callback('disable');
	}
}

function jiuyuanGetLoc() {
	getLocation(function(latitude, longitude) {
		var result;
		if (latitude == 'error') {
			result = '获取失败，<a href="#" onclick="jiuyuanGetLoc()">重试</a>';
		} else if (latitude == 'disable') {
			result = '浏览器不支持';
		} else {
			result = latitude + ',' + longitude;
		}
		$('#jiuyuan-myloc > span').html(result);
	});
}

function howToShare() {
	var layer;
	if ($('#share-overlayer').length > 0) {
		layer = $('#share-overlayer');
	} else {
		layer = $('<div id="share-overlayer" data-role="popup" data-overlay-theme="a" data-theme="none" data-shadow="false" class="ui-content"><img src="/img/share-tip.png" alt="" width="160" /></div>');
		layer.appendTo($.mobile.activePage);
	}
	layer.popup().popup('open');
}





