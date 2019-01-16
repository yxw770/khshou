function showTooltip(target,msg) {
    var index = layer.tips(msg, target, {
        tips: [1, '#4B4B4B'],
        time: 0,
        area: ['auto', 'auto']//这个属性可以设置宽高  auto 表示自动
    });
    $(target).attr("data-index", index);
}
function hideTooltip(target) {
    layer.close($(target).attr("data-index"));
}