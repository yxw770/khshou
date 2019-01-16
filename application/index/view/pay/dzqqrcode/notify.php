<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/26
 * Time: 16:41
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PayResultDZQQrcode.php';
require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';

if (request()->isPost()) {
//if (request()->isGet()) {

    //1.获取数据
    $post_arr = trim_arr(input('post.'));
//    $post_arr = trim_arr(input('get.'));
    //商户订单号
    $orderid = $post_arr['MerchantOrderNo'];
    //交易状态
    $Status = $post_arr['Status'];
    //实际收款
    $Amount = $post_arr['Amount'];
    //签名
    $Sign = $post_arr['Sign'];
    //点缀支付平台订单号
    $PlatformOrderNo = $post_arr['PlatformOrderNo'];

    //2.进行验签
    $PayResult = new PayResultDZQQrcode();
    $verify_sign = $PayResult->MakeSign_dianzhui($post_arr,$password);

    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file, "\r\n ------------------ 开始时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
    fwrite($file, "\r\n 订单号：" . $orderid);
    fwrite($file, "\r\n 交易状态：" . $Status);
    fwrite($file, "\r\n 实际收款：" . $Amount);
    fclose($file);
    //-----结束记录日志----//
    if ($Sign==$verify_sign&&$Status=='1') {
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\n 验签状态：验签成功");
        fclose($file);
        //-----结束记录日志----//
        $orderlist_model = new \app\index\model\Notifyhost();
        $res = $orderlist_model->updateOrderStatusForBank($orderid, $Amount, 1);
        echo "ok";	//请不要修改或删除
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\n 更新订单状态：" . $res);
        fwrite($file, "\r \n 输出结果：ok" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
        fclose($file);
        //-----结束记录日志----//
    }else {
        //验证失败
        echo "fail";
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\r \n 输出结果：fail" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
        fclose($file);
        //-----结束记录日志----//
    }
}