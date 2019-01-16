$(function(){
    // 特色服务
    $(".lab1").click(function(){
        if($(this).children("input").attr('checked')){
            $(this).addClass("checked");
        }
        else{
            $(this).removeClass("checked");
        }
    });
    $(".lab3").click(function(){
        // check
        $(this).children('input').attr('checked',true);
        $(this).siblings("label").children('input').attr('checked',false);
        // 样式
        $(this).siblings("label").find('li').removeClass("hover");
        $(this).find('li').addClass("hover");
    });
});