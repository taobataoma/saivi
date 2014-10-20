(function(window,$){
    var mark = 'http://api.map.baidu.com/marker';
    var PATH_DATA = '/index.php?g=Wap&m=Marrycard&a=sendwish';
    var PATH_JOINBLESS = '/index.php?g=Wap&m=Marrycard&a=joinbless';
    var PATH_SENDWISH = '/index.php?g=Wap&m=Marrycard&a=sendwish';
    var DataGroup;
    var Directive = {

        '.cover' : {
            '.head@style+' : ';background:url(#{wedding.cover_img});'
        },

        '.tpl-head' : {
            '.groom_name' : 'wedding.groom_name',
            '.bride_name' : 'wedding.bride_name',
            '.head-pic@style+' : ';background-image:url(#{wedding.thumb});'
        },

        '.tpl-photolist' :{
            '.tpl-photo-item': {
                'img<-wedding.photo'  : {
                    '.tpl-photo-body@src' : 'img.src',
                    '.tpl-photo-title' : 'img.title'
                }
            }
        },

        '.tpl-dec-box' : {
            '.@style+' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.declarations){
                    return ';display:block;';
                }
                return ';display:none;';
            },

            '.dec-content' : 'wedding.declarations'
        },

        '.tpl-video' : {
            '.@style+' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.video_url){
                    return ';display:block;';
                }
                return ';display:none;';
            },

            '.' : 'wedding.video_url'
        },

        '.tpl-date' :  {
            '.@style+' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.date){
                    return ';display:block;';
                }
                return ';display:none;';
            },

            '.text' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.date){
                    return $.formatDate(new Date(parseInt(arg.context.wedding.date)), 'yyyy年M月d日 HH:mm')
                }
                return '';
            }
        },

        '.tpl-address' :  {
            '.@style+' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.address){
                    return ';display:block;';
                }
                return ';display:none;';
            },

            '.@href' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.lng && arg.context.wedding.lat){
                    var wedding = arg.context.wedding;
                    return (mark + '?location=' + wedding.lat + ',' + wedding.lng + '&title='+ (wedding.groom_name + '和' + wedding.bride_name) + '婚宴&name=' + (wedding.groom_name + '和' + wedding.bride_name)  + '&content=' + wedding.address + '&output=html&src=weiba|weiweb');
                }
                return '';
            },

            '.label' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.lng && arg.context.wedding.lat){
                    return '点击定位';
                }
                return '宴会地址';
            },

            '.text' : 'wedding.address'
        },

        '.tpl-tel' :  {
            '.@style+' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.telephone){
                    return ';display:block;';
                }
                return ';display:none;';
            },
            '.@href' : function(arg){
                if(arg.context && arg.context.wedding && arg.context.wedding.telephone){
                    return 'tel:' + arg.context.wedding.telephone;
                }
                return '';
            },
            '.text' : 'wedding.telephone'
        },
        '.tpl-music' : {
            '.@src' : 'wedding.music'
        },
        '.support': {
            '.support-link' : 'vweb.name',
            '.support-link@href' : function(arg){
                if(arg['context'] && arg['context']['vweb'] && arg['context']['vweb']['homepage']){
                    return arg['context']['vweb']['homepage'];
                }else{
                    return '';
                }

            }
        },
        '.follow' : {
            '.mp' : 'vweb.mp_username'
        },
        '.qrcode' : {
            '.@src' :  'http://open.weixin.qq.com/qr/code/?username=#{vweb.mp_username}'
        }



    };

    $(function(){
        //1.获取喜帖ID和用户id
        var id = getQueryString('id');
        var wxid = getQueryString('wxid');
        var wecha_id = getQueryString('wecha_id');
        var token = getQueryString('token');
        if(id===undefined){
            return;
        }

		var $cover = $('.cover').show();
		$cover.animate({ top: '-120px' }, 'easeOutBack', function () {
			$cover.animate({ top: 0 }, 500, 'easeOutElastic');
		});
		
        $cover.on('touchstart', function (e) {
			var $main = $('.main').show();
			$(this).hide();
			$('audio')[0].play();
			$('.music').addClass('playing');
			e.preventDefault();
		});
		
		$cover.on('click', function (e) {
			var $main = $('.main').show();
			$(this).hide();
			$('audio')[0].play();
			$('.music').addClass('playing');
			e.preventDefault();
		});

        $(document).on('tap', '.music', function () {
            var audio = $('audio')[0];
            $(this).hasClass('pause') ? (audio.play(), $(this).removeClass('pause')) : (audio.pause(), $(this).addClass('pause'));
        });

        //5.按钮事件
        $(document).on('tap', '.control-box > .button', function () {
            var _this = $(this);
            if (_this.hasClass('join')) { $('.dialog.join').showDialog(); }
            if (_this.hasClass('bless')) { $('.dialog.bless').showDialog(); }
            if (_this.hasClass('forward')) { $('.helper.forward').showHelper(); }
        });
        $('.dialog .button.close').on('tap', function () {
            $(this).parent().closeDialog();
        })
        //6.按钮事件集合
        //6.1.赴宴
        var $join = $('.dialog.join');
        $('.sumbit.button', $join).on('tap',function () {
            var name = $.trim($('.name', $join).val()),
                tel = $.trim($('.tel', $join).val()),
                num = parseInt($.trim($('.num', $join).val()));
            if (!name) {
                $.popTip('请输入你的大名哦', $('.name', $join), 'TopLeft');
                return;
            }

            if (!tel) {
                $.popTip('请输入手机号码', $('.tel', $join), 'TopLeft');
                return;
            }
            if (!$.isNumeric(num) || num < 1) {
                $.popTip('请输入正确的赴宴人数哦', $('.num', $join), 'TopLeft');
                return;
            }
            b_name = name; b_tel = tel;

            var ld = $.showLoading();
            $.get(PATH_JOINBLESS, {
                'id': id,
				'token': token,
				'wecha_id': wecha_id,
                'type': 'join',
                'name': name,
                'tel': tel,
                'num': num
            }, function (result) {
                $.hideLoading(ld);
                if (result && result['ret'] == 0) {
                    $join.closeDialog();
                } else {
                    $.popWaningTip('提交赴宴申请遇到小蝌蚪:' + result['msg']);
                }
            }, 'json').error(function () {
                $.hideLoading(ld);
                $.popWaningTip('提交赴宴申请遇到小蝌蚪！');
            });
        });
        //6.2.祝福
        var $bless = $('.dialog.bless');
        $('.sumbit.button').on('tap',function () {
            var name = $.trim($('.name', $bless).val()),
                tel = $.trim($('.tel', $bless).val()),
                txt = $.trim($('.blesstext', $bless).val());
            if (!name) {
                $.popTip('请输入你的大名哦', $('.name', $bless), 'TopLeft');
                return;
            }
            if (!txt) {
                $.popTip('请输入祝福语哦', $('.blesstext', $bless), 'TopLeft');
                return;
            }
            b_name = name; b_tel = tel;
            var ld = $.showLoading();
            $.get(PATH_SENDWISH,
                {
                'id': id,
				'token': token,
				'wecha_id': wecha_id,
                'type': 'wish',
                'name': name,
                'tel': tel,
                'bless': txt
            }, function (result) {
                $.hideLoading(ld);
                if (result && result['ret'] == 0) {
                    $bless.closeDialog();
                } else {
                    $.popWaningTip('发送祝福遇到小海鸥:' + result['msg']);
                }
            },'json').error(function () {
                $.hideLoading(ld);
                $.popWaningTip('发送祝福遇到小海鸥！');
            });

        });
    });

	function getQueryString(name){
		var reg = new RegExp("(^|\\?|&)"+ name +"=([^&]*)(\\s|&|$)", "i");
		var str = location.href;
		var lurl = str.replace(/#code.jiawoyige.com.qq.com/g, "");
		if (reg.test(lurl)) {
			return decodeURIComponent(RegExp.$2.replace(/\+/g, " "));
		}
		return undefined;
	}

    function setForward(data){
        var wedding = data['wedding'];
        var title = wedding['groom_name'] + '和' + wedding['bride_name'] + '的喜帖',
            imgUrl = (wedding['thumb'])?(window.location.origin+wedding['thumb']) : undefined,
            desc = '',
            $body = $('body'), appId = '';

        $body.attr('weiba-title',title);
        $body.attr('weiba-link',window.location.href);
        if(imgUrl){
            $body.attr('weiba-icon',imgUrl);
        }
        if (wedding['date']) {
            $body.attr('weiba-desc', $.formatDate(new Date(parseInt(wedding['date'])), 'yyyy年M月d日 HH:mm') + '  ' + wedding['address']);
        } else {
            $body.attr('weiba-desc', '我们结婚啦');
        }

        $(document).trigger('weibachanged');
    }

    function loadData(id,callback){
        var lid = WBPage.Loader.append();
        $.getJSON(PATH_DATA,{'id' : id},callback).complete(function(){
        });
    }

    function loadImages(imgs,callback){
        var lid = WBPage.Loader.append();
        $.preLoadImage(imgs,function(){
            callback.call(this);
            WBPage.Loader.remove(lid);
        });
    }

    function renderTpl(data,directive){
        var lid = WBPage.Loader.append();
        $.each(directive,function(key,dir){
            $(key).render(data,dir);
        });
        WBPage.Loader.remove(lid);
    }

})(window, jQuery);
