<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/27
 * Time: 9:51
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';



if (request()->isPost()) {
    //1.获取数据
    $ReturnArray = array( // 返回字段
        "memberid" => input("memberid"), // 商户ID
        "orderid" => input("orderid"), // 订单号
        "transaction_id" => input('transaction_id'),//支付流水号
        "amount" => input("amount"), // 交易金额
        "datetime" => input("datetime"), // 交易时间
        "returncode" => input("returncode")
    );
    //2.进行验签
    ksort($ReturnArray);
    reset($ReturnArray);
    $md5str = "";
    foreach ($ReturnArray as $key => $val) {
        $md5str = $md5str . $key . "=" . $val . "&";
    }
    $Md5key = $password;   //密钥
    $sign = strtoupper(md5($md5str . "key=" . $Md5key));
    if ($sign == input("sign") && input("returncode") == '00') {
        //执行业务
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\n 验签状态：验签成功");
        fclose($file);
        //-----结束记录日志----//
        $orderlist_model = new \app\index\model\Notifyhost();
        $res = $orderlist_model->updateOrderStatusForBank(input("orderid"), input("amount"), 1);
        echo "ok";	//请不要修改或删除
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\n 更新订单状态：" . $res);
        fwrite($file, "\r \n 输出结果：ok" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
        fclose($file);
        //-----结束记录日志----//
        exit();
    }else {
        //验证失败
        echo "fail";
        //-----开始记录日志----//
        $file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'notify.txt', 'a+');
        fwrite($file, "\r \n 输出结果：fail" . "\r\n ------------------ 结束时间：" . date('Y-m-d H:i:s', time()) . "------------------\r\n");
        fclose($file);
        //-----结束记录日志----//
        exit();
    }
}

