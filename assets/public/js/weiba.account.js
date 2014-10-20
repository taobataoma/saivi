/**
 * User: suolan
 * Date: 13-8-19
 * Time: 下午5:57
 */
(function(window,$){
    var path_verify = './data/ac_verify';
    var storage = window.localStorage,
        prefix = {
            'uid' : 'MYUID',
            'wxid' : 'WXID',
            'time' : 'TIME'
        };

    if(!storage){
        console.log('系统版本过低，无法正常访问我们的网站，建议您升级浏览器。');
        return;
    }

    var getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }

    var account = window.WBAccount = {
        'wxid':function(){
            var uid = storage.getItem(prefix['wxid']);
            if(uid){
                return uid;
            }
            else{
                return false;
            }
            return !!storage.getItem(prefix['wxid']);
        },
        'isLogin' : function(){
            var uid = storage.getItem(prefix['uid']);
            if(uid){
                return uid;
            }
            else{
                return false;
            }
            return !!storage.getItem(prefix['uid']);
        },

        'quit' : function(redirect_url){
            if(!redirect_url){
                redirect_url = '/';
            }
            storage.removeItem('UID');
            window.location.replace(redirect_url);
        },

        'login' : function(mob,pwd,redirect_url,callback_fail){
            if(!redirect_url){
                redirect_url = './account/';
            }
            window.WBAccount.verify(mob,pwd,function(ac){
                storage.setItem(prefix['uid'],ac['id']);
                storage.setItem(prefix['time'],Date.now());
                window.location.replace(redirect_url);
            },function(){
                if(typeof callback_fail =='function'){
                    callback_fail.call(this);
                }
            });
        },
        'verify' : function(mob,pwd,callback_success,callback_fail){
            $.getJSON(path_verify,{
                'mobile' : mob,
                'password' : pwd
            },function(result){
                if(result && result['ret']==0 && $.isPlainObject(result['data'])){
                    if(typeof callback_success === 'function'){
                        callback_success.call(this,result['data']);
                    }
                }else{
                    console.log('data_err',result);
                    if(typeof callback_fail === 'function'){
                        callback_fail.call(this);
                    }
                }
            }).fail(function(){
                if(typeof callback_fail === 'function'){
                    callback_fail.call(this);
                }
            });
        }
    };

})(window,jQuery);

