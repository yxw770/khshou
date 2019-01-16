<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/26
 * Time: 12:18
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmit.php';


//1.组装数据
$params=array(
    'mch_id'=>$accountid,
    'nonce_str'=>mt_rand(time(),time()+rand()),
    'body'=>"客服QQ:860529523 \n订单号:".$orderid,
    'out_trade_no'=>$orderid,
    'attach'=>$orderid,
    'fee_type'=>'CNY',
    'total_fee'=>$price*100,
    'spbill_create_ip'=>getIp(),
    'trade_type'=>'NATIVE',
    'notify_url'=>request()->domain().'/notify/'.$directory,
);

//2.生成签名
$paysubmit = new PaySubmit();
$sign = $paysubmit->createSign_normal($params,$password);

//3.组装数据
$params['sign'] = strtoupper($sign);

//4.提交数据
$xmldata=$paysubmit::arrayToXml_qqrcode($params);

//执行http请求
$url = "https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi";
$res=$paysubmit->http_curl_qqrcode($url,$xmldata);

//5.获取数据
$arr = $paysubmit->xmlToArray($res);

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file, "\n 请求数据：" . urldecode(http_build_query($params)));
fwrite($file,"\n 请求结果：".urldecode(http_build_query($arr)));
fclose($file);
//-----结束记录日志----//

$code_img_url = $arr ["code_url"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>云腾发卡平台</title>


    <link rel="stylesheet" href="/pay/Monarch/css/iconfont.css">
    <link rel="stylesheet" href="/pay/Monarch/css/animate.min.css">
    <link rel="stylesheet" href="/pay/Monarch/css/main.css">
    <link rel="stylesheet" href="/pay/Monarch/css/main2.css">
    <script src="/pay/Monarch/js/jquery-2.2.1.min.js"></script>
    <script src="/pay/Monarch/js/jquery.qrcode.min.js"></script>
</head>
<style>
    .buy_info > p {
        color: #666;
        margin-bottom: 20px;
    }

    .buy_info > p > span {
        color: orangered;
    }
</style>
<body style="background: url('/pay/Monarch/images/subtle_dots_@2X.png')">
<!--头部-->
<header class="header_fix" style="position: relative">
    <div class="container">
        <div class="logo">云腾发卡平台</div>
        <nav>
            <ul>
                <li><a href="/#page1">首页</a></li>
                <li><a href="/#page3">优势</a></li>
                <li><a href="/#page5">联系</a></li>
                <li><a href="/orderquery"><i class="iconfont icon-sousuo"></i> 订单查询</a></li>
                <li><a href="/report/search" class="btn btn-default">投诉查询</a></li>
            </ul>
        </nav>
    </div>
</header>
<div class="user_form buy_info">
    <div class="user_tab">
        <ul>
            <li class="actived" style="width: 100%;margin-bottom: 30px"><a style="cursor: pointer">手机QQ扫码付款</a></li>
        </ul>
    </div>
    <p>订单号码：<?php echo $orderid ?></p>
    <p>付款金额：<span><?php echo $price ?></span>元</p>
    <p align="center" style="font-size: 14px" id="qrcode">
        <!--        <img src="/pay/qrcode/get.php?data=--><?php //echo $img ?><!--" width="210px" height="210px"/>-->
    </p>

    <p align="center" style="font-size: 14px">
        <span id="msg"><?php echo isMobile() ? "请长按二维码，选择识别二维码，完成支付。" : "请使用手机QQ扫描二维码支付。"; ?><br>30分钟内未付款的订单将会超时关闭。</span></p>
</div>
<script>
    //校验是否已经付款
    function oderquery() {
        var orderid = '{$orderid}';
        var time = '{:time()}';
        $.post(
            "{:url('ajax/checkOrderid')}",
            {orderid:orderid,time:time},
            function (data) {
                data = eval("(" + data + ")");
                // console.log(data);
                if (data.status == '1') {
                    clearInterval(setInterval1);
                    $("#msg").html("请稍候，正在处理付款结果...");
                    window.location.href = "/orderquery?kw={$orderid}";
                    // window.location.href = '/orderquery?kw=' + orderid;

                }else {
                    // window.location.href = "{:url('/orderquery?kw='.$orderid)}";
                }
            }
            // ,"json"
        );
    }

    var setInterval1 = setInterval('oderquery()', 3000);
</script>
<style>
    .footer_left{
        display: inline-block;
        width: 440px;

    }
    .footer_right{
        display: inline-block;
        width: 754px;
        text-align: right;

    }
</style>
<script>
    jQuery(function(){
        // console.log(window.location.href);
        // var url = window.location.href;
        jQuery('#qrcode').qrcode(
            {
                render : "canvas",    //设置渲染方式，有table和canvas，使用canvas方式渲染性能相对来说比较好
                width: 210,				//二维码的宽度
                height: 210,				//二维码的高度
                // foreground: "#cc674a",				//二维码的后景色
                background: "#FFF",				//二维码的前景色
                text:'<?php echo $code_img_url ?>',				//扫描二维码后显示的内容,可以直接填一个网址，扫描二维码后自动跳向该链接
                // src: 'photo.jpg'             //二维码中间的图片
            });
    })
</script>

</body>
</html>

