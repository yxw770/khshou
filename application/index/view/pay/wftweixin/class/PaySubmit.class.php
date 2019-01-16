<?php
class PaySubmit{
    /**
     * 将数据转为XML
     */
    public static function toXml($array){
        $xml = '<xml>';
        forEach($array as $k=>$v){
            $xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
        }
        $xml.='</xml>';
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
    //执行http调用
    public function http_curl($url,$date) {
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
}