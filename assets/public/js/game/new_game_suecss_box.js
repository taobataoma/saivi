(function ($) {

    $.game_sucess_box = function (options) {

        var defaults = {
            top_text: '恭喜中奖',
            prize: '',
            img: '',
            mobile: '',
            submit: function () {
            },
            cancel: function () {
            }
        };
        var opt = $.extend(defaults, options);
        var w=$(window).width(),h=$(window).height();
        var box_html = '';
        box_html += '<div class="game-boxs">';
        box_html += '<div class="game-sucess-tip">' + opt.top_text + '</div>';
        box_html += '<div class="game-sucess-prize"><div>' + opt.prize + '</div>';
        if (opt.img) {
            box_html += '<img src="' + opt.img + '">';
        }
        box_html += '</div>';
        box_html += '<div style="text-align: center;">(*领奖以您填写的信息为准，截屏无效)</div>';
        box_html += '<div class="game-sucess-but"><a id="game_sub">填写领奖信息</a><a id="game_fangqi" >放弃领奖</a></div>';
        box_html += '</div>';
        var back_html = '<div class="back_black" style="height:'+h+'px;"></div>';
        $.game_sucess_box.remove_box();
        $('body').append(back_html);
        $('body').append(box_html);



        var gh=$('.game-boxs').height();
        $('.game-boxs').css('margin-top', '-'+gh/2+'px');
        $('#game_sub').off('click').on('click', function () {
            opt.submit();
        })

        $('#game_fangqi').off('click').on('click', function () {
            opt.cancel();
        })


    }
    $.game_sucess_box.remove_box = function () {
        $('.game-boxs').remove();
        $('.back_black').remove();
    }


})(jQuery);