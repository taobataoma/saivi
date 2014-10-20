

function menu(data) {
	this.menuDiv = $('#' + data.id);
	this.menuModel = $('#' + data.id + '-model');
	this.init = function() {
		var tis = this;
		if (!this.menuDiv.get(0)) {
			var className, url, style, title, last;
			var html = '<div class="menu-div" id="' + data.id + '" style="' + (data.style ? data.style : '') + (data.bottom ? 'bottom:' + data.bottom + 'px;' : '') + (data.top ? 'top:' + data.top + 'px;' : '') + '">';
			for (var i in data.list) {
				last = (parseInt(i) + 1) === data.list.length ? ' last' : '';
				url = data.list[i].url;
				style = data.list[i].style + (data.list[i].icon ? 'background-image:url(\'' + data.list[i].icon + '\');' : '');
				title = data.list[i].title;
				className = data.list[i].class + last;
				html += '<div class="' + className + '"><a href="' + url + '" style="' + style + '">' + title + '</a></div>';
			}
			html += '</div>';
			html += '<div class="menu-model" id="' + data.id + '-model"></div>';
			$('body').append(html);
			this.menuDiv = $('#' + data.id);
			this.menuModel = $('#' + data.id + '-model');
		}
		data.target.bind(globalTouchEnd, function() {
			if (tis.menuDiv.hasClass('display')) {
				tis.hide();
			} else {
				tis.show();
			}
		});
		this.menuModel.bind(globalTouchEnd, function() {
			tis.hide();
		});
	};
	this.show = function() {
		this.menuDiv.show();
		this.menuModel.show();
		this.menuDiv.removeClass('animated bounceOutDown');
		this.menuDiv.addClass('animated bounceInUp');
		this.menuDiv.addClass('display');
	};
	this.hide = function() {
		var tis = this;
		this.menuDiv.removeClass('animated bounceInUp');
		this.menuDiv.addClass('animated bounceOutDown');
		setTimeout(function() {
			tis.menuDiv.hide();
			tis.menuModel.hide();
			tis.menuDiv.removeClass('display');
		}, 500);
	};
	this.init();
}