(function(window,$){
    var storage = window.localStorage;
    //进入清楚用户缓存
    //storage.removeItem('UID');
    //storage.removeItem('MYUID');
    //storage.removeItem('WXID');

    var getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    };

    var redirect = function(url){
        //芝麻开门实现，另外逻辑在weiba.js
        if(!url){
            url = storage.getItem('weiba.history');
            if(!url){
                url = '/';
            }
        }

        url = decodeURIComponent(url);

        if(window.history.replaceState){
            var new_url = url.replace('http://','').toLowerCase();
            if(new_url.indexOf(location.host)==0){
                window.history.replaceState({},'',url);
            }
        }
        window.location.replace(url);
    };

    var checkWXID = function(wxid){
        if(typeof wxid === 'string'){
            return (wxid.length>20);
        }
        else{
            return false;
        }
    };

    $get_url = getUrlParam('url');
    $get_wxid = decodeURIComponent(getUrlParam('wxid'));

    if($get_wxid){
        if(checkWXID($get_wxid) && window.localStorage){
            storage.setItem('WXID',$get_wxid);
        }
        $.getJSON('./Data/account',{
            'wxid' : $get_wxid
        },function(result){
            if(storage && result && result['ret']==0 && result['data'] && result['data']['id']){
                storage.setItem('MYUID',result['data']['id']);
                storage.setItem('TIME',Date.now());
            }
        }).complete(function(){
            redirect($get_url);
        });
    }
    else{
        redirect($get_url);
    }
})(window,jQuery);
