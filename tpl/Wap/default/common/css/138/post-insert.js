
var postInsert = function(data) {

	var html = '';
	html += '<div class="post-insert" id="post-insert-box">';
	html += '	<div class="button">';
	html += '		<div class="send" id="post-insert-send">发帖</div>';
	html += '		<div class="cancel" id="post-insert-cancel">取消</div>';
	html += '		<em></em>';
	html += '	</div>';
	html += '	<div class="title">';
	html += '		<input type="text" id="post-insert-title" placeholder="标题" />';
	html += '	</div>';
	html += '	<div class="tag">';
	html += '		<input type="text" id="post-insert-tag" placeholder="标签，多个标签请使用逗号隔开。" />';
	html += '	</div>';
	html += '	<div class="textarea">';
	html += '		<textarea id="post-insert-textarea" placeholder="禁止发布广告、色情、谩骂等违反相关法律的内容。"></textarea>';
	html += '	</div>';
	html += '	<div class="image">';
	html += '		<div class="add"><input type="file" id="post-insert-file-1" name="fileToUpload" /></div>';
	html += '		<em></em>';
	html += '	</div>';
	html += '	<div class="other">';
	html += '		<div class="ophone"><input type="file" id="post-insert-file-2" name="fileToUpload" /></div>';
	html += '		<div class="obrow" id="post-insert-brow"></div>';
	html += '		<em></em>';
	html += '	</div>';
	html += '</div>';

	$('body').append(html);

	// init

	$('#post-insert-title').val(getItem('post-insert-title', ''));
	$('#post-insert-tag').val(getItem('post-insert-tag', ''));
	$('#post-insert-textarea').val(getItem('post-insert-textarea', ''));

	$(window).resize(function() {
		postInsertResetTextareaSize();
	});

	// bind event

	data.target.click(function() {
		if (data.power === false) {
			confirmBox('非会员用户不能发帖、评论以及回复。');
			return false;
		}
		$('#post-insert-box').show();
		$('#post-insert-box').removeClass('animated bounceOutDown');
		$('#post-insert-box').addClass('animated bounceInUp');
	});

	$('#post-insert-cancel').click(function() {
		$('#post-insert-box').removeClass('animated bounceInUp');
		$('#post-insert-box').addClass('animated bounceOutDown');
	});

	$('#post-insert-send').click(function() {
		postInsertSend();
	});

	$('#post-insert-title').keyup(function() {
		setItem('post-insert-title', this.value);
	});

	$('#post-insert-tag').keyup(function() {
		setItem('post-insert-tag', this.value);
	});

	$('#post-insert-textarea').keyup(function() {
		setItem('post-insert-textarea', this.value);
	});

	// upload
	if (getItem('post-insert-upload', '')) {
		var image = getItem('post-insert-upload', '').split(',');
	} else {
		var image = [];
	}
	postInsertUploadInit(image.length, image, 5);
	postInsertUploadReg();
	postInsertResetTextareaSize();
};

/**
 * 发贴
 * @returns {undefined}
 */
function postInsertSend() {
	postInsertSendStart();
	var title = $('#post-insert-title').val();
	var tag = $('#post-insert-tag').val();
	var textarea = $('#post-insert-textarea').val();
	$.post('/discuss/append', {
		title: title,
		tags: tag,
		content: textarea,
		images: uploadImage.toString()
	}, function(data) {
		try {
			var json = $.parseJSON(data);
			if (parseInt(json.status) === 2) {
				setItem('post-insert-title', '');
				setItem('post-insert-tag', '');
				setItem('post-insert-textarea', '');
				setItem('post-insert-upload', '');
				$('#post-insert-box').removeClass('animated bounceInUp');
				$('#post-insert-box').addClass('animated bounceOutDown');
				if (parseInt($('#page-discuss').val()) === 1) {
					window.location.reload();
				} else {
					confirmBox('帖子发表成功。', 'tip');
				}
			}
		} catch (e) {
			confirmBox(data);
		}
		postInsertSendEnd();
	});
}

function postInsertSendStart() {
	showLoading();
}

function postInsertSendEnd() {
	hideLoading();
}

////////////////////////////////////////////////////////////////////////////////

var uploadIndex = 0; // 图片的数量
var uploadImage = []; // 图片路径调用这个变量
var uploadMax = 5; // 最大上传图片的数量

function postInsertUploadInit(index, image, max) {
	uploadIndex = index;
	uploadImage = image;
	uploadMax = max;
	for (var i in uploadImage) {
		$('#post-insert-file-1').parent().before('<div id="post-insert-upload-index-' + i + '" class="img" img="' + image[i] + '" style="background-image: url(' + outerImgDomain + formatImageUrl(image[i], 120, 0) + ');"><img src="/themes/default/images/talk-remove.png"></div>');
	}
	if (uploadImage.length > 0) {
		postInsertUploadShowImageBox();
	}
	if (uploadImage.length >= uploadMax) {
		postInsertUploadHideButton();
	}
	$('#post-insert-box > .image > .img > img').bind('click', function() {
		postInsertUploadDelete(this);
		postInsertResetTextareaSize();
	});
}

function postInsertUploadReg() {
	new upload({
		target: [$('#post-insert-file-1').get(0), $('#post-insert-file-2').get(0)],
		url: '/discuss/uploadImage',
		loadstart: function(evt) {
			this.tag = uploadIndex;
			this.upload.tag = uploadIndex;
			postInsertUploadStart(this);
			postInsertResetTextareaSize();
			uploadIndex++;
		},
		progress: function(evt) {
			if (evt.lengthComputable) {
				postInsertUploadProgress(this, Math.round(evt.loaded * 100 / evt.total));
			}
		},
		complete: function(evt) {
			var error = false;
			if (evt.target.responseText.length > 200) {
				confirmBox('网络故障，请重试！');
				error = true;
			}
			if (parseInt(evt.target.responseText) === 1) {
				confirmBox('您上传的文件不是图片！');
				error = true;
			} else if (parseInt(evt.target.responseText) === 2) {
				confirmBox('文件上传失败，请重试！');
				error = true;
			}
			postInsertUploadComplete(this, evt.target.responseText, error);
		},
		unupload: function() {
			if (navigator.userAgent.match(/(Android)|(iPhone OS (5_0|5_0_1|5_1|5_1_1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle\/(1.0|2.0|2.5|3.0))/)) {
				$('#post-insert-file-2').parent().remove();
			}
		},
		failed: function() {
			confirmBox('由于网络故障，上传失败，请重试！');
		}
	});
}

/**
 * 图片上传开始
 * @param {type} el 上传的对象
 * @returns {undefined}
 */
function postInsertUploadStart(el) {
	$('#post-insert-file-1').parent().before('<div id="post-insert-upload-index-' + el.tag + '" class="img"><div><span></span></div></div>');
	postInsertUploadShowImageBox();
	postInsertUploadHideButton();
}

/**
 * 图片上传中（用于展示进度）
 * @param {type} el 上传的对象
 * @param {type} percent 进度百分比
 * @returns {undefined}
 */
function postInsertUploadProgress(el, percent) {
	$('#post-insert-upload-index-' + el.tag + ' div span').width(percent + '%');
}

/**
 * 图片上传完成
 * @param {type} el 上传的对象
 * @param {type} url 图片的URL地址
 * @param {type} error 是否错误
 * @returns {undefined}
 */
function postInsertUploadComplete(el, url, error) {
	var $item = $('#post-insert-upload-index-' + el.tag);
	if (error) {
		$item.remove();
		return;
	}
	uploadImage.push(url);
	$item.attr('img', url);
	$item.css('backgroundImage', 'url(' + outerImgDomain + formatImageUrl(url, 120, 0) + ')');
	$item.append('<img src="/themes/default/images/talk-remove.png">');
	$item.find('div').remove();
	$item.find('img').bind('click', function() {
		postInsertUploadDelete(this);
		postInsertResetTextareaSize();
	});
	if (uploadImage.length >= uploadMax) {
		postInsertUploadHideButton();
	} else {
		postInsertUploadShowButton();
	}
	postInsertResetTextareaSize();
	setItem('post-insert-upload', uploadImage);
}

/**
 * 图片上传失败
 * @param {type} el 上传的对象
 * @returns {undefined}
 */
function postInsertUploadFailed(el) {
	$('#post-insert-upload-index-' + el.tag).append('<img class="g-link-item g-link-remove" src="/themes/default/images/talk-remove.png">');
}

/**
 * 删除上传的图片
 * @param {element} el 删除的按钮对象
 * @returns {undefined}
 */
function postInsertUploadDelete(el) {
	for (var i in uploadImage) {
		if (uploadImage[i] === $(el).parent().attr('img')) {
			uploadImage.splice(i, 1);
			break;
		}
	}
	if (uploadImage.length === 0) {
		postInsertUploadHideImageBox();
	}
	if (uploadImage.length < uploadMax) {
		postInsertUploadShowButton();
	}
	$(el).parent().remove();
	setItem('post-insert-upload', uploadImage);
}

/**
 * 显示上传图片的按钮
 * @returns {undefined}
 */
function postInsertUploadShowButton() {
	$('#post-insert-box > .image > .add').show();
	$('#post-insert-box > .other > .ophone').show();
}

/**
 * 隐藏上传图片的按钮
 * @returns {undefined}
 */
function postInsertUploadHideButton() {
	$('#post-insert-box > .image > .add').hide();
	$('#post-insert-box > .other > .ophone').hide();
}

/**
 * 显示图片框框
 * @returns {undefined}
 */
function postInsertUploadShowImageBox() {
	$('#post-insert-box > .image').show();
}

/**
 * 隐藏图片框框
 * @returns {undefined}
 */
function postInsertUploadHideImageBox() {
	$('#post-insert-box > .image').hide();
}

////////////////////////////////////////////////////////////////////////////////

/**
 * 重置内容框的高度（在某些元素的高度发生变化时，需要调用此方法）
 * @returns {undefined}
 */
function postInsertResetTextareaSize() {
	var hidden = $('#post-insert-box').css('display') === 'none';
	var width = $(window).width() - 30;
	var height = $(window).height() - $('.home_nav').get(0).offsetHeight;
	if (hidden) {
		$('#post-insert-box').css('opacity', 0);
		$('#post-insert-box').show();
	}
	height -= 30;
	height -= $('#post-insert-box > .button').get(0).offsetHeight;
	height -= $('#post-insert-box > .title').get(0).offsetHeight;
	height -= $('#post-insert-box > .tag').get(0).offsetHeight;
	height -= $('#post-insert-box > .image').get(0).offsetHeight;
	height -= $('#post-insert-box > .other').get(0).offsetHeight;
	$('#post-insert-textarea').width(width);
	$('#post-insert-textarea').height(height);
	if (hidden) {
		$('#post-insert-box').hide();
		$('#post-insert-box').css('opacity', 1);
	}
}