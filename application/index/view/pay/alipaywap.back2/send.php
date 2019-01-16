<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/23
 * Time: 18:47
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmitAlipaywap.php';

//$Test = new \my\Test();
//$Test->sayHello();
//exit();


//1.组装数据
$biz_content = array(
    'out_trade_no'=>$orderid,
    'product_code'=>'QUICK_WAP_WAY',
    'total_amount'=>$price,
    'subject'=>"客服QQ:860529523 订单号:".$orderid,
    'body'=>"客服QQ:860529523 订单号:".$orderid,
);
$params=array(
    'app_id'=>'2018121562576045',
    'method'=>'alipay.trade.wap.pay',
    'charset'=>'utf-8',
    'sign_type'=>'RSA2',
    'timestamp'=>date('Y-m-d H:i:s'),
    'version'=>'1.0',
    'notify_url'=>request()->domain().'/notify/'.$directory,
    'format' => 'JSON',

//    'return_url'=>request()->domain().'/orderquery?kw='.$orderid,
);
//2.生成业务参数字符串
$paysubmit = new PaySubmitAlipaywap();
$bizString = $paysubmit->CreateBizContentString($biz_content);
$params['biz_content']=$bizString;
//3.生成待签名字符串
$stringToBeSigned = $paysubmit->createStringToBeSigned_alipay($params);


//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file,"\n 生成待签名字符串：".$stringToBeSigned);
fwrite($file,"\n 请求数据：".urldecode(http_build_query($params)));
fclose($file);
//-----结束记录日志----//

//4.执行加密业务参数
$screct_key = "MIIEpAIBAAKCAQEAvYUdNhKnwnK4z9LmC+ejIr59kbnL+tsjlUp57HVKADazKoePyKzHIgQ6QxIjkfjRSmne9XQWtyrRrPaCOXbPXcnFQN+28WVSV7m2bcbNpx3HrzAVADAjf3pVG7fL05siG5FnIXQknPmuU8ofS17b86VGSnO/icQ43LCRitNmij2Ghp2ibg/QA8FjXYtHM/cgA5fkEor2lyT6eAHX02DETN11T7CdHkqB8/bm/tJjqI3QUsozLnA37Cyy8eLaECot713FZ53ZJmhQ99u3hHQ7LbOx+vbyYAG9zpEPwDVZkvKDiFUTXsItpN9zUH+FYLTcxv5Wv3XauCHy36YaS6bFpQIDAQABAoIBABVntXboAH6bynfekBEEJo7ECRaub3Vzzr8L3F1ymdsQUogZK4o0yYD0DUiKAe54tdq7vmzmobIqaegpJ2gqHR2wrlC3c8gr83LpDMu1SoD3ghgtTBHrXfcF8BrjVWge+2k1sPo8Te5U3PWlvIccvab1Ic1C6X075lg2sRmG6uvt+OorXPNmDZf0kW2X/PRUH4wZm/xXOVr8LdjaHTrgLwJ/adwLa3hbr0nOnRM3HgHzpi7afFJf4rC9GVrGpoqNUnFh3CkqW4oNup+4F56my3+UVJIV5Y2EPCX7LKEDV8sgoO60Cxhnoprg0pW1jb2y3Hw69/x9h/HNpIqdcFNhk0ECgYEA+AyMEwvuAclEtjpkpPMKhhMq0qRYTIei/TFD+KXche2kWYYU+mEd4wsvWV0Fy5wz0MNgWbq/2thnux+JFMuK9EsJ4dspWfq17966a7OvIiA6NKSEIHpK9PvefYONPzyQPxElox+NoHtEcmRLZRr0QYwtocJXP4GurO61d7y4FFkCgYEAw5hJV4lRJy5yHVOFcpWNODGLblQCdoV1pfKQAkg8lkOcAo8dWN9frkhgY7h6/+fFsSwzKgF7NC0GH5lan84jPLrvm4G3OM9hJlKETU/Aqm+iKBryGytOjl3SpS7kh64CHk56s/MF9vnCYVP0XrCrS6VbUTHGZhzfGRwaYaXmgi0CgYBHIBHqRDly+atiNUw2oBocZ9KXo9hN2Xa68y1yVtzs6j9+DBxnzwEq4UIqdan7wEZ7TgqKsCnjLrpPxbMg61xkwD+NK7KnwmP46/y4dUV6m5MVFTdtuKNVrAMYKmiOAWe7LceFBr8tjSsLQgb8bsYrJW7QO1GZPC5CGBCgzNRbwQKBgQCepNI38eSkxwM/zhKbEbntLiJNZk5pG26FHuy3LSgXKNdNLA89mk/c6E9mEqeREMhEH/mPxIvNkgc5cgUTQPtLssPNebTGm03M63jrzBgVJV588P4WE1YwGuFToWkys2x8cGMNubvIymNi1dmLQ/hr7r7WaGznPdumXij3srv4MQKBgQDGgE5XUa+fUXjYtL1yKnFay3P76fAzOZ9N4z6BP4qdbF8y6B2LkfoCh8rI2qm1gFjNNUfjtJptqqtsD4PnUoTcP1Q4FpP+/p/AbiCo9TzqNlRfPlLUw4Mzxanr0nNJ2nu+KUyFpu1XZNIAT+R8qbYEACaI069t3rpJmFw1H9CBfg==";
$sign = $paysubmit->sign_alipay($stringToBeSigned,$screct_key,'',"RSA2");

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file,"\n 签名：".$sign);
fclose($file);
//-----结束记录日志----//

//5.赋值签名
$params['sign'] = $sign;

//6.请求数据
//发起HTTP请求
$url = "https://openapi.alipay.com/gateway.do";
$resp = $paysubmit->buildRequestForm_alipay($url,$params);

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file,"\n 返回结果：".$resp);
fclose($file);
//-----结束记录日志----//

//7.跳转页面
echo $resp;

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file,"\n ------------------------ \n");
fclose($file);
//-----结束记录日志----//
?>
