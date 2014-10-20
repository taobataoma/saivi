/*
 *
 * write by fay
 * time:2015-04-25 : 16:17
 *
 * */
;
//初始化LOADING

$('html').attr('style', 'visibility:hidden;');
window.WBPage = {};

$(function () {
    if (!$('.weiba-js').length > 0) {
        var src = '<script type="text/javascript" class="weiba-js" src="/assets/public/js/weiba.js" charset="utf-8"></script>';
        $('script[src*="/assets/public/js/weiba.new.js"]').before(src);

    }

    $('body').append('<div class="new-loading"><div class="small-loading"></div></div>');
    if ($.isIPHONE()) {
        $('body').children(':not(.new-loading)').css({'visibility': 'hidden'});
    }
    $('html').attr('style', 'visibility:visible;');

});


$(document).ready(function () {
    $.addFooter();
    $.getAd();
});

//页面快捷方法
;
(function (window, $) {
    var prefix = {
        'uid': 'MYUID',
        'wxid': 'WXID'
    }

    $.RMLOAD = function () {
        if ($('.new-loading').length > 0) {
            $('.new-loading').remove();
        }
        if ($.isIPHONE()) {
            $('body').children().css({'visibility': 'visible'});
        }
    }
    $.fn.lazyLoad = function () {
        var here = this;
        var winHeight = $(window).height();

        function showImg() {
            $(here).each(function () {

                var _self = $(this);
                var img = new Image();
                img.src = _self.attr('original');

                var inter = setInterval(function () {
                    var pW = _self.parent().width();
                    var imgH = img.height;
                    var imgW = img.width;
                    var topVal = _self.get(0).getBoundingClientRect().top;
                    if (pW > 0) {
                        _self.css({'background-color': '#eee'});

                        var newH = pW * imgH / imgW;

                        _self.height(newH);
                        if ((topVal < (winHeight - (imgH / 3))) && !_self.hasClass('lazyed')) {

                            _self.hide().attr('src', _self.attr('original')).fadeIn().addClass('lazyed');
                        }
                        clearInterval(inter);
                    }

                }, 100);


            });
        }

        showImg();
        $(here).parents().scroll(function () {
            showImg();
        });
        $(window).scroll(function () {
            showImg();
        })
    }

    $.gundong = function (obj, height) {
        setInterval(function () {
            $(obj).find('ul:first').animate({
                marginTop: '-' + height + 'px'
            }, function () {
                $(this).css({marginTop: '0px'}).find('li:first').appendTo(this);

            });
        }, 3000);

    }
    $.getAd = function () {
        if ($('.my-ad').length > 0) {
            var url = './Data/advert';
            var TYPE = $('.my-ad').attr('ad-type');
            var datas = {type: TYPE};

            $.ajax({
                type: 'get',
                dataType: 'jsonp',
                url: url,
                data: datas
            }).done(function (a) {
                //广告
                if (a && a.ret == 0 && a.data && $.isArray(a.data) && a.data.length > 0) {
                    var ad = '';
                    for (var i in a.data) {
                        var datas = a.data[i];
                        ad += '<li style="background-image: url(' + datas.ad_pic + ')">';
                        ad += '<a ';
                        if (datas.ad_url) {
                            ad += 'href="' + datas.ad_url + '"';
                        }
                        ad += '>';

                        ad += '<div class="ad-title">' + datas.ad_title + '</div>';
                        ad += '</a>'
                        ad += '</li>';
                    }
                    $('.my-ad').html(ad);
                    setTimeout(function () {
                        var w = $('.my-ad').width();
                        $('.my-ad').find('li').height(w / 2);
                    })

                } else {
                    //$.yalert({content:'广告加载失败！'});
                }
            });
        }
    }
    //底部菜单
    $.addFooter = function () {
		
        var HAS = $('body').attr('nofooter');
		HAS = 0;
        $.when($.ajax({
                dataType: 'jsonp',
                type: 'get',
                url: './Data/info?token='+token
            }), $.ajax({
                dataType: 'jsonp',
                type: 'get',
                url: './Data/footer?token='+token
            })).done(function (a1, a2) {
					
            if (a1[0] && a1[0].ret == 0) {
                window.WBPage['info'] = a1[0].data;
                $('body').append('<input id="MobileCountry" type="hidden" value="'+WBPage.info.country+'" />');


            }
            if (a2[0] && a2[0].ret == 0) {
                window.WBPage['footer'] = a2[0].data;
            }
			
            if (parseInt(HAS) == 1) {

            } else {
				
                var str = '<div class="weiba-footer">';
                if (a1[0].ret == 0 && a1[0].data && a1[0].data.web_name) {
                    str += '<div class="weiba-copyright">&copy2014　' + a1[0].data.web_name + '</div>';
                }
                if (a2[0].ret == 0 && a2[0].data && a2[0].data.support && a2[0].data.support.title) {
                    str += '<a href="' + a2[0].data.support.href + '" class="weiba-support">' + a2[0].data.support.title + '</a>';
                }
                str += '</div>';
                $('body').append(str);
            }
        })


    }

    //填充姓名和手机
    $.getUserInfo = function () {
        var url = '/Data/account/';
        var data = {
            'uid': $.getUID(),
            'wxid': $.getWXID(),
            'token': token
        }
        $.getJSON(url, data, function (rs) {
            if (rs['ret'] == 0) {
                var username = WBPage.GAME['name']||rs['data']['name'];
                var usermobile = WBPage.GAME['mobile']||rs['data']['mobile'];
                var useraddress=WBPage.GAME['address']
                $('.weiba-user-name').val(username);
                $('.weiba-user-mobile').val(usermobile);
                $('.weiba-user-address').val(useraddress);
            }
        })
    }
    /*分享按钮start*/
    function alert_share() {
        var str = '<div class="share-alert-box"></div>';
        $('body').append(str);
        $('.share-alert-box').on('click', function () {
            $('.share-alert-box').remove();
        })
    }

    $.alert_share = function () {
        var str = '<div class="share-alert-box"></div>';
        $('body').append(str);
        $('.share-alert-box').on('click', function () {
            $('.share-alert-box').remove();
        })
    }
    $(document).ready(function () {
        $('.share-friend,.share-quan').on('click', function () {

            alert_share();
        })
    });
    $.up_img = function (elem) {

        elem.off('change').on('change', function () {
            var $self = $(this);
            var f = $(this).get(0).files[0];
            if (f.size >= 2097151) {
                $.yalert('您传的这张"' + f.name + '"图片，大小超过2M！');
            } else {

                var fr = new FileReader();
                var w = elem.width();
                fr.readAsDataURL(f);
                fr.onload = function (e) {
                    src = e.target.result;
                    var str = '<div style="height: ' + w + 'px" class="up-img-box "><img class="up-img" src="' + src + '"><div class="up-img-loading"></div></div>';
                    $self.after(str);
                    if ($.validateImg(src)) {
                        $.post('/data/image/upload_gd', {image: src},function (a) {
                            if (a && a.ret == 0) {
                                $self.val('');

                                $('.up-img-loading').remove();
                                $('.up-img-box').append('<div class="up-del-img"></div>');
                                $('.up-img').attr('src', a.data.img_url);
                                $.del_img();

                            } else {

                                $.yalert(a.msg);
                                $('.up-img-box').remove();
                            }


                        }, 'json').fail(function () {
                            $.yalert('上传失败！请重试！');
                            $('.up-img-box').remove();
                        })


                    } else {
                        $.yalert('我们只支持PNG,JPG,GIF上传');
                    }
                }
            }


        });

    }

    $.validateImg = function (Data) {
        var filters = {
            "jpg": "/9j/4",
            "gif": "R0lGOD",
            "png": "iVBORw"
        }
        var pos = Data.indexOf(",") + 1;
        for (var e in filters) {
            if (Data.indexOf(filters[e]) === pos) {
                return e;
            }
        }
        return null;
    }

    $.del_img = function () {
        $('.up-del-img').off('click').on('click', function () {
            $(this).parent().remove();
        })
    }
    /*分享按钮start*/
    //浏览器版本

    $.isANDROID = function () {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.indexOf('android') > -1) {
            return true;
        } else {
            return false;
        }
    }
    $.isIPHONE = function () {
        var ua = navigator.userAgent.toLowerCase();

        if (ua.indexOf('iphone') > -1) {
            return true;
        } else {
            return false;
        }
    }
    /*隐藏顶部按钮*/
    $.hideTOP = function () {
        function onBridgeReady() {

            WeixinJSBridge.call('hideOptionMenu');

        }

        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }
        } else {
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }
    }
    //分享数据灌入
    $.setSHARE = function (options) {
        var option = {
            title: '',
            desc: '',
            link: window.location.href,
            icon: ''
        }
        var opt = $.extend(option, options);
        $('body').attr({
            'weiba-title': opt.title,
            'weiba-desc': opt.desc,
            'weiba-link': opt.link,
            'weiba-icon': opt.icon
        });
    }
    //获取URL上参数
    $.getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
    //获取UID
    $.getUID = function () {
        if (!$('.weiba-account-js').length > 0) {
            var src = '<script type="text/javascript" class="weiba-account-js" src="/assets/public/js/weiba.account.js" charset="utf-8"></script>';
            $('script[src*="/assets/public/js/weiba.new.js"]').before(src);

        }


        var uid = window.WBAccount.isLogin();
        if (uid) {
            return uid;
        }
        else {
            return false;
        }
        return !!window.localStorage.getItem(prefix['uid']);
    }
    //获取微信ID
    $.getWXID = function () {
        if (!$('.weiba-account-js').length > 0) {
            var src = '<script type="text/javascript" class="weiba-account-js" src="./assets/public/js/weiba.account.js" charset="utf-8"></script>';
            $('script[src*="./assets/public/js/weiba.new.js"]').before(src);
        }
        var wxid = window.WBAccount.wxid();
        if (wxid) {
            return wxid;
        }
        else {
            return false;
        }
        return !!window.localStorage.getItem(prefix['wxid']);
    }
    //验证是否有UID
    $.checkUID = function (tips) {
        var tip = tips ? tips : '请先关注公众号或从公众账号进入游戏!';
        var uid = $.getUID();
        if (!uid) {
            alert(tip);
            window.location.replace(mpguanzhuurl);
            return false;
        }
    }
    $.randnum = function (under, over) {
        under = under ? under : 0;
        return Math.floor(Math.random() * (over - under) + under);
    }

    $.getDW = function (option) {
        var opts = {
            success: function (rs) {
            },
            error: function (rs) {
            }

        }
        var opt = $.extend(opts, option);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(opt.success, opt.error);
        } else {
            var errors = {code: 13};
            opt.error(errors);
        }
    }
    $.getMAPURL = function (option) {
        var country = '';
        country = $('#MobileCountry').val();
        if (country == '中国') {
            return $.baiduMAP(option);

        } else {
            return $.googleMAP(option);
        }
    }
    $.googleMAP = function (option) {
        var opts = {
            lat: '',
            lng: '',
            name: '',
            address: ''
        }
        var opt = $.extend(opts, option);
        var mark = 'http://api.map.baidu.com/marker';
        var url = mark;
        url += '?location=' + opt.lat + ',' + opt.lng + '&';
        url += 'title=' + encodeURIComponent(opt.name) + '&';
        url += 'name=' + encodeURIComponent(opt.name) + '&';
        url += 'content=' + encodeURIComponent(opt.address) + '&';
        url += 'output=html&src=weiba|weiweb';
        return url;
    }
    //百度地图
    $.baiduMAP = function (option) {
        var opts = {
            lat: '',
            lng: '',
            name: '',
            address: ''
        }
        var opt = $.extend(opts, option);
        var mark = 'http://api.map.baidu.com/marker';
        var url = mark;
        url += '?location=' + opt.lat + ',' + opt.lng + '&';
        url += 'title=' + encodeURIComponent(opt.name) + '&';
        url += 'name=' + encodeURIComponent(opt.name) + '&';
        url += 'content=' + encodeURIComponent(opt.address) + '&';
        url += 'output=html&src=weiba|weiweb';
        return url;
    }

    var _addBlank = function (type) {
        var type = type;
        var w = $(window).width(), h = $(window).height();
        var htmls = '<div class="back-black" black-type="' + type + '" style="height:' + h + 'px"></div>';
        this.add = function () {

            $('body').append(htmls);
        }

        this.close = function () {
            $('.back-black[black-type=' + type + ']').remove();
        }
    }

    $.addBlank = function (type) {
        var a = new _addBlank(type);
        a.add();
    }
    $.addBlank.close = function (type) {
        console.log(type);
        var a = new _addBlank(type);
        a.close();
    }
    //弹窗公用方法
    var _alert_box = {
        type: "alert-box",
        view: function (option, yconfirm) {
            var opts = {
                id: '',
                title: '提示',
                content: '',
                submit_text: '确定',
                cancel_text: '取消',
                submit: function (e) {
                },
                cancel: function (e) {
                }
            }
            var w = $(window).width(), h = $(window).height();

            var opt = $.extend(opts, option);
            var htmls = '';

            htmls += '<div class="alert_w" ';
            if (opt.id) {
                htmls += ' id="' + opt.id + '"';
            }
            htmls += '>';
            htmls += '<div class="alert_w_top">' + opt.title + '</div>';
            htmls += '<div class="alert_w_tip">' + opt.content + '</div>';
            htmls += '<div class="alert_w_btn">';
            if (yconfirm) {
                htmls += '<a class="alert_w_submit" id="alert_w_quxiao">' + opt.cancel_text + '</a>';
                htmls += '<a class="alert_w_submit" id="alert_w_queding">' + opt.submit_text + '</a>';
            } else {
                htmls += '<a class="alert_w_submit" id="alert_w_queding" style="width: 100%;">' + opt.submit_text + '</a>';
            }


            htmls += '</div>';
            htmls += '</div>';
            $('.alert_w').remove();
            $.addBlank.close(_alert_box.type);

            $.addBlank(_alert_box.type);
            $('body').append(htmls);
            var alert_h = $('.alert_w').height();


            $('.alert_w').css({'margin-top': '-' + alert_h / 2 + 'px'});

            $('#alert_w_queding').off('click').on('click', function () {

                if (option.submit) {
                    opt.submit($(this));
                } else {
                    $('.alert_w').remove();
                    $.addBlank.close(_alert_box.type);
                }

            })
            if (yconfirm) {
                $('#alert_w_quxiao').off('click').on('click', function () {
                    if (option.cancel) {
                        opt.cancel($(this));
                    } else {
                        $('.alert_w').remove();
                        $.addBlank.close(_alert_box.type);
                    }

                })
            }


        },
        close: function () {
            $('.alert_w').remove();
            $.addBlank.close(_alert_box.type);

        }

    }
    //确认窗
    $.yalert = function (option, submit) {

        if (typeof(option) == 'object') {

            _alert_box.view(option, 0);
        } else {

            var Datas = {
                content: option,
                submit: submit
            }
            _alert_box.view(Datas, 0);

        }


    }
    $.yalert.close = function () {
        _alert_box.close();
    }
    //有取消的
    $.yconfirm = function (option) {
        _alert_box.view(option, 1);
    }
    $.yconfirm.close = function () {
        _alert_box.close();

    }

    //上滑框
    $.yslide = function (option) {
        var opts = {
            top: '',
            content: '',
            callback: function () {
            },
            close: function () {
            }

        }

        var opt = $.extend(opts, option);

        var w = $(window).width(), h = $(window).height();
        var dw = $('body').width(), dh = $('body').height();
        var str = '<div class="y-slide-boxs" style="height: ' + h + 'px;top:' + h + 'px;">';
        str += '<div class="y-slide-box">'
        str += '<div class="y-slide-top"><span class="y-slide-back">取消</span>' + opt.top + '</div>';
        str += '<div class="y-slide-content" id="y-slide-content">';
        str += opt.content
        str += '</div>';
        str += '</div>';
        str += '</div>';


        $('.y-slide-boxs').remove();

        $(str).appendTo('body').animate({
            'top': '0'

        }, function () {
            $('.y-slide-boxs').css({'position': 'absolute'});
            $('body').children(':not(.y-slide-boxs):visible').addClass('slide-old-visible');
            $('body').children(':not(.y-slide-boxs):visible').hide();
            opt.callback();
        })


        $(window).resize(function () {
            if ($('div').is('.y-slide-boxs') && !$('.y-slide-boxs').is(':animated')) {
                $('.y-slide-boxs').css('top', 0);
            }
        })
        $('.y-slide-back').off('click').on('click', function () {

            $.yslide.close();
            opt.close();

        })

    }
    $.yslide.close = function () {
        var w = $(window).width(), h = $(window).height();
        $('.slide-old-visible').show().removeClass('slide-old-visible');
        $('.y-slide-boxs').animate({top: h + 'px'}, function () {
            $('.y-slide-boxs').remove();
        });
    }
    $.floatbox=function(opt){
        var config={
            content:''

        }

        var str='<div class="float-box">'+content+'</div>';
        $('body').append(str);
        var ww=$('.float-box').width(),wh=$('.float-box').height();

        $('.float-box').css({'margin-top':(0-wh/2)+'px','margin-left':(0-ww/2)+'px'}).show();
        setTimeout(function(){
            $('.float-box').hide(500).remove();
        },1000);

    }
    var _smallLoading = {
        type: 'small-loading',
        add: function () {

            $('body').append('<div class="small-loading"></div>');
        },
        close: function () {

            $('.small-loading').remove();
        }
    }
    $.sloading = function () {
        _smallLoading.add();
    }
    $.sloading.close = function () {
        _smallLoading.close();
    }

    $.audios = function (opt) {


        var options = {
            loop: true,
            preload: "auto",
            src: '',
            callback: function () {
            }
        }
        var opts = $.extend(options, opt);
        var _audio = new Audio();
        for (var key in opts) {
            if (opts.hasOwnProperty(key) && (key in _audio)) {
                _audio[key] = opts[key];
            }
        }
        _audio.load();
        opts.callback(_audio);

    }
    //字符串TOJSON
    $.STRtoJSON = JSON.parse;
    //JSONTO字符串
    $.JSONtoSTR = JSON.stringify;

    /*
     格式化日期
     "yyyy-MM-dd hh:mm:ss.S"==> 2006-07-02 08:09:04.423
     "yyyy-MM-dd E HH:mm:ss" ==> 2009-03-10 二 20:09:04
     "yyyy-MM-dd EE hh:mm:ss" ==> 2009-03-10 周二 08:09:04
     "yyyy-MM-dd EEE hh:mm:ss" ==> 2009-03-10 星期二 08:09:04
     "yyyy-M-d h:m:s.S" ==> 2006-7-2 8:9:4.18
     */
    $.formatDate = function (dates, fmts) {
        //date=new Date(dates);
        //fmt=fmts||'yyyy-MM-dd hh:mm';
        var o = {
            'M+': date.getMonth() + 1, //月份
            'd+': date.getDate(), //日
            'h+': date.getHours() % 12 == 0 ? 12 : date.getHours() % 12, //小时
            'H+': date.getHours(), //小时
            'm+': date.getMinutes(), //分
            's+': date.getSeconds(), //秒
            'q+': Math.floor((date.getMonth() + 3) / 3), //季度
            'S': date.getMilliseconds() //毫秒
        };
        var week = {
            '0': '/u65e5',
            '1': '/u4e00',
            '2': '/u4e8c',
            '3': '/u4e09',
            '4': '/u56db',
            '5': '/u4e94',
            '6': '/u516d'
        };
        if (/(y+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length));
        }
        if (/(E+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, ((RegExp.$1.length > 1) ? (RegExp.$1.length > 2 ? '/u661f/u671f' : '/u5468') : '') + week[date.getDay() + '']);
        }
        for (var k in o) {
            if (new RegExp('(' + k + ')').test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (('00' + o[k]).substr(('' + o[k]).length)));
            }
        }
        return fmt;
    };

})(window, jQuery);
;
//微信监听
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {

    WeixinJSBridge.on('menu:share:appmessage', function (argv) {
        WeixinJSBridge.invoke('sendAppMessage', {
            //'appid': 'kczxs88',
            'img_url': $('body').attr('weiba-icon'),
            'link': $('body').attr('weiba-link') || window.location.href,
            'desc': $('body').attr('weiba-desc') || $('body').attr('weiba-link') || window.location.href,
            'title': $('body').attr('weiba-title')
        }, function (res) {
            if (res.err_msg.indexOf('send_app_msg:confirm') > -1) {
            }

        });
    });

    WeixinJSBridge.on('menu:share:timeline', function (argv) {
        WeixinJSBridge.invoke('shareTimeline', {
            'img_url': $('body').attr('weiba-icon'),
            'link': $('body').attr('weiba-link') || window.location.href,
            'desc': $('body').attr('weiba-desc') || $('body').attr('weiba-link') || window.location.href,
            'title': $('body').attr('weiba-title')

        }, function (res) {
            if (res.err_msg.indexOf('share_timeline:ok') > -1) {

            }
        });

    });
});

(function (window, $) {
    /*
     * Swipe 2.0
     *
     * Brad Birdsall
     * Copyright 2013, MIT License
     *
     */

    function Swipe(container, options) {

        "use strict";

        // utilities
        var noop = function () {
        }; // simple no operation function
        var offloadFn = function (fn) {
            setTimeout(fn || noop, 0)
        }; // offload a functions execution

        // check browser capabilities
        var browser = {
            addEventListener: !!window.addEventListener,
            touch: ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch,
            transitions: (function (temp) {
                var props = ['transformProperty', 'WebkitTransform', 'MozTransform', 'OTransform', 'msTransform'];
                for (var i in props) if (temp.style[ props[i] ] !== undefined) return true;
                return false;
            })(document.createElement('swipe'))
        };

        // quit if no root element
        if (!container) return;
        var element = container.children[0];
        var slides, slidePos, width;
        options = options || {};
        var index = parseInt(options.startSlide, 10) || 0;
        var speed = options.speed || 300;

        function setup() {

            // cache slides
            slides = element.children;

            // create an array to store current positions of each slide
            slidePos = new Array(slides.length);

            // determine width of each slide
            width = container.getBoundingClientRect().width || container.offsetWidth;

            element.style.width = (slides.length * width) + 'px';

            // stack elements
            var pos = slides.length;
            while (pos--) {

                var slide = slides[pos];

                slide.style.width = width + 'px';
                slide.setAttribute('Data-index', pos);

                if (browser.transitions) {
                    slide.style.left = (pos * -width) + 'px';
                    move(pos, index > pos ? -width : (index < pos ? width : 0), 0);
                }

            }

            if (!browser.transitions) element.style.left = (index * -width) + 'px';

            container.style.visibility = 'visible';

        }

        function prev() {

            if (index) slide(index - 1);
            else if (options.continuous) slide(slides.length - 1);

        }

        function next() {

            if (index < slides.length - 1) slide(index + 1);
            else if (options.continuous) slide(0);

        }

        function slide(to, slideSpeed) {

            // do nothing if already on requested slide
            if (index == to) return;

            if (browser.transitions) {

                var diff = Math.abs(index - to) - 1;
                var direction = Math.abs(index - to) / (index - to); // 1:right -1:left

                while (diff--) move((to > index ? to : index) - diff - 1, width * direction, 0);

                move(index, width * direction, slideSpeed || speed);
                move(to, 0, slideSpeed || speed);

            } else {

                animate(index * -width, to * -width, slideSpeed || speed);

            }

            index = to;

            offloadFn(options.callback && options.callback(index, slides[index]));

        }

        function move(index, dist, speed) {

            translate(index, dist, speed);
            slidePos[index] = dist;

        }

        function translate(index, dist, speed) {

            var slide = slides[index];
            var style = slide && slide.style;

            if (!style) return;

            style.webkitTransitionDuration =
                style.MozTransitionDuration =
                    style.msTransitionDuration =
                        style.OTransitionDuration =
                            style.transitionDuration = speed + 'ms';

            style.webkitTransform = 'translate(' + dist + 'px,0)' + 'translateZ(0)';
            style.msTransform =
                style.MozTransform =
                    style.OTransform = 'translateX(' + dist + 'px)';

        }

        function animate(from, to, speed) {

            // if not an animation, just reposition
            if (!speed) {

                element.style.left = to + 'px';
                return;

            }

            var start = +new Date;

            var timer = setInterval(function () {

                var timeElap = +new Date - start;

                if (timeElap > speed) {

                    element.style.left = to + 'px';

                    if (delay) begin();

                    options.transitionEnd && options.transitionEnd.call(event, index, slides[index]);

                    clearInterval(timer);
                    return;

                }

                element.style.left = (( (to - from) * (Math.floor((timeElap / speed) * 100) / 100) ) + from) + 'px';

            }, 4);

        }

        // setup auto slideshow
        var delay = options.auto || 0;
        var interval;

        function begin() {

            interval = setTimeout(next, delay);

        }

        function stop() {

            delay = 0;
            clearTimeout(interval);

        }


        // setup initial vars
        var start = {};
        var delta = {};
        var isScrolling;

        // setup event capturing
        var events = {

            handleEvent: function (event) {

                switch (event.type) {
                    case 'touchstart':
                        this.start(event);
                        break;
                    case 'touchmove':
                        this.move(event);
                        break;
                    case 'touchend':
                        offloadFn(this.end(event));
                        break;
                    case 'webkitTransitionEnd':
                    case 'msTransitionEnd':
                    case 'oTransitionEnd':
                    case 'otransitionend':
                    case 'transitionend':
                        offloadFn(this.transitionEnd(event));
                        break;
                    case 'resize':
                        offloadFn(setup.call());
                        break;
                }

                if (options.stopPropagation) event.stopPropagation();

            },
            start: function (event) {

                var touches = event.touches[0];

                // measure start values
                start = {

                    // get initial touch coords
                    x: touches.pageX,
                    y: touches.pageY,

                    // store time to determine touch duration
                    time: +new Date

                };

                // used for testing first move event
                isScrolling = undefined;

                // reset delta and end measurements
                delta = {};

                // attach touchmove and touchend listeners
                element.addEventListener('touchmove', this, false);
                element.addEventListener('touchend', this, false);

            },
            move: function (event) {

                // ensure swiping with one touch and not pinching
                if (event.touches.length > 1 || event.scale && event.scale !== 1) return

                if (options.disableScroll) event.preventDefault();

                var touches = event.touches[0];

                // measure change in x and y
                delta = {
                    x: touches.pageX - start.x,
                    y: touches.pageY - start.y
                }

                // determine if scrolling test has run - one time test
                if (typeof isScrolling == 'undefined') {
                    isScrolling = !!( isScrolling || Math.abs(delta.x) < Math.abs(delta.y) );
                }

                // if user is not trying to scroll vertically
                if (!isScrolling) {

                    // prevent native scrolling
                    event.preventDefault();

                    // stop slideshow
                    stop();

                    // increase resistance if first or last slide
                    delta.x =
                        delta.x /
                            ( (!index && delta.x > 0               // if first slide and sliding left
                                || index == slides.length - 1        // or if last slide and sliding right
                                && delta.x < 0                       // and if sliding at all
                                ) ?
                                ( Math.abs(delta.x) / width + 1 )      // determine resistance level
                                : 1 );                                 // no resistance if false

                    // translate 1:1
                    translate(index - 1, delta.x + slidePos[index - 1], 0);
                    translate(index, delta.x + slidePos[index], 0);
                    translate(index + 1, delta.x + slidePos[index + 1], 0);

                }

            },
            end: function (event) {

                // measure duration
                var duration = +new Date - start.time;

                // determine if slide attempt triggers next/prev slide
                var isValidSlide =
                    Number(duration) < 250               // if slide duration is less than 250ms
                        && Math.abs(delta.x) > 20            // and if slide amt is greater than 20px
                        || Math.abs(delta.x) > width / 2;      // or if slide amt is greater than half the width

                // determine if slide attempt is past start and end
                var isPastBounds =
                    !index && delta.x > 0                            // if first slide and slide amt is greater than 0
                        || index == slides.length - 1 && delta.x < 0;    // or if last slide and slide amt is less than 0

                // determine direction of swipe (true:right, false:left)
                var direction = delta.x < 0;

                // if not scrolling vertically
                if (!isScrolling) {

                    if (isValidSlide && !isPastBounds) {

                        if (direction) {

                            move(index - 1, -width, 0);
                            move(index, slidePos[index] - width, speed);
                            move(index + 1, slidePos[index + 1] - width, speed);
                            index += 1;

                        } else {

                            move(index + 1, width, 0);
                            move(index, slidePos[index] + width, speed);
                            move(index - 1, slidePos[index - 1] + width, speed);
                            index += -1;

                        }

                        options.callback && options.callback(index, slides[index]);

                    } else {

                        move(index - 1, -width, speed);
                        move(index, 0, speed);
                        move(index + 1, width, speed);

                    }

                }

                // kill touchmove and touchend event listeners until touchstart called again
                element.removeEventListener('touchmove', events, false)
                element.removeEventListener('touchend', events, false)

            },
            transitionEnd: function (event) {

                if (parseInt(event.target.getAttribute('Data-index'), 10) == index) {

                    if (delay) begin();

                    options.transitionEnd && options.transitionEnd.call(event, index, slides[index]);

                }

            }

        }

        // trigger setup
        setup();

        // start auto slideshow if applicable
        if (delay) begin();


        // add event listeners
        if (browser.addEventListener) {

            // set touchstart event on element
            if (browser.touch) element.addEventListener('touchstart', events, false);

            if (browser.transitions) {
                element.addEventListener('webkitTransitionEnd', events, false);
                element.addEventListener('msTransitionEnd', events, false);
                element.addEventListener('oTransitionEnd', events, false);
                element.addEventListener('otransitionend', events, false);
                element.addEventListener('transitionend', events, false);
            }

            // set resize event on window
            window.addEventListener('resize', events, false);

        } else {

            window.onresize = function () {
                setup()
            }; // to play nice with old IE

        }

        // expose the Swipe API
        return {
            setup: function () {

                setup();

            },
            slide: function (to, speed) {

                slide(to, speed);

            },
            prev: function () {

                // cancel slideshow
                stop();

                prev();

            },
            next: function () {

                stop();

                next();

            },
            getPos: function () {

                // return current index position
                return index;

            },
            kill: function () {

                // cancel slideshow
                stop();

                // reset element
                element.style.width = 'auto';
                element.style.left = 0;

                // reset slides
                var pos = slides.length;
                while (pos--) {

                    var slide = slides[pos];
                    slide.style.width = '100%';
                    slide.style.left = 0;

                    if (browser.transitions) translate(pos, 0, 0);

                }

                // removed event listeners
                if (browser.addEventListener) {

                    // remove current event listeners
                    element.removeEventListener('touchstart', events, false);
                    element.removeEventListener('webkitTransitionEnd', events, false);
                    element.removeEventListener('msTransitionEnd', events, false);
                    element.removeEventListener('oTransitionEnd', events, false);
                    element.removeEventListener('otransitionend', events, false);
                    element.removeEventListener('transitionend', events, false);
                    window.removeEventListener('resize', events, false);

                }
                else {

                    window.onresize = null;

                }

            }
        }

    }


    $.fn.swipe = function (params) {
        return this.each(function () {
            $(this).data('swipe', new Swipe($(this)[0], params));
        });
    }

})(window, jQuery);

(function (window, $) {
    $.fn.wb_ui_banner = function () {
        return this.each(function () {
            var $this = $(this),
                count = $('.weiba-banner-box', $this).children('.weiba-banner-item').length;
            if (count > 1) {
                //1.插入工具条
                var html = '<div class="weiba-banner-toolbar">';
                for (var i = 0; i < count; i++) {
                    html += '<span class="weiba-banner-toolbar-item l' + i + '"></span>';
                }
                html += '</div>';

                //2.绑定事件
                var $toolbar = $(html).appendTo($this);
                //3.选中第一个
                selectedIndex(0);

                $(window).bind('rendercomplete', function () {

                    $this.swipe({
                        startSlide: 0,
                        speed: 500,
                        auto: 1500,
                        continuous: true,
                        disableScroll: false,
                        stopPropagation: false,
                        callback: function (index, elm) {
                            selectedIndex(index);
                        },
                        transitionEnd: function (index, elm) {
                        }
                    });
                });
            }
            else if (count <= 0) {
                $this.remove();
            }

            function selectedIndex(index) {
                $($toolbar.children().removeClass('selected')[index]).addClass('selected');
            }
        });
    };
})(window, jQuery);