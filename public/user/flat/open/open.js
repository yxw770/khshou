var showContent=function(title,url,height,width){
    layer.open({
        type: 2,
        // title: title,
        title: false,
        closeBtn:0,
        shadeClose: true,
        shade: 0.3,
        // skin: 'layui-layer-rim', //加上边框
        area: [height, width], //宽高
        content: url //获取商品链接
    });
};
