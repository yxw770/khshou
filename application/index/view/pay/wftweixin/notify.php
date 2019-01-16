<?php
require_once APP_PATH_INDEX.'pay/'.$directory.'/class/PayResult.class.php';
require_once APP_PATH_INDEX.'pay/'.$directory.'/inc.php';

$payResult = new PayResult();
//1.获取数据
$xmldata=file_get_contents('php://input');
$arraydata=$payResult->xmlToArray($xmldata);
//2.验证签名是否正确
$sign_verify = $payResult->createSign($arraydata,$password);
$sign = $arraydata['sign'];
//3.写入数据库操作
$out_trade_no = $arraydata['out_trade_no'];
$money = $arraydata['total_fee']/100;
if ($sign_verify == $sign) {
    if ($arraydata['status'] == 0 && $arraydata['result_code '] == 0) {
        $orderlist_model = new \app\index\model\Notifyhost();
        $res = $orderlist_model->updateOrderStatusForBank($out_trade_no, $money, 1);
        echo "success";
    }

}
