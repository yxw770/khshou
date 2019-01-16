<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/27
 * Time: 8:40
 */

require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmitWeixinwap.php';

//1.组装数据
$params=array(
    'appid'=>$apiurl,
    'mch_id'=>$accountid,
    'nonce_str'=>mt_rand(time(),time()+rand()),
    'body'=>"客服QQ:860529523 订单号:".$orderid,
    'out_trade_no'=>$orderid,
    'fee_type'=>'CNY',
    'total_fee'=>$price*100,
    'spbill_create_ip'=>getIp(),
    'notify_url' => request()->domain() . '/notify/' . $directory,
    'trade_type'=>'MWEB',
    'scene_info'=>'{"h5_info": {"type":"Wap","wap_url": "ceshi.bugufa.com","wap_name": "微信支付"}} ',
);

//2.生成签名
$paysubmit = new PaySubmitWeixinwap();
$sign = $paysubmit->createSign_normal($params,$password);

//3.组装数据
$params['sign'] = strtoupper($sign);

//4.格式化数据为xml格式数据
$xmldata=$paysubmit::arrayToXml_qqrcode($params);

//5.提交数据，执行http请求
$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
$res=$paysubmit->http_curl_qqrcode($url,$xmldata);

//5.获取数据
$arr = $paysubmit->xmlToArray($res);

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX.'pay/'.$directory."/".date('Ymd').'log.txt', 'a+');
fwrite($file,"\n 请求数据：".urldecode(http_build_query($params)));
fwrite($file,"\n 返回结果：".urldecode(http_build_query($arr)));
fwrite($file,"\n ------------------------ \n");
fclose($file);
//-----结束记录日志----//

$url_pay = $arr['mweb_url'];

//6.组装同步跳转通知
$redirect_url = request()->domain() . '/orderquery?kw=' . $orderid;
$url_redirect_url = $url_pay . "&redirect_url=" . urldecode($redirect_url);

//7.执行跳转
?>
<script src="/pay/Monarch/js/jquery-2.2.1.min.js"></script>
<a href="<?php echo $url_redirect_url; ?>" style="display: none" id="clicka"></a>
<script>
    $(function () {
        // 打印$("a")[0]，得到的是http://www.mo2g.com，但实际上$("a")[0]是一个object对象，或许说是DOM对象更贴切一些。经过测试发现，其实原生的js就已经实现了类似的点击a标签的功能。原生js写法类似如下：
        document.getElementsByTagName("a")[0].click();
        //或
        // 只要获取到A标签的DOM对象，就能使用click()函数激活点击事件了。
        // $('#clicka')[0].click();
    })
</script>