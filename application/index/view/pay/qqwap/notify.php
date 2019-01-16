<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/26
 * Time: 13:12
 */


require_once APP_PATH_INDEX.'pay/'.$directory.'/PayResult.php';
require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';

//1.获取数据
$payResult = new PayResult();
$xmldata=file_get_contents('php://input');
$arraydata=$payResult->xmlToArray($xmldata);
//商户订单号
$orderid = $arraydata['out_trade_no'];
//交易状态
$trade_state = $arraydata['trade_state'];
//实际收款
$cash_fee = $arraydata['cash_fee']/100;

//2.验证签名是否正确
$sign_verify = $payResult->createSign($arraydata,$password);
$sign = $arraydata['sign'];

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
fwrite($file,"\r\n ------------------ 开始时间：".date('Y-m-d H:i:s',time())."------------------\r\n");
fwrite($file,"\r\n 订单号：".$orderid);
fwrite($file,"\r\n 交易状态：".$trade_state);
fwrite($file,"\r\n 接受数据：".serialize($arraydata));
fwrite($file,"\r\n 实际收款：".$cash_fee);
fwrite($file,"\r\n sign_verify：".$sign_verify);
fwrite($file,"\r\n sign：".$sign);
//fwrite($file, "\n res：" . $res);
fclose($file);
//-----结束记录日志----//

//3.写入数据库操作
if ($sign_verify == $sign && $trade_state=='SUCCESS') {
//    if ($arraydata['status'] == 0 && $arraydata['result_code '] == 0) {
    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'notify.txt', 'a+');
    fwrite($file,"\n 验签状态：验签成功");
    fclose($file);
    //-----结束记录日志----//
    $orderlist_model = new \app\index\model\Notifyhost();
    $res = $orderlist_model->updateOrderStatusForBank($orderid, $cash_fee, 1);
    echo '<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>';
    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file,"\n 更新订单状态：".$res);
    fwrite($file, "\r \n 输出结果：ok" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
    fclose($file);
    //-----结束记录日志----//
//    }

} else {
    //验证失败
    echo '<xml><return_code><![CDATA[FAIL]]></return_code></xml>';
    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'notify.txt', 'a+');
    fwrite($file,"\r \n 输出结果：fail"."\r\n ------------------ 结束时间：".date('Y-m-d H:i:s',time())."------------------\r\n");
    fclose($file);
    //-----结束记录日志----//
}

