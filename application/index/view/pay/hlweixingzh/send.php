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
$pay_bankcode = "901";   //银行编码  //902 支付宝扫码
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
$jsapi["is_show"] = "1";
//4.请求数据
//提交form表单数据
echo buildRequestForm_alipay($tjurl, $jsapi);

/**
 * 建立请求，以表单HTML形式构造（默认）
 * @param $para_temp 请求参数数组
 * @return 提交表单HTML文本
 */
function buildRequestForm_alipay($url,$para_temp) {
    reset($para_temp);
    $string = '';
    $sHtml = "<form id='alipaysubmit' action='".$url."' method='post'>";
    while (list ($key, $val) = each ($para_temp)) {
        $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
    }
    //submit按钮控件请不要含有name属性
    $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
    $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
    //用于返回给控制器echo $sHtml; 来提交
    return $sHtml;
}
?>
