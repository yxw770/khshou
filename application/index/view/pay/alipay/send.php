<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/23
 * Time: 18:47
 */

//require_once APP_PATH_INDEX.'pay/'.$directory.'/PaySubmit.class.php';
//dump(APP_PATH_INDEX . 'pay/' . $directory . '/PaySubmit.class.php');
//\think\Loader::addNamespace('dmfalipay','../application/index/view/pay/dmfalipay/');
require_once APP_PATH_INDEX . 'pay/' . $directory . '/PaySubmit.php';


//$Test = new \my\Test();
//$Test->sayHello();
//exit();


//1.组装数据
$biz_content = array(
    'out_trade_no' => $orderid,
    'product_code' => 'FAST_INSTANT_TRADE_PAY',
    'total_amount' => $price,
    'subject' => "客服QQ:860529523 订单号:" . $orderid,
    'body' => "客服QQ:860529523 订单号:" . $orderid,
);
$params = array(
    'app_id' => '2018121562576045',
    'method' => 'alipay.trade.page.pay',
    'charset' => 'UTF-8',
    'sign_type' => 'RSA2',
    'timestamp' => date('Y-m-d H:i:s'),
    'version' => '1.0',
    'notify_url' => request()->domain() . '/notify/' . $directory,
    'return_url' => request()->domain() . '/orderquery?kw=' . $orderid,
);
$paysubmit = new PaySubmit();
//2.生成业务参数字符串
$bizString = $paysubmit->CreateBizContentString($biz_content);
$params['biz_content'] = $bizString;
//3.生成待签名字符串
$stringToBeSigned = $paysubmit->createStringToBeSigned_alipay($params);


//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'log.txt', 'a+');
fwrite($file, "\n 生成待签名字符串：" . $stringToBeSigned);
fclose($file);
//-----结束记录日志----//

//4.执行加密业务参数
//$screct_key = "MIIEowIBAAKCAQEAy4ohzMlKI5MUpGUqKQzPNXRL8Xokuie2UMRhS5c1jcB8T+SKkJcWJp/yZc8GR97xANMpC14rlnTCMKzxWbz0ImgthhnIUxmAHHxQfk/SQG0G/31Z5uS7JwGCovOAwKS7hYUURfclKoNgbxyQtwmbBL8Pt1GG+E5Gx2zl3aIhZ0g+k4zx2JtngEpam3/JSoTLj0rHYvpXmswtLSVm8s0JL9kHFSUO1idlUJCTczfLNBFhJMT4amaBeKbqS/Nza7UHsISmZrh1K+l3ohdmwVDVPqsYXFvQPtZqnNNGCCli/k8Wake3gGMZDVWGRZJcmK9LrrQ37bSOfE7dkRDVWuHCSQIDAQABAoIBAGDmzXqypkJThrNRmoXanFfFm1ZGoW+i8nB3Xh7fyVZIOqbKlpGJ7LjM01lW+5WB9VlALr1DwxqpUb5cx7bCd7RdxraeVboCXPSy1pucIuwbPAasxf1gDh3e4TP1G+obQ0+o8OLya9ZWn7WmcttBjnTvBWtwPALBOX8+QB2JT7/cUtdHaYgYmd+atrjEqWuXVb4tbWg+6SJBX+lcpCOn/eibFxQW74KXPjcmxQru7RYcRKBwrymi+R4PDovm5pL+MQL9GtzssSy86VNN+ckKywY20CchjhmMh4doyjLzXDt80+KgRK1/ySRshlsb8xOx9aghu50aU5zqsafysO3tgRECgYEA52GuTnY2bLP7QEVQiGP3YCHe7LRCT+uznMQ86/kg+fqYbfB1vDzH+3xYyxr1kYunVvQjR3nCtUDrr7EDC84CbK3fF8JOP50nHFyj7oMlRiQmVAk81CUSSsM31Zxd4WAsGeoNIDycDc2IyNPP9sCPjE5t+iSLGgq113D4ODRrOj0CgYEA4TIY6z8g/HheT+Ct98jzhsUhQk3ugdhHu4UzwYKT0U48dRTZbu0a+muKVITkttbVna7aSqW2Pokr4o0LaUhPwu8JP13ZNkax9nK5GG87AhpY/8cQ/lsrjecC5cLPqA8LFzhtRRQae27AMDmpAqzJHiIgphRFDvB5qx606MtFRP0CgYEA1aiAkZldNlGYT8fxzpAo7Q85z+/AUBaBe+BFtU0OY2dA/DwQK+sga5xuTdauLmD7/DJipWzNHBw/xrO48VyhJoQDvufA48MVS35MLkHR2IwnDHCfR/KXiovr8gd7NVNeReu3hS4SmDhT98aHgf5cT55YtaSrAjgjpDaALz5QSa0CgYB9C5torXI8nv4HrsQLLiuDr1zLe4iv0w/p4Yoersoz1BSgEsa14LU1TpfOXMi/ekW9vET8ZE3xJawXragjewLHRIJHDG67w7+4EHYVSlZi0YyP4tLd2nQx5I/oUyeJs2i+KRgL3qDh31qarLnbgWRf9iVY8zJxnDiDO5bLGWxKZQKBgCtNYqYzAJZlwupSrnIcrmTl63VisqWvZL9NIb6Hg9rIN+FyBm9pREXx4lB4iuAB/JVvPnNTQedTPNJa/jTxwCn+3YvoZ7dAEeC/gIxawEFvaMwA4zk9pivwfDYkBczRu5gSxRqq8FwEkbZQF10cV+lmuUWEpFmEvMoXNq/HMnGK";
$screct_key = "MIIEowIBAAKCAQEAyrdTOUiI7VG1M7X/z1vOu1EKvu56QK42kVbeivfsQQpwlKGe4Re787NN3x92TyzUZ+Q9tJiJnZxrH9rKjNctmPcsNb+iJvwYyTVJ7k6U5Zl3XCk/5m36CzL4rkX5MIYJmouWZAB0M9wxm3jICLx3DZscozhC9f5MlGfgcrOjIpyKBCApVgbCqhKshTVvnPozIEERdAeiEMuRAnRBiM86REDw21/qARYqzZRrB+1nhk8R5iFO/x0ex4PYk2wOfR6Vt9DH0A5oHUK4vWmkDA7fpnPnyYq4CjV7MOFcUWAN2m7z7vO3KCGT+XeLaudGPf+oRhzgGAejZ+TzysoGEVpqdwIDAQABAoIBAG9J48OgAaQf5lXERfMF2OHXJQobDDy0J7r0sOokH6y5WYMPk5TJneK1fjvPHvlqiBWdJQ8favV/KQRs7iQSAbNnhvrfANtcLRNRUQwmUfIWpYOQFcegetRpIEM0oRA1ldIlbePqp+DMsuNClLbPGKD8leU5fvFjsZEL0hnGavoVcdGteCoxWu3Ku9QKxHout/JlF4e4tvCSa+C4R9YcNfwO34hVBpeov2Fmgo5MYY/JwR/MdWTVIuq3Tb4ZGxw9GuRSnMQmTu8ibiUbz9NYv/xLIzX6r+bgPBBT55zZmzLY2qxiI5zV/21hfYZzgFCInTAEc3kla9bFNnLWkLdAI5ECgYEA8cHpqncZP8xopwvi+ydhCBpmw86973Legoo1BqUD/X+BEqh+hMu4RxRgbnWHSh3xx8tEXTLOZo8Pgv7u+ligs4kwSQcSJM6tQstefXyDgtOZ3FNLfKkZyq55/5AYyw6AHEa78xo9ilmi/2j9YHQv5gehuQefC+++VGhu6sRzm58CgYEA1qibRvubDwVw3C20NwT5/60GihdUXaxA6e6NI2DSrBIX6HNMTX0JT2XMezB3AeuTH8lahTlm659KLFDWd1r1IJR2zmnnVEfklJgdM9gCXOxGEKmOTgDJs2TpRfAm8z6ouZRGbF2vIGs/wqVfVF3JxQsBNS8DFeCx7kVPsJgYwikCgYBxNDwWdm58UVsv3YEL2gS1pqc4STbnSQKoXc82rU6RBYOLGRslU7+WiRQepSoUqrDsvWHlwkSkAZlh3b5Ju/aEG1lg2M7GLYE0Jca5O/TY7fy+DsLqPMS3A7xczzzM2J1koDqiO0dV4WNR9fevkrhbHne8DLWNAVY+cCiABj2cQQKBgHlkZFuiEjBOfWKmnhG3SMHkUZxqURWgQh2JAFwPd1RD7y1Pdskjes5h3V+VKtf1JwCE2CIbMi7xp/qjxIU+9Pz1uLJGhcHN3mCCHZGiCMQQAheOK6HFJEvFN36LrxtERhqc1a6W1GDbBfXHm7+UQnSiIo63BWGr7jO3Erfla6rxAoGBAIsj9XUrkMNTcru6aovDid4PxvuIVDqYfl77tGCDFTKFfF3Rpa8hEZHor8hIAFWhgUAOrBfMlm3IDqZiyYlA1vpLopr5F0+nR9+Uu+jM5N0w3X6hvXAQDlOk/lzNcRByS2LyVDro0sHZiCjL9OPf4T5vlvHl2ZDR9Dbc5snBWHBn";
$sign = $paysubmit->sign_alipay($stringToBeSigned, $screct_key, '', "RSA2");

//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'log.txt', 'a+');
fwrite($file, "\n 签名：" . $sign);
fclose($file);
//-----结束记录日志----//

//5.赋值签名
$params['sign'] = $sign;


//-----开始记录日志----//
$file = fopen(APP_PATH_INDEX . 'pay/' . $directory . "/" . date('Ymd') . 'log.txt', 'a+');
fwrite($file, "\n 请求数据：" . urldecode(http_build_query($params)));
fclose($file);
//-----结束记录日志----//

//6.请求数据
//发起HTTP请求
$url = "https://openapi.alipay.com/gateway.do";
$resp = $paysubmit->buildRequestForm_alipay($url, $params);
echo $resp;
