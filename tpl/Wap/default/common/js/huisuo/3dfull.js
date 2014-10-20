var FCAPP = FCAPP || {};
FCAPP.HOUSE = FCAPP.HOUSE || {};
FCAPP.HOUSE.FULL3D = {
    CONFIG: {},
    RUNTIME: {},
    init: function() {
        var R = FULL3D.RUNTIME;
        FULL3D.initElements(R);
        FULL3D.initEvents(R);
        R.support360 = FULL3D.check360Support();
        FULL3D.loadData();
        window.shareData = window.shareData || {};
        window.shareData.linkKeep = '/Webestate/Housedata/pid/'+PID+'/wechatid/'+WECHATID;
        window.shareData.link = window.shareData.linkKeep;
        var id = '';
        if (window.gQuery && gQuery.id) {
            id = gQuery.id;
            window.shareData.link += '&id=' + id;
            window.shareData.linkKeep += '&id=' + id;
        }
        if (window.gQuery && gQuery.houseid) {
            window.shareData.link += '&houseid=' + gQuery.houseid;
            window.shareData.linkKeep += '&houseid=' + gQuery.houseid;
        }
        FCAPP.Common.loadShareData(id);
        FCAPP.Common.hideToolbar();
    },
    go3D: function(url) {
        var t = new Date();
        FCAPP.Common.jumpTo('/Webestate/Picfullshow'+url);
    },
    resizeLayout: function() {
        var R = FULL3D.RUNTIME;
        R.w = document.documentElement.clientWidth;
        R.h = document.documentElement.clientHeight;
        R.whRadio = window.innerWidth > window.innerHeight ? 2 : 1;
        R.mH = R.h - 93;
        if (R.mH < 150) {
            R.mH = 150;
        }
        R.picContainer.css({
            width: R.w + 'px',
            height: R.h + 'px'
        });
        if (R.bgImg) {
            FULL3D.renderBgImg(R.bgImg);
        }
        if (R.linkNums) {
            FULL3D.centerLinks(R);
        }
    },
    centerLinks: function(R) {
        var obj = {
            'min-height': '150px',
            'overflow-y': 'scroll',
            'max-height': R.mH + 'px'
        },
        h = 150,
        w = 20;
        FULL3D.getLW(R);
        w = Math.floor(R.sizes[0].lw / 2);
        if (!R.placeChild) {
            R.placeChild = $('#placeLink ul');
        }
        if (R.sizes.length > 1) {
            var th = 36 * R.sizes.length;
            for (var i = 0,
            il = R.sizes.length; i < il; i++) {
                th += R.sizes[i].lh * 57;
            }
            h = th + 72;
        } else {
            h = R.sizes[0].lh * 57 + 15;
        }
        if (h < 150) {
            h = 150;
        }
        if (h > R.mH) {
            obj['margin-bottom'] = '20px';
            obj['margin-top'] = '20px';
            obj['padding-top'] = '0px';
            obj['height'] = R.mH + 'px';
        } else {
            obj['height'] = h + 'px';
            obj['padding-top'] = Math.floor((R.h - h - 15) / 2) + 'px';
            obj['margin-top'] = '0px';
        }
        R.placeChild.css({
            'margin-left': w + 'px'
        });
        R.placeHold.css(obj);
        obj.h = R.h;
    },
    initElements: function(R) {
        if (!R.placeLink) {
            R.placeLink = $('#placeLink');
            R.placeHold = R.placeLink.parent();
            R.currPlace = $('#currPlace');
            R.currHold = R.currPlace.parent();
            R.frame = $('#full3d');
            R.picContainer = $('#full3dDiv');
            R.popMask = $('#popMask');
            R.closeBtn = $('#closeBtn');
            R.template = FCAPP.Common.escTpl($('#template').html());
            R.rowLinks = [];
        }
    },
    trigResize: function() {
        var R = FULL3D.RUNTIME,
        w = document.documentElement.clientWidth,
        h = document.documentElement.clientHeight;
        if (w != R.w || h != R.h) {
            FULL3D.resizeLayout();
        }
    },
    initEvents: function(R) {
        if (!R.binded) {
            R.binded = true;
            R.closeBtn.click(FULL3D.back2List);
            $(window).on("orientationchange", FULL3D.resizeLayout);
        }
        setInterval(FULL3D.trigResize, 300);
    },
    back2List: function() {
        if (/Mac OS/i.test(navigator.userAgent)) {
            history.back();
        } else {
            var url = 'house.html' + location.search.replace('&houseid=' + gQuery.houseid, '');
            location.href = url;
            WeixinJSBridge.invoke('closeWindow');
        }
    },
    showNotSupport: function() {
        FCAPP.Common.msg(true, {
            msg: '你的手机版本过低，升级至4.0以上可正常使用'
        });
    },
    switchLinks: function() {
        var R = FULL3D.RUNTIME;
        FCAPP.Common.hideLoading();
    },
    hideLoading: function() {
        var R = FULL3D.RUNTIME;
        R.popMask.hide();
        FCAPP.Common.hideLoading();
    },
    loadData: function() {
        window.showRooms = FULL3D.showRooms;
        var datafile = window.gQuery && gQuery.id ? gQuery.id + '.': '',
        dt = new Date();
        datafile = datafile.replace(/[<>\'\"\/\\&#\?\s\r\n]+/gi, '');
        datafile += 'rooms.js?';
        $.ajax({
            //url: 'http://trade.qq.com/fangchan/static/' + datafile + dt.getDate() + dt.getHours(),
            url: '/Webestate/Housedata/pid/'+PID+'/wechatid/'+WECHATID,
            dataType: 'jsonp',
            error: function() {
                FCAPP.Common.msg(true, {
                    msg: '无效的户型！'
                });
            }
        });
    },
    renderBgImg: function(img) {
        var R = FULL3D.RUNTIME,
        pw = R.bgImgW ? R.bgImgW: img.width,
        ph = R.bgImgH ? R.bgImgH: img.height,
        sw = R.w,
        sh = R.h,
        fw = 0,
        fh = 0,
        style = '';
        if (!R.bgImgW) {
            R.bgImgW = pw;
            R.bgImgH = ph;
        }
        if (ph / pw > sh / sw) {
            fw = sw;
            fh = Math.floor(ph * sw / pw);
            style = 'margin:' + Math.floor((sh - fh) / 2) + "px 0;";
        } else {
            fh = sh;
            fw = Math.floor(pw * sh / ph);
            style = 'margin:0 ' + Math.floor((sw - fw) / 2) + "px;";
        }
        R.OP = 0;
        img.width = fw;
        img.height = fh;
        img.style.cssText = style;
        R.bgImg = img;
        if (!R.opacity) {
            R.opacity = 0;
            R.bgInterval = setInterval(FULL3D.alphaBg, 12);
        }
    },
    alphaBg: function() {
        var R = FULL3D.RUNTIME;
        R.opacity += 2;
        if (R.opacity > 100) {
            clearInterval(R.bgInterval);
            FCAPP.Common.hideLoading();
            FULL3D.showFloat();
            delete R.bgInterval;
            R.bgImg.style.opacity = 1;
        } else {
            R.bgImg.style.opacity = R.opacity / 100;
        }
    },
    showRooms: function(data) {
        var R = FULL3D.RUNTIME,
        rooms = [],
        name = false,
        full3d = [];
        if (data.rooms) {
            rooms = data.rooms;
            for (var i = 0, il = rooms.length; i < il; i++) {
                if (rooms[i].id == gQuery.houseid && rooms[i].full3d) {
                    full3d = rooms[i].full3d;
                    R.currPlace.html((rooms[i].desc || '') + '-' + rooms[i].name);
                    window.shareData.desc = '户型【360度全景】-' + rooms[i].desc + rooms[i].name;
                    window.shareData.descKeep = '户型【360度全景】-' + rooms[i].desc + rooms[i].name;
                    break;
                }
            }
            if (full3d.length > 0) {
                FCAPP.Common.loadImg(full3d[0].bimg, 'bgImg', FULL3D.renderBgImg);
                R.full3d = full3d;
                return;
            }
        }
        FCAPP.Common.hideLoading();
        FCAPP.Common.msg(true, {
            msg: '错误户型'
        });
    },
    alphaList: function() {
        var R = FULL3D.RUNTIME,
        content = '';
        R.hold += 2;
        if (R.hold > 100) {
            clearInterval(R.holdInterval);
            delete R.holdInterval;
        }
        R.placeHold.css({
            opacity: R.hold / 100
        });
        R.placeLink.css({
            opacity: R.hold / 100
        });
        R.currHold.css({
            opacity: R.hold / 100
        });
    },
    showFloat: function() {
        var R = FULL3D.RUNTIME,
        full3d = R.full3d,
        content = '',
        maxLinks = 0;
        for (var i = 0,
        il = full3d.length; i < il; i++) {
            R.rowLinks[i] = full3d[i].list.length;
            if (R.rowLinks[i] > maxLinks) {
                maxLinks = R.rowLinks[i];
            }
        }
        content = $.template(R.template, {
            data: full3d,
            f3d: R.support360
        });
        R.placeLink.html(content);
        R.linkNums = maxLinks;
        FULL3D.getLW(R);
        FULL3D.resizeLayout();
        if (!R.hold) {
            R.hold = 0;
            R.holdInterval = setInterval(FULL3D.alphaList, 10);
        }
    },
    getLW: function(R) {
        R.w = document.documentElement.clientWidth;
        R.h = document.documentElement.clientHeight;
        R.sizes = [];
        var links = R.rowLinks,
        uWidth = Math.floor(R.w * 0.95) - 20,
        ln = 0,
        lw = 0,
        lh = 0,
        cnt = 0;
        lw = uWidth - Math.floor(uWidth / 90) * 90 + 30;
        for (var i = 0,
        il = links.length; i < il; i++) {
            ln = links[i];
            cnt = Math.floor(uWidth / 90);
            lh = Math.ceil(ln / cnt);
            R.sizes[i] = {
                lw: lw,
                lh: lh
            };
        }
    },
    hasWebGL: function() {
        var h;
        if (h = !!window.WebGLRenderingContext) try {
            var y = document.createElement("canvas");
            y.width = 100;
            y.height = 100;
            var k = y.getContext("webgl");
            k || (k = y.getContext("experimental-webgl"));
            h = k ? true: false
        } catch(l) {
            h = false
        }
        return h
    },
    hasHtml5Css3D: function() {
        var h = "perspective",
        y = ["Webkit", "Moz", "O", "ms", "Ms"],
        k;
        k = false;
        for (k = 0; k < y.length; k++)"undefined" !== typeof document.documentElement.style[y[k] + "Perspective"] && (h = y[k] + "Perspective");
        "undefined" !== typeof document.documentElement.style[h] ? "webkitPerspective" in document.documentElement.style ? (h = document.createElement("style"), y = document.createElement("div"), k = document.head || document.getElementsByTagName("head")[0], h.textContent = "@media (-webkit-transform-3d) {#ggswhtml5{height:5px}}", k.appendChild(h), y.id = "ggswhtml5", document.documentElement.appendChild(y), k = 5 === y.offsetHeight, h.parentNode.removeChild(h), y.parentNode.removeChild(y)) : k = true: k = false;
        return k
    },
    hasFlash: function() {
        var f = navigator.plugins && navigator.plugins['Shockwave Flash'],
        b = typeof(f) != 'undefined';
        if (b && f.description) {
            b = f.description.replace(/[^\d\.]+/gi, '');
            b = parseFloat(b) > 9.0;
        }
        return b;
    },
    check360Support: function() {
        return FULL3D.hasHtml5Css3D() || FULL3D.hasWebGL() || FULL3D.hasFlash();
    }
};
var FULL3D = FCAPP.HOUSE.FULL3D;
$(document).ready(FULL3D.init);