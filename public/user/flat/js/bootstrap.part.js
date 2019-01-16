$(function () {
    $('.selectAllCheckbox').click(function () {
        if ($(this).prop('checked')) {
            $('.checkbox').prop('checked', true);
        } else {
            $('.checkbox').prop('checked', false);
        }
    });
});

var add_discount=function(){
    if($('tr#is_discount_desc table tr').length>=12){
        alert('最多添加10个优惠区间');
        return false;
    };
    $('tr#add_button').before('<tr><td><img src="/user/flat/images/ico_del.png" style="cursor: pointer" onclick="del_discount(this)" align="absmiddle" title="移除" /></td><td><div class="input-group"><span class="input-group-addon">大于</span><input type="text" name="dis_quantity[]" class="form-control" value=""><span class="input-group-addon">张</span></div></td><td><div class="input-group"><input type="text" class="form-control" name="dis_price[]" value="" /><span class="input-group-addon">元</span></div></td></tr>');
};

var del_discount=function(id){
    $(id).parent().parent().remove();
};