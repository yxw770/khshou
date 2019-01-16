<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/27
 * Time: 8:26
 */

//require_once APP_PATH_INDEX.'pay/'.$directory.'/PayResultAlipaywap.php';
//require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';

require_once APP_PATH_INDEX.'pay/'.$directory."/config.php";
require_once APP_PATH_INDEX.'pay/'.$directory.'/service/AlipayTradeService.php';


$arr=$_POST;

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
fwrite($file, "\n 获取数据：".json_encode($arr));
fclose($file);
//-----结束记录日志----//

$alipaySevice = new AlipayTradeService($config);
$alipaySevice->writeLog(var_export($_POST,true));
$result = $alipaySevice->check($arr);

/* 实际验证过程建议商户添加以下校验。
1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
4、验证app_id是否为该商户本身。
*/

if($result) {
    //验证成功
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //请在这里加上商户的业务逻辑程序代


    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

    //商户订单号

    $out_trade_no = $_POST['out_trade_no'];

    //支付宝交易号

    $trade_no = $_POST['trade_no'];

    //交易状态
    $trade_status = $_POST['trade_status'];

    //实际收款
    $receipt_amount = $_POST['receipt_amount'];

    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file, "\n 验签状态：验签成功");
    fclose($file);
    //-----结束记录日志----//
//    if ($trade_status == 'TRADE_SUCCESS') {
        //执行更新订单支付状态为：已付款，待发货，更新发货状态为：等待发卡
        $orderlist_model = new \app\index\model\Notifyhost();
        $res = $orderlist_model->updateOrderStatusForBank($out_trade_no, $receipt_amount, 1);
        echo "success";    //请不要修改或删除
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\n 更新订单状态：" . $res);
        fwrite($file, "\r \n 输出结果：ok" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
        fclose($file);
        //-----结束记录日志----//
//    }
} else {
    //验证失败
    echo "fail";
    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file, "\r \n 输出结果：fail" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
    fclose($file);
    //-----结束记录日志----//
}

