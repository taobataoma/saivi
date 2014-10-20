var upload = function(options) {
	var __tis__ = this;
	//
	this.settings = {};
	this.init = function(options) {
		this.settings.target = options.target || null;
		this.settings.url = options.url || '';
		this.settings.loadstart = options.loadstart || this.loadstart;
		this.settings.progress = options.progress || this.progress;
		this.settings.complete = options.complete || this.complete;
		this.settings.failed = options.failed || this.failed;
		this.settings.canceled = options.canceled || this.canceled;
		this.settings.unupload = options.unupload || this.unupload;
	};
	this.change = function(target) {
		var file = target.files[0];
		if (file) {
			//
			var fd = new FormData();
			fd.append(target.name, target.files[0]);
			//
			var xhr = new XMLHttpRequest();
			xhr.upload.addEventListener("progress", this.settings.progress, false);
			xhr.addEventListener("loadstart", this.settings.loadstart, false);
			xhr.addEventListener("load", this.settings.complete, false);
			xhr.addEventListener("error", this.settings.failed, false);
			xhr.addEventListener("abort", this.settings.canceled, false);
			xhr.open("POST", this.settings.url);
			xhr.send(fd);
		}
	};
	this.loadstart = function(evt) {

	};
	this.progress = function(evt) {
//		if (evt.lengthComputable) {
//			var percentComplete = Math.round(evt.loaded * 100 / evt.total);
//		} else {
//		}
	};
	this.complete = function(evt) {
	};
	this.failed = function(evt) {
		alert("上传文件失败，请重试！");
	};
	this.canceled = function(evt) {
	};
	this.unupload = function() {
		alert('您的设备可能不支持文件上传功能！');
	};
	this.check = function() {
		if (navigator.userAgent.match(/(Android (1.0|1.1|1.5|1.6|2.0|2.1|2.2|2.3))|(iPhone OS (5_0|5_0_1|5_1|5_1_1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle\/(1.0|2.0|2.5|3.0))/)) {
			this.settings.unupload();
		}
	};
	//
	this.init(options);
	if (this.settings.target === null) {
		return;
	}
	this.check();
	if (typeof (this.settings.target) === 'object') {
		for (var i in this.settings.target) {
			this.settings.target[i].onchange = function() {
				__tis__.change(this);
			};
		}
	} else {
		this.settings.target.onchange = function() {
			__tis__.change(this);
		};
	}
};