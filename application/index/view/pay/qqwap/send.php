<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/26
 * Time: 12:18
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmit.php';


//1.组装数据
$params=array(
    'mch_id'=>$accountid,
    'nonce_str'=>mt_rand(time(),time()+rand()),
    'body'=>"客服QQ:860529523 \n订单号:".$orderid,
    'out_trade_no'=>$orderid,
    'attach'=>$orderid,
    'fee_type'=>'CNY',
    'total_fee'=>$price*100,
    'spbill_create_ip'=>getIp(),
    'trade_type'=>'NATIVE',
    'notify_url'=>request()->domain().'/notify/'.$directory,
);

//2.生成签名
$paysubmit = new PaySubmit();
$sign = $paysubmit->createSign_normal($params,$password);

//3.组装数据
$params['sign'] = strtoupper($sign);

//4.提交数据
$xmldata=$paysubmit::arrayToXml_qqrcode($params);

//执行http请求
$url = "https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi";
$res=$paysubmit->http_curl_qqrcode($url,$xmldata);

//5.获取数据
$arr = $paysubmit->xmlToArray($res);

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file, "\n 请求数据：" . urldecode(http_build_query($params)));
fwrite($file,"\n 请求结果：".urldecode(http_build_query($arr)));
fclose($file);
//-----结束记录日志----//

$code_img_url = $arr ["code_url"];
header("location:" . $code_img_url);
exit();

?>

