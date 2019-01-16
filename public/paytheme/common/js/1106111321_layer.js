function showMsg(title, message) {
    layer.msg(title + message, {
        time: 2000,
        icon: 1
    })
}
function showError(title, message) {
    layer.msg(title + message, {
        time: 2000,
        icon: 2
    })
}
function showWarning(title, message) {
    layer.msg(title + message, {
        time: 2000,
        icon: 7
    })
}
function showInfor(title, message) {
    layer.msg(title + message, {
        time: 2000,
        icon: 7
    })
}
function selectcateid() {
    var userid = $('input[name="userid"]').val();
    var cateid = $('select[name="cateid"]').val();
    var linkid = $('input[name="linkid"]').val();
    var token = $('input[name="' + linkid + '_token"]').val();
    var option = '<option value="">请选择商品</option>';
    if (cateid != null && cateid != undefined && cateid != "") {
        $('#goodid_img').show();
        $('select[name="goodlistid"]').hide();
        $.post("/ajax/getlists", {
            linkid: linkid,
            userid: userid,
            cateid: cateid,
            token: token
        }, function(data) {
            data = eval("(" + data + ")");
            if (data.status == '1') {
                $.each(data.msg, function(n, value) {
                    option += "<option value='" + value.goodlistid + "'>" + value.goodname + "</option>"
                });
                $('select[name="goodlistid"]').html(option).show().focus();
                $('#goodid_img').hide()
            } else if (data.status == '0') {
                $('#goodid_img').hide();
                $('select[name="goodlistid"]').html('<option value="">暂无商品 请重新选择分类</option>').show().focus()
            }
        })
    }
}
function selectgoodid() {
    var goodlistid = $('select[name="goodlistid"]').val();
    var userid = $('input[name="userid"]').val();
    var linkid = $('input[name="linkid"]').val();
    var token = $('input[name="' + linkid + '_token"]').val();
    if (goodlistid != null && goodlistid != undefined && goodlistid != "") {
        $('#price_img').show();
        $('#price').hide();
        $.post("/ajax/getdetail", {
            linkid: linkid,
            userid: userid,
            goodlistid: goodlistid,
            token: token
        }, function(data) {
            data = eval("(" + data + ")");
            if (data.status == '1') {
                var detail = data.msg;
                $('#price').html(detail.price);
                $('input[name="price"]').val(detail.price);
                $('#remark').html(detail.remark);
                $('#tprice').html(detail.price);
                $('input[name="contact_limit"]').val(detail.contact_limit);
                var showmsg_msg = '推荐填写手机号或QQ号 订单查询的重要凭证!';
                if ($('input[name="is_tel"]').is(':checked')) {
                    $('input[name="contact"]').attr('placeholder', '请填写您的手机号！');
                    var reg = /^(\d){11}$/;
                    var contact = $.trim($('[name="contact"]').val());
                    if (!reg.test(contact)) {
                        showError('', '请填写您的手机号！');
                        $('[name="contact"]').focus();
                        return
                    }
                } else if (!$('input[name="is_tel"]').is(':checked')) {
                    switch (detail.contact_limit) {
                        case '0':
                            showmsg_msg = '推荐填写手机号或QQ号 订单查询的重要凭证!';
                            $('input[name="contact"]').attr('placeholder', showmsg_msg);
                            break;
                        case '1':
                            showmsg_msg = '请输入QQ号';
                            $('input[name="contact"]').val('').attr('placeholder', showmsg_msg);
                            break;
                        case '2':
                            showmsg_msg = '请输入手机号';
                            $('input[name="contact"]').val('').attr('placeholder', showmsg_msg);
                            break
                    }
                }
                var kucun_status = detail.kucun_status;
                $('input[name="kucun"]').val(detail.kucun);
                if (kucun_status == '2') {
                    var kucun = detail.kucun;
                    if (kucun > 100) {
                        $('#kucun').html('非常多')
                    } else if (kucun > 30) {
                        $('#kucun').html('很多')
                    } else if (kucun > 20) {
                        $('#kucun').html('较多')
                    } else if (kucun > 10) {
                        $('#kucun').html('一般')
                    } else {
                        $('#kucun').html('少量')
                    }
                } else {
                    $('#kucun').html("库存" + detail.kucun + "件")
                }
                if (detail.kucun == 0) {
                    $('input[name="quantity"]').val(0)
                } else if ($('input[name="quantity"]').val() == 0) {
                    $('input[name="quantity"]').val(1)
                }
                if (detail.wholesale) {
                    var discountdetail_html = '';
                    wholesale_arr = detail.wholesale;
                    $('input[name="is_wholesale"]').val('1');
                    discountdetail_html = '<table style="color: #ffffff">' + '<tr><th>购买数量</th><th>优惠价格</th></tr>';
                    $.each(detail.wholesale, function(n, value) {
                        discountdetail_html += "<tr><td>" + value.define_quantity + "张</td><td>" + value.define_price + "元</td>"
                    });
                    discountdetail_html += '</table>';
                    $('#wholesaledetail').html(discountdetail_html);
                    $('#wholesale').show()
                } else {
                    wholesale_arr = [];
                    $('input[name="is_wholesale"]').val(0);
                    $('#wholesaledetail').html('');
                    $('#wholesale').hide()
                }
                if (detail.low_limit > 1) {
                    detail.low_limit = detail.low_limit.toString();
                    var quantity = $('input[name="quantity"]');
                    if (quantity.val() < detail.low_limit) {
                        quantity.val(detail.low_limit)
                    }
                    $('input[name="low_limit"]').val(detail.low_limit)
                } else {
                    $('input[name="low_limit"]').val('')
                }
                $('#pwd1').hide();
                $('#pwd2').hide();
                if (detail.psd_limit == '1') {
                    $('input[name="psd_limit"]').val(detail.psd_limit);
                    $('#pwd1').show()
                } else if (detail.psd_limit == '2') {
                    $('input[name="psd_limit"]').val(detail.psd_limit);
                    $('#pwd2').show()
                } else {
                    $("input[name='pwd1']").val('');
                    $("input[name='pwd2']").val('');
                    $('input[name="psd_limit"]').val('0')
                }
                $('#discouponcode').hide();
                $('#coupon').hide();
                $('input[name="couponcode"]').val('');
                $('input[name="checkcoupon"]').html('');
                if (detail.is_coupon == '1') {
                    $('input[name="is_coupon"]').val(detail.is_coupon);
                    $('#coupon').show()
                } else {
                    $('input[name="is_coupon"]').val('0')
                }
                $('#price_img').hide();
                $('#price').show()
            } else if (data.status == '0') {
                $('#goodid_img').hide();
                $('select[name="goodlistid"]').html('<option value="">请选择商品</option>').show().focus()
            }
        })
    }
}
function changequantity() {
    var quantity = parseInt($.trim($('input[name="quantity"]').val()));
    var kucun = $('input[name="kucun"]').val();
    var low_limit = $('input[name="low_limit"]').val();
    if (kucun <= 0) {
        $('input[name="quantity"]').val(0);
        showWarning('库存', '卡密已售完' + '，请联系卖家补卡' + '');
        return
    } else if (quantity < 1) {
        $('input[name="quantity"]').val(1);
        showWarning('', '请选择购买数量');
        return
    } else if (quantity > kucun) {
        $('input[name="quantity"]').val(kucun);
        showWarning('库存剩余', kucun + '张卡');
        return
    } else if (quantity < low_limit) {
        $('input[name="quantity"]').val(low_limit);
        showWarning('该商品起购数量', '为：' + low_limit + '件');
        return
    }
    var is_wholesale = $('input[name="is_wholesale"]').val();
    if (is_wholesale == '1') {
        wholesale_arr = wholesale_arr.sort(function(a, b) {
            return (a.define_quantity < b.define_quantity) ? 1 : -1
        });
        $.each(wholesale_arr, function(n, value) {
            if (quantity >= value.define_quantity) {
                $('#price').html(value.define_price);
                return false
            } else {
                $('#price').html($('input[name="price"]').val())
            }
        })
    }
    calculate_tprice()
}
function calculate_tprice() {
    var price = parseFloat($('#price').html());
    var quantity = parseInt($.trim($('input[name="quantity"]').val()));
    var tel_amount = parseFloat($.trim($('input[name="tel_amount"]').val()));
    var amount = parseFloat($('input[name="amount"]').val());
    var tprice = quantity * price - amount + tel_amount;
    tprice = tprice.toFixed(2);
    $('input[name="tprice"]').val(tprice);
    $('#tprice').html(tprice)
}
function changecontact() {
    var contact = $.trim($('[name="contact"]').val());
    var contact_limit = $.trim($('[name="contact_limit"]').val());
    if (!$('input[name="is_tel"]').is(':checked')) {
        if (contact_limit == 1) {
            $('input[name="contact"]').attr('placeholder', '请填写您的QQ号！');
            var reg = /^[\d]{6,11}$/;
            if (!reg.test(contact)) {
                showError('', '请填写您的QQ号！');
                $('[name="contact"]').focus();
                return
            }
        } else if (contact_limit == 2) {
            $('input[name="contact"]').attr('placeholder', '请填写您的手机号！');
            var reg = /^(\d){11}$/;
            if (!reg.test(contact)) {
                showError('', '请填写您的手机号！');
                $('[name="contact"]').focus();
                return
            }
        } else if (contact_limit == 0) {
            $('input[name="contact"]').attr('placeholder', '推荐填写手机号或QQ号 订单查询的重要凭证！');
            if (contact == '') {
                showError('', '请您填写6位以上联系方式！');
                $('[name="contact"]').focus();
                return
            }
            if (contact.length < 6 || contact.length > 20) {
                showError('', '请您填写[6-20]位以上联系方式！');
                $('[name="contact"]').focus();
                return
            }
        }
    } else if ($('input[name="is_tel"]').is(':checked')) {
        $('input[name="contact"]').attr('placeholder', '请填写您的手机号！');
        var reg = /^(\d){11}$/;
        if (!reg.test(contact)) {
            showError('', '请填写您的手机号！');
            $('[name="contact"]').focus();
            return
        }
    }
}
function changeemail() {
    var email = $('input[name="email"]').val();
    var reg = /^([0-9a-zA-Z_-])+@([0-9a-zA-Z_-])+((\.[0-9a-zA-Z_-]{2,3}){1,2})$/;
    if (!reg.test(email)) {
        showError('', '填写你常用的邮箱地址');
        $('[name="email"]').focus();
        return
    }
}
function checkpwd1() {
    var pwd1 = $("input[name='pwd1']").val();
    var reg = /^([0-9A-Za-z]+){6,20}$/;
    if (pwd1 == '' || !reg.test(pwd1)) {
        showWarning('', '[必填]请输入取卡密码（6-20位）');
        $('[name=pwd1]').focus();
        return
    }
}
function checkpwd2() {
    var pwd2 = $("input[name='pwd2']").val();
    if (pwd2 != null && pwd2 != undefined && pwd2 != "") {
        var reg = /^([0-9A-Za-z]+){6,20}$/;
        if (!reg.test(pwd2)) {
            showWarning('', '请输入取卡密码（6-20位）');
            $('[name=pwd2]').focus();
            return
        }
    } else {
        return true
    }
}
function checkCoupon() {
    var userid = $('input[name="userid"]').val();
    var goodlistid = $('select[name="goodlistid"]').val();
    var couponcode = $('input[name="couponcode"]').val();
    var linkid = $('input[name="linkid"]').val();
    var token = $('input[name="' + linkid + '_token"]').val();
    $.post("/ajax/checkcoupon", {
        linkid: linkid,
        userid: userid,
        goodlistid: goodlistid,
        couponcode: couponcode,
        token: token
    }, function(data) {
        data = eval("(" + data + ")");
        if (data.status == '1') {
            $('#discouponcode').show();
            $('#checkcoupon').html(data.msg).show();
            $('input[name="amount"]').val(data.orther);
            calculate_tprice()
        } else if (data.status == '0') {
            $('#discouponcode').show();
            $('#checkcoupon').html(data.msg).show();
            $('input[name="amount"]').val(0);
            calculate_tprice()
        }
    })
}
$(function() {
    $('[name=amount]').val(0);
    $('[name=tel_amount]').val(0);
    $('input[name="is_tel"]').click(function() {
        if ($(this).attr('checked')) {
            $('input[name="contact"]').focus().attr('placeholder', '请填写你的手机号');
            $('[name=tel_amount]').val(0.1);
            calculate_tprice()
        } else {
            $('input[name="contact"]').blur().attr('placeholder', '推荐填写手机号或QQ号 订单查询的重要凭证!');
            $('[name=tel_amount]').val(0);
            calculate_tprice()
        }
    });
    $('input[name="is_email"]').click(function() {
        $('#email_show').toggle();
        $('[name="email"]').focus()
    });
    jQuery(function() {
        var url = window.location.href;
        jQuery('#qrcode').qrcode({
            render: "canvas",
            width: 100,
            height: 100,
            background: "#FFF",
            text: url,
        })
    });
    $("#wholesale").hover(function() {
        var index = layer.tips($("#wholesaledetail").html(), this, {
            tips: [2, '#4B4B4B'],
            time: 0
        });
        $(this).attr("data-index", index)
    }, function() {
        layer.close($(this).attr("data-index"))
    })
});

function submitform() {
    var quantity = parseInt($.trim($('input[name="quantity"]').val()));
    var kucun = $('input[name="kucun"]').val();
    var psd_limit = $('input[name="psd_limit"]').val();
    var low_limit = $('input[name="low_limit"]').val();
    var contact = $.trim($('[name="contact"]').val());
    var contact_limit = $.trim($('[name="contact_limit"]').val());
    var goodlistid = $('select[name="goodlistid"]').val();
    if (goodlistid == null || goodlistid == undefined || goodlistid == "") {
        showWarning('', '请选择商品');
        $('[name=goodlistid]').focus();
        return
    }
    if (kucun <= 0) {
        $('input[name="quantity"]').val(0);
        showWarning('库存', '卡密已售完' + '，请联系卖家补卡' + '');
        $('[name=quantity]').focus();
        return
    } else if (quantity < 1) {
        showWarning('', '请选择购买数量');
        $('[name=quantity]').focus();
        return
    } else if (quantity > kucun) {
        $('input[name="quantity"]').val(kucun);
        showWarning('库存剩余', kucun + '张卡');
        $('[name=quantity]').focus();
        return
    } else if (quantity < low_limit) {
        $('input[name="quantity"]').val(low_limit);
        showWarning('', '该商品起购' + low_limit + '件');
        $('[name=quantity]').focus();
        return
    }
    if (!$('input[name="is_tel"]').is(':checked')) {
        if (contact_limit == 1) {
            var reg = /^[\d]{6,11}$/;
            if (!reg.test(contact)) {
                showError('', '请填写您的QQ号！');
                $('[name="contact"]').focus();
                return
            }
        } else if (contact_limit == 2) {
            var reg = /^(\d){11}$/;
            if (!reg.test(contact)) {
                showError('', '请填写您的手机号！');
                $('[name="contact"]').focus();
                return
            }
        }
        if (contact == '') {
            showError('', '请您填写6位以上联系方式！');
            $('[name="contact"]').focus();
            return
        }
        if (contact.length < 6) {
            showError('', '请您填写6位以上联系方式！');
            $('[name="contact"]').focus();
            return
        }
    } else if ($('input[name="is_tel"]').is(':checked')) {
        var reg = /^(\d){11}$/;
        if (!reg.test(contact)) {
            showError('', '请填写您的手机号！');
            $('[name="contact"]').focus();
            return
        }
    }
    if (psd_limit == '1') {
        var pwd1 = $("input[name='pwd1']").val();
        var reg = /^([0-9A-Za-z]+){6,20}$/;
        if (pwd1 == '' || !reg.test(pwd1)) {
            showWarning('', '[必填]请输入取卡密码（6-20位）');
            $('[name=pwd1]').focus();
            return
        }
    } else if (psd_limit == '2') {
        var pwd2 = $("input[name='pwd2']").val();
        if (pwd2 != null && pwd2 != undefined && pwd2 != "") {
            var reg = /^([0-9A-Za-z]+){6,20}$/;
            if (!reg.test(pwd2)) {
                showWarning('', '请输入取卡密码（6-20位）');
                $('[name=pwd2]').focus();
                return
            }
        }
    }
    $.post("/submit/add", $("#ajaxform").serialize(), function(data) {
        data = eval("(" + data + ")");
        if (data.status == '1') {
            layer.open({
                type: 1,
                title: '订单信息',
                shadeClose: false,
                shade: 0.4,
                content: data.msg,
                end: function() {
                    clearInterval(setInterval1)
                }
            })
        } else if (data.status == '0') {
            layer.msg(data.msg, {
                icon: 5,
                time: 2000
            })
        }
    })
}