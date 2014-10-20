$(document).ready(function() {
    var a = new rotate();
    a.init()
});

function rotate() {
    var u = "/Wap/Shakeprize/";
    var d = {
        home: u + "info",
        run: u + "run",
        submit: "/Wap/Shakeprize/submit",
        giveup: "/Wap/Shakeprize/giveUpPrize",
        exchange: "/Wap/Shakeprize/setStatus"
    };
    var l = $.getUrlParam("id");
    var g = $.getUID();
    var o = document.getElementById("audio1");
    var m = document.getElementById("audio2");
	var wxid = window.localStorage.getItem("WXID");
    function a() {
        if (o) {
            if (o.paused) {
                o.play()
            }
        }
    }
    this.init = function() {
        window.localStorage.setItem("is_yao", 0);
        p()
    };

    function p() {
        $.ajax({
            type: "get",
            dataType: "json",
            url: d.home,
            data: {
                id: l,
                token: token,
                wxid: wxid,
                uid: g
            }
        }).done(function(y) {
            console.log(y);
            if (y.ret == 0) {
                var A = y.data;
                if (A.backgroundimage) {
                    $("body").css({
                        "background-image": "url(" + A.backgroundimage + ")"
                    })
                }
                if (A.myRecord) {
                    console.log(A.myRecord);
                    var z = {};
                    var B = {};
                    z.data = B;
                    z.data["prize"] = A.myRecord.prize_name;
                    z.data["pic"] = A.myRecord.pic ? A.myRecord.pic : "";
                    z.data["recordid"] = A.myRecord.rid;
                    z.data["tips"] = A.tips.wintips;
                    console.log(z);
                    x(z);
                    window.WBPage.GAME = {};
                    window.WBPage.GAME.name = A.myRecord.name;
                    window.WBPage.GAME.mobile = A.myRecord.mobile;
                    window.WBPage.GAME.address = A.myRecord.address
                }
                $.setSHARE({
                    title: A.title,
                    desc: A.desc,
                    icon: A.cover_img.image_start,
                    link: window.location.href
                });
                k(A);
                switch (parseInt(y.data.game_status)) {
                    case 0:
                        $(".game-chance").remove();
                        $(".game-start").remove();
                        $(".game-yao").addClass("no-start");
                        var C = v(y.data.start_time * 1000);
                        $(".game-last-times").html('<span style="color: #fff;">开始</span>' + C);
                        $(".page-desc").html(A.content ? A.content : "");
                        break;
                    case 1:
                        var C = v(y.data.end_time * 1000);
                        $(".game-last-times").html('<span style="color: #fff;">结束</span>' + C);
                        $(".page-desc").html(A.content ? A.content : "");
                        break;
                    case 2:
                        $(".game-chance").remove();
                        $(".game-start").remove();
                        $(".game-last-time").remove();
                        $(".game-yao").addClass("endding");
                        $(".page-desc").html(A.tips.endtitle ? A.tips.endtitle : "");
                        break
                }
                r();
                $.RMLOAD()
            } else {
                alert(y.msg);
                $(".loading").html(y.msg)
            }
        }).always(function() {})
    }

    function k(C) {
        $(".shake-title").html(C.title ? C.title : "");
        $(".page-chance").html(C.chance ? C.chance : "0");
        var F = C.prize,
            D = "";
        if (F && $.isArray(F) && F.length > 0) {
            for (var E in F) {
                D += '<li class="clearfix">';
                if (F[E].pic) {
                    D += '<img class="prize-img" src="' + F[E].pic + '">'
                }
                D += '<span class="prize-name">' + F[E].name + "</span>";
                if (parseInt(C.advset.showprizenum) == 1) {
                    D += '<span class="prize-num">' + F[E].number + "个</span>"
                }
                D += "</li>"
            }
            $(".page-prize-list").html(D)
        } else {
            $(".page-prize-lists").hide()
        }
        var B = C.myRecordList,
            z = "";
        if (B && $.isArray(B) && B.length > 0) {
            for (var E in B) {
                z += '<li class="clearfix">';
                z += '<span class="result-time">' + $.formatDate(new Date(B[E].inputtime * 1000), "yyyy-MM-dd") + "</span>";
                z += '<span class="result-prize font-c63535">' + B[E].prize_name + "</span>";
                if (C.validatecode) {
                    if (B[E].status == 2) {
                        z += '<span class="exchange done">已兑奖</span>'
                    } else {
                        if (B[E].status == 1) {
                            z += '<span class="exchange page-exchange" pname="' + B[E].prize_name + '" rid="' + B[E].id + '">兑奖</span>'
                        }
                    }
                }
                z += "</li>"
            }
            $(".page-record-list").html(z);
            n()
        } else {
            $(".page-record-lists").hide()
        }
        var A = C.recordList,
            y = "";
        if (A && $.isArray(A) && A.length > 0 && parseInt(C.advset.displaywinner) == 1) {
            y += '<ul class="xunhuan-item xunhuan-item1 clearfix">';
            for (var E in A) {
                y += "<li>";
                y += '恭喜 <span class="font-c63535">' + A[E].mobile + "</span> 摇中 " + A[E].prize_name;
                y += "</li>"
            }
            y += "</ul>";
            y += '<ul class="xunhuan-item xunhuan-item2 clearfix">';
            for (var E in A) {
                y += "<li>";
                y += '恭喜 <span class="font-c63535">' + A[E].mobile + "</span> 摇中 " + A[E].prize_name;
                y += "</li>"
            }
            y += "</ul>";
            $(".page-all-list").html(y);
            e()
        } else {
            $(".page-all-lists").hide()
        } if (parseInt(C.game_status) == 1) {
            j()
        }
    }

    function v(D) {
        var y = new Date(D).getTime();
        var B = new Date().getTime();
        var F = y - B;
        var H = F / 1000;
        var A = Math.floor(H / 60);
        var G = Math.floor(A / 60);
        var J = Math.floor(G / 24);
        var z = J;
        var I = G % 24;
        var E = A % 60;
        var C = Math.floor(H % 60);
        if (y < B) {
            return ""
        } else {
            return J + "天" + I + "小时" + E + "分钟"
        }
    }

    function e() {
        var y = 1;
        setInterval(function() {
            var z = $(".xunhuan-item1").height();
            $(".xunhuan-item1").css({
                "margin-top": "-" + y + "px"
            });
            y++;
            if (parseInt($(".xunhuan-item1").css("margin-top")) == (0 - z)) {
                $(".xunhuan-item1").remove();
                var A = $(".xunhuan-item2").clone();
                $(".xunhuan-item2").removeClass("xunhuan-item2").addClass("xunhuan-item1");
                $(".xunhuan-item1").after(A);
                y = 1
            }
        }, 100)
    }

    function n() {
        $(".page-exchange").on("click", function() {
            var z = $(this).attr("rid"),
                y = $(this).attr("pname");
            var A = '<div class="get-rs">';
            A += "<div>是否兑换奖品：" + y + "</div>";
            A += '<div><input id="game_code" class="name" type="text" placeholder="* 兑奖密码"></div>';
            A += "</div>";
            A += '<div class="get-rs-btn"><span class="submit-dj">提交并兑换</span></div>';
            $.yslide({
                content: A,
                callback: function() {
                    b(z)
                }
            })
        })
    }

    function b(y) {
        $(".submit-dj").one("click", function() {
            $(".submit-dj").html("正在提交...");
            $.ajax({
                type: "post",
                dataType: "json",
                url: d.exchange,
                data: {
                    id: y,
                    uid: g,
                    gid: l,
                    validatecode: $("#game_code").val()
                }
            }).done(function(z) {
                if (z.ret == 0) {
                    $.yalert({
                        content: "兑奖成功",
                        submit: function() {
                            window.location.reload()
                        }
                    })
                } else {
                    $.yalert({
                        content: z.msg,
                        submit: function() {
                            $.yalert.close();
                            b(y)
                        }
                    })
                }
            }).always(function() {
                $(".submit-dj").html("提交并兑换")
            })
        })
    }

    function j() {
        $(".game-start-btn,.lihua").off("click").on("click", function() {
            $(".wap").hide();
            var z = '<div class="game-blank"><div class="game-run-top"></div><div class="game-run-bottom"></div><div class="game-run-tips">大力摇吧，摇出大奖</div></div>';
            $("body").append(z);
            t();
            c();
            if ($.isIPHONE()) {
                o.load()
            }
            if ($.isANDROID()) {
                o.pause();
                m.pause()
            }
            var y = setInterval(function() {
                var A = window.localStorage.getItem("is_yao");
                if (A == 1) {
                    clearInterval(y);
                    s()
                }
            }, 1000)
        })
    }

    function s() {
        $.checkUID("您需要关注或从聊天窗口进入该应用才能抽奖");
        $.ajax({
            type: "post",
            dataType: "json",
            url: d.run,
            data: {
                id: l,
                uid: g,
                token: token,
                wxid: wxid,

            }
        }).done(function(y) {
            console.log(y);
            if (y.ret == 0) {
                q(y.data);
                window.WBPage.GAME = {};
                window.WBPage.GAME.name = y.data.data.name;
                window.WBPage.GAME.mobile = y.data.data.mobile;
                window.WBPage.GAME.address = y.data.data.address
            } else {
                $.yalert({
                    content: y.msg,
                    submit: function() {
                        $.yalert.close()
                    }
                })
            }
        })
    }

    function t() {
        $(".game-blank").on("click", function() {
            window.localStorage.setItem("is_yao", 1)
        })
    }

    function c() {
        var E = 6000;
        if ($.isANDROID()) {
            E = 1000
        }
        var A = 0;
        var I, H, G, F = 0,
            D = 0,
            C = 0;

        function B(J) {
            var z = J.accelerationIncludingGravity;
            var L = new Date().getTime();
            if ((L - A) > 10) {
                var y = L - A;
                A = L;
                I = z.x;
                H = z.y;
                G = z.z;
                if (window.navigator.userAgent.toLowerCase().indexOf("android") > -1) {
                    I = -I;
                    H = -H;
                    G = -G
                }
                var K = Math.abs(I + H + G - F - D - C) / y * 10000;
                if (K > E) {
                    window.localStorage.setItem("is_yao", 1);
                    a()
                }
                F = I;
                D = H;
                C = G
            }
        }
        if (window.DeviceMotionEvent) {
            window.addEventListener("devicemotion", B, false)
        } else {}
    }

    function q(y) {
        if ($.isIPHONE()) {
            m.load()
        }
        $(".game-run-top").animate({
            marginTop: "-25%"
        }, 300, function() {
            $(".game-run-top").animate({
                marginTop: "0"
            }, 300)
        });
        $(".game-run-bottom").animate({
            marginTop: "50%"
        }, 300, function() {
            $(".game-run-bottom").animate({
                marginTop: "0"
            }, 300, function() {
                m.play();
                if (y.data.prize_type == 0) {
                    h(y.data.tips, y.data.prize)
                } else {
                    x(y)
                }
            })
        })
    }

    function x(y) {
        $.game_sucess_box({
            top_text: y.data.tips,
            prize: y.data.prize,
            img: y.data.pic,
            submit: function() {
                i(y)
            },
            cancel: function() {
                $.yconfirm({
                    content: "放弃将失去该奖励，是否继续放弃？",
                    submit: function() {
                        f(y.data.recordid)
                    },
                    cancel: function() {
                        $.yconfirm.close()
                    }
                })
            }
        })
    }

    function f(y) {
        $.ajax({
            dataType: "json",
            type: "post",
            url: d.giveup,
            data: {
                uid: g,
                wxid: wxid,
                id: y
            }
        }).done(function(z) {
            if (z.ret == 0) {
                window.location.reload()
            } else {
                $.yalert({
                    content: z.msg,
                    submit: function() {
                        $.yalert.close()
                    }
                })
            }
        })
    }

    function i(y) {
        var z = '<div class="get-rs">';
        z += '<div><input id="game_name" class="name weiba-user-name" type="text" placeholder="* 姓名"></div>';
        z += '<div><input id="game_tel" class="mobile weiba-user-mobile" type="tel" placeholder="* 电话号码"></div>';
        z += '<div style="display:none;"><input id="game_address" class="address weiba-user-address" type="text" placeholder="地址"></div>';
        z += "</div>";
        z += '<div class="get-rs-btn"><span class="submit-con">提交并返回</span></div>';
        $.yslide({
            content: z,
            callback: function() {
                $.getUserInfo();
                w(y)
            }
        })
    }

    function w(y) {
        $(".submit-con").one("click", function() {
            $(".submit-con").html("正在提交...");
            $.ajax({
                type: "post",
                dataType: "json",
                url: d.submit,
                data: {
                    id: l,
                    wxid: wxid,
                    rid: y.data.recordid,
                    mobile: $("#game_tel").val(),
                    username: $("#game_name").val(),
                    address: $("#game_address").val()
                }
            }).done(function(z) {
                if (z.ret == 0) {
                    window.location.reload();
                } else {
                    w(y);
                    $.yalert({
                        content: z.msg,
                        submit: function() {
                            $.yalert.close()
                        }
                    })
                }
            }).always(function() {
                $(".submit-con").html("提交并返回")
            })
        })
    }

    function h(B, y) {
        var z = parseInt($(".page-chance").html());
        var A = "";
        A += '<div class="no-prize-back"></div>';
        A += '<div class="no-prize-box">';
        A += '<div class="no-prize-box-title">' + B + "</div>";
        A += '<div class="no-prize-box-con">' + y + "</div>";
        A += '<div class="no-prize-box-btn">再来一次（' + (z - 1) + "）</div>";
        A += "</div>";
        if (!$("div").is(".no-prize-box")) {
            $("body").append(A)
        }
        $(".no-prize-box-btn").on("click", function() {
            $(".alert_w_b").remove();
            window.location.reload()
        })
    }

    function r() {
        var y = $(window).width();
        $(".game-status").height(y - 20)
    }
};