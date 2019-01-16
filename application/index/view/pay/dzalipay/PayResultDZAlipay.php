<?php
class PayResultDZAlipay{
        //------------------------------点缀支付开始------------------------------//
        /**
         * 生成签名
         * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
         */
        public function MakeSign_dianzhui($params,$key)
        {
            //签名步骤一：按字典序排序参数
            ksort($params);
            $string = $this->ToUrlParams_dianzhui($params);
            //签名步骤二：在string后加入KEY
            $string = $string . "&Key=".$key;
            //签名步骤三：MD5加密
            $string = md5($string);
            //签名步骤四：所有字符转为大写
            $result = strtoupper($string);
            return $result;
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
    //------------------------------点缀支付结束------------------------------//



}