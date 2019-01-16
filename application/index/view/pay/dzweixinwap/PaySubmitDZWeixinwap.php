<?php
class PaySubmitDZWeixinwap{
    //------------------------------基本版开始------------------------------//
    /**
     * 将数据转为XML
     * return string
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
     * 4.、xml转数组格式数据格式
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
     * 1.、创建md5签名,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     * return string
     */
    public function createSign($arr,$userkey) {
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
     * 创建待签名的字符串，按参数名称a-z排序,遇到空值的参数不参加签名。
     * return string
     */
    public function createStringToBeSigned($arr) {
        //第一步：对参数按照key=value的格式，并按照参数名ASCII字典序排序
        ksort($arr);
        $signPars = "";
        foreach($arr as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        //第二步：去除最后一个&
        $sign=substr($signPars,0,strlen($signPars)-1);
        return $sign;
    }
    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    public function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }
    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    public function characet($data,$fileType, $targetCharset) {
        if (!empty($data)) {
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
    //------------------------------基本版结束------------------------------//

    //------------------------------QQ官方电脑网站扫码版本开始------------------------------//
    /**
     * 2.、数组转换成xml字符串
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
     * 3.、执行http调用
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
    //------------------------------QQ官方扫码电脑网站版本结束------------------------------//

    //------------------------------支付宝官方电脑网站扫码版本开始------------------------------//

    /*alipay3.、
     * 生成待签名的字符串
     */
    public function createStringToBeSigned_alipay($params,$targetCharset='UTF-8') {
        //1.对数组进行排序
        ksort($params);

        //2.设置变量用于保存待签名字符串
        $stringToBeSigned = "";
        $i = 0;
        //3.遍历数组中的数据
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 4.把数组中的数据转换成目标字符集
                $v = $this->characet_alipay($v, $targetCharset);
                //5.组装待签名字符串
                //首个字符拼接时
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                    //首个字符后面的字符拼接时，
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }
    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet_alipay($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = 'UTF-8';
            //如果用忽略大小写比较字符串，为真。
            //如果要转换的字符串类型值和UTF-8不同，那么把要转换的字符串，，进行从UTF-8，到，$targetCharset，转换
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

    /*
     * 生成待签名字符串
     * 此方法对value做urlencod
     * get方式传递可能存在字符乱码问题,所以先urlencoe转码再传递数据
     */
    public function createUrlencodeStringToBeSigned_alipay($params, $targetCharset='UTF-8') {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet_alipay($v, $targetCharset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . urlencode($v);
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }
    /*
     * alipay1.、
     * 处理业务参数
     */
    public function CreateBizContentString($bizContent)
    {
        if(!empty($bizContent)){
            $bizContent = json_encode($bizContent,JSON_UNESCAPED_UNICODE);
        }
        return $bizContent;
    }

    /*
     * alipay4.、
     * 使用待签名字符串生成签名RSA格式的
     * $data 待签名的字符串，参数名=参数值&参数名=参数值..连接的方式
     * $RSAPath 私钥文件放的路径
     * 如果没有私钥文件只有私钥字符串，就用商户私钥$priKey
     */
    public function sign_alipay($data,$priKey,$RSAPath="",$signType = "RSA") {
        if($this->checkEmpty($RSAPath)){
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($priKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        }else {
            //读取私钥文件
            $priKey = file_get_contents($RSAPath);
            //转换为openssl格式密钥
            $res = openssl_get_privatekey($priKey);
        }

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        if ("RSA2" == $signType) {
            //openssl_sign() 用私钥 priv_key_id 对 data 生成一个加密的签名。数据本身并不会被加密。
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
            //成功签名返回签名到变量 signature.
        } else {
            openssl_sign($data, $sign, $res);
        }

        if(!$this->checkEmpty($RSAPath)){
            //释放资源
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    public function sign($data, $priKey,$signType = "RSA") {
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        $sign = base64_encode($sign);
        return $sign;
    }
    /**
     * 加密方法
     * @param string 业务参数biz_content $str 字符串类型数据
     * @return string
     */
    public function encrypt($str,$screct_key){
        //AES, 128 模式加密数据 CBC
        $screct_key = base64_decode($screct_key);
        $str = trim($str);
        $str = addPKCS7Padding($str);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
        $encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
        return base64_encode($encrypt_str);
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    public function buildRequestForm_alipay($url,$para_temp) {
        $string = '';
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$url."?charset=UTF-8' method='POST'>";
        while (list ($key, $val) = each ($para_temp)) {
            if (false === $this->checkEmpty($val)) {
                //把'转化为XML格式中的单引号
//                $val = str_replace("'","&apos;",$val);
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
//                $string .= "key=" . $key . " value=" . $val;
            }
        }
//        echo $string;
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        //用于返回给控制器echo $sHtml; 来提交
        return $sHtml;
    }

    /*
     * alipay6.、
     * curl提交数据
     */
    public function curl_alipay($url, $postFields = null,$targetChaer='UTF-8') {
        //启动一个CURL会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $postBodyString = "";
        $encodeArray = Array();
        $postMultipart = false;
        //判断是数组格式，并且数组个数>0
        if (is_array($postFields) && 0 < count($postFields)) {
            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) //判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($this->characet_alipay($v,$targetChaer )) . "&";
                    $encodeArray[$k] = $this->characet_alipay($v, $targetChaer);
                } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                    $encodeArray[$k] = new \CURLFile(substr($v, 1));
                }
            }
            unset ($k, $v);
            //发送一个常规的POST请求。
            curl_setopt($ch, CURLOPT_POST, true);
            //要传送的所有数据
            if ($postMultipart) {//发送文件
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
            } else {//发送字符串
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        if ($postMultipart) {//发送文件
            $headers = array('content-type: multipart/form-data;charset=' . $targetChaer . ';boundary=' . $this->getMillisecond());
        } else {

            $headers = array('content-type: application/x-www-form-urlencoded;charset=' . $targetChaer);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // 执行操作
        $reponse = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    /*
     * 获取一个数字
     */
    public function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }




    //------------------------------支付宝官方电脑网站扫码版本开始------------------------------//



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
            if (false === $this->checkEmpty($val)) {
                //把'转化为XML格式中的单引号
//                $val = str_replace("'","&apos;",$val);
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
//                $string .= "key=" . $key . " value=" . $val;
            }
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

    public function curl_dianzhui($url,$param,$second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch,CURLOPT_TIMEOUT, $second);
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