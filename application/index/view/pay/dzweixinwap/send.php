<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/27
 * Time: 11:25
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmitDZWeixinwap.php';


//1.组装数据
$biz_content = array(
    'MerchantOrderNo'=>$orderid,
    'PayChannel'=>'1201',
    'Amount'=>$price,
    'ProductName'=>"客服QQ:860529523 订单号:".$orderid,
    'ProductDescription'=>"客服QQ:860529523 订单号:".$orderid,
    'SceneInfo'=>'{"h5_info":{"type":"Wap","wap_url":"https://m.jd.com","wap_name":"京东官网"}}',
);
$params=array(
    'AppId'=>$accountid,
    'ReqDate'=>date('YmdHis'),
    'NotifyUrl'=>request()->domain().'/notify/'.$directory,
    'ReturnUrl'=>request()->domain().'/orderquery?kw='.$orderid,
);
$params += $biz_content;
//2.生成签名
$paysubmit = new PaySubmitDZWeixinwap();
$screct_key = $password;
$sign = $paysubmit->createSign_dianzhui($params,$screct_key);
//3.赋值签名
$tring =$paysubmit->ToUrlParams_dianzhui($params);
$urlString=$tring.'&Sign='.$sign;
//4.请求数据
//发起HTTP请求
$url = "http://api.0592pay.com/Order/ToPay";
$resp = $paysubmit->curl_dianzhui($url,$urlString);
$resarr = json_decode($resp, true);

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file,"\n 请求数据：".urldecode(http_build_query($params)));
fwrite($file,"\n 请求结果：".urldecode(http_build_query($resarr)));
fclose($file);
//-----结束记录日志----//
if ($resarr['RespCode'] != '00') {
    header("Content-type: text/html; charset=utf-8");
    echo $resarr['RespMessage'];
    exit();
}
$code_img_url = $resarr['ToPayData'];
header('location:' . $code_img_url);
?>
