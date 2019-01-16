<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/23
 * Time: 18:47
 */

//require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmitAlipaywap.php';
require_once APP_PATH_INDEX.'pay/'.$directory.'/service/AlipayTradeService.php';
require_once APP_PATH_INDEX.'pay/'.$directory.'/buildermodel/AlipayTradeWapPayContentBuilder.php';
//$Test = new \my\Test();
//$Test->sayHello();
//exit();

//商户订单号，商户网站订单系统中唯一订单号，必填
$out_trade_no = $orderid;

//订单名称，必填
$subject = "客服QQ:860529523 订单号:".$orderid;

//付款金额，必填
$total_amount = $price;

//商品描述，可空
$body = "客服QQ:860529523 订单号:".$orderid;

//超时时间
$timeout_express="1m";

$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
$payRequestBuilder->setBody($body);
$payRequestBuilder->setSubject($subject);
$payRequestBuilder->setOutTradeNo($out_trade_no);
$payRequestBuilder->setTotalAmount($total_amount);
$payRequestBuilder->setTimeExpress($timeout_express);

$payResponse = new AlipayTradeService($config);
$result=$payResponse->wapPay($payRequestBuilder,request()->domain().'/orderquery?kw='.$orderid,request()->domain().'/notify/'.$directory);

return ;


//
////1.组装数据
//$biz_content = array(
//    'out_trade_no'=>$orderid,
//    'product_code'=>"QUICK_WAP_WAY",
//    'total_amount'=>$price,
//    'subject'=>"客服QQ:860529523 订单号:".$orderid,
//    'body'=>"客服QQ:860529523 订单号:".$orderid,
//);
//$params=array(
////    'app_id'=>'2018052160236046',
//    'app_id'=>'2018121562576045',
//    'method'=>'alipay.trade.wap.pay',
//    'charset'=>'utf-8',
//    'sign_type'=>'RSA2',
//    'timestamp'=>date('Y-m-d H:i:s'),
//    'version'=>'1.0',
//    'notify_url'=>request()->domain().'/notify/'.$directory,
//    'format' => 'JSON',
////    'return_url'=>request()->domain().'/orderquery?kw='.$orderid,
//);
////2.生成业务参数字符串
//$paysubmit = new PaySubmitAlipaywap();
//$bizString = $paysubmit->CreateBizContentString($biz_content);
//$params['biz_content']=$bizString;
////3.生成待签名字符串
//$stringToBeSigned = $paysubmit->createStringToBeSigned_alipay($params);
//
//
////-----开始记录日志----//
//$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
//fwrite($file,"\n 生成待签名字符串：".$stringToBeSigned);
//fwrite($file,"\n 请求数据：".urldecode(http_build_query($params)));
//fclose($file);
////-----结束记录日志----//
//
////4.执行加密业务参数
////$screct_key = "MIIEowIBAAKCAQEAv8Bw55L98ObIgRdjcvqLYqEXzesYov3Ku9OLSoSYf7LwWpYlfd8SJKws/6mgbtKrxX/QpW5yARzoVk1+XJQAGIbt64VFtkpoxtrpAOpvd3JVZpjKsTE8AfmVg71mptpcMA3CzIv7TnaO59zeleEwUWFKGB2Ifot+QjZ9IOL2nDQa8S/IorG3HAtZVLqccTmsmP/TO7ipQQx+JHE59PjNI9kQtFiye6GqbZuQCGH1ZH3lWi1FPhPjH/wYXhg2TU/tbh0J2spldwuTr1FITKajNq+3VbmkMJR3xecf85dmM95EwtiPIJsrMezBef5QJf1EgqCilLWGjWa2X36XO3S32QIDAQABAoIBAFCQ+t9R04C0dupGeXKF8qquJOJQNTnGiLgCWjQlIi4FcReenoIUh/sAnKePDemCesmwumnHzf7JtpUMWltrk966UpzykifVe9CRifToi40UbO1+pStuFFiAFWIU4lyYwsMuBPsMlbqR9dlV4+/1feeziP446nDBC3umiUyYECXwxFyShcnik1fBwX1ccNSM+Tl45IyVScHDFsO8N+lzywSRm66/8yMJ/0GCRE1qIs9KVdjQ57GX2sUc6+dXG8MHgqRfrHdnY3oIh5Hh3P9wfy/+385l4sOFyq6BsXC41bWQbmyhfBEiN4TpBeSv83wvp06wV32Z8ct3wPQr5EJR4AECgYEA5Fl6Mxl9cuwjvhwbTPaV8IX3N3HPiR7ro7R9+qxZyI1YD923mlie8dvuTPKjrqdGA693gQAEXYb4IifVDHpSAm0MyefFtlNed/2Ni1w2em1G1q2CLKGL5nh5thKHGK3RU2zQEAltDhZNqm9IMk/SutBgG4MvErWlHBOj3rDAVUECgYEA1vh7ZpBqSeRw0TnU4KaKndFqNRmMsT+cPLK2Jq/k46LhsFxlk5Jzmffaucp9az6CW5z2UdRzqjf1X+8hqmY5g6khWSvOI+U872srR2n9ok+/5gmYcRMxfaCc3ZWzRhnaCGt0QoZQGOBp4rPvdVaCRLD9rluW+C49i5udNVw8xJkCgYAdfMyB8sW3ZHUNJLYFz0X3hGUzPLgvHdYSEsm96CnD8zSu/9Oo087arqrsNt0aPbGV4j/NYcIujbJXPsuNS/JxK8HeBNcMP2HQnuZ7W5IRZzEhodBQkR/uyBn3gBcAAdopKsEHgSAbFJg87Rfmn1Y4F5aBlwQqJ1jF8mdT54E+wQKBgQCSyHYSETfSHp597lwRzQXHPu+jND/h40o+E9TWU7IZUOWDs4NUIWK31gpOZBoBOVxvS5zRQRx9NNph1/mHzWcmJDz2EzgdQHC8FdQmXhPmWUfxvcmOJAnd+uZBUu9nRU2gBCcNKYLViZ4jQrOVzi0C1EtTW4yZB5HUmrvcbI+9uQKBgCmQcbJ9v35XwVdmkwjW6prm5prEo9OfnIritOFLCep8qiFZXPErd404uu4v4TBnMe8e2XjNLFGkJKbIQbqR0OdfStHKj1VJY8aY+VwTIwlGFraNT9Sn5TXj04jkBClYHRjUJST0ujLvBYUZN2TLXdCCapjKtA+kCzhoTwpkEhcL";
//$screct_key = "MIIEowIBAAKCAQEAxZ01Gh41ITYOLRQNtUJFFe+12Zqu6sO4ql7QUyART0y4vTEVVPxxMVrgnLBzgUDUErbXYoYtvOrFI1KwdXAN+JBU4bdeZC+WVuAn/KhWdh/07uS8Aoc/7FKD9U+Cn5yHXVOZsLxCL+Nz5CxLW3LglN02+Vm1f7b4eAILnn++fwmhLRcyjBtVj2UVTU/7qkkkSNMGSD0eotOjxiWzJAZXVaRXibNZq3Q02NDQ/zK8/Lp3IqRrhirHb1546kt+X+tAy0ETdmxm5ZvFm9YeR2HewXxGE3q74Z06Q/aa1qpv8GUDuHF1xQM6eygQDGyu+3om4dQK2krCrUSIb6z0Q7swGwIDAQABAoIBAE89PXjOmhFKIp8SxnhjLV6hczLhYrhCaP7v6+sZFbfNlNpQHzSn0R+wSBasbnRqsV8br/wSv65cGVaTpqxAlWmRPmgP5iqYJlmJop8eRSUypT1RvM3qu8ggQkeQbVlhI6pZhmwm4Qdg1iytUj5GLyWiGpQb07p1fOZqM4yHvrQjTMwhiyohUUCJ/kyFLJuwBSS3aR9SIEV1HPO8ssHrpGgTSPXv1kO5N866uIWiyFfGGSBPJgOy+ARC59pt1rjcA5nuR0A13qHHuZh8Vd++mUrRdcxErLm8czQmz5TZW4SPTxzppx+viTIYvzGnPSpjCdz21H1Gwj0w1vDzEJS21xkCgYEA61nmdgxmYHPjBJcQ3GAVuew9KU5x6Iyhpbh13x5wvVYD/uEB6URzIPKTL0It3/S4zqoI8B2GxJ1vPYDlGiAzNrG2n8TgLdvaamMqal3WUO9vIkhg96TZbwZ7PbgvCNrysyPK8jombVG6qHJhDkwTQBMXF3/WkNv+Un/WTSX29X0CgYEA1vO23aSc+Ejc1tn26Aboy9u8w1ajhKWtA+tcW3Ga9M2MNcWlrdFbxtp6O2EzhqWcoPTUbayvoDQSg93asf/sceM0CEIaOljOGD7lcpWO2yU6SUWiyFCqTPkiceG74P2/MuVStNJtgSvJD42XZvXBAlZdKCbhHFF2pQ1EknJbz3cCgYBIXy/QU0XUGS7RRYcIzX9A2hWAsz+x7DT1GgEdKGYwwtedtCF7UvI4Sn/aQ3aJ7N47puvyspGzulnvxsgsvYQiKInpHYh2can7lxz+8nYqE0bQewNeg9HdI1gYhZ/pEDMbxUDuNdWFPmGw1ONlmx08UeKC9mvLxeqyAd+rf6YunQKBgGL5680ZPFIzMsUXrlJexBkCgGOt5DNzGjzAxlEw+XoZn6Mu9EAaM9lXxorLEi/A2GNg+OPbbS8maxQfNtFZl6VFSjM3RN5y/8s4QuzFIveTp8gPcYotYo149jxcBefuz3h/EdtDPbsJz1YDC6EULiCGZfTUGjmw5SHQ/y8zy0S3AoGBALlQ1BNAwd+dbLILAf3V+KnupnOlrVDMI0S8t27VrXiiWJUbw/8Il8kQyPhvR32OD2bEJlHB/K+N6ptmPFWnAxuu0T5+urEWCDeaVOCHEnp7QYMhxVe2hDnMKkuuv0KukVc6GWHkR1gUQXNFK3E5E4gO3gf8SxhMAjQS5tDq4z4I";
//$sign = $paysubmit->sign_alipay($stringToBeSigned,$screct_key,'',"RSA2");
//
////-----开始记录日志----//
//$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
//fwrite($file,"\n 签名：".$sign);
//fclose($file);
////-----结束记录日志----//
//
////5.赋值签名
//$params['sign'] = $sign;
//
////6.请求数据
////发起HTTP请求
//$url = "https://openapi.alipay.com/gateway.do";
//$resp = $paysubmit->buildRequestForm_alipay($url,$params);
//
////-----开始记录日志----//
//$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
//fwrite($file,"\n 返回结果：".$resp);
//fclose($file);
////-----结束记录日志----//
//
////7.跳转页面
//echo $resp;
//
////-----开始记录日志----//
//$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
//fwrite($file,"\n ------------------------ \n");
//fclose($file);
////-----结束记录日志----//
?>
