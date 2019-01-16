<?php
class PaySubmitDZAliapy{

    //------------------------------点缀支付开始------------------------------//
    /**
     * 1.、创建md5签名,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     * return string
     */
    public function createSign_dianzhui($arr,$userkey) {
        //第一步：对参数按照key=value的格式，并按照参数名ASCII字典序排序
        ksort($arr);
        $signPars = "";
        foreach($arr as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        //第二步：拼接API密钥并md5
        $signPars .= "Key=" . $userkey;
        //转成大写
        $sign = strtoupper(md5($signPars));
        return $sign;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    public function buildRequestForm_dianzhui($url,$para_temp) {
        $string = '';
        $sHtml = "<form id='alipaysubmit' name='' action='".$url."?charset=UTF-8' method='POST'>";
        while (list ($key, $val) = each ($para_temp)) {
                //把'转化为XML格式中的单引号
//                $val = str_replace("'","&apos;",$val);
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
//                $string .= "key=" . $key . " value=" . $val;
        }
//        echo $string;
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        //用于返回给控制器echo $sHtml; 来提交
        return $sHtml;
    }

    /*
     * 4.、xml转数组格式数据格式
     */
    public function xmlToArray_dianzhui($content) {
        libxml_disable_entity_loader(true);
        //接收xml数据并转化为对象
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        //转化为数组
        $array = json_decode(json_encode($xml),true);
        return $array;

    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams_dianzhui($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "Sign" && $v."" != "" && !is_array($v)){
                if(is_float($v))
                {
                    $buff .= $k . "=" . sprintf('%.2f', $v) . "&";
                }
                else
                {
                    $buff .= $k . "=" . $v . "&";
                }
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function curl_dianzhui($url,$param)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch,CURLOPT_URL, $url);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new Exception("curl出错，错误码:$error");
        }
    }

    //------------------------------点缀支付结束------------------------------//



}