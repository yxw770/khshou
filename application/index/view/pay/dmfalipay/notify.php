<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/23
 * Time: 18:47
 */

\think\Loader::addNamespace('dmfalipay','../application/index/view/pay/dmfalipay/');
require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';

$paynotify = new \dmfalipay\Paynotify();
//$Test = new \my\Test();
//$Test->sayHello();
//exit();
if (request()->isPost()) {
//if (request()->isGet()) {

    //1.获取数据
    $post_arr = trim_arr(input('post.'));
//    $post_arr = trim_arr(input('get.'));
    //商户订单号
    $orderid = $post_arr['out_trade_no'];
    //交易状态
    $trade_status = $post_arr['trade_status'];
    //实际收款
    $receipt_amount = $post_arr['receipt_amount'];

    //2.进行排序
    $rsaPublicKey = "";
    $res = $paynotify->rsaCheckV1_alipay($post_arr, $rsaPublicKey, 'RSA2');

    //-----开始记录日志----//
    $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
    fwrite($file,"\r\n ------------------ 开始时间：".date('Y-m-d H:i:s',time())."------------------\r\n");
    fwrite($file,"\r\n 订单号：".$orderid);
    fwrite($file,"\r\n 交易状态：".$trade_status);
    fwrite($file,"\r\n 实际收款：".$receipt_amount);
    fwrite($file, "\n res：" . $res);
    fclose($file);
    //-----结束记录日志----//

    if ($res) {
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\n 验签状态：验签成功");
        fclose($file);
        //-----结束记录日志----//
        if ($trade_status == 'TRADE_SUCCESS') {
            //执行更新订单支付状态为：已付款，待发货，更新发货状态为：等待发卡
            $orderlist_model = new \app\index\model\Notifyhost();
            $res = $orderlist_model->updateOrderStatusForBank($orderid, $receipt_amount, 1);
            echo "success";    //请不要修改或删除
            //-----开始记录日志----//
            $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
            fwrite($file, "\n 更新订单状态：" . $res);
            fwrite($file, "\r \n 输出结果：ok" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
            fclose($file);
            //-----结束记录日志----//
        }
    } else {
        //验证失败
        echo "fail";
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\r \n 输出结果：fail" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
        fclose($file);
        //-----结束记录日志----//
    }
}
?>