<?php
require_once 'http.class.php';
require_once 'xml.class.php';
class wx{
    private $debug=true;
    private $appid;
    private $mchid;
    private $key;
    private $device_info='WEB';
    private $tradetype='NATIVE';
    private $openid='';

    function __construct(){
        $this->gateUrl='https://api.mch.weixin.qq.com/pay/unifiedorder';
        $this->queryUrl='https://api.mch.weixin.qq.com/pay/orderquery';
        $this->xml=new Xml();
        $this->nonce_str=time()+mt_rand(10000,99999);
    }

    public function setAppID($email){
        $this->appid=$email;
    }

    public function setMchID($userid){
        $this->mchid=$userid;
    }

    public function setKey($key){
        $this->key=$key;
    }

    public function setTradeType($t){
        $this->tradetype=$t;
    }

    public function setOpenID($openid){
        $this->openid=$openid;
    }

    public function submitOrder($data){
        $data['appid']=$this->appid;
        $data['mch_id']=$this->mchid;
        $data['device_info']=$this->device_info;
        $data['nonce_str']=$this->nonce_str;
        $data['detail']='';
        $data['attach']='';
        $data['fee_type']='CNY';
        $data['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
        $data['time_start']='';
        $data['time_expire']='';
        $data['goods_tag']='';
        $data['trade_type']=$this->tradetype;
        $data['product_id']='';
        $data['limit_pay']='';
        $data['openid']=$this->openid;
        $data['sign']=$this->makeSign($data);

        $http=new Http($this->gateUrl,$this->xml->toXml($data));
        $http->toUrl();
        $ret=$this->xml->parseXml($http->getResContent());
        //$this->logs('submitOrder',$ret);
        if($ret['return_code']=='SUCCESS'){
            if($ret['result_code']=='SUCCESS'){
                return array('status'=>1,'url'=>$ret['code_url']);
            } else {
                return array('status'=>0,'msg'=>$ret['err_code'].'|'.$ret['err_code_des']);
            }
        } else {
            return array('status'=>0,'msg'=>$ret['return_msg']);
        }
    }

    public function orderNotify(){
        $data=file_get_contents('php://input');
        if(!$data) return false;
        $ret=$this->xml->parseXml($data);
        //$this->logs('orderNotify',$ret);
        if($ret['return_code']=='SUCCESS'){
            if($ret['result_code']=='SUCCESS'){
                $mySign=$this->makeSign($ret);
                if($ret['sign']==$mySign){
                    return array('status'=>1,'orderid'=>$ret['out_trade_no'],'cash_fee'=>$ret['cash_fee']);
                } else {
                    return array('status'=>0,'mysign='.$mySign.'|sign='.$ret['sign']);
                }
            } else {
                return array('status'=>0,'msg'=>$ret['err_code'].'|'.$ret['err_code_des']);
            }
        } else {
            return array('status'=>0,'msg'=>$ret['return_msg']);
        }
    }

    public function orderQuery($orderid){
        $data=array(
            'appid'=>$this->appid,
            'mch_id'=>$this->mchid,
            'out_trade_no'=>$orderid,
            'nonce_str'=>$this->nonce_str,
        );
        $data+=array('sign'=>$this->makeSign($data));

        $http=new Http($this->queryUrl,$this->xml->toXml($data));
        $http->toUrl();
        $ret=$this->xml->parseXml($http->getResContent());
        //$this->logs('orderQuery',$ret);
        if($ret['return_code']=='SUCCESS'){
            if($ret['result_code']=='SUCCESS'){
                if($ret['trade_state']=='SUCCESS'){
                    return array('status'=>1,'cash_fee'=>$ret['cash_fee']);
                } else {
                    return array('status'=>0,'msg'=>$ret['trade_state']);
                }
            } else {
                return array('status'=>0,'msg'=>$ret['err_code'].'|'.$ret['err_code_des']);
            }
        } else {
            return array('status'=>0,'msg'=>$ret['return_msg']);
        }
    }

    public function makeSign($data){
        ksort($data);
        $str='';
        foreach($data as $key=>$val){
            if($key!='sign' && $val!=''){
                $str.=$str ? '&' : '';
                $str.=$key.'='.$val;
            }
        }
        $str.='&key='.$this->key;
        return strtoupper(md5($str));
    }

    public function logs($title,$data){
        if(!$this->debug) return false;
        $handler = fopen('result.txt','a+');
        $content = "================".$title."===================\n";
        if(is_string($data) === true){
            $content .= $data."\n";
        }
        if(is_array($data) === true){
            forEach($data as $k=>$v){
                $content .= "key: ".$k." value: ".$v."\n";
            }
        }
        $flag = fwrite($handler,$content);
        fclose($handler);
    }
}
?>
