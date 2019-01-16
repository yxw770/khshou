<?php
namespace dmfalipay;

class Paynotify{
    //------------------------------基本版开始------------------------------//

    /*
     * xml转数组格式数据格式
     */
    public function xmlToArray($content) {
        libxml_disable_entity_loader(true);
        //接收xml数据并转化为对象
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        //转化为数组
        $array = json_decode(json_encode($xml),true);
        return $array;

    }

    /**
     *创建md5签名,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     */
    public function createSign($arr,$userkey) {
        $signPars = "";
        ksort($arr);
        foreach($arr as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $userkey;
        $sign = strtoupper(md5($signPars));
        return $sign;
    }
    //------------------------------基本版结束------------------------------//

    //------------------------------支付宝官方电脑网站扫码版本开始------------------------------//

    /** rsaCheckV1 & rsaCheckV2
     *  验证签名
     *  在使用本方法前，必须初始化AopClient且传入公钥参数。
     *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
     **/
    public function rsaCheckV1_alipay($params, $rsaPublicKey,$signType='RSA') {
        $sign = $params['sign'];
        $params['sign_type'] = '';
        $params['sign'] = '';
        return $this->verify($this->getSignContent_alipay($params), $sign, $rsaPublicKey,$signType);
    }
    public function rsaCheckV2_alipay($params, $rsaPublicKey, $signType='RSA') {
        $sign = $params['sign'];
        $params['sign'] = '';
        $params['sign_type'] = '';
        return $this->verify($this->getSignContent_alipay($params), $sign, $rsaPublicKey, $signType);
    }

    /*
     * 生成待签名字符串
     * 下一步是使用RSA生成签名
     * return string
     */
    public function getSignContent_alipay($params) {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if ($v!='' && "@" != substr($v, 0, 1)) {
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    /*
     * RSA生成签名校验数据
     */
    public function verify($data, $sign, $rsaPublicKey, $signType = 'RSA') {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($rsaPublicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }



        //-----开始记录日志----//
//        $file = fopen(date('Ymd').'notify.txt', 'a+');
//        fwrite($file,"\r\n 组装后的数据信息：".($data));
//        fwrite($file,"\r\n base64解密后的数据信息：".base64_decode($sign));
//        fclose($file);
        //-----结束记录日志----//

        //释放资源
//        openssl_free_key($res);
        return $result;
    }



        //------------------------------支付宝官方电脑网站扫码版本开始------------------------------//


}