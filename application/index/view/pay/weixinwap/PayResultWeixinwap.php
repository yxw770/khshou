<?php
class PayResultWeixinwap{
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
}