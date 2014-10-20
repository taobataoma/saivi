if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (elt /*, from*/) {
        var len = this.length;

        var from = Number(arguments[1]) || 0;
        from = (from < 0)
            ? Math.ceil(from)
            : Math.floor(from);
        if (from < 0)
            from += len;

        for (; from < len; from++) {
            if (from in this && this[from] === elt)
                return from;
        }
        return -1;
    };
}


if (!Date.now) {
    Date.now = function () {
        return new Date();
    };
}

var $get_wxid=window.localStorage.getItem('WXID');
var $UID=window.localStorage.getItem('MYUID');
var storage=window.localStorage;
$.ajax({

    url:'./Data/userinfo',
    data:{
        'wxid' : $get_wxid,
        'uid' : $UID
    },type:'post',
    dataType: "json",
    async:false,
    success:function(result){
        if( result && result['ret']==0 && result['data'] && result['data']['id']){
            storage.setItem('MYUID',result['data']['id']);
            storage.setItem('TIME',Date.now());
            document.cookie  = 'MYUID='+result['data']['id'];
        }
    }})

//临时解决wxid的问题,拨号问题
$(window).bind('rendercomplete', function () {





    $('a').each(function (index, item) {
        var url = $(item).attr('href');
        if (url) {
            if (url.indexOf('vipcard') != -1 || url.indexOf('marketing_scratch') != -1 || url.indexOf('marketing_fruit') != -1 || url.indexOf('marketing_rotate') != -1) {
                if ($(item).attr('href').indexOf(window.localStorage.getItem('WXID')) == -1) {//不包含微信ID则添加
                    $(item).attr('href', url + window.localStorage.getItem('WXID'));
                }
            }

            //增加第三方接口调用传递openid的支持。自动会在包含from_wxopenid=的url里面添加openid
            if(url.indexOf('{{wx_openid}}')>-1){
                url = $(item).attr('href');
                url = url.replace('{{wx_openid}}',window.localStorage.getItem('WXID'));
                $(item).attr('href', url);
            }
            /*
             else if(url.indexOf('tel:')==0){
             var b = navigator.userAgent.match(/i(Pod|Pad|Phone)\.*\sOS\s([\_0-9]+)/);
             if (!b) {//非iphone，电话链接
             $(item).on('click',function(e){
             if(confirm('你确定拨打' + url.replace('tel:','') + '吗?')){
             e.preventDefault();
             window.setTimeout(function(){
             location.href= url;
             },100);
             return false;
             }
             }).addClass('autotel');
             }
             }
             */
        }
    });
    //alert('系统正在升级，请稍后：' + window.localStorage.getItem('WXID'));
});
$(function () {
    var flg_weixin = /(MicroMessenger)/i.test(navigator.userAgent);
    var flg_ios = /(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent);
    if (flg_weixin && !flg_ios) {
        $('body').on('click', 'a', function (e) {
            var url = $(this).attr('href');
            if (url) {
                if (url.indexOf('tel:') == 0) {

                } else {
                    var new_url = url.replace('http://', '').toLowerCase();
                    if (new_url.indexOf(location.host) == 0 || new_url.indexOf('/') == 0) {
                        e.preventDefault();
                        if (new_url.indexOf('?') != -1) {
                            window.location.replace($(this).attr('href') + '&woaibeijingtiananmen=mp.weixin.qq.com');
                        } else {
                            window.location.replace($(this).attr('href') + '?woaibeijingtiananmen=mp.weixin.qq.com');
                        }
                        return false;
                    }
                }
            }
        });
    }
});

(function (window, $) {
    $.translateTel = function (tel) {
        tel += '';
        var numbers = '-0123456789', telNumber = '', spStrings = [' ', ':', '：'];
        spStrings.forEach(function (ele) {
            var spIndex = tel.indexOf(ele);
            if (spIndex > -1) {
                tel = tel.substring(spIndex + 1);
            }
        });

        if (typeof tel === 'string') {
            for (var i = 0, length = tel.length; i < length; i++) {
                var t = tel.charAt(i);
                if (numbers.indexOf(t) > -1) {
                    telNumber += t;
                }
            }
        }
        return telNumber;
    };

    function getRad(d) {
        var PI = Math.PI;
        return d * PI / 180.0;
    }

    $.getFriendDistance = function (lat1, lng1, lat2, lng2) {
        var dis = 0;
        if (arguments.length == 1) {
            dis = lat1;
        } else {
            dis = $.getDistance(lat1, lng1, lat2, lng2);
        }
        if (dis < 1000) {
            return (dis >> 0) + 'm';
        } else {
            return ((dis / 1000) >> 0) + 'km';
        }
    };

    $.getDistance = function (lat1, lng1, lat2, lng2) {
        var EARTH_RADIUS = 6378137.0;
        lat1 = lat1 * 1;
        lng1 = lng1 * 1;
        lat2 = lat2 * 1;
        lng2 = lng2 * 1;
        var f = getRad((lat1 + lat2) / 2);
        var g = getRad((lat1 - lat2) / 2);
        var l = getRad((lng1 - lng2) / 2);

        var sg = Math.sin(g);
        var sl = Math.sin(l);
        var sf = Math.sin(f);

        var s, c, w, r, d, h1, h2;
        var a = EARTH_RADIUS;
        var fl = 1 / 298.257;

        sg = sg * sg;
        sl = sl * sl;
        sf = sf * sf;

        s = sg * (1 - sl) + (1 - sf) * sl;
        c = (1 - sg) * (1 - sl) + sf * sl;

        w = Math.atan(Math.sqrt(s / c));
        r = Math.sqrt(s * c) / w;
        d = 2 * w * a;
        h1 = (3 * r - 1) / 2 / c;
        h2 = (3 * r + 1) / 2 / s;

        return d * (1 + fl * (h1 * sf * (1 - sg) - h2 * (1 - sf) * sg));
    };

})(window, jQuery);

(function (window, $) {
    /**
     * 通过把格式字符串 2001-10-18 13:20:15 转换成date对象
     */
    $.translateDatetime = function (str) {
        return new Date(str);
    };

    /**
     * "yyyy-MM-dd hh:mm:ss.S"==> 2006-07-02 08:09:04.423
     * "yyyy-MM-dd E HH:mm:ss" ==> 2009-03-10 二 20:09:04
     * "yyyy-MM-dd EE hh:mm:ss" ==> 2009-03-10 周二 08:09:04
     * "yyyy-MM-dd EEE hh:mm:ss" ==> 2009-03-10 星期二 08:09:04
     * "yyyy-M-d h:m:s.S" ==> 2006-7-2 8:9:4.18
     */
    $.formatDate = function (date, fmt) {
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

(function (window, $) {

    $.getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
    /**
     * 加载多个json文件
     * @param reqs 请求的多个文件,格式类似：
     *          {
     *              key : {
     *                      url : '',
     *                      data : '',
     *                    }
     *          }
     * @param success
     * @param fail
     */
    $.getMultiJSON = function (reqs, success, fail) {
        var count = 0;
        var result = {};

        if ($.isPlainObject(reqs)) {
            $.each(reqs, function (key, item) {
                count++;
                if (reqs.hasOwnProperty(key)) {
                    $.getJSON(item.url + '?&callback=?', item.data,function (r) {
                        count--;
                        if (r['ret'] === 0) {
                            result[key] = r['data'];
                            if (typeof item.success === 'function') {
                                item.success.call(this, r['data']);
                            }
                        }
                        fn_ok();
                    }).fail(fn_err);
                }
            });

            if (count == 0) {
                fn_ok();
            }
        }
        else {
            fn_err();
        }

        function fn_ok(e) {
            if (count <= 0) {
                if (typeof success === 'function') {
                    success.call(this, result);
                }
            }
        }

        function fn_err(e) {
            count--;
            if (typeof fail === 'function') {
                fail.call(this, e);
            }
        }
    };


})(window, jQuery);

(function (window, $) {
    var onBridgeReady = function () {

        $(document).trigger('bridgeready');

        var $body = $('body'), appId = '',
            title = $body.attr('weiba-title'),
            imgUrl = $body.attr('weiba-icon'),
            link = $body.attr('weiba-link') || window.location.href,
            desc = $body.attr('weiba-desc') || link;
        if (!setForward()) {
            $(document).bind('weibachanged', function () {
                setForward();
            });
        }
    };
    if (document.addEventListener) {
        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    } else if (document.attachEvent) {
        document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }

    function setForward(card_type) {
        var $body = $('body'), appId = '',
            title = $body.attr('weiba-title'),
            imgUrl = $body.attr('weiba-icon'),
            link = $body.attr('weiba-link') || window.location.href,
            desc = $body.attr('weiba-desc') || link;
        if (title && link) {
            WeixinJSBridge.on('menu:share:appmessage', function (argv) {

                WeixinJSBridge.invoke('sendAppMessage', {
                    //'appid': 'kczxs88',
                    'img_url': imgUrl ? imgUrl : undefined,
                    'link': link,
                    'desc': desc ? desc : undefined,
                    'title': title
                }, function (res) {
                    if (res && res['err_msg'] && res['err_msg'].indexOf('confirm') > -1) {
                        $(document).trigger('wx_sendmessage_confirm');
                        $.ajax({
                            url : '/data/partner/share',
                            type : 'post',
                            dataType : 'json',
                            data : {},
                            success : function(result){
                                if( result.ret == 0 && result.data ){
                                    var _url = window.location.href,
                                        wxid = window.localStorage.getItem('WXID');
                                    $.get(result.data, { url: _url, from: wxid } );
                                }
                            }
                        })
                        // if( card_type ){
                        //     $.get('/data/gcard/add_forward_num', { type_id : card_type } );
                        // }
                    }
                });
            });
            WeixinJSBridge.on('menu:share:timeline', function (argv) {
                $(document).trigger('wx_timeline_before');

                WeixinJSBridge.invoke('shareTimeline', {
                    'img_url': imgUrl ? imgUrl : undefined,
                    'link': link,
                    'desc': desc ? desc : undefined,
                    'title': title
                }, function (res) {
                    //貌似目前没有简报
                });
            });
            /*
             WeixinJSBridge.on('menu:share:weibo', function (argv) {
             WeixinJSBridge.invoke('shareWeibo', {
             'content': title + desc,
             'url': link
             }, function (res) {

             });
             });
             */
            return true;
        }
        else {
            return false;
        }
    }

    $.cardForward = function(){
        setForward();
    }

    $.imagePreview = function (urls, cur, elem) {
        if (!elem.parent().is('a')) {
            if ($.isArray(urls) && urls.length > 0) {
                if ($.isNumeric(cur) && urls[cur]) {//如果是数字
                    cur = urls[cur];
                } else if (!cur) {
                    cur = urls[0];
                }
                if (window.WeixinJSBridge) {
                    var params = {
                        'urls': urls,
                        'current': cur
                    };
                    WeixinJSBridge.invoke("imagePreview", params, function (e) {

                    });
                }
            }
        }
    };


})(window, jQuery);

/**
 * 芝麻开门客户端实现,使用代码见redirect.js
 * */
(function(window,$){
    //当前访问路径
    var url = window.location.href;
    if(url){
        if(url.indexOf('/account/login')==-1){//如果不是登陆页面则记录浏览历史
            window.localStorage.setItem('weiba_history', url);
        }
    }

})(window, jQuery);

(function(window,$){
    $.taobao_redirect=function(url){
        var agent=window.navigator.appVersion.toLocaleLowerCase();
        if(agent.indexOf('micromessenger')>-1){
            if(url.indexOf('taobao')>-1||url.indexOf('tmall')>-1){
                //alert(1);
                var str='';
                str+='<div class="show-tiaozhuan"></div>';
                $('body').append(str);
                $('.show-tiaozhuan').on('touchend',function(e){
                    $(this).remove();
                    e.preventDefault();
                    e.stopPropagation();

                })
            }else{
                window.location.replace(url);
            }

        }else{
            window.location.replace(url);
        }
    }
})(window,jQuery)

;(function($){
    //电话号码验证
    mobileReg = function(mobile){
        if( $('input#MobileCountry').size() ){
            var country = $('input#MobileCountry').val();
            var reg = '';
            if( country == '中国' ){
                reg = /^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0-9])\d{8}$/;
            }else{
                reg = /^\d+$/;
            }
            return reg.test(mobile);
        }
    }
})(jQuery);
