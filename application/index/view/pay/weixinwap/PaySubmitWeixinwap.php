<?php
class PaySubmitWeixinwap{
    /**
     * 将数据转为XML
     * return string
     */
    public static function toXml_normal($array){
        $xml = '<xml>';
        forEach($array as $k=>$v){
            $xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
        }
        $xml.='</xml>';
        return $xml;
    }
    /**
     * 数组转换成xml字符串
     * QQ二维码扫码支付官方api适用
     * @param $arr
     * @return string
     */
    public static function arrayToXml_qqrcode($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val){
            if (is_numeric($val)){
                $xml.="<$key>$val</$key>";
            }
            else
                $xml.="<$key><![CDATA[$val]]></$key>";
        }
        $xml.="</xml>";
        return $xml;
    }
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
    /*
     * 执行http调用
     */
    public function http_curl_normal($url,$date) {
        //启动一个CURL会话
        $ch = curl_init();

        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $date);

        // 执行操作
        $res = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($res == NULL) {
            echo $errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
            curl_close($ch);
            return false;
        } else if($responseCode  != "200") {
            echo $errInfo = "call http err httpcode=" . $responseCode  ;
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $res;
    }
    /*
     * 执行http调用
     * qqrcode扫码官方版本能用
     */
    public function http_curl_qqrcode($url,$date, $timeout = 10, $needSSL = false) {
        //启动一个CURL会话
        $ch = curl_init();

        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 隐藏文件头
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $date);
        //是否使用ssl证书
        if(isset($needSSL) && $needSSL != false){
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, 'CERT_FILE_PATH路径');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, "SSLKEY路径");
        }

        // 执行操作
        $res = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($res == NULL) {
            echo $errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
            curl_close($ch);
            return false;
        } else if($responseCode  != "200") {
            echo $errInfo = "call http err httpcode=" . $responseCode  ;
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $res;
    }

    /**
     * 创建md5签名,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     * return string
     */
    public function createSign_normal($arr,$userkey) {
        //第一步：对参数按照key=value的格式，并按照参数名ASCII字典序排序
        ksort($arr);
        $signPars = "";
        foreach($arr as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        //第二步：拼接API密钥并md5
        $signPars .= "key=" . $userkey;
        //转成大写
        $sign = strtoupper(md5($signPars));
        return $sign;
    }

    /**
     * 将参数转为hash形式
     * @param $params
     * @param $urlencode
     * @return string
     */
    public static function httpbuildquery_qqrcode($params) {
        $arrTmp = array();
        foreach ($params as $k => $v){
            //参数为空不参与签名
            if(isset($v) && $v != ''){
                array_push($arrTmp, "$k=$v");
            }
        }
        return implode('&', $arrTmp);
    }
}