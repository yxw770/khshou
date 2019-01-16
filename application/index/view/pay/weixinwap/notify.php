<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/27
 * Time: 9:05
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PayResultWeixinwap.php';
require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';

//1.获取数据
//$xmldata=file_get_contents('php://input');
//$arraydata=$payResult->xmlToArray($xmldata);
$arraydata = input('get.');

//4.获取数据
//商户订单号
$orderid = $arraydata['out_trade_no'];
//交易状态
$return_code = $arraydata['return_code'];
//实际收款
$cash_fee = $arraydata['cash_fee']/100;


//2.验证签名是否正确
$payResult = new PayResultWeixinwap();
$sign_verify = $payResult->createSign($arraydata,$password);
$sign = $arraydata['sign'];
//$sign = $sign_verify;//test data

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
fwrite($file, "\r\n ------------------ 开始时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
fwrite($file, "\r\n 订单号：" . $orderid);
fwrite($file, "\r\n 实际收款：" . $cash_fee);
fwrite($file, "\n 交易状态：" . $return_code);
fclose($file);
//-----结束记录日志----//

//5.执行更新订单支付状态为：已付款，待发货，更新发货状态为：等待发卡
if ($sign_verify == $sign && $return_code=='SUCCESS') {
    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file, "\n 验签状态：验签成功");
    fclose($file);
    //-----结束记录日志----//
    $orderlist_model = new \app\index\model\Notifyhost();
    $res = $orderlist_model->updateOrderStatusForBank($orderid, $cash_fee, 1);
    echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file, "\n 更新订单状态：" . $res);
    fwrite($file, "\r \n 输出结果：ok" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
    fclose($file);
    //-----结束记录日志----//
} else {
    //验证失败
    echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[fail]]></return_msg></xml>';
    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file, "\r \n 输出结果：fail" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
    fclose($file);
    //-----结束记录日志----//
}


