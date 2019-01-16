<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/27
 * Time: 12:03
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmitDZWxgzh.php';

//1.组装数据
$biz_content = array(
    'MerchantOrderNo'=>$orderid,
    'PayChannel'=>'1203',
    'Amount'=>$price,
    'ProductName'=>"客服QQ:860529523 订单号:".$orderid,
    'ProductDescription'=>"客服QQ:860529523 订单号:".$orderid,
    'PromptView'=>"0",
);
$params=array(
    'AppId'=>$accountid,
    'ReqDate'=>date('YmdHis'),
    'NotifyUrl'=>request()->domain().'/notify/'.$directory,
    'ReturnUrl'=>request()->domain().'/orderquery?kw='.$orderid,
);
$params += $biz_content;
//2.生成签名
$paysubmit = new PaySubmitDZWxgzh();
$screct_key = $password;
$sign = $paysubmit->createSign_dianzhui($params,$screct_key);
//3.赋值签名
$tring =$paysubmit->ToUrlParams_dianzhui($params);
$urlString=$tring.'&Sign='.$sign;
//4.请求数据
//发起HTTP请求
$url = "http://api.0592pay.com/Order/ToPay";
$resp = $paysubmit->curl_dianzhui($url,$urlString);
$resarr = json_decode($resp, true);

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file,"\n 请求数据：".urldecode(http_build_query($params)));
fwrite($file,"\n 请求结果：".urldecode(http_build_query($resarr)));
//fwrite($file,"\n 跳转连接：".$resarr['ToPayData']);
fclose($file);
//-----结束记录日志----//

if ($resarr['RespCode'] != '00') {
    header("Content-type: text/html; charset=utf-8");
    echo $resarr['RespMessage'];
    exit();
}

$url_redirect_url = $resarr['ToPayData'];
//header('location:' . $url_redirect_url);

//7.执行跳转
?>
<!--如果是微信打开-->
<?php if (request()->isMobile() && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false): ?>
<script src="/pay/Monarch/js/jquery-2.2.1.min.js"></script>
<a href="<?php echo $url_redirect_url; ?>" style="display: none" id="clicka"></a>
<script>
    $(function () {
        // 打印$("a")[0]，得到的是http://www.mo2g.com，但实际上$("a")[0]是一个object对象，或许说是DOM对象更贴切一些。经过测试发现，其实原生的js就已经实现了类似的点击a标签的功能。原生js写法类似如下：
        document.getElementsByTagName("a")[0].click();
        //或
        // 只要获取到A标签的DOM对象，就能使用click()函数激活点击事件了。
        // $('#clicka')[0].click();
    })
</script>
<?php endif; ?>
<!--如果是外部浏览器打开-->
<?php if (request()->isMobile() && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false): ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no" />
    <script src="/pay/Monarch/js/jquery-2.2.1.min.js"></script>
    <script src="/pay/Monarch/js/clipboard.min.js"></script>
    <script src="/pay/Monarch/layer/layer.js"></script>
    <script src="/pay/Monarch/js/jquery.qrcode.min.js"></script>
</head>
<style>
    body,p{
        font-family: "Microsoft Yahei", "微软雅黑", "Pinghei";
    }
</style>
<body style="background: #fff;">
<div style="border: solid 2px #4BB4ED;border-radius: 5px">
    <header>
        <p style="background: rgb(50,122,183);margin: 0;text-align: center;color: #fff;height: 37px;line-height: 37px">微信安全支付</p>
        <p style="background: rgb(217,237,247);margin: 0;text-align: center;color: rgb(78,134,162);height: 40px;line-height: 40px">请保存截图 打开微信扫一扫 点击右上角“···” 从相册选取二维码</p>
    </header>
    <section style="text-align: center" id="qrcode"></section>
    <section>
        <p style="background: rgb(217,237,247);margin: 0;text-align: center;color: rgb(78,134,162);height: 40px;line-height: 40px">或 <label class="copy_btn" data-clipboard-action="copy" data-clipboard-target="#a1" style="color: #feb41c;cursor: pointer;font-weight: 500">一键复制</label> 以下链接到微信打开：</p>
    </section>
    <section>
        <!--展示链接-->
        <p id="a1"><?php echo $url_redirect_url; ?></p>
    </section>
    <section>
        <p style="padding: 0 11px;text-indent:11px;color: #333;width: auto;word-wrap:break-word; word-break:break-all;border-bottom: 1px solid #999;font-size: 14px;margin: 0;font-weight: 500">提示：你可以将以上链接发到自己微信的聊天框（在微信内顶部搜索框可以搜到自己的微信），即可进入支付</p>
    </section>
    <section>
        <p style="padding: 0 11px;text-indent:11px;color: #333;width: auto;word-wrap:break-word; word-break:break-all;border-bottom: 1px solid #999;font-size: 18px;margin: 0;text-align: center;font-weight: 500">支付成功自动跳转</p>
    </section>
<!--展示链接的二维码-->
<div id="qrcode"></div>
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
                text:'<?php echo $url_redirect_url ?>',				//扫描二维码后显示的内容,可以直接填一个网址，扫描二维码后自动跳向该链接
                // src: 'photo.jpg'             //二维码中间的图片
            });
    })
</script>
<script>
    $(function () {
        var clipboard = new Clipboard('.copy_btn');

        clipboard.on('success', function (e) {
            // console.log(e);
            layer.msg('复制成功', {icon: 6, time: 2000});
        });

        clipboard.on('error', function (e) {
            // console.log(e);
            layer.msg('复制失败', {icon: 6, time: 2000});
        });

    });
</script>
<script>
    //校验是否已经付款
    function oderquery() {
        var orderid = '<?php echo $orderid; ?>';
        var time = '<?php echo time(); ?>';
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
                    // window.location.href = "{:url('/orderquery?kw='.$orderid)}";
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
<?php endif; ?>
