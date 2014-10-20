/*
 by suolan

 */

//var dataBasePath = 'http://wt.bama555.com';     //本地测试路径
var dataBasePath = '';
var effectPlugBasePath = '/assets/public/js/effect';

$('html').css({'visibility':'hidden'});

//1.WeiBaUI
(function (window, $) {
    window.WBPage = {};

    $(function () {
        /**
         * 添加分享按钮点击支持
         */
        $('body').on('touchend','.weiba-button-share', function () {

            var m = WBPage.MaskLayer.show('black');
            var z = WBPage.MaskLayer.getZIndex();
            var htmls = '<div class="weiba-layer-sharehelper" style="z-index: ' + (z + 1) + '"></div>';
            m.after(htmls);

            $('.weiba-layer-sharehelper').on('touchend', function () {

                WBPage.MaskLayer.close();
                $(this).remove();
            });
        })



        /*
         //如果发现没有历史浏览记录，则把当前页面url作为历史记录保存，以便返回按钮一直有效
         alert(window.history.length);
         if(window.history.length<=1){
         var ls = window.localStorage;
         if(ls){
         ls.setItem('default_history',window.location.href);
         }
         }
         */

    });

    document.write('<script src="/assets/public/js/jDialog/jquery.drag.js" type="text/javascript"><\/' + 'script>');
    document.write('<script src="/assets/public/js/jDialog/jquery.mask.js" type="text/javascript"><\/' + 'script>');
    document.write('<script src="/assets/public/js/jDialog/jquery.dialog.js" type="text/javascript"><\/' + 'script>');

    $(window).bind('rendercomplete', function () {
        /*临时添加账号系统链接*/
        $('.weiba-navbar-item.home').attr('href', '/account/').removeClass('home').addClass('account');

        /*临时添加快捷列表首页链接*/
        var html = '<a class="weiba-list-item tpl-catelist-item" href="/">';
        html += '<div class="weiba-list-item-line">';
        html += '<div class="weiba-list-item-title tpl-cate-title">网站首页</div>';
        html += '</div>';
        html += '<div class="weiba-list-item-icon icon-arrow-r"></div>';
        html += '</a>';
        $(html).prependTo('.weiba-quickpanel .weiba-list.tpl-quickpanel');

        //存储历史浏览路径
        var agent = window.navigator.userAgent.toLowerCase();
        if (agent.indexOf('android') > -1) {
            var thisUrl = window.location.href;
            var str = window.sessionStorage.getItem('historyUrl');
            if (!str) {
                window.sessionStorage.setItem('historyUrl', '[]');
            }
            var history = str ? $.parseJSON(str) : [];
            var len = history.length;
            if (len > 0) {
                if (history[len - 1] != thisUrl) {
                    history.push(thisUrl);
                }
            } else {
                history.push(thisUrl);
            }
            window.sessionStorage.setItem('historyUrl', JSON.stringify(history));
        }
        /**
         *填充姓名和电话
         */
        $.getUserInfo();
    });

    $.extend(window.WBPage, {
        'hasRender': function () {
            return ($('html').hasClass('RENDERCOMPLETE'));
        },
        /**
         * 返回
         */
        'goBack': function () {
            var agent = window.navigator.userAgent.toLowerCase();
            if (agent.indexOf('android') > -1) {
                var history = $.parseJSON(window.sessionStorage.getItem('historyUrl')),
                    len = history.length,
                    thisUrl = window.location.href;
                if (len > 1) {
                    if (thisUrl != history[len - 2]) {
                        history.pop();
                        window.sessionStorage.setItem('historyUrl', JSON.stringify(history));
                        window.location.href = history[len - 2];
                    }
                } else {
                    window.location.href = (window.WBPage.Info.home);
                }
            } else {
                if (window.history.length <= 1) {
                    window.location.href = (window.WBPage.Info.home);
                } else {
                    window.history.back();
                }
            }
        },

        'getWBData': function (name) {
            return $('body').getWBData(name);
        },
        'show': function () {
            $('.weiba-page').show();
        },
        'hide': function () {
            $('.weiba-page').hide();
        },

       
        },

        /**
         * 初始化插件
         * @param name 插件名称
         */
        'widget_init': function (name) {
            switch (name) {
                case 'banner':
                    $('.weiba-banner').wb_ui_banner();
                    break;
                case 'navbar':
                    $('.weiba-navbar').wb_ui_navbar();
                    break;
                case 'quickpanel':
                    $('.weiba-quickpanel').wb_ui_quickpanel();
                    break;
                case 'easycall':
                    $('.weiba-easycall').wb_ui_easycall();
                    break;
                case 'listscroll':
                    $('.weiba-listscroll').wb_ui_list();
                    break;
                case 'select':
                    $('.weiba-select').wb_ui_select();
                    break;
                case 'datetime':
                    $('.weiba-datetime').wb_ui_datetime();
                    break;
                case 'date':
                    $('.weiba-date').wb_ui_date();
                    break;
                case 'time':
                    $('.weiba-time').wb_ui_time();
                    break;


            }
        },
        /**
         * 渲染模板数据
         */
        'tpl_render': function (data, directive) {
            $.each(directive, function (key, dir) {
                if (dir) {
                    var $t = $(key);
                    if ($t.length > 0) {
                        $(key).render(data, dir);
                    } else {
                        console.log('no target:', key);
                    }
                }
            });
        },
        'PATH_TYPE_PROCESSOR': '/assets/public/js/type_processor/',
        'PATH_INVITATION': dataBasePath + '/data/invitation',
        'PATH_AUTOCARD': dataBasePath + '/data/autocard/',
        'PATH_GAME_SUBMIT': dataBasePath + '/game/',
        'PATH_GAME': dataBasePath + '/data/game/',
        'PATH_RESERVE': dataBasePath + '/data/reserve/',
        'PATH_DATA_INFO': dataBasePath + '/data/info/', /*站点基本信息*/
        'PATH_DATA_BANNER': dataBasePath + '/data/cate_banner/', /*首页banner*/
        'PATH_DATA_DETAIL_LIST': dataBasePath + '/data/detail_list/', /*栏目下文章列表*/
        'PATH_DATA_FOOTER': dataBasePath + '/data/footer/', /*底部技术支持*/
        'PATH_DATA_EASYCALL': dataBasePath + '/data/assist/', /*辅助按钮*/
        'PATH_DATA_CATELIST': dataBasePath + '/data/cate_list/', /*栏目列表*/
        'PATH_DATA_DETAIL': dataBasePath + '/data/detail', /*详情*/
        'PATH_DATA_LINKUS': dataBasePath + '/data/linkus', /*详情*/
        'PATH_SMS_SENDCODE': dataBasePath + '/data/sms/send_verfy_code', /*发送短信验证*/
        'PATH_SMS_SENDVOICECODE': dataBasePath + '/data/sms/send_voice_code', /*发送语音短信验证*/
        'PATH_SMS_CHECKCODE': dataBasePath + '/data/sms/check_verfy_code', /*手机验证码校验*/
        'PATH_DATA_CHANGE_PWD': dataBasePath + '/data/ac_reset_password', /*更改密码*/
        'PATH_DATA_REG': dataBasePath + '/data/ac_add', /*注册*/
        'PATH_DATA_ACCOUNT': dataBasePath + '/data/ac_search', /*账号信息*/
        'PATH_DATA_ACCOUNT_BINDWEIXIN': dataBasePath + '/data/ac_bind', /*绑定微信*/
        'PATH_DATA_ACCOUNT_CONSUME': dataBasePath + '/data/mc_record/consume', /*消费记录*/
        'PATH_DATA_ACCOUNT_CREDIT': dataBasePath + '/data/mc_record/credit', /*积分记录*/
        'PATH_DATA_VIP_ADD': dataBasePath + '/data/mc_user_card/add', /*激活vip*/
        'PATH_DATA_VIP_INFO': dataBasePath + '/data/mc_user_card', /*用户VIP信息*/
        'PATH_DATA_VIP_TICKET': dataBasePath + '/data/mc_user_ticket', /*用户拥有的抵物券*/
        'PATH_DATA_MC_INFO': dataBasePath + '/data/mc_info', /*商家定义的VIP会员卡信息*/
        'PATH_DATA_VIP_PAY': dataBasePath + '/data/ac_sequestrate', /*扣费*/
        'PATH_DATA_VIP_TICKET_VERIFY': dataBasePath + '/data/mc_user_ticket/check_my_ticket',//校验优惠券和用户
        'PATH_DATA_VIP_EXCHANGE': dataBasePath + '/data/member_ticket/to_exchange_ticket', /*兑换券*/
        'PATH_DATA_VIP_USETICKET': dataBasePath + '/data/member_ticket/use_ticket', /*用券*/
        'PATH_DATA_MC_TICKET': dataBasePath + '/data/mc_ticket', /*商家提供的所有可兑换的抵物券*/
        'PATH_DATA_ACCOUNT_SERVICE': dataBasePath + '/data/ac_project', /*返回当前微网站所有会员服务*/
        'PATH_VOTE': dataBasePath + '/data/vote/', /*采集和考试路径*/
        'PATH_EMI': dataBasePath + '/data/emigrated/', /*采集和考试路径*/
        'PATH_GROUPON': dataBasePath + '/data/groupon/', /*团购总路径*/
        'PATH_CONTACT': dataBasePath + '/data/contact/', /*新联系我们地址*/
        'PATH_DATA_ADDRESS': dataBasePath + '/data/address/', /*地址详情*/
        'PATH_MALL': dataBasePath + '/data/mall/', /*商城*/
        'PATH_EFFECT': dataBasePath + '/data/effect', /*特效数据*/
        'PATH_USERCENTER': dataBasePath + '/data/usercenter/', /**用户中心**/
        'PATH_EFFECT_PLUG_FILE': {
            'Atmo': effectPlugBasePath + '/weather.js', /*天气效果插件*/
            'Music': effectPlugBasePath + '/music.js', /*背景音乐插件*/
            'Door': effectPlugBasePath + '/door.js'    /*开门效果插件*/
        },
        'PATH_EFFECT_IMG': '/assets/public/images/', /*图片包地址*/
        'PATH_LIVE': dataBasePath + '/data/live/', /*图片包地址*/
        'PATH_DIFF_UID': dataBasePath + '/data/ac_verify/', /*UID对比*/
        'PATH_NEW_ACCOUNT': dataBasePath + '/data/ac_new_account/',    /*直接绑定帐号*/
        'PATH_VIP_MYCARD' : dataBasePath + '/data/mcard/',   /**新版会员卡**/
        'PATH_BUSINESS_CARD':dataBasePath+'/data/business_card/', /*名片*/
        'PATH_ACCOUNT_PAY':dataBasePath+'/data/account/', /*钱包和积分*/
        'PATH_PAY_SITE':dataBasePath+'/data/site/', /*钱包和积分*/
        'PATH_WAIMAI':dataBasePath+'/data/waimai/',
        'PATH_SPREAD':dataBasePath+'/data/spread/'
    });

    //扩展$对象
    $.fn.getWBData = function (name) {
        var dataname = 'weiba-' + name;
        return this.attr(dataname);
    };
})(window, jQuery);


//自定义插件
(function (window, $, undefined) {
    var $layer_mask, curZIndex = 9999;
    $.getUserInfo = function () {
        /**
         *填充姓名和电话
         */
        var wbp = WBPage.PageData;
        if (wbp) {

            var userinfo = WBPage.PageData['user'];
            if (userinfo) {
                var username = userinfo['name'];
                var usermobile = userinfo['mobile'];
                $('.weiba-user-name').val(username);
                $('.weiba-user-mobile').val(usermobile);
            } else {
                var url = WBPage.PATH_ACCOUNT_PAY
                var data = {
                    'uid': window.localStorage.getItem('MYUID')
                }
                $.getJSON(url, data, function (rs) {
                    if (rs['ret'] == 0) {
                        var username = rs['data']['name'];
                        var usermobile = rs['data']['mobile'];
                        $('.weiba-user-name').val(username);
                        $('.weiba-user-mobile').val(usermobile);
                    }
                })
            }

        }
    };
    $.fn.showDialog = function (arg) {
        $layer_mask = getMaskLayer().fadeIn();
        return this.each(function () {
            var $this = $(this).css({
                'z-index': curZIndex++
            }).css({
                    'bottom': -$(this).height()
                }).show().animate({
                    'bottom': 0
                },function () {
                    $layer_mask.one('click', function () {
                        $this.closeDialog();
                    });
                }).data('isOpened', true);
        });
    };

    $.fn.closeDialog = function () {
        return this.each(function () {
            var $this = $(this);
            if ($this.data('isOpened')) {
                $this.animate({
                    'bottom': -$this.height()
                },function () {
                    if ($layer_mask) {
                        $layer_mask.fadeOut(function () {
                            $layer_mask.remove();
                            $layer_mask = null;
                        });
                    }
                    $this.hide();
                }).data('isOpened', false);
            }
            ;
        });
    }

    $.fn.showHelper = function (arg) {
        $layer_mask = getMaskLayer().fadeIn();
        return this.each(function () {
            var $this = $(this).css({
                'z-index': curZIndex++
            }).fadeIn(function () {
                    $layer_mask.one('click', function () {
                        $this.closeHelper();
                    });
                }).data('isOpened', true);
        });
    }

    $.fn.closeHelper = function () {
        return this.each(function () {
            var $this = $(this);
            if ($this.data('isOpened')) {
                $this.hide(function () {
                    if ($layer_mask) {
                        $layer_mask.fadeOut(function () {
                            $layer_mask.remove();
                            $layer_mask = null;
                        });
                    }
                }).data('isOpened', false);
            }
            ;
        });
    }

    function getMaskLayer() {
        if (!$layer_mask) {
            $layer_mask = $('<div class="weiba-masklayer black"></div>').css({
                'z-index': (curZIndex - 1)
            }).appendTo('body');
        }
        return $layer_mask;
    }

    //显示一个Pop text=内容,$parent=显示在哪个元素旁边,pos=在什么位置
    $.popTip = function (text, $parent, pos, type) {
        var html = '<div class="Tip ' + (type ? type : '') + '"><div class="Text">' + text + '</div><div class="ICON"></div></div>';
        var $pop = $(html).appendTo('body');
        var $icon = $('.ICON', $pop);
        var top, left;
        if ($parent) {
            switch (pos) {
                case 'TopRight':
                    top = $parent.offset().top - $pop.height() - $icon.height();
                    left = $parent.offset().left + $parent.width() - $pop.width();
                    break;
                case 'BottomRight':
                    top = $parent.offset().top + $parent.height() + $icon.height();
                    left = $parent.offset().left + $parent.width() - $pop.width();
                    break;
                case 'BottomLeft':
                    top = $parent.offset().top + $parent.height() + $icon.height();
                    left = $parent.offset().left;
                    break;
                case 'TopLeft':
                    top = $parent.offset().top - $pop.height() - $icon.height();
                    left = $parent.offset().left;
                    break;
                case 'Center':
                default:
                    pos = 'Center';
                    top = $parent.offset().top + ($parent.height() - $pop.height()) / 2;
                    left = $parent.offset().left + ($parent.width() - $pop.width()) / 2;
                    $icon.hide();
                    break;
            }
        }
        else {
            $icon.hide();
            top = '50%';
            left = '50%';
            $pop.css({
                'position': 'fixed',
                'margin-left': -$pop.width() / 2 + 'px',
                'margin-right': -$pop.height() / 2 + 'px'
            });
        }
        $pop.addClass(pos).css({
            'top': top,
            'left': left
        }).fadeIn(function () {
                window.setTimeout(function () {
                    $pop.fadeOut(function () {
                        $pop.remove();
                        $pop = null;
                    });
                }, 800);
            });

    };

    //显示一个警告Pop
    $.popWaningTip = function (text, $parent, pos) {
        $.popTip(text, $parent, pos, 'Warning')
    };

    $Loadings = {};
    //显示一个Loadding($cover = loading要遮挡的区域,默认是body)
    $.showLoading = function ($cover) {
        if (!$cover) {
            $cover = $('body');
        }
        var id = 'LOADING' + Math.round(Math.random() * 8000000 + 1000000);
        $Loadings[id] = $('<div class="System-Loading"><div class="BG"></div><div class="ICON"></div></div>').show().appendTo($cover);
        return id;
    };

    $.hideLoading = function (id) {
        if ($Loadings[id]) {
            $Loadings[id].hide();
            $Loadings[id].remove();
            $Loadings[id] = null;
            delete $Loadings[id];
        }
    };

    /*
     "yyyy-MM-dd hh:mm:ss.S"==> 2006-07-02 08:09:04.423
     "yyyy-MM-dd E HH:mm:ss" ==> 2009-03-10 二 20:09:04
     "yyyy-MM-dd EE hh:mm:ss" ==> 2009-03-10 周二 08:09:04
     "yyyy-MM-dd EEE hh:mm:ss" ==> 2009-03-10 星期二 08:09:04
     "yyyy-M-d h:m:s.S" ==> 2006-7-2 8:9:4.18
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

    /**
     ** author : zhupinglei ;  desc : 侧边滑入式弹框
     ***************************************************
     ** $.OpenPopUp : 打开弹框
     ** tit : 标题
     ** str : popCon显示内容
     ** colors : 主色调
     ** callback : 弹框完全出现后的回调函数
     ***************************************************
     ** $.ClosePopUp : 关闭弹框
     ** $.PopUpCheck : 沟选回调
     ***************************************************
     ** $.OpenPopTips : 弹框内的消息提示框
     ** txt : 消息内容
     ** time : 消息显示的时间 单位为 ms
     ***************************************************
     **/

    $.ClosePopUp = function () {
        if ($('#popUp').size()) {
            $('#popUp').css({'webkitTransform': 'translateX(' + parseInt($(window).width() + 2) + 'px)'});
            setTimeout(function () {
                $('#popUpCss').size() ? $('#popUpCss').remove() : '';
                $('#popUp').size() ? $('#popUp').remove() : '';
            }, 400);
        }
    }
    $.PopUpCheck = function(callback){
        if ($('#popUp').size()) {
            $('#popUp i.check').on('tap',function(){
                callback();
            })
        }
    }
    $.OpenPopTips = function (txt, time) {
        if ($('#popUp').size()) {
            $('#popUp .error').html(txt).show();
            setTimeout(function () {
                $('#popUp .error').hide().html('');
            }, time);
        }
    }
    $.OpenPopUp = function (tit, str, colors, callback) {
        $('#popUpCss').size() ? $('#popUpCss').remove() : '';
        $('#popUp').size() ? $('#popUp').remove() : '';
        var str = '<style id="popUpCss">' +
            '#popUp{position:fixed; left:0; top:0; z-index:999999; width:100%; height:100%; background:#eaebee; box-shadow:0px -1px 2px #333; -webkit-transform: translateX(' + parseInt($(window).width() + 2) + 'px);-webkit-transition: -webkit-transform 400ms ease;}' +
            '#popUp .popTit{height:45px; line-height:45px; overflow:hidden; background:' + colors + '; color:#fff; text-align:center; font-weight:bold; font-size:20px; position:relative;}' +
            '#popUp .popTit i{position:absolute; top:8px; width:29px; height:29px; overflow:hidden;}' +
            '#popUp .popTit i.back{left:10px; background:url(' + WBPage.PATH_EFFECT_IMG + 'back.png) no-repeat; background-size:29px 29px;}' +
            '#popUp .popTit i.check{right:10px; background:url(' + WBPage.PATH_EFFECT_IMG + 'check.png) no-repeat; background-size:29px 29px;}' +
            '#popUp .popCon{border:1px solid #dbdbdb; background:#fff; margin:10px; overflow:hidden;}' +
            '#popUp input{border-radius:0;}' +
            '#popUp input[type=text],#popUp input[type=password]{display:block;width:88%;border:none;border-bottom:1px solid #ccc;height:15px;line-height: 15px;overflow: hidden;padding:12px 10% 12px 2%;font-size:15px;margin:0;}' +
            '#popUp .error{margin:0; background:#8c040e; color:#fff; opacity:0.7; width:90%; padding:5px 5%; position:absolute; top:45px; left:0; display:none;}' +
            '</style>' +
            '<div id="popUp">' +
            '<div class="popTit">' +
            '<i class="back"></i><span>' + tit + '</span><i class="check"></i>' +
            '</div>' +
            '<div class="popCon">' + str + '</div>' +
            '<p class="error"></p>' +
            '</div>';
        $('body').append(str);
        setTimeout(function () {
            $('#popUp').css({'webkitTransform': 'translateX(0px)'});
        }, 0);
        setTimeout(function () {
            $('#popUp .popTit i.back').on('touchend', function () {
                setTimeout(function () {
                    $.ClosePopUp();
                }, 400);
            });
            if (typeof callback === 'function') {
                callback();
            }
        }, 400);
    }

})(window, jQuery, undefined);


//Loader
(function (window, $) {
    var $loader, loader_ids = {};

    window.WBPage.Loader = {
        /**
         * 添加显示一个loader,并返回loaderid
         */
        'append': function () {
            $('html').css({'visibility':'hidden'});
            if (!$loader) {
                var zindex = WBPage.MaskLayer.getZIndex() + 1;
                if (zindex < 999) {
                    zindex = 999999;
                }
                $loader = $('<div class="weiba-loader" style="z-index: ' + zindex + '"></div>').appendTo('body');
            }
            $loader.show();
            return getNewLoaderID();
        },
        /**
         * 完成删除loader
         */
        'remove': function (id) {
            if (loader_ids.hasOwnProperty(id)) {
                delete loader_ids[id];
            }
            if (WBPage.Loader.getAllIds().length == 0) {
                $loader.css('display', 'none');
            }
            $('html').css({'visibility':'visible'});
        },
        /**
         * 删除所有loader
         */
        'removeAll': function () {
            loader_ids = {};
            $loader.hide();
            $('html').css({'visibility':'visible'});
        },
        /**
         * 返回正在执行的所有loaderid
         */
        'getAllIds': function () {
            var ids = [];
            $.each(loader_ids, function (key, value) {
                ids.push(key);
            });
            return ids;
        }
    };


    function getNewLoaderID() {
        var loaderid = 'weiba_loaders_' + Math.round(Math.random() * 8000000 + 1000000);
        if (!loader_ids.hasOwnProperty(loaderid)) {
            loader_ids[loaderid] = true;
            return loaderid;
        }
        else {
            return getNewLoaderID();
        }
    }


})(window, jQuery);


//MaskLayer
(function (window, $) {
    var $masklayer;
    window.WBPage.MaskLayer = {
        'show': function (color) {
            if (!$masklayer) {
                $masklayer = $('<div class="weiba-masklayer"></div>').addClass(color ? color : '');
            }
            return $masklayer.hide().appendTo('body').fadeIn();
        },
        'close': function () {
            if ($masklayer) {
                $masklayer.fadeOut(function () {
                    $masklayer.off();
                    $masklayer.unbind();
                    $masklayer.remove();
                    $masklayer = null;
                });
            }
        },
        'getZIndex': function () {
            if ($masklayer) {
                return $masklayer.css('z-index');
            }
            else {
                return 0;
            }
        }
    };


})(window, jQuery);


//2.navbar
(function (window, $) {
    $.fn.wb_ui_navbar = function () {


        var $navBar = this.each(function () {
            $(this).on('tap', '.weiba-navbar-item', function (e) {
                if ($(this).hasClass('quick')) {
                    if (WBPage.QuickPanel) {
                        if (!WBPage.QuickPanel.isOpened) {
                            WBPage.QuickPanel.open();
                        }
                        else {
                            WBPage.QuickPanel.close();
                        }
                    }
                }
                else if ($(this).hasClass('easycall')) {
                    if ($(this).hasClass('easycall-one')) {//只有一个easycall按钮，则直接执行链接
                        return;
                    } else {
                        if (WBPage.EasyCall) {
                            if (!WBPage.EasyCall.isOpened) {
                                WBPage.EasyCall.open();
                            }
                            else {
                                WBPage.EasyCall.close();
                            }
                        }
                    }
                }
                else if ($(this).hasClass('home')) {
                    return;
                    //if(WBPage.Info){
                    //    window.location.href = WBPage.Info.home;
                    //}
                }
                else if ($(this).hasClass('account')) {
                    return;
                }
                else if ($(this).hasClass('back')) {
                    WBPage.goBack();
                    //window.history.back();
                }
                e.preventDefault();
                return false;
            });
        });

        window.WBPage.NavBar = {
            'Dom': $navBar
        };

        return $navBar;
    };

    function quickpanelclose(e) {
        console.log('close');
        $('body').removeClass('weiba-quickpanel-animate-push');
        $('.weiba-quickpanel').hide();
        $('.weiba-page').unbind('tap.quickpanel', quickpanelclose);
        e.preventDefault();
        return false;
    }
})(window, jQuery);

//3.quickpanel
(function (window, $) {
    $.fn.wb_ui_quickpanel = function (action) {
        var _transitionEndEvents = 'webkitTransitionEnd oTransitionEnd otransitionend transitionend msTransitionEnd';
        var $quickpanel = this;
        var $pannel_box;
        var $navbar = $('.weiba-navbar');
        if ($quickpanel.length > 0) {
            $pannel_box = $('<div class="weiba-quickpanel-box"><div class="weiba-quickpanel-toolbar"><div class="weiba-quickpanel-toolbar-title">快捷导航</div><div class="weiba-quickpanel-toolbar-close icon-delete"></div></div></div>')
                .append($quickpanel.show()).appendTo('body')
                .on('tap', '.weiba-quickpanel-toolbar', function () {
                    window.WBPage.QuickPanel.close();
                });
            $('body').on('swipeleft', '.weiba-quickpanel-box', function () {
                window.WBPage.QuickPanel.close();
            });

        }

        window.WBPage.QuickPanel = {
            'isOpened': false,
            'open': function () {
                var wvm = window.WBPage.MaskLayer.show('black');
                wvm.on('tap', function () {
                    window.WBPage.QuickPanel.close();
                });

                $pannel_box.css({
                    'z-index': window.WBPage.MaskLayer.getZIndex() + 1,
                    'width': $quickpanel.width(),
                    'top': -$navbar.height() + 'px',
                    'right': -$quickpanel.width() + 'px'
                    //'height' : $(window).height()
                }).show().animate({
                        'right': 0
                    }, function () {
                        window.WBPage.QuickPanel.isOpened = true;
                    });
            },
            'close': function () {
                $pannel_box.css({
                    'top': -$navbar.height() + 'px',
                    'right': 0
                }).show().animate({
                        'right': -$quickpanel.width() + 'px'
                    }, function () {
                        window.WBPage.QuickPanel.isOpened = false;
                        $pannel_box.hide();
                        window.WBPage.MaskLayer.close();
                    });
            }
        };
        return $quickpanel;
    };
})(window, jQuery);

//4.weiba-easycall
(function (window, $) {
    $.fn.wb_ui_easycall = function () {

        var $easycall = $('.weiba-easycall-but').on('tap', function () {


            if ($('.weiba-easycall-show').hasClass('open')) {
                close_easycall();

            } else {
                window.WBPage.MaskLayer.show('black').on('tap', function (e) {

                    close_easycall();
                });
                $('.weiba-easycall-show').show().addClass('open').parent().css({'z-index': window.WBPage.MaskLayer.getZIndex() + 1});
                $('.weiba-easycall-but').addClass('open');
                $('.weiba-easycall-item').removeClass('margin-bottom33').addClass('margin-bottom20');

            }


        })

        function close_easycall() {
            $('.weiba-easycall-item').addClass('margin-bottom33').removeClass('margin-bottom20');
            $('.weiba-easycall-show').hide(100).removeClass('open');
            window.WBPage.MaskLayer.close();
            $('.weiba-easycall-but').removeClass('open');
        }

//        this.addClass('child' + this.children('.weiba-easycall-item').each(function(index,item){
//            $(item).addClass('no' + index);
//        }).length);
//        var $easycall =  this.on('tap','.weiba-easycall-item',function(e){
//            e.stopPropagation();
//            //return false;
//        }).on('tap',function(){
//            if(!$(this).hasClass('weiba-easycall-item')){
//                //window.WBPage.EasyCall.close();
//            }
//        });
//
//        if(this.children('.weiba-easycall-item').length<=0){//隐藏navBar的图标
//            $('.weiba-navbar').addClass('easycall-no');
//        }
//        else if(this.children('.weiba-easycall-item').length==1){//一个的时候navbar直接操作
//            $('.weiba-navbar-item.easycall').addClass('easycall-one').attr('href',this.children('.weiba-easycall-item').attr('href'));
//        }

//        window.WBPage.EasyCall = {
//            'isOpened': false,
//            'open' : function(){
//                window.WBPage.MaskLayer.show('black').on('tap',function(){
//                    window.WBPage.EasyCall.close();
//                });
//                $easycall.css({
//                    'z-index' : window.WBPage.MaskLayer.getZIndex()+1
//                }).show(function(){
//                    window.WBPage.EasyCall.isOpened = true;
//                });
//            },
//            'close' : function(){
//                $easycall.hide(function(){
//                        window.WBPage.EasyCall.isOpened = false;
//                        window.WBPage.MaskLayer.close();
//                    });
//            }
//        };
        //return $easycall;
    };
})(window, jQuery);

//4.banner
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
                        speed: 400,
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

//
/**
 * 5.日期选择器 date
 *
 * <input class="weiba-date" type=text />
 *
 */
(function (window, $) {

    function getdatetime(str) {
        str = $.trim(str);
        var now = new Date().getTime();
        if (str == 'now') {
            return new Date();
        } else if (str.indexOf('now+y') > -1) {//现在增加几年
            var addmm = str.replace('now+y', '') * 1 * 365 * 24 * 3600 * 1000;
            return new Date(now + addmm);
        } else if (str.indexOf('now+d') > -1) {//现在增加几天
            var addmm = str.replace('now+d', '') * 1 * 24 * 3600 * 1000;
            return new Date(now + addmm);
        } else if (str.indexOf('now+M') > -1) {//现在增加几个月
            var addmm = str.replace('now+M', '') * 1 * 30 * 24 * 3600 * 1000;
            return new Date(now + addmm);
        } else if (str.indexOf('now+m') > -1) {//现在增加几分钟
            var addmm = str.replace('now+m', '') * 1 * 60 * 1000;
            return new Date(now + addmm);
        } else if (str.indexOf('now+h') > -1) {//现在增加几小时
            var addmm = str.replace('now+h', '') * 1 * 3600 * 1000;
            return new Date(now + addmm);
        } else if (str.indexOf('now-y') > -1) {//现在减少几年
            var addmm = str.replace('now-y', '') * 1 * 365 * 24 * 3600 * 1000;
            return new Date(now - addmm);
        } else if (str.indexOf('now-d') > -1) {//现在减少几天
            var addmm = str.replace('now-d', '') * 1 * 24 * 3600 * 1000;
            return new Date(now - addmm);
        } else if (str.indexOf('now-M') > -1) {//现在减少几个月
            var addmm = str.replace('now-M', '') * 1 * 30 * 24 * 3600 * 1000;
            return new Date(now - addmm);
        } else if (str.indexOf('now-m') > -1) {//现在减少几分钟
            var addmm = str.replace('now-m', '') * 1 * 60 * 1000;
            return new Date(now - addmm);
        } else if (str.indexOf('now-h') > -1) {//现在减少几小时
            var addmm = str.replace('now-h', '') * 1 * 3600 * 1000;
            return new Date(now - addmm);
        }
        else {
            return $.translateDatetime(str);
        }
    }

    $.myGetdate = function(str){
        return getdatetime(str);
    }


    $.fn.wb_ui_date = function () {
        return this.each(function () {
            var $this = $(this);
            var options = {'preset': 'date'},
                minDate = $this.attr('weiba-datetime-min'),
                maxDate = $this.attr('weiba-datetime-max'),
                stepMinute = $this.attr('weiba-datetime-step');
            if (minDate) {
                options['minDate'] = getdatetime(minDate);
            }
            if (maxDate) {
                options['maxDate'] = getdatetime(maxDate);
            }

            if (stepMinute) {
                options['stepMinute'] = stepMinute * 1;
            }
            $(this).scroller(options);
        });
    };

    /**
     * 6.日期时间
     * <input class="weiba-datetime" weiba-datetime-min="2001-10-18 13:20:15||now"  weiba-datetime-max="2001-10-18 13:20:15||now" weiba-datetime-step="5"  type=text />
     */


    $.fn.wb_ui_datetime = function () {
        return this.each(function () {
            var $this = $(this);
            var options = {'preset': 'datetime'},
                minDate = $this.attr('weiba-datetime-min'),
                maxDate = $this.attr('weiba-datetime-max'),
                stepMinute = $this.attr('weiba-datetime-step');
            if (minDate) {
                options['minDate'] = getdatetime(minDate);
            }
            if (maxDate) {
                options['maxDate'] = getdatetime(maxDate);
            }

            if (stepMinute) {
                options['stepMinute'] = stepMinute * 1;
            }
            $(this).scroller(options);
        });
    };
})(window, jQuery);

/**
 * 7.时间
 * <input class="weiba-time" type=text />
 */
(function (window, $) {
    $.fn.wb_ui_time = function () {
        return this.each(function () {
            $(this).scroller({'preset': 'time'});
        });
    };
})(window, jQuery);

/**
 * 8.列表选择器 select
 * <select class="weiba-select" />
 *  <option value="1">选项1</option>
 * </select>
 */
//8.列表选择器 select
(function (window, $) {
    $.fn.wb_ui_select = function () {
        return this.each(function () {
            $(this).scroller({'preset': 'select'});
        });


    };
})(window, jQuery);

/**
 * 10.列表
 *
 * <ul class="weiba-listscroll">
 *     <li>
 *         列1-选项1
 *         <ul>
 *             <li>
 *                 列2-选项1
 *             </li>
 *     </li>
 * </ul>
 */
(function (window, $) {
    $.fn.wb_ui_list = function () {
        return this.each(function () {
            $(this).scroller({'preset': 'list'});
        });
    };
})(window, jQuery);

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
                slide.setAttribute('data-index', pos);

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

                if (parseInt(event.target.getAttribute('data-index'), 10) == index) {

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

/**
 *
 */


(function (window, $) {

    function Scroller(elem, settings) {
        var m,
            hi,
            v,
            dw,
            ww, // Window width
            wh, // Window height
            rwh,
            mw, // Modal width
            mh, // Modal height
            lock,
            anim,
            debounce,
            theme,
            lang,
            click,
            scrollable,
            moved,
            start,
            startTime,
            stop,
            p,
            min,
            max,
            target,
            index,
            timer,
            readOnly,
            preventShow,
            that = this,
            ms = $.mobiscroll,
            e = elem,
            elm = $(e),
            s = extend({}, defaults),
            pres = {},
            iv = {},
            pos = {},
            pixels = {},
            wheels = [],
            input = elm.is('input'),
            visible = false,
            onStart = function (e) {
                // Scroll start
                if (testTouch(e) && !move && !isReadOnly(this) && !click) {
                    // Prevent scroll
                    e.preventDefault();

                    move = true;
                    scrollable = s.mode != 'clickpick';
                    target = $('.dw-ul', this);
                    setGlobals(target);
                    moved = iv[index] !== undefined; // Don't allow tap, if still moving
                    p = moved ? getCurrentPosition(target) : pos[index];
                    start = getCoord(e, 'Y');
                    startTime = new Date();
                    stop = start;
                    scroll(target, index, p, 0.001);

                    if (scrollable) {
                        target.closest('.dwwl').addClass('dwa');
                    }

                    $(document).on(MOVE_EVENT, onMove).on(END_EVENT, onEnd);
                }
            },
            onMove = function (e) {
                if (scrollable) {
                    e.preventDefault();
                    e.stopPropagation();
                    stop = getCoord(e, 'Y');
                    scroll(target, index, constrain(p + (start - stop) / hi, min - 1, max + 1));
                }
                moved = true;
            },
            onEnd = function (e) {
                var time = new Date() - startTime,
                    val = constrain(p + (start - stop) / hi, min - 1, max + 1),
                    speed,
                    dist,
                    tindex,
                    ttop = target.offset().top;

                if (time < 300) {
                    speed = (stop - start) / time;
                    dist = (speed * speed) / s.speedUnit;
                    if (stop - start < 0) {
                        dist = -dist;
                    }
                } else {
                    dist = stop - start;
                }

                tindex = Math.round(p - dist / hi);

                if (!dist && !moved) { // this is a "tap"
                    var idx = Math.floor((stop - ttop) / hi),
                        li = $('.dw-li', target).eq(idx),
                        hl = scrollable;

                    if (event('onValueTap', [li]) !== false) {
                        tindex = idx;
                    } else {
                        hl = true;
                    }

                    if (hl) {
                        li.addClass('dw-hl'); // Highlight
                        setTimeout(function () {
                            li.removeClass('dw-hl');
                        }, 200);
                    }
                }

                if (scrollable) {
                    calc(target, tindex, 0, true, Math.round(val));
                }

                move = false;
                target = null;

                $(document).off(MOVE_EVENT, onMove).off(END_EVENT, onEnd);
            },
            onBtnStart = function (e) {
                var btn = $(this);
                $(document).on(END_EVENT, onBtnEnd);
                // Active button
                if (!btn.hasClass('dwb-d')) {
                    btn.addClass('dwb-a');
                }
                setTimeout(function () {
                    btn.blur();
                }, 10);
                // +/- buttons
                if (btn.hasClass('dwwb')) {
                    if (testTouch(e)) {
                        step(e, btn.closest('.dwwl'), btn.hasClass('dwwbp') ? plus : minus);
                    }
                }
            },
            onBtnEnd = function (e) {
                if (click) {
                    clearInterval(timer);
                    click = false;
                }
                $(document).off(END_EVENT, onBtnEnd);
                $('.dwb-a', dw).removeClass('dwb-a');
            },
            onKeyDown = function (e) {
                if (e.keyCode == 38) { // up
                    step(e, $(this), minus);
                } else if (e.keyCode == 40) { // down
                    step(e, $(this), plus);
                }
            },
            onKeyUp = function (e) {
                if (click) {
                    clearInterval(timer);
                    click = false;
                }
            },
            onScroll = function (e) {
                if (!isReadOnly(this)) {
                    e.preventDefault();
                    e = e.originalEvent || e;
                    var delta = e.wheelDelta ? (e.wheelDelta / 120) : (e.detail ? (-e.detail / 3) : 0),
                        t = $('.dw-ul', this);

                    setGlobals(t);
                    calc(t, Math.round(pos[index] - delta), delta < 0 ? 1 : 2);
                }
            };

        // Private functions

        function step(e, w, func) {
            e.stopPropagation();
            e.preventDefault();
            if (!click && !isReadOnly(w) && !w.hasClass('dwa')) {
                click = true;
                // + Button
                var t = w.find('.dw-ul');

                setGlobals(t);
                clearInterval(timer);
                timer = setInterval(function () {
                    func(t);
                }, s.delay);
                func(t);
            }
        }

        function isReadOnly(wh) {
            if ($.isArray(s.readonly)) {
                var i = $('.dwwl', dw).index(wh);
                return s.readonly[i];
            }
            return s.readonly;
        }

        function generateWheelItems(i) {
            var html = '<div class="dw-bf">',
                ww = wheels[i],
                w = ww.values ? ww : convert(ww),
                l = 1,
                labels = w.labels || [],
                values = w.values,
                keys = w.keys || values;

            $.each(values, function (j, v) {
                if (l % 20 == 0) {
                    html += '</div><div class="dw-bf">';
                }
                html += '<div role="option" aria-selected="false" class="dw-li dw-v" data-val="' + keys[j] + '"' + (labels[j] ? ' aria-label="' + labels[j] + '"' : '') + ' style="height:' + hi + 'px;line-height:' + hi + 'px;"><div class="dw-i">' + v + '</div></div>';
                l++;
            });

            html += '</div>';
            return html;
        }

        function setGlobals(t) {
            min = $('.dw-li', t).index($('.dw-v', t).eq(0));
            max = $('.dw-li', t).index($('.dw-v', t).eq(-1));
            index = $('.dw-ul', dw).index(t);
        }

        function formatHeader(v) {
            var t = s.headerText;
            return t ? (typeof t === 'function' ? t.call(e, v) : t.replace(/\{value\}/i, v)) : '';
        }

        function read() {
            that.temp = ((input && that.val !== null && that.val != elm.val()) || that.values === null) ? s.parseValue(elm.val() || '', that) : that.values.slice(0);
            setVal();
        }

        function getCurrentPosition(t) {
            var style = window.getComputedStyle ? getComputedStyle(t[0]) : t[0].style,
                matrix,
                px;

            if (has3d) {
                $.each(['t', 'webkitT', 'MozT', 'OT', 'msT'], function (i, v) {
                    if (style[v + 'ransform'] !== undefined) {
                        matrix = style[v + 'ransform'];
                        return false;
                    }
                });
                matrix = matrix.split(')')[0].split(', ');
                px = matrix[13] || matrix[5];
            } else {
                px = style.top.replace('px', '');
            }

            return Math.round(m - (px / hi));
        }

        function ready(t, i) {
            clearTimeout(iv[i]);
            delete iv[i];
            t.closest('.dwwl').removeClass('dwa');
        }

        function scroll(t, index, val, time, active) {

            var px = (m - val) * hi,
                style = t[0].style,
                i;

            if (px == pixels[index] && iv[index]) {
                return;
            }

            if (time && px != pixels[index]) {
                // Trigger animation start event
                event('onAnimStart', [dw, index, time]);
            }

            pixels[index] = px;

            style[pr + 'Transition'] = 'all ' + (time ? time.toFixed(3) : 0) + 's ease-out';

            if (has3d) {
                style[pr + 'Transform'] = 'translate3d(0,' + px + 'px,0)';
            } else {
                style.top = px + 'px';
            }

            if (iv[index]) {
                ready(t, index);
            }

            if (time && active) {
                t.closest('.dwwl').addClass('dwa');
                iv[index] = setTimeout(function () {
                    ready(t, index);
                }, time * 1000);
            }

            pos[index] = val;
        }

        function scrollToPos(time, index, manual, dir, active) {

            // Call validation event
            if (event('validate', [dw, index, time]) !== false) {

                // Set scrollers to position
                $('.dw-ul', dw).each(function (i) {
                    var t = $(this),
                        cell = $('.dw-li[data-val="' + that.temp[i] + '"]', t),
                        cells = $('.dw-li', t),
                        v = cells.index(cell),
                        l = cells.length,
                        sc = i == index || index === undefined;

                    // Scroll to a valid cell
                    if (!cell.hasClass('dw-v')) {
                        var cell1 = cell,
                            cell2 = cell,
                            dist1 = 0,
                            dist2 = 0;

                        while (v - dist1 >= 0 && !cell1.hasClass('dw-v')) {
                            dist1++;
                            cell1 = cells.eq(v - dist1);
                        }

                        while (v + dist2 < l && !cell2.hasClass('dw-v')) {
                            dist2++;
                            cell2 = cells.eq(v + dist2);
                        }

                        // If we have direction (+/- or mouse wheel), the distance does not count
                        if (((dist2 < dist1 && dist2 && dir !== 2) || !dist1 || (v - dist1 < 0) || dir == 1) && cell2.hasClass('dw-v')) {
                            cell = cell2;
                            v = v + dist2;
                        } else {
                            cell = cell1;
                            v = v - dist1;
                        }
                    }

                    if (!(cell.hasClass('dw-sel')) || sc) {
                        // Set valid value
                        that.temp[i] = cell.attr('data-val');

                        // Add selected class to cell
                        $('.dw-sel', t).removeClass('dw-sel');

                        if (!s.multiple) {
                            $('.dw-sel', t).removeAttr('aria-selected');
                            cell.attr('aria-selected', 'true');
                        }

                        cell.addClass('dw-sel');

                        // Scroll to position
                        scroll(t, i, v, sc ? time : 0.1, sc ? active : false);
                    }
                });

                // Reformat value if validation changed something
                v = s.formatResult(that.temp);
                if (s.display == 'inline') {
                    setVal(manual, 0, true);
                } else {
                    $('.dwv', dw).html(formatHeader(v));
                }

                if (manual) {
                    event('onChange', [v]);
                }
            }

        }

        function event(name, args) {
            var ret;
            args.push(that);
            $.each([theme.defaults, pres, settings], function (i, v) {
                if (v[name]) { // Call preset event
                    ret = v[name].apply(e, args);
                }
            });
            return ret;
        }

        function calc(t, val, dir, anim, orig) {
            val = constrain(val, min, max);

            var cell = $('.dw-li', t).eq(val),
                o = orig === undefined ? val : orig,
                idx = index,
                time = anim ? (val == o ? 0.1 : Math.abs((val - o) * s.timeUnit)) : 0;

            // Set selected scroller value
            that.temp[idx] = cell.attr('data-val');

            scroll(t, idx, val, time, orig);

            setTimeout(function () {
                // Validate
                scrollToPos(time, idx, true, dir, orig !== undefined);
            }, 10);
        }

        function plus(t) {
            var val = pos[index] + 1;
            calc(t, val > max ? min : val, 1, true);
        }

        function minus(t) {
            var val = pos[index] - 1;
            calc(t, val < min ? max : val, 2, true);
        }

        function setVal(fill, time, noscroll, temp) {

            if (visible && !noscroll) {
                scrollToPos(time);
            }

            v = s.formatResult(that.temp);

            if (!temp) {
                that.values = that.temp.slice(0);
                that.val = v;
            }

            if (fill) {
                if (input) {
                    elm.val(v).trigger('change');
                }
            }
        }

        // Public functions

        that.position = function (check) {

            if (s.display == 'inline' || (ww === $(window).width() && rwh === $(window).height() && check) || (event('onPosition', [dw]) === false)) {
                return;
            }

            var w,
                l,
                t,
                aw, // anchor width
                ah, // anchor height
                ap, // anchor position
                at, // anchor top
                al, // anchor left
                arr, // arrow
                arrw, // arrow width
                arrl, // arrow left
                scroll,
                totalw = 0,
                minw = 0,
                st = $(window).scrollTop(),
                wr = $('.dwwr', dw),
                d = $('.dw', dw),
                css = {},
                anchor = s.anchor === undefined ? elm : s.anchor;

            ww = $(window).width();
            rwh = $(window).height();
            wh = window.innerHeight; // on iOS we need innerHeight
            wh = wh || rwh;

            if (/modal|bubble/.test(s.display)) {
                $('.dwc', dw).each(function () {
                    w = $(this).outerWidth(true);
                    totalw += w;
                    minw = (w > minw) ? w : minw;
                });
                w = totalw > ww ? minw : totalw;
                wr.width(w).css('white-space', totalw > ww ? '' : 'nowrap');
            }

            mw = d.outerWidth();
            mh = d.outerHeight(true);

            lock = mh <= wh && mw <= ww;

            if (s.display == 'modal') {
                l = (ww - mw) / 2;
                t = st + (wh - mh) / 2;
            } else if (s.display == 'bubble') {
                scroll = true;
                arr = $('.dw-arrw-i', dw);
                ap = anchor.offset();
                at = ap.top;
                al = ap.left;

                // horizontal positioning
                aw = anchor.outerWidth();
                ah = anchor.outerHeight();
                l = al - (d.outerWidth(true) - aw) / 2;
                l = l > (ww - mw) ? (ww - (mw + 20)) : l;
                l = l >= 0 ? l : 20;

                // vertical positioning
                t = at - mh; // above the input
                if ((t < st) || (at > st + wh)) { // if doesn't fit above or the input is out of the screen
                    d.removeClass('dw-bubble-top').addClass('dw-bubble-bottom');
                    t = at + ah; // below the input
                } else {
                    d.removeClass('dw-bubble-bottom').addClass('dw-bubble-top');
                }

                // Calculate Arrow position
                arrw = arr.outerWidth();
                arrl = al + aw / 2 - (l + (mw - arrw) / 2);

                // Limit Arrow position
                $('.dw-arr', dw).css({ left: constrain(arrl, 0, arrw) });
            } else {
                css.width = '100%';
                if (s.display == 'top') {
                    t = st;
                } else if (s.display == 'bottom') {
                    t = st + wh - mh;
                }
            }

            css.top = t < 0 ? 0 : t;
            css.left = l;
            d.css(css);

            // If top + modal height > doc height, increase doc height
            $('.dw-persp', dw).height(0).height(t + mh > $(document).height() ? t + mh : $(document).height());

            // Scroll needed
            if (scroll && ((t + mh > st + wh) || (at > st + wh))) {
                $(window).scrollTop(t + mh - wh);
            }
        };

        /**
         * Enables the scroller and the associated input.
         */
        that.enable = function () {
            s.disabled = false;
            if (input) {
                elm.prop('disabled', false);
            }
        };

        /**
         * Disables the scroller and the associated input.
         */
        that.disable = function () {
            s.disabled = true;
            if (input) {
                elm.prop('disabled', true);
            }
        };

        /**
         * Gets the selected wheel values, formats it, and set the value of the scroller instance.
         * If input parameter is true, populates the associated input element.
         * @param {Array} values - Wheel values.
         * @param {Boolean} [fill=false] - Also set the value of the associated input element.
         * @param {Number} [time=0] - Animation time
         * @param {Boolean} [temp=false] - If true, then only set the temporary value.(only scroll there but not set the value)
         */
        that.setValue = function (values, fill, time, temp) {
            that.temp = $.isArray(values) ? values.slice(0) : s.parseValue.call(e, values + '', that);
            setVal(fill, time, false, temp);
        };

        that.getValue = function () {
            return that.values;
        };

        that.getValues = function () {
            var ret = [],
                i;

            for (i in that._selectedValues) {
                ret.push(that._selectedValues[i]);
            }
            return ret;
        };

        /**
         * Changes the values of a wheel, and scrolls to the correct position
         */
        that.changeWheel = function (idx, time) {
            if (dw) {
                var i = 0,
                    nr = idx.length;

                $.each(s.wheels, function (j, wg) {
                    $.each(wg, function (k, w) {
                        if ($.inArray(i, idx) > -1) {
                            wheels[i] = w;
                            $('.dw-ul', dw).eq(i).html(generateWheelItems(i));
                            nr--;
                            if (!nr) {
                                that.position();
                                scrollToPos(time, undefined, true);
                                return false;
                            }
                        }
                        i++;
                    });
                    if (!nr) {
                        return false;
                    }
                });
            }
        };

        /**
         * Return true if the scroller is currently visible.
         */
        that.isVisible = function () {
            return visible;
        };

        that.tap = function (el, handler) {
            var startX,
                startY;

            if (s.tap) {
                el.on('touchstart.dw',function (e) {
                    e.preventDefault();
                    startX = getCoord(e, 'X');
                    startY = getCoord(e, 'Y');
                }).on('touchend.dw', function (e) {
                        // If movement is less than 20px, fire the click event handler
                        if (Math.abs(getCoord(e, 'X') - startX) < 20 && Math.abs(getCoord(e, 'Y') - startY) < 20) {
                            handler.call(this, e);
                        }
                        tap = true;
                        setTimeout(function () {
                            tap = false;
                        }, 300);
                    });
            }

            el.on('click.dw', function (e) {
                if (!tap) {
                    // If handler was not called on touchend, call it on click;
                    handler.call(this, e);
                }
            });

        };

        /**
         * Shows the scroller instance.
         * @param {Boolean} prevAnim - Prevent animation if true
         */
        that.show = function (prevAnim) {
            if (s.disabled || visible) {
                return false;
            }

            if (s.display == 'top') {
                anim = 'slidedown';
            }

            if (s.display == 'bottom') {
                anim = 'slideup';
            }

            // Parse value from input
            read();

            event('onBeforeShow', []);

            // Create wheels
            var lbl,
                l = 0,
                mAnim = '';

            if (anim && !prevAnim) {
                mAnim = 'dw-' + anim + ' dw-in';
            }
            // Create wheels containers
            var html = '<div role="dialog" class="' + s.theme + ' dw-' + s.display + (prefix ? ' dw' + prefix : '') + '">' + (s.display == 'inline' ? '<div class="dw dwbg dwi"><div class="dwwr">' : '<div class="dw-persp"><div class="dwo"></div><div class="dw dwbg ' + mAnim + '"><div class="dw-arrw"><div class="dw-arrw-i"><div class="dw-arr"></div></div></div><div class="dwwr"><div aria-live="assertive" class="dwv' + (s.headerText ? '' : ' dw-hidden') + '"></div>') + '<div class="dwcc">';

            $.each(s.wheels, function (i, wg) { // Wheel groups
                html += '<div class="dwc' + (s.mode != 'scroller' ? ' dwpm' : ' dwsc') + (s.showLabel ? '' : ' dwhl') + '"><div class="dwwc dwrc"><table cellpadding="0" cellspacing="0"><tr>';
                $.each(wg, function (j, w) { // Wheels
                    wheels[l] = w;
                    lbl = w.label !== undefined ? w.label : j;
                    html += '<td><div class="dwwl dwrc dwwl' + l + '">' + (s.mode != 'scroller' ? '<div class="dwb-e dwwb dwwbp" style="height:' + hi + 'px;line-height:' + hi + 'px;"><span>+</span></div><div class="dwb-e dwwb dwwbm" style="height:' + hi + 'px;line-height:' + hi + 'px;"><span>&ndash;</span></div>' : '') + '<div class="dwl">' + lbl + '</div><div tabindex="0" aria-live="off" aria-label="' + lbl + '" role="listbox" class="dwww"><div class="dww" style="height:' + (s.rows * hi) + 'px;min-width:' + s.width + 'px;"><div class="dw-ul">';
                    // Create wheel values
                    html += generateWheelItems(l);
                    html += '</div><div class="dwwol"></div></div><div class="dwwo"></div></div><div class="dwwol"></div></div></td>';
                    l++;
                });

                html += '</tr></table></div></div>';
            });

            html += '</div>' + (s.display != 'inline' ? '<div class="dwbc' + (s.button3 ? ' dwbc-p' : '') + '"><span class="dwbw dwb-s"><span class="dwb dwb-e" role="button" tabindex="0">' + s.setText + '</span></span>' + (s.button3 ? '<span class="dwbw dwb-n"><span class="dwb dwb-e" role="button" tabindex="0">' + s.button3Text + '</span></span>' : '') + '<span class="dwbw dwb-c"><span class="dwb dwb-e" role="button" tabindex="0">' + s.cancelText + '</span></span></div></div>' : '') + '</div></div></div>';
            dw = $(html);

            scrollToPos();

            event('onMarkupReady', [dw]);

            // Show
            if (s.display != 'inline') {
                dw.appendTo('body');
                if (anim && !prevAnim) {
                    dw.addClass('dw-trans');
                    // Remove animation class
                    setTimeout(function () {
                        dw.removeClass('dw-trans').find('.dw').removeClass(mAnim);
                    }, 350);
                }
            } else if (elm.is('div')) {
                elm.html(dw);
            } else {
                dw.insertAfter(elm);
            }

            event('onMarkupInserted', [dw]);

            visible = true;

            // Theme init
            theme.init(dw, that);

            if (s.display != 'inline') {
                // Init buttons
                that.tap($('.dwb-s span', dw), function () {
                    that.select();
                });

                that.tap($('.dwb-c span', dw), function () {
                    that.cancel();
                });

                if (s.button3) {
                    that.tap($('.dwb-n span', dw), s.button3);
                }

                // Enter / ESC
                $(window).on('keydown.dw', function (e) {
                    if (e.keyCode == 13) {
                        that.select();
                    } else if (e.keyCode == 27) {
                        that.cancel();
                    }
                });

                // Prevent scroll if not specified otherwise
                if (s.scrollLock) {
                    dw.on('touchmove touchstart', function (e) {
                        if (lock) {
                            e.preventDefault();
                        }
                    });
                }

                // Disable inputs to prevent bleed through (Android bug) and set autocomplete to off (for Firefox)
                $('input,select,button').each(function () {
                    if (!this.disabled) {
                        if ($(this).attr('autocomplete')) {
                            $(this).data('autocomplete', $(this).attr('autocomplete'));
                        }
                        $(this).addClass('dwtd').prop('disabled', true).attr('autocomplete', 'off');
                    }
                });

                // Set position
                that.position();
                $(window).on('orientationchange.dw resize.dw scroll.dw', function (e) {
                    // Sometimes scrollTop is not correctly set, so we wait a little
                    clearTimeout(debounce);
                    debounce = setTimeout(function () {
                        var scroll = e.type == 'scroll';
                        if ((scroll && lock) || !scroll) {
                            that.position(!scroll);
                        }
                    }, 100);
                });

                that.alert(s.ariaDesc);
            }

            // Events
            $('.dwwl', dw)
                .on('DOMMouseScroll mousewheel', onScroll)
                .on(START_EVENT, onStart)
                .on('keydown', onKeyDown)
                .on('keyup', onKeyUp);

            dw.on(START_EVENT, '.dwb-e', onBtnStart).on('keydown', '.dwb-e', function (e) {
                if (e.keyCode == 32) { // Space
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).click();
                }
            });

            event('onShow', [dw, v]);
        };

        /**
         * Hides the scroller instance.
         */
        that.hide = function (prevAnim, btn) {
            // If onClose handler returns false, prevent hide
            if (!visible || event('onClose', [v, btn]) === false) {
                return false;
            }

            // Re-enable temporary disabled fields
            $('.dwtd').each(function () {
                $(this).prop('disabled', false).removeClass('dwtd');
                if ($(this).data('autocomplete')) {
                    $(this).attr('autocomplete', $(this).data('autocomplete'));
                } else {
                    $(this).removeAttr('autocomplete');
                }
            });

            // Hide wheels and overlay
            if (dw) {
                if (s.display != 'inline' && anim && !prevAnim) {
                    dw.addClass('dw-trans').find('.dw').addClass('dw-' + anim + ' dw-out');
                    setTimeout(function () {
                        dw.remove();
                        dw = null;
                    }, 350);
                } else {
                    dw.remove();
                    dw = null;
                }

                // Stop positioning on window resize
                $(window).off('.dw');
            }

            pixels = {};
            visible = false;
            preventShow = true;

            elm.focus();
        };

        that.select = function () {
            if (that.hide(false, 'set') !== false) {
                setVal(true, 0, true);
                event('onSelect', [that.val]);
            }
        };

        that.alert = function (txt) {
            aria.text(txt);
            clearTimeout(alertTimer);
            alertTimer = setTimeout(function () {
                aria.text('');
            }, 5000);
        };

        /**
         * Cancel and hide the scroller instance.
         */
        that.cancel = function () {
            if (that.hide(false, 'cancel') !== false) {
                event('onCancel', [that.val]);
            }
        };

        /**
         * Scroller initialization.
         */
        that.init = function (ss) {
            // Get theme defaults
            theme = extend({ defaults: {}, init: empty }, ms.themes[ss.theme || s.theme]);

            // Get language defaults
            lang = ms.i18n[ss.lang || s.lang];

            extend(settings, ss); // Update original user settings
            extend(s, theme.defaults, lang, settings);

            that.settings = s;

            // Unbind all events (if re-init)
            elm.off('.dw');

            var preset = ms.presets[s.preset];

            if (preset) {
                pres = preset.call(e, that);
                extend(s, pres, settings); // Load preset settings
            }

            // Set private members
            m = Math.floor(s.rows / 2);
            hi = s.height;
            anim = s.animate;

            if (visible) {
                that.hide();
            }

            if (s.display == 'inline') {
                that.show();
            } else {
                read();
                if (input) {
                    // Set element readonly, save original state
                    if (readOnly === undefined) {
                        readOnly = e.readOnly;
                    }
                    e.readOnly = true;
                    // Init show datewheel
                    if (s.showOnFocus) {
                        elm.on('focus.dw', function () {
                            if (!preventShow) {
                                that.show();
                            }
                            preventShow = false;
                        });
                    }
                }
                if (s.showOnTap) {
                    that.tap(elm, function () {
                        that.show();
                    });
                }
            }
        };

        that.trigger = function (name, params) {
            return event(name, params);
        };

        that.option = function (opt, value) {
            var obj = {};
            if (typeof opt === 'object') {
                obj = opt;
            } else {
                obj[opt] = value;
            }
            that.init(obj);
        };

        that.destroy = function () {
            that.hide();
            elm.off('.dw');
            delete scrollers[e.id];
            if (input) {
                e.readOnly = readOnly;
            }
        };

        that.getInst = function () {
            return that;
        };

        that.values = null;
        that.val = null;
        that.temp = null;
        that._selectedValues = {};

        that.init(settings);
    }

    function testProps(props) {
        var i;
        for (i in props) {
            if (mod[props[i]] !== undefined) {
                return true;
            }
        }
        return false;
    }

    function testPrefix() {
        var prefixes = ['Webkit', 'Moz', 'O', 'ms'],
            p;

        for (p in prefixes) {
            if (testProps([prefixes[p] + 'Transform'])) {
                return '-' + prefixes[p].toLowerCase();
            }
        }
        return '';
    }

    function testTouch(e) {
        if (e.type === 'touchstart') {
            touch = true;
            /*setTimeout(function () {
             touch = false; // Reset if mouse event was not fired
             }, 500);*/
        } else if (touch) {
            touch = false;
            return false;
        }
        return true;
    }

    function getCoord(e, c) {
        var org = e.originalEvent,
            ct = e.changedTouches;
        return ct || (org && org.changedTouches) ? (org ? org.changedTouches[0]['page' + c] : ct[0]['page' + c]) : e['page' + c];

    }

    function constrain(val, min, max) {
        return Math.max(min, Math.min(val, max));
    }

    function convert(w) {
        var ret = {
            values: [],
            keys: []
        };
        $.each(w, function (k, v) {
            ret.keys.push(k);
            ret.values.push(v);
        });
        return ret;
    }

    function init(that, options, args) {
        var ret = that;

        // Init
        if (typeof options === 'object') {
            return that.each(function () {
                if (!this.id) {
                    uuid += 1;
                    this.id = 'mobiscroll' + uuid;
                }
                scrollers[this.id] = new Scroller(this, options);
            });
        }

        // Method call
        if (typeof options === 'string') {
            that.each(function () {
                var r,
                    inst = scrollers[this.id];

                if (inst && inst[options]) {
                    r = inst[options].apply(this, Array.prototype.slice.call(args, 1));
                    if (r !== undefined) {
                        ret = r;
                        return false;
                    }
                }
            });
        }

        return ret;
    }

    var move,
        tap,
        touch,
        alertTimer,
        aria,
        date = new Date(),
        uuid = date.getTime(),
        scrollers = {},
        empty = function () {
        },
        mod = document.createElement('modernizr').style,
        has3d = testProps(['perspectiveProperty', 'WebkitPerspective', 'MozPerspective', 'OPerspective', 'msPerspective']),
        prefix = testPrefix(),
        pr = prefix.replace(/^\-/, '').replace('moz', 'Moz'),
        extend = $.extend,
        START_EVENT = 'touchstart mousedown',
        MOVE_EVENT = 'touchmove mousemove',
        END_EVENT = 'touchend mouseup',
        defaults = {
            // Options
            width: 70,
            height: 40,
            rows: 3,
            delay: 300,
            disabled: false,
            readonly: false,
            showOnFocus: true,
            showOnTap: true,
            showLabel: true,
            wheels: [],
            theme: 'ios',
            headerText: '{value}',
            display: 'bottom',
            mode: 'scroller',
            preset: '',
            lang: 'zh',
            setText: 'Set',
            cancelText: 'Cancel',
            ariaDesc: 'Select a value',
            scrollLock: true,
            tap: true,
            speedUnit: 0.0012,
            timeUnit: 0.1,
            formatResult: function (d) {
                return d.join(' ');
            },
            parseValue: function (value, inst) {
                var val = value.split(' '),
                    ret = [],
                    i = 0,
                    keys;

                $.each(inst.settings.wheels, function (j, wg) {
                    $.each(wg, function (k, w) {
                        w = w.values ? w : convert(w);
                        keys = w.keys || w.values;
                        if ($.inArray(val[i], keys) !== -1) {
                            ret.push(val[i]);
                        } else {
                            ret.push(keys[0]);
                        }
                        i++;
                    });
                });
                return ret;
            }
        };

    $(function () {
        aria = $('<div class="dw-hidden" role="alert"></div>').appendTo('body');
    });

    $(document).on('mouseover mouseup mousedown click', function (e) { // Prevent standard behaviour on body click
        if (tap) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        }
    });

    $.fn.mobiscroll = function (method) {
        extend(this, $.mobiscroll.shorts);
        return init(this, method, arguments);
    };

    $.mobiscroll = $.mobiscroll || {
        /**
         * Set settings for all instances.
         * @param {Object} o - New default settings.
         */
        setDefaults: function (o) {
            extend(defaults, o);
        },
        presetShort: function (name) {
            this.shorts[name] = function (method) {
                return init(this, extend(method, { preset: name }), arguments);
            };
        },
        has3d: has3d,
        shorts: {},
        presets: {},
        themes: {
            'ios': {
                defaults: {
                    dateOrder: 'yyMMd',
                    rows: 5,
                    height: 30,
                    width: 55,
                    headerText: false,
                    showLabel: false,
                    useShortLabels: true
                }
            }
        },
        i18n: {
            'zh': {
                // Core
                setText: '确定',
                cancelText: '取消',
                // Datetime component
                dateFormat: 'yy年mm月dd日',
                dateOrder: 'yymmdd',
                dayNames: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
                dayNamesShort: ['日', '一', '二', '三', '四', '五', '六'],
                dayText: '日',
                hourText: '时',
                minuteText: '分',
                monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
                monthNamesShort: ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二'],
                monthText: '月',
                secText: '秒',
                timeFormat: 'HH:ii',
                timeWheels: 'HHii',
                yearText: '年',
                nowText: '当前',
                // Calendar component
                dateText: '日',
                timeText: '时间',
                calendarText: '日历',
                // Measurement components
                wholeText: 'Whole',
                fractionText: 'Fraction',
                unitText: 'Unit',
                // Time / Timespan component
                labels: ['Years', 'Months', 'Days', 'Hours', 'Minutes', 'Seconds', ''],
                labelsShort: ['Yrs', 'Mths', 'Days', 'Hrs', 'Mins', 'Secs', ''],
                // Timer component
                startText: 'Start',
                stopText: 'Stop',
                resetText: 'Reset',
                lapText: 'Lap',
                hideText: 'Hide'
            }
        }
    };

    $.scroller = $.scroller || $.mobiscroll;
    $.fn.scroller = $.fn.scroller || $.fn.mobiscroll;

})(window, jQuery);
(function ($) {

    var defaults = {
        inputClass: '',
        invalid: [],
        rtl: false,
        group: false,
        groupLabel: 'Groups'
    };

    $.mobiscroll.presetShort('select');

    $.mobiscroll.presets.select = function (inst) {
        var orig = $.extend({}, inst.settings),
            s = $.extend(inst.settings, defaults, orig),
            elm = $(this),
            multiple = elm.prop('multiple'),
            id = this.id + '_dummy',
            option = multiple ? (elm.val() ? elm.val()[0] : $('option', elm).attr('value')) : elm.val(),
            group = elm.find('option[value="' + option + '"]').parent(),
            prev = group.index() + '',
            gr = prev,
            prevent,
            l1 = $('label[for="' + this.id + '"]').attr('for', id),
            l2 = $('label[for="' + id + '"]'),
            label = s.label !== undefined ? s.label : (l2.length ? l2.text() : elm.attr('name')),
            invalid = [],
            origValues = [],
            main = {},
            grIdx,
            optIdx,
            timer,
            input,
            roPre = s.readonly,
            w;

        function genWheels() {
            var cont,
                wg = 0,
                values = [],
                keys = [],
                w = [
                    []
                ];

            if (s.group) {
                if (s.rtl) {
                    wg = 1;
                }

                $('optgroup', elm).each(function (i) {
                    values.push($(this).attr('label'));
                    keys.push(i);
                });

                w[wg] = [
                    {
                        values: values,
                        keys: keys,
                        label: s.groupLabel
                    }
                ];

                cont = group;
                wg += (s.rtl ? -1 : 1);

            } else {
                cont = elm;
            }

            values = [];
            keys = [];

            $('option', cont).each(function () {
                var v = $(this).attr('value');
                values.push($(this).text());
                keys.push(v);
                if ($(this).prop('disabled')) {
                    invalid.push(v);
                }
            });

            w[wg] = [
                {
                    values: values,
                    keys: keys,
                    label: label
                }
            ];

            return w;
        }

        function setVal(v, fill) {
            var value = [];

            if (multiple) {
                var sel = [],
                    i = 0;

                for (i in inst._selectedValues) {
                    sel.push(main[i]);
                    value.push(i);
                }
                input.val(sel.join(', '));
            } else {
                input.val(v);
                value = fill ? inst.values[optIdx] : null;
            }

            if (fill) {
                prevent = true;
                elm.val(value).trigger('change');
            }
        }

        function onTap(li) {
            if (multiple && li.hasClass('dw-v') && li.closest('.dw').find('.dw-ul').index(li.closest('.dw-ul')) == optIdx) {
                var val = li.attr('data-val'),
                    selected = li.hasClass('dw-msel');

                if (selected) {
                    li.removeClass('dw-msel').removeAttr('aria-selected');
                    delete inst._selectedValues[val];
                } else {
                    li.addClass('dw-msel').attr('aria-selected', 'true');
                    inst._selectedValues[val] = val;
                }

                if (s.display == 'inline') {
                    setVal(val, true);
                }
                return false;
            }
        }

        // if groups is true and there are no groups fall back to no grouping
        if (s.group && !$('optgroup', elm).length) {
            s.group = false;
        }

        if (!s.invalid.length) {
            s.invalid = invalid;
        }

        if (s.group) {
            if (s.rtl) {
                grIdx = 1;
                optIdx = 0;
            } else {
                grIdx = 0;
                optIdx = 1;
            }
        } else {
            grIdx = -1;
            optIdx = 0;
        }

        $('#' + id).remove();

        input = $('<input type="text" id="' + id + '" class="' + s.inputClass + '" readonly />').insertBefore(elm),

            $('option', elm).each(function () {
                main[$(this).attr('value')] = $(this).text();
            });

        if (s.showOnFocus) {
            input.focus(function () {
                inst.show();
            });
        }

        if (s.showOnTap) {
            inst.tap(input, function () {
                inst.show();
            });
        }

        var v = elm.val() || [],
            i = 0;

        for (i; i < v.length; i++) {
            inst._selectedValues[v[i]] = v[i];
        }

        setVal(main[option]);

        elm.off('.dwsel').on('change.dwsel',function () {
            if (!prevent) {
                inst.setValue(multiple ? elm.val() || [] : [elm.val()], true);
            }
            prevent = false;
        }).hide().closest('.ui-field-contain').trigger('create');

        // Extended methods
        // ---

        if (!inst._setValue) {
            inst._setValue = inst.setValue;
        }

        inst.setValue = function (d, fill, time, noscroll, temp) {
            var value,
                v = $.isArray(d) ? d[0] : d;

            option = v !== undefined ? v : $('option', elm).attr('value');

            if (multiple) {
                inst._selectedValues = {};
                var i = 0;
                for (i; i < d.length; i++) {
                    inst._selectedValues[d[i]] = d[i];
                }
            }

            if (s.group) {
                group = elm.find('option[value="' + option + '"]').parent();
                gr = group.index();
                value = s.rtl ? [option, group.index()] : [group.index(), option];
                if (gr !== prev) { // Need to regenerate wheels, if group changed
                    s.wheels = genWheels();
                    inst.changeWheel([optIdx]);
                    prev = gr + '';
                }
            } else {
                value = [option];
            }

            inst._setValue(value, fill, time, noscroll, temp);

            // Set input/select values
            if (fill) {
                var changed = multiple ? true : option !== elm.val();
                setVal(main[option], changed);
            }
        };

        inst.getValue = function (temp) {
            var val = temp ? inst.temp : inst.values;
            return val[optIdx];
        };

        // ---

        return {
            width: 50,
            wheels: w,
            headerText: false,
            multiple: multiple,
            anchor: input,
            formatResult: function (d) {
                return main[d[optIdx]];
            },
            parseValue: function () {
                var v = elm.val() || [],
                    i = 0;

                if (multiple) {
                    inst._selectedValues = {};
                    for (i; i < v.length; i++) {
                        inst._selectedValues[v[i]] = v[i];
                    }
                }

                option = multiple ? (elm.val() ? elm.val()[0] : $('option', elm).attr('value')) : elm.val();

                group = elm.find('option[value="' + option + '"]').parent();
                gr = group.index();
                prev = gr + '';
                return s.group && s.rtl ? [option, gr] : s.group ? [gr, option] : [option];
            },
            validate: function (dw, i, time) {
                if (i === undefined && multiple) {
                    var v = inst._selectedValues,
                        j = 0;

                    $('.dwwl' + optIdx + ' .dw-li', dw).removeClass('dw-msel').removeAttr('aria-selected');

                    for (j in v) {
                        $('.dwwl' + optIdx + ' .dw-li[data-val="' + v[j] + '"]', dw).addClass('dw-msel').attr('aria-selected', 'true');
                    }
                }

                if (i === grIdx) {
                    gr = inst.temp[grIdx];
                    if (gr !== prev) {
                        group = elm.find('optgroup').eq(gr);
                        gr = group.index();
                        option = group.find('option').eq(0).val();
                        option = option || elm.val();
                        s.wheels = genWheels();
                        if (s.group) {
                            inst.temp = s.rtl ? [option, gr] : [gr, option];
                            s.readonly = [s.rtl, !s.rtl];
                            clearTimeout(timer);
                            timer = setTimeout(function () {
                                inst.changeWheel([optIdx]);
                                s.readonly = roPre;
                                prev = gr + '';
                            }, time * 1000);
                            return false;
                        }
                    } else {
                        s.readonly = roPre;
                    }
                } else {
                    option = inst.temp[optIdx];
                }

                var t = $('.dw-ul', dw).eq(optIdx);
                $.each(s.invalid, function (i, v) {
                    $('.dw-li[data-val="' + v + '"]', t).removeClass('dw-v');
                });
            },
            onBeforeShow: function (dw) {
                s.wheels = genWheels();
                if (s.group) {
                    inst.temp = s.rtl ? [option, group.index()] : [group.index(), option];
                }
            },
            onMarkupReady: function (dw) {
                $('.dwwl' + grIdx, dw).on('mousedown touchstart', function () {
                    clearTimeout(timer);
                });
                if (multiple) {
                    dw.addClass('dwms');
                    $('.dwwl', dw).eq(optIdx).addClass('dwwms').attr('aria-multiselectable', 'true');
                    $('.dwwl', dw).on('keydown', function (e) {
                        if (e.keyCode == 32) { // Space
                            e.preventDefault();
                            e.stopPropagation();
                            onTap($('.dw-sel', this));
                        }
                    });
                    origValues = {};
                    var i;
                    for (i in inst._selectedValues) {
                        origValues[i] = inst._selectedValues[i];
                    }
                }
            },
            onValueTap: onTap,
            onSelect: function (v) {
                setVal(v, true);
                if (s.group) {
                    inst.values = null;
                }
            },
            onCancel: function () {
                if (s.group) {
                    inst.values = null;
                }
                if (multiple) {
                    inst._selectedValues = {};
                    var i;
                    for (i in origValues) {
                        inst._selectedValues[i] = origValues[i];
                    }
                }
            },
            onChange: function (v) {
                if (s.display == 'inline' && !multiple) {
                    input.val(v);
                    prevent = true;
                    elm.val(inst.temp[optIdx]).trigger('change');
                }
            },
            onClose: function () {
                input.blur();
            }
        };
    };

})(jQuery);
(function ($) {

    var ms = $.mobiscroll,
        date = new Date(),
        defaults = {
            dateFormat: 'mm/dd/yy',
            dateOrder: 'mmddy',
            timeWheels: 'hhiiA',
            timeFormat: 'hh:ii A',
            startYear: date.getFullYear() - 100,
            endYear: date.getFullYear() + 1,
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            shortYearCutoff: '+10',
            monthText: 'Month',
            dayText: 'Day',
            yearText: 'Year',
            hourText: 'Hours',
            minuteText: 'Minutes',
            secText: 'Seconds',
            ampmText: '&nbsp;',
            nowText: 'Now',
            showNow: false,
            stepHour: 1,
            stepMinute: 1,
            stepSecond: 1,
            separator: ' '
        },
        preset = function (inst) {
            var that = $(this),
                html5def = {},
                format;
            // Force format for html5 date inputs (experimental)
            if (that.is('input')) {
                switch (that.attr('type')) {
                    case 'date':
                        format = 'yy-mm-dd';
                        break;
                    case 'datetime':
                        format = 'yy-mm-ddTHH:ii:ssZ';
                        break;
                    case 'datetime-local':
                        format = 'yy-mm-ddTHH:ii:ss';
                        break;
                    case 'month':
                        format = 'yy-mm';
                        html5def.dateOrder = 'mmyy';
                        break;
                    case 'time':
                        format = 'HH:ii:ss';
                        break;
                }
                // Check for min/max attributes
                var min = that.attr('min'),
                    max = that.attr('max');
                if (min) {
                    html5def.minDate = ms.parseDate(format, min);
                }
                if (max) {
                    html5def.maxDate = ms.parseDate(format, max);
                }
            }

            // Set year-month-day order
            var i,
                k,
                keys,
                values,
                wg,
                start,
                end,
                orig = $.extend({}, inst.settings),
                s = $.extend(inst.settings, defaults, html5def, orig),
                offset = 0,
                wheels = [],
                ord = [],
                o = {},
                f = { y: 'getFullYear', m: 'getMonth', d: 'getDate', h: getHour, i: getMinute, s: getSecond, a: getAmPm },
                p = s.preset,
                dord = s.dateOrder,
                tord = s.timeWheels,
                regen = dord.match(/D/),
                ampm = tord.match(/a/i),
                hampm = tord.match(/h/),
                hformat = p == 'datetime' ? s.dateFormat + s.separator + s.timeFormat : p == 'time' ? s.timeFormat : s.dateFormat,
                defd = new Date(),
                stepH = s.stepHour,
                stepM = s.stepMinute,
                stepS = s.stepSecond,
                mind = s.minDate || new Date(s.startYear, 0, 1),
                maxd = s.maxDate || new Date(s.endYear, 11, 31, 23, 59, 59);

            format = format || hformat;

            if (p.match(/date/i)) {

                // Determine the order of year, month, day wheels
                $.each(['y', 'm', 'd'], function (j, v) {
                    i = dord.search(new RegExp(v, 'i'));
                    if (i > -1) {
                        ord.push({ o: i, v: v });
                    }
                });
                ord.sort(function (a, b) {
                    return a.o > b.o ? 1 : -1;
                });
                $.each(ord, function (i, v) {
                    o[v.v] = i;
                });

                wg = [];
                for (k = 0; k < 3; k++) {
                    if (k == o.y) {
                        offset++;
                        values = [];
                        keys = [];
                        start = mind.getFullYear();
                        end = maxd.getFullYear();
                        for (i = start; i <= end; i++) {
                            keys.push(i);
                            values.push(dord.match(/yy/i) ? i : (i + '').substr(2, 2));
                        }
                        addWheel(wg, keys, values, s.yearText);
                    } else if (k == o.m) {
                        offset++;
                        values = [];
                        keys = [];
                        for (i = 0; i < 12; i++) {
                            var str = dord.replace(/[dy]/gi, '').replace(/mm/, i < 9 ? '0' + (i + 1) : i + 1).replace(/m/, (i + 1));
                            keys.push(i);
                            values.push(str.match(/MM/) ? str.replace(/MM/, '<span class="dw-mon">' + s.monthNames[i] + '</span>') : str.replace(/M/, '<span class="dw-mon">' + s.monthNamesShort[i] + '</span>'));
                        }
                        addWheel(wg, keys, values, s.monthText);
                    } else if (k == o.d) {
                        offset++;
                        values = [];
                        keys = [];
                        for (i = 1; i < 32; i++) {
                            keys.push(i);
                            values.push(dord.match(/dd/i) && i < 10 ? '0' + i : i);
                        }
                        addWheel(wg, keys, values, s.dayText);
                    }
                }
                wheels.push(wg);
            }

            if (p.match(/time/i)) {

                // Determine the order of hours, minutes, seconds wheels
                ord = [];
                $.each(['h', 'i', 's', 'a'], function (i, v) {
                    i = tord.search(new RegExp(v, 'i'));
                    if (i > -1) {
                        ord.push({ o: i, v: v });
                    }
                });
                ord.sort(function (a, b) {
                    return a.o > b.o ? 1 : -1;
                });
                $.each(ord, function (i, v) {
                    o[v.v] = offset + i;
                });

                wg = [];
                for (k = offset; k < offset + 4; k++) {
                    if (k == o.h) {
                        offset++;
                        values = [];
                        keys = [];
                        for (i = 0; i < (hampm ? 12 : 24); i += stepH) {
                            keys.push(i);
                            values.push(hampm && i == 0 ? 12 : tord.match(/hh/i) && i < 10 ? '0' + i : i);
                        }
                        addWheel(wg, keys, values, s.hourText);
                    } else if (k == o.i) {
                        offset++;
                        values = [];
                        keys = [];
                        for (i = 0; i < 60; i += stepM) {
                            keys.push(i);
                            values.push(tord.match(/ii/) && i < 10 ? '0' + i : i);
                        }
                        addWheel(wg, keys, values, s.minuteText);
                    } else if (k == o.s) {
                        offset++;
                        values = [];
                        keys = [];
                        for (i = 0; i < 60; i += stepS) {
                            keys.push(i);
                            values.push(tord.match(/ss/) && i < 10 ? '0' + i : i);
                        }
                        addWheel(wg, keys, values, s.secText);
                    } else if (k == o.a) {
                        offset++;
                        var upper = tord.match(/A/);
                        addWheel(wg, [0, 1], upper ? ['AM', 'PM'] : ['am', 'pm'], s.ampmText);
                    }
                }

                wheels.push(wg);
            }

            function get(d, i, def) {
                if (o[i] !== undefined) {
                    return +d[o[i]];
                }
                if (def !== undefined) {
                    return def;
                }
                return defd[f[i]] ? defd[f[i]]() : f[i](defd);
            }

            function addWheel(wg, k, v, lbl) {
                wg.push({
                    values: v,
                    keys: k,
                    label: lbl
                });
            }

            function step(v, st) {
                return Math.floor(v / st) * st;
            }

            function getHour(d) {
                var hour = d.getHours();
                hour = hampm && hour >= 12 ? hour - 12 : hour;
                return step(hour, stepH);
            }

            function getMinute(d) {
                return step(d.getMinutes(), stepM);
            }

            function getSecond(d) {
                return step(d.getSeconds(), stepS);
            }

            function getAmPm(d) {
                return ampm && d.getHours() > 11 ? 1 : 0;
            }

            function getDate(d) {
                var hour = get(d, 'h', 0);
                return new Date(get(d, 'y'), get(d, 'm'), get(d, 'd', 1), get(d, 'a') ? hour + 12 : hour, get(d, 'i', 0), get(d, 's', 0));
            }

            // Extended methods
            // ---

            /**
             * Sets the selected date
             *
             * @param {Date} d Date to select.
             * @param {Boolean} [fill=false] Also set the value of the associated input element. Default is true.
             * @return {Object} jQuery object to maintain chainability
             */
            inst.setDate = function (d, fill, time, temp) {
                var i;

                // Set wheels
                for (i in o) {
                    inst.temp[o[i]] = d[f[i]] ? d[f[i]]() : f[i](d);
                }
                inst.setValue(inst.temp, fill, time, temp);
            };

            /**
             * Returns the currently selected date.
             *
             * @param {Boolean} [temp=false] If true, return the currently shown date on the picker, otherwise the last selected one
             * @return {Date}
             */
            inst.getDate = function (temp) {
                return getDate(temp ? inst.temp : inst.values);
            };

            // ---

            return {
                button3Text: s.showNow ? s.nowText : undefined,
                button3: s.showNow ? function () {
                    inst.setDate(new Date(), false, 0.3, true);
                } : undefined,
                wheels: wheels,
                headerText: function (v) {
                    return ms.formatDate(hformat, getDate(inst.temp), s);
                },
                /**
                 * Builds a date object from the wheel selections and formats it to the given date/time format
                 * @param {Array} d - An array containing the selected wheel values
                 * @return {String} - The formatted date string
                 */
                formatResult: function (d) {
                    return ms.formatDate(format, getDate(d), s);
                },
                /**
                 * Builds a date object from the input value and returns an array to set wheel values
                 * @return {Array} - An array containing the wheel values to set
                 */
                parseValue: function (val) {
                    var d = new Date(),
                        i,
                        result = [];
                    try {
                        d = ms.parseDate(format, val, s);
                    } catch (e) {
                    }
                    // Set wheels
                    for (i in o) {
                        result[o[i]] = d[f[i]] ? d[f[i]]() : f[i](d);
                    }
                    return result;
                },
                /**
                 * Validates the selected date to be in the minDate / maxDate range and sets unselectable values to disabled
                 * @param {Object} dw - jQuery object containing the generated html
                 * @param {Integer} [i] - Index of the changed wheel, not set for initial validation
                 */
                validate: function (dw, i) {
                    var temp = inst.temp, //.slice(0),
                        mins = { y: mind.getFullYear(), m: 0, d: 1, h: 0, i: 0, s: 0, a: 0 },
                        maxs = { y: maxd.getFullYear(), m: 11, d: 31, h: step(hampm ? 11 : 23, stepH), i: step(59, stepM), s: step(59, stepS), a: 1 },
                        minprop = true,
                        maxprop = true;
                    $.each(['y', 'm', 'd', 'a', 'h', 'i', 's'], function (x, i) {
                        if (o[i] !== undefined) {
                            var min = mins[i],
                                max = maxs[i],
                                maxdays = 31,
                                val = get(temp, i),
                                t = $('.dw-ul', dw).eq(o[i]),
                                y,
                                m;
                            if (i == 'd') {
                                y = get(temp, 'y');
                                m = get(temp, 'm');
                                maxdays = 32 - new Date(y, m, 32).getDate();
                                max = maxdays;
                                if (regen) {
                                    $('.dw-li', t).each(function () {
                                        var that = $(this),
                                            d = that.data('val'),
                                            w = new Date(y, m, d).getDay(),
                                            str = dord.replace(/[my]/gi, '').replace(/dd/, d < 10 ? '0' + d : d).replace(/d/, d);
                                        $('.dw-i', that).html(str.match(/DD/) ? str.replace(/DD/, '<span class="dw-day">' + s.dayNames[w] + '</span>') : str.replace(/D/, '<span class="dw-day">' + s.dayNamesShort[w] + '</span>'));
                                    });
                                }
                            }
                            if (minprop && mind) {
                                min = mind[f[i]] ? mind[f[i]]() : f[i](mind);
                            }
                            if (maxprop && maxd) {
                                max = maxd[f[i]] ? maxd[f[i]]() : f[i](maxd);
                            }
                            if (i != 'y') {
                                var i1 = $('.dw-li', t).index($('.dw-li[data-val="' + min + '"]', t)),
                                    i2 = $('.dw-li', t).index($('.dw-li[data-val="' + max + '"]', t));
                                $('.dw-li', t).removeClass('dw-v').slice(i1, i2 + 1).addClass('dw-v');
                                if (i == 'd') { // Hide days not in month
                                    $('.dw-li', t).removeClass('dw-h').slice(maxdays).addClass('dw-h');
                                }
                            }
                            if (val < min) {
                                val = min;
                            }
                            if (val > max) {
                                val = max;
                            }
                            if (minprop) {
                                minprop = val == min;
                            }
                            if (maxprop) {
                                maxprop = val == max;
                            }
                            // Disable some days
                            if (s.invalid && i == 'd') {
                                var idx = [];
                                // Disable exact dates
                                if (s.invalid.dates) {
                                    $.each(s.invalid.dates, function (i, v) {
                                        if (v.getFullYear() == y && v.getMonth() == m) {
                                            idx.push(v.getDate() - 1);
                                        }
                                    });
                                }
                                // Disable days of week
                                if (s.invalid.daysOfWeek) {
                                    var first = new Date(y, m, 1).getDay(),
                                        j;
                                    $.each(s.invalid.daysOfWeek, function (i, v) {
                                        for (j = v - first; j < maxdays; j += 7) {
                                            if (j >= 0) {
                                                idx.push(j);
                                            }
                                        }
                                    });
                                }
                                // Disable days of month
                                if (s.invalid.daysOfMonth) {
                                    $.each(s.invalid.daysOfMonth, function (i, v) {
                                        v = (v + '').split('/');
                                        if (v[1]) {
                                            if (v[0] - 1 == m) {
                                                idx.push(v[1] - 1);
                                            }
                                        } else {
                                            idx.push(v[0] - 1);
                                        }
                                    });
                                }
                                $.each(idx, function (i, v) {
                                    $('.dw-li', t).eq(v).removeClass('dw-v');
                                });
                            }

                            // Set modified value
                            temp[o[i]] = val;
                        }
                    });
                }
            };
        };

    $.each(['date', 'time', 'datetime'], function (i, v) {
        ms.presets[v] = preset;
        ms.presetShort(v);
    });

    /**
     * Format a date into a string value with a specified format.
     * @param {String} format - Output format.
     * @param {Date} date - Date to format.
     * @param {Object} settings - Settings.
     * @return {String} - Returns the formatted date string.
     */
    ms.formatDate = function (format, date, settings) {
        if (!date) {
            return null;
        }
        var s = $.extend({}, defaults, settings),
            look = function (m) { // Check whether a format character is doubled
                var n = 0;
                while (i + 1 < format.length && format.charAt(i + 1) == m) {
                    n++;
                    i++;
                }
                return n;
            },
            f1 = function (m, val, len) { // Format a number, with leading zero if necessary
                var n = '' + val;
                if (look(m)) {
                    while (n.length < len) {
                        n = '0' + n;
                    }
                }
                return n;
            },
            f2 = function (m, val, s, l) { // Format a name, short or long as requested
                return (look(m) ? l[val] : s[val]);
            },
            i,
            output = '',
            literal = false;

        for (i = 0; i < format.length; i++) {
            if (literal) {
                if (format.charAt(i) == "'" && !look("'")) {
                    literal = false;
                } else {
                    output += format.charAt(i);
                }
            } else {
                switch (format.charAt(i)) {
                    case 'd':
                        output += f1('d', date.getDate(), 2);
                        break;
                    case 'D':
                        output += f2('D', date.getDay(), s.dayNamesShort, s.dayNames);
                        break;
                    case 'o':
                        output += f1('o', (date.getTime() - new Date(date.getFullYear(), 0, 0).getTime()) / 86400000, 3);
                        break;
                    case 'm':
                        output += f1('m', date.getMonth() + 1, 2);
                        break;
                    case 'M':
                        output += f2('M', date.getMonth(), s.monthNamesShort, s.monthNames);
                        break;
                    case 'y':
                        output += (look('y') ? date.getFullYear() : (date.getYear() % 100 < 10 ? '0' : '') + date.getYear() % 100);
                        break;
                    case 'h':
                        var h = date.getHours();
                        output += f1('h', (h > 12 ? (h - 12) : (h == 0 ? 12 : h)), 2);
                        break;
                    case 'H':
                        output += f1('H', date.getHours(), 2);
                        break;
                    case 'i':
                        output += f1('i', date.getMinutes(), 2);
                        break;
                    case 's':
                        output += f1('s', date.getSeconds(), 2);
                        break;
                    case 'a':
                        output += date.getHours() > 11 ? 'pm' : 'am';
                        break;
                    case 'A':
                        output += date.getHours() > 11 ? 'PM' : 'AM';
                        break;
                    case "'":
                        if (look("'")) {
                            output += "'";
                        } else {
                            literal = true;
                        }
                        break;
                    default:
                        output += format.charAt(i);
                }
            }
        }
        return output;
    };

    /**
     * Extract a date from a string value with a specified format.
     * @param {String} format - Input format.
     * @param {String} value - String to parse.
     * @param {Object} settings - Settings.
     * @return {Date} - Returns the extracted date.
     */
    ms.parseDate = function (format, value, settings) {
        var def = new Date();

        if (!format || !value) {
            return def;
        }

        value = (typeof value == 'object' ? value.toString() : value + '');

        var s = $.extend({}, defaults, settings),
            shortYearCutoff = s.shortYearCutoff,
            year = def.getFullYear(),
            month = def.getMonth() + 1,
            day = def.getDate(),
            doy = -1,
            hours = def.getHours(),
            minutes = def.getMinutes(),
            seconds = 0, //def.getSeconds(),
            ampm = -1,
            literal = false, // Check whether a format character is doubled
            lookAhead = function (match) {
                var matches = (iFormat + 1 < format.length && format.charAt(iFormat + 1) == match);
                if (matches) {
                    iFormat++;
                }
                return matches;
            },
            getNumber = function (match) { // Extract a number from the string value
                lookAhead(match);
                var size = (match == '@' ? 14 : (match == '!' ? 20 : (match == 'y' ? 4 : (match == 'o' ? 3 : 2)))),
                    digits = new RegExp('^\\d{1,' + size + '}'),
                    num = value.substr(iValue).match(digits);

                if (!num) {
                    return 0;
                }
                //throw 'Missing number at position ' + iValue;
                iValue += num[0].length;
                return parseInt(num[0], 10);
            },
            getName = function (match, s, l) { // Extract a name from the string value and convert to an index
                var names = (lookAhead(match) ? l : s),
                    i;

                for (i = 0; i < names.length; i++) {
                    if (value.substr(iValue, names[i].length).toLowerCase() == names[i].toLowerCase()) {
                        iValue += names[i].length;
                        return i + 1;
                    }
                }
                return 0;
                //throw 'Unknown name at position ' + iValue;
            },
            checkLiteral = function () {
                //if (value.charAt(iValue) != format.charAt(iFormat))
                //throw 'Unexpected literal at position ' + iValue;
                iValue++;
            },
            iValue = 0,
            iFormat;

        for (iFormat = 0; iFormat < format.length; iFormat++) {
            if (literal) {
                if (format.charAt(iFormat) == "'" && !lookAhead("'")) {
                    literal = false;
                } else {
                    checkLiteral();
                }
            } else {
                switch (format.charAt(iFormat)) {
                    case 'd':
                        day = getNumber('d');
                        break;
                    case 'D':
                        getName('D', s.dayNamesShort, s.dayNames);
                        break;
                    case 'o':
                        doy = getNumber('o');
                        break;
                    case 'm':
                        month = getNumber('m');
                        break;
                    case 'M':
                        month = getName('M', s.monthNamesShort, s.monthNames);
                        break;
                    case 'y':
                        year = getNumber('y');
                        break;
                    case 'H':
                        hours = getNumber('H');
                        break;
                    case 'h':
                        hours = getNumber('h');
                        break;
                    case 'i':
                        minutes = getNumber('i');
                        break;
                    case 's':
                        seconds = getNumber('s');
                        break;
                    case 'a':
                        ampm = getName('a', ['am', 'pm'], ['am', 'pm']) - 1;
                        break;
                    case 'A':
                        ampm = getName('A', ['am', 'pm'], ['am', 'pm']) - 1;
                        break;
                    case "'":
                        if (lookAhead("'")) {
                            checkLiteral();
                        } else {
                            literal = true;
                        }
                        break;
                    default:
                        checkLiteral();
                }
            }
        }
        if (year < 100) {
            year += new Date().getFullYear() - new Date().getFullYear() % 100 +
                (year <= (typeof shortYearCutoff != 'string' ? shortYearCutoff : new Date().getFullYear() % 100 + parseInt(shortYearCutoff, 10)) ? 0 : -100);
        }
        if (doy > -1) {
            month = 1;
            day = doy;
            do {
                var dim = 32 - new Date(year, month - 1, 32).getDate();
                if (day <= dim) {
                    break;
                }
                month++;
                day -= dim;
            } while (true);
        }
        hours = (ampm == -1) ? hours : ((ampm && hours < 12) ? (hours + 12) : (!ampm && hours == 12 ? 0 : hours));
        var date = new Date(year, month - 1, day, hours, minutes, seconds);
        if (date.getFullYear() != year || date.getMonth() + 1 != month || date.getDate() != day) {
            throw 'Invalid date';
        }
        return date;
    };

})(jQuery);

(function ($) {
    var ms = $.mobiscroll,
        defaults = {
            invalid: [],
            showInput: true,
            inputClass: ''
        },
        preset = function (inst) {
            var orig = $.extend({}, inst.settings),
                s = $.extend(inst.settings, defaults, orig),
                elm = $(this),
                input,
                prevent,
                id = this.id + '_dummy',
                lvl = 0,
                ilvl = 0,
                timer = {},
                wa = s.wheelArray || createWheelArray(elm),
                labels = generateLabels(lvl),
                currWheelVector = [],
                fwv = firstWheelVector(wa),
                w = generateWheelsFromVector(fwv, lvl);

            /**
             * Disables the invalid items on the wheels
             * @param {Object} dw - the jQuery mobiscroll object
             * @param {Number} nrWheels - the number of the current wheels
             * @param {Array} whArray - The wheel array objects containing the wheel tree
             * @param {Array} whVector - the wheel vector containing the current keys
             */
            function setDisabled(dw, nrWheels, whArray, whVector) {
                var i = 0;
                while (i < nrWheels) {
                    var currWh = $('.dwwl' + i, dw),
                        inv = getInvalidKeys(whVector, i, whArray);
                    $.each(inv, function (i, v) {
                        $('.dw-li[data-val="' + v + '"]', currWh).removeClass('dw-v');
                    });
                    i++;
                }
            }

            /**
             * Returns the invalid keys of one wheel as an array
             * @param {Array} whVector - the wheel vector used to search for the wheel in the wheel array
             * @param {Number} index - index of the wheel in the wheel vector, that we are interested in
             * @param {Array} whArray - the wheel array we are searching in
             * @return {Array} - list of invalid keys
             */
            function getInvalidKeys(whVector, index, whArray) {
                var i = 0,
                    n,
                    whObjA = whArray,
                    invalids = [];

                while (i < index) {
                    var ii = whVector[i];
                    //whObjA = whObjA[ii].children;
                    for (n in whObjA) {
                        if (whObjA[n].key == ii) {
                            whObjA = whObjA[n].children;
                            break;
                        }
                    }
                    i++;
                }
                i = 0;
                while (i < whObjA.length) {
                    if (whObjA[i].invalid) {
                        invalids.push(whObjA[i].key);
                    }
                    i++;
                }
                return invalids;
            }

            /**
             * Creates a Boolean vector with true values (except one) that can be used as the readonly vector
             * n - the length of the vector
             * i - the index of the value that's going to be false
             */
            function createROVector(n, i) {
                var a = [];
                while (n) {
                    a[--n] = true;
                }
                a[i] = false;
                return a;
            }

            /**
             * Creates a labels vector, from values if they are defined, otherwise from numbers
             * l - the length of the vector
             */
            function generateLabels(l) {
                var a = [],
                    i;
                for (i = 0; i < l; i++) {
                    a[i] = s.labels && s.labels[i] ? s.labels[i] : i;
                }
                return a;
            }

            /**
             * Creates the wheel array from the vector provided
             * wv - wheel vector containing the values that should be selected on the wheels
             * l - the length of the wheel array
             */
            function generateWheelsFromVector(wv, l, index) {
                var i = 0, j, obj, chInd,
                    w = [],
                    wtObjA = wa;

                if (l) { // if length is defined we need to generate that many wheels (even if they are empty)
                    for (j = 0; j < l; j++) {
                        w[j] = [
                            {}
                        ];
                        //w[j] = {};
                        //w[j][labels[j]] = {}; // each wheel will have a label generated by the generateLabels method
                    }
                }
                while (i < wv.length) { // we generate the wheels until the length of the wheel vector
                    //w[i] = {};
                    //w[i][labels[i]] = getWheelFromObjA(wtObjA);
                    w[i] = [getWheelFromObjA(wtObjA, labels[i])];

                    j = 0;
                    chInd = undefined;

                    while (j < wtObjA.length && chInd === undefined) {
                        if (wtObjA[j].key == wv[i] && ((index !== undefined && i <= index) || index === undefined)) {
                            chInd = j;
                        }
                        j++;
                    }

                    if (chInd !== undefined && wtObjA[chInd].children) {
                        i++;
                        wtObjA = wtObjA[chInd].children;
                    } else if ((obj = getFirstValidItemObjOrInd(wtObjA)) && obj.children) {
                        i++;
                        wtObjA = obj.children;
                    } else {
                        return w;
                    }
                }
                return w;
            }

            /**
             * Returns the first valid Wheel Node Object or its index from a Wheel Node Object Array
             * getInd - if it is true then the return value is going to be the index, otherwise the object itself
             */
            function getFirstValidItemObjOrInd(wtObjA, getInd) {
                if (!wtObjA) {
                    return false;
                }

                var i = 0,
                    obj;

                while (i < wtObjA.length) {
                    if (!(obj = wtObjA[i++]).invalid) {
                        return getInd ? i - 1 : obj;
                    }
                }
                return false;
            }

            function getWheelFromObjA(objA, lbl) {
                var wheel = {
                        keys: [],
                        values: [],
                        label: lbl
                    },
                    j = 0;

                while (j < objA.length) {
                    wheel.values.push(objA[j].value);
                    wheel.keys.push(objA[j].key);
                    j++;
                    //wheel[objA[j].key] = objA[j++].value;
                }
                return wheel;
            }

            /**
             * Hides the last i number of wheels
             * i - the last number of wheels that has to be hidden
             */
            function hideWheels(dw, i) {
                $('.dwc', dw).css('display', '').slice(i).hide();
            }

            /**
             * Generates the first wheel vector from the wheeltree
             * wt - the wheel tree object
             * uses the lvl global variable to determine the length of the vector
             */
            function firstWheelVector(wa) {
                var t = [],
                    ndObjA = wa,
                    obj,
                    ok = true,
                    i = 0;

                while (ok) {
                    obj = getFirstValidItemObjOrInd(ndObjA);
                    t[i++] = obj.key;
                    if (ok = obj.children) {
                        ndObjA = obj.children;
                    }
                }
                return t;
            }

            /**
             * Calculates the level of a wheel vector and the new wheel vector, depending on current wheel vector and the index of the changed wheel
             * wv - current wheel vector
             * index - index of the changed wheel
             */
            function calcLevelOfVector2(wv, index) {
                var t = [],
                    ndObjA = wa,
                    lvl = 0,
                    next = false,
                    i,
                    childName,
                    chInd;

                if (wv[lvl] !== undefined && lvl <= index) {
                    i = 0;

                    childName = wv[lvl];
                    chInd = undefined;

                    while (i < ndObjA.length && chInd === undefined) {
                        if (ndObjA[i].key == wv[lvl] && !ndObjA[i].invalid) {
                            chInd = i;
                        }
                        i++;
                    }
                } else {
                    chInd = getFirstValidItemObjOrInd(ndObjA, true);
                    childName = ndObjA[chInd].key;
                }

                next = chInd !== undefined ? ndObjA[chInd].children : false;

                t[lvl] = childName;

                while (next) {
                    ndObjA = ndObjA[chInd].children;
                    lvl++;
                    next = false;
                    chInd = undefined;

                    if (wv[lvl] !== undefined && lvl <= index) {
                        i = 0;

                        childName = wv[lvl];
                        chInd = undefined;

                        while (i < ndObjA.length && chInd === undefined) {
                            if (ndObjA[i].key == wv[lvl] && !ndObjA[i].invalid) {
                                chInd = i;
                            }
                            i++;
                        }
                    } else {
                        chInd = getFirstValidItemObjOrInd(ndObjA, true);
                        chInd = chInd === false ? undefined : chInd;
                        childName = ndObjA[chInd].key;
                    }
                    next = chInd !== undefined && getFirstValidItemObjOrInd(ndObjA[chInd].children) ? ndObjA[chInd].children : false;
                    t[lvl] = childName;
                }
                return {
                    lvl: lvl + 1,
                    nVector: t
                }; // return the calculated level and the wheel vector as an object
            }

            function createWheelArray(ul) {
                var wheelArray = [];

                lvl = lvl > ilvl++ ? lvl : ilvl;

                ul.children('li').each(function (index) {
                    var that = $(this),
                        c = that.clone();

                    c.children('ul,ol').remove();

                    var v = c.html().replace(/^\s\s*/, '').replace(/\s\s*$/, ''),
                        inv = that.data('invalid') ? true : false,
                        wheelObj = {
                            key: that.data('val') || index,
                            value: v,
                            invalid: inv,
                            children: null
                        },
                        nest = that.children('ul,ol');

                    if (nest.length) {
                        wheelObj.children = createWheelArray(nest);
                    }

                    wheelArray.push(wheelObj);
                });

                ilvl--;
                return wheelArray;
            }

            $('#' + id).remove(); // Remove input if exists

            if (s.showInput) {
                input = $('<input type="text" id="' + id + '" value="" class="' + s.inputClass + '" readonly />').insertBefore(elm);
                inst.settings.anchor = input; // give the core the input element for the bubble positioning

                if (s.showOnFocus) {
                    input.focus(function () {
                        inst.show();
                    });
                }

                if (s.showOnTap) {
                    inst.tap(input, function () {
                        inst.show();
                    });
                }
            }

            if (!s.wheelArray) {
                elm.hide().closest('.ui-field-contain').trigger('create');
            }

            return {
                width: 50,
                wheels: w,
                headerText: false,
                onBeforeShow: function (dw) {
                    var t = inst.temp;
                    currWheelVector = t.slice(0);
                    inst.settings.wheels = generateWheelsFromVector(t, lvl, lvl);
                    prevent = true;
                },
                onSelect: function (v, inst) {
                    if (input) {
                        input.val(v);
                    }
                },
                onChange: function (v, inst) {
                    if (input && s.display == 'inline') {
                        input.val(v);
                    }
                },
                onClose: function () {
                    if (input) {
                        input.blur();
                    }
                },
                onShow: function (dw) {
                    $('.dwwl', dw).on('mousedown touchstart', function () {
                        clearTimeout(timer[$('.dwwl', dw).index(this)]);
                    });
                },
                validate: function (dw, index, time) {
                    var t = inst.temp;
                    if ((index !== undefined && currWheelVector[index] != t[index]) || (index === undefined && !prevent)) {
                        inst.settings.wheels = generateWheelsFromVector(t, null, index);
                        var args = [],
                            i = (index || 0) + 1,
                            o = calcLevelOfVector2(t, index);
                        if (index !== undefined) {
                            inst.temp = o.nVector.slice(0);
                        }
                        while (i < o.lvl) {
                            args.push(i++);
                        }
                        hideWheels(dw, o.lvl);
                        currWheelVector = inst.temp.slice(0);
                        if (args.length) {
                            prevent = true;
                            inst.settings.readonly = createROVector(lvl, index);
                            clearTimeout(timer[index]);
                            timer[index] = setTimeout(function () {
                                inst.changeWheel(args);
                                inst.settings.readonly = false;
                            }, time * 1000);
                            return false;
                        }
                        setDisabled(dw, o.lvl, wa, inst.temp);
                    } else {
                        var o = calcLevelOfVector2(t, t.length);
                        setDisabled(dw, o.lvl, wa, t);
                        hideWheels(dw, o.lvl);
                    }
                    prevent = false;
                }
            };
        };

    $.each(['list', 'image', 'treelist'], function (i, v) {
        ms.presets[v] = preset;
        ms.presetShort(v);
    });

})(jQuery);

/*baidu*/
(function (document) {
    var _hmt = _hmt || [];
    (function () {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?0c9af66a7ea367f4a9ba08ccc52b2fa8";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
})(document);



