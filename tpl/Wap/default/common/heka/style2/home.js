/**
author : zhupinglei
desc : home
**/
function home(){
	this.init();
}
home.prototype = {
	init : function(){
		this.getList();
		this.footer();
		this.toLogin();
	},
	toLogin : function(){
		if( !window.localStorage.getItem('MYUID') ){
			$('.toLogin').on('tap',function(){
				window.location.href = '/account/login';
			});
		}else{
			$('.toLogin').hide();
		}
		
	},
	footer : function(){
		var d = new Date,
			d = d.getFullYear();
		var data = window.WBPage.PageData;
		$('.footer a.copyright').html('贺卡提供：'+data.info.web_name+'').attr('href',data.info.url);
		if( data.footer.support.title ){
			$('.footer a.support').html('技术支持：<span style="color:#639639;">'+data.footer.support.title+'</span>').attr('href',data.footer.support.href);
		}else{
			$('.footer a.support').hide();
		}
	},
	getList : function(){
		$.ajax({
			url : '/data/gcard/types',
			type : 'get',
			dataType : 'json',
			success : function(result){
				console.log(result);
				if( result.ret == 0 ){
					var data = result.data ? result.data : {}
					if ( data.on == 1 ) {
						$('title').html(data.title);
						if( data.types && data.types.length > 0 ){
							var str = '',strN = '',strX = '';
							for(var i = 0; i < data.types.length; i++){
								switch(data.types[i].cate){
									case 'xmas':
										strX +=  '<li lid="'+data.types[i].id+'" cate="'+data.types[i].cate+'" subid="'+data.types[i].sub_id+'">'+
								                    '<a href="/gcard/preview?cate='+data.types[i].cate+'&sub_id='+data.types[i].sub_id+'&lid='+data.types[i].id+'&id=0&key='+data.types[i].keyword+'">'+
								                        '<img src="../assets/public/images/gcard_preview/'+data.types[i].cate+'_'+data.types[i].sub_id+'.jpg" />'+
								                    '</a>'+
								                    '<p class="p1">'+data.types[i].title+'</p>'+
								                    '<p class="p2">回复<span>'+data.types[i].keyword+'</span>获取</p>'+
								                '</li>';
								        break;
								    case 'newyear':
								    	strN += '<li lid="'+data.types[i].id+'" cate="'+data.types[i].cate+'" subid="'+data.types[i].sub_id+'">'+
								                    '<a href="/gcard/preview?cate='+data.types[i].cate+'&sub_id='+data.types[i].sub_id+'&lid='+data.types[i].id+'&id=0&key='+data.types[i].keyword+'">'+
								                        '<img src="../assets/public/images/gcard_preview/'+data.types[i].cate+'_'+data.types[i].sub_id+'.jpg" />'+
								                    '</a>'+
								                    '<p class="p1">'+data.types[i].title+'</p>'+
								                    '<p class="p2">回复<span>'+data.types[i].keyword+'</span>获取</p>'+
								                '</li>';
								        break;
								    default:
								    	break;
								}	
							}
							str = strN + strX;
							$('.gcard_list ul').html(str);
						}
					}else{
						jDialog.alert('微贺卡功能未开启!',{
							handler : function(){
								window.WBPage.goBack();
							}
						});
					};
				}else{
					jDialog.alert(result.msg);
				}
			}
		});
	}
}
$(window).on('rendercomplete',function(){
	new home();
});