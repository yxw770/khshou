<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/27
 * Time: 9:25
 */

//1.组装数据
header("Content-type: text/html; charset=utf-8");
$pay_memberid = $accountid;   //商户ID
$pay_orderid = $orderid;    //订单号
$pay_amount = $price;    //交易金额
$pay_applydate = date("Y-m-d H:i:s");  //订单时间
$pay_bankcode = "902";   //银行编码  //902 微信扫码
$pay_notifyurl = request()->domain().'/notify/'.$directory;   //服务端返回地址
$pay_callbackurl = request()->domain().'/orderquery?kw='.$orderid;  //页面跳转返回地址
$Md5key = $password;   //密钥
$tjurl = "http://henglpay.com/Pay_Index.html"; //新网关
$pay_productname = "客服QQ: 订单号:".$orderid;
$jsapi = array(
    "pay_memberid" => $pay_memberid,
    "pay_orderid" => $pay_orderid,
    "pay_applydate" => $pay_applydate,
    "pay_bankcode" => $pay_bankcode,
    "pay_notifyurl" => $pay_notifyurl,
    "pay_callbackurl" => $pay_callbackurl,
    "pay_amount" => $pay_amount,
    "pay_productname" => $pay_productname
);
//2.生成签名
ksort($jsapi);
$md5str = "";
foreach ($jsapi as $key => $val) {
    $md5str = $md5str . $key . "=" . $val . "&";
}
$sign = strtoupper(md5($md5str . "key=" . $Md5key));
//3.赋值签名
$jsapi["pay_md5sign"] = $sign;
//4.请求数据
//发起HTTP请求
$returnData = http_curl($tjurl, 'post', $jsapi);

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
//fwrite($file,"\n 请求数据：".urldecode(http_build_query($jsapi)));
fwrite($file,"\n 请求结果：".urldecode($returnData));
fclose($file);
//-----结束记录日志----//

$returnData = json_decode($returnData, true);

if ($returnData['status'] != 200) {
    exit($returnData['errormsg']);
}
$code_img_url = $returnData['data']['QRCodeUrl'];

$guestconfig = cache('guestconfig');


function http_curl($url = "", $type = 'get', $arr = '')
{
    //1.初始化curl
    $ch = curl_init();
    //2.设置curl的参数
    curl_setopt($ch, CURLOPT_URL, $url);//设置我们的url
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//curl_exec执行成功则返回执行结果
    if ($type == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);//将$arr发送给$url
    }//if end
    //3.采集，抓取
    $output = curl_exec($ch);
    //4.关闭
    curl_close($ch);
    return $output;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{$guestconfig.sitename}-{$guestconfig.siteurl}</title>
    <meta name="description" content="{$guestconfig.description}" />
    <meta name="keywords" content="{$guestconfig.sitekeyword}" />
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
        <div class="logo">{$guestconfig.sitename}</div>
        <nav>
            <ul>
                <li><a href="/#page1">首页</a></li>
                <li><a href="/#page3">优势</a></li>
                <li><a href="/#page5">联系</a></li>
                <li><a href="/orderquery"><i class="iconfont icon-sousuo"></i> 订单查询</a></li>
            </ul>
        </nav>
    </div>
</header>
<div class="user_form buy_info">
    <div class="user_tab">
        <ul>
            <li class="actived" style="width: 100%;margin-bottom: 30px"><a style="cursor: pointer">手机微信扫码付款</a></li>
        </ul>
    </div>
    <p>订单号码：<?php echo $orderid ?></p>
    <p>付款金额：<span><?php echo $price ?></span>元</p>
    <p align="center" style="font-size: 14px" id="qrcode">
    </p>

    <p align="center" style="font-size: 14px">
        <span id="msg"><?php echo isMobile() ? "请长按二维码，选择识别二维码，完成支付。" : "请使用手机微信扫描二维码支付。"; ?><br>30分钟内未付款的订单将会超时关闭。</span></p>
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
<footer style="position:fixed;height: auto">
    <div class="container">
        <p class="footer_left" style="margin: 10px 0"></p>
    </div>
</footer>
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

