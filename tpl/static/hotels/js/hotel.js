$(function(){
	$(".op-add").on("touchstart",function(){
		$(this).addClass("active");
		
	});
	$(".op-minus").on("touchend",function(){
		$(this).removeClass("active");
		
	});
	$("#input-minus").on("click",function(){
		var self=$(this),
			minus=parseInt(self.attr("data-min")),
			input=$("#input-number"),
			value=parseInt(input.val());
		if(value&&value!==""&&value>minus){
			input.val(value-1);
		}else{
			input.val(minus);
			self.addClass("disable");
		}
		$("#input-add").removeClass("disable");
		sum_price(input.val());
	});
	$("#input-add").on("click",function(){
		var self=$(this),
			max=parseInt(self.attr("data-max")),
			input=$("#input-number"),
			value=parseInt(input.val());
		if(value&&value!==""&&value<max){
			input.val(value+1);
		}else{
			input.val(max);
			self.addClass("disable");
		}
		$("#input-minus").removeClass("disable");
		sum_price(input.val());
	});
	$("#input-number").on("keyup",function(){
		var self=$(this),
			max=parseInt(self.attr("data-max")),
			minus=parseInt(self.attr("data-min")),
			value=parseInt(self.val());
			self.val(self.val().replace(/\D/g, ''));
		if(value>max){
			self.val(max);
		}else if(value<minus){
			self.val(minus);
		}
	});
	$("#btn-order").on("click",function(){
		$("#pop-order").show();
	});
	$(".pop .btn").on("click",function(){
		$(".pop").hide();
	});
	
});

