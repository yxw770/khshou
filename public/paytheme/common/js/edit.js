$(function() {
    $('.lab3').click(function() {
        $(this).siblings('label').find('li').removeClass('current');
        $(this).find('li').addClass('current')
    });
    $("#wholesaleprice").hover(function() {
            if ($("#discountdetail").html() != '') {
                var index = layer.tips($("#discountdetail").html(), $('#wholesaleprice'), {
                    tips: [2, '#4B4B4B'],
                    time: 0
                });
                $(this).attr("data-index", index)
            }
        },
        function() {
            layer.close($(this).attr("data-index"))
        });
    $(".lab1").click(function() {
        if ($(this).children("input").is(':checked')) {
            $(this).addClass("current")
        } else {
            $(this).removeClass("current")
        }
    })
});