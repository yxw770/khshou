<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/23
 * Time: 18:47
 */
require_once APP_PATH_INDEX.'pay/'.$directory.'/wx.class.php';
require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';

    //2.实例化类，赋值属性
    $wx = new wx();
    $wx->setAppID($apiurl);
    $wx->setMchID($accountid);
    $wx->setKey($password);
    //3.执行验证签名
    $ret = $wx->orderNotify();

    //4.获取数据
    //商户订单号
    $orderid = $ret['orderid'];
    //交易状态
    $status = $ret['status'];
    //实际收款
    $cash_fee = $ret['cash_fee']/100;

    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file, "\r\n ------------------ 开始时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
    fwrite($file, "\r\n 订单号：" . $orderid);
    fwrite($file, "\r\n 实际收款：" . $cash_fee);
    fwrite($file, "\n res：" . $status);
    fclose($file);
    //-----结束记录日志----//

    //5.执行更新订单支付状态为：已付款，待发货，更新发货状态为：等待发卡
    if ($ret['status']) {
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
        echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[' . $ret['msg'] . ']]></return_msg></xml>';
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\r \n 输出结果：fail" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
        fclose($file);
        //-----结束记录日志----//
    }
?>