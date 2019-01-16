// JavaScript Document
$(function(){
	$('.main_shadow').click(function(){
		$('.main').css('right','0');
		$('.main_slide').css('right','-120px');
		$('.main_shadow').hide();
	});
	$('.user_name').click(function(){
		$('.main').css('right','120px');
		$('.main_slide').css('right','0');
		$('.main_shadow').show();
	});
	$(".tab1 td label").click(function(){
		$(".tab1 td label").parent("td").next("td").css("color","#999");
		$(this).parent("td").next("td").css("color","#e573c5");
	});
    $("#shiyong").click(function(){
        $("#shiyong_out").fadeIn();
    });
	$(".add_btn").click(function(){
		$(this).before("<p><input class='pf_num' name='define_quantity[]' type='text' style='width:120px;' placeholder='购买数量大于'> 张  <br><br><input class='pf_price' name='define_price[]' type='text' style='width:120px' placeholder='优惠价格'> 元 <a class='delete_btn'>删除</a><br><br></p>");
        $('.pf_num').change(function(){
            var pf_num = $(this).val();
            if(pf_num <= 2){
                alert("数值太小");
                $(this).val(null);
                return false;
            }
            $('.pf_num').not($(this)).each(function(){
                if($(this).val() == pf_num && $(this).val() != ""){
                    alert("购买数量填写重复");
                    return false;
                }
            });
        });
        $('.pf_price').change(function(){
            var pf_price = $(this).val();
            var sell_price = $('.sell_price').val();
            if(sell_price <= pf_price){
                alert("批发价不能高于等于销售价");
                $(this).val(null);
                return false;
            }
        });
		$(".delete_btn").click(function(){
			$(this).parent("p").remove();
		});
	});
    $(".delete_btn").click(function(){
        $(this).parent("p").remove();
    });
	$("#bushiyong").click(function(){
		$("#shiyong_out").fadeOut();
	});
	$(".tab1 td label").not("#tongzhi1,#tongzhi2").click(function(){
		$(this).siblings("label").removeClass("checked");
		$(this).addClass("checked");
	});
	$("#tongzhi1").click(function(){
		if($(this).children("input").attr('checked')){
			$(this).addClass("checked");
		}
		else{
			$(this).removeClass("checked");
		}
	});
	$("#tongzhi2").click(function(){
		if($(this).children("input").attr('checked')){
			$(this).addClass("checked");
		}
		else{
			$(this).removeClass("checked");
		}
	});
	$("#gs1").click(function(){
		$(".yanshi span").hide();
		$(".yanshi span:eq(0)").show()
	});
	$("#gs2").click(function(){
		$(".yanshi span").hide();
		$(".yanshi span:eq(1)").show()
	});
	$("#gs3").click(function(){
		$(".yanshi span").hide();
		$(".yanshi span:eq(2)").show()
	});
	$("#gs4").click(function(){
		$(".yanshi span").hide();
		$(".yanshi span:eq(3)").show()
	});
	$(".channels i,.link_box i").click(function(){
		if($(this).hasClass("fa-toggle-on")){
			$(this).removeClass("fa-toggle-on").addClass("fa-toggle-off");
			$(this).siblings("span").html("关闭");
		}
		else if($(this).hasClass("fa-toggle-off")){
			$(this).removeClass("fa-toggle-off").addClass("fa-toggle-on");
			$(this).siblings("span").html("开启");
		}
	});
	$(".channels span,.zlkg").click(function(){
		if($(this).siblings("i").hasClass("fa-toggle-on")){
			$(this).siblings("i").removeClass("fa-toggle-on").addClass("fa-toggle-off");
			$(this).html("关闭");
		}
		else if($(this).siblings("i").hasClass("fa-toggle-off")){
			$(this).siblings("i").removeClass("fa-toggle-off").addClass("fa-toggle-on");
			$(this).html("开启");
		}
	});
	$(".rates input").keyup(function(){
		var num = $(this).val();
		var sum = 100 / (num / 100);
		if(num>100){
			alert("价值比例设置不应大于100！");
		}
		else if(num<1){
			alert("价值比例设置不应小于1！");
		}
		else{
			$(this).parent("td").siblings("td").children("span").text(sum.toFixed(2));
		}
	});
	$('.xiugai').click(function(){
		$(this).parents('td').siblings('td').children('input').css({'outline':'1px solid #a1cdeb','background-color':'#fff'});
	});
	$('.tab2 input').click(function(){
		$(this).css({'outline':'1px solid #a1cdeb','background-color':'#fff'});
	});
	$("#pass_open").click(function(){
		$(".shezhimima").show()
	});
	$("#pass_close").click(function(){
		$(".shezhimima").hide()
	});
});