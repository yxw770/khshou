<?php

use think\Session;

function encrypt_userpass($world)
{
    $pwd = md5($world . userpass_token);
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    return $hash;
}

function decrypt_userpass($password, $hash)
{
    $pwd = md5($password . userpass_token);
    $boolean = password_verify($pwd, $hash);
    return $boolean;
}

function encrypt_usertoken($world)
{
    $pwd = md5($world . usertoken_token);
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    return $hash;
}

function decrypt_usertoken($password, $hash)
{
    $pwd = md5($password . usertoken_token);
    $boolean = password_verify($pwd, $hash);
    return $boolean;
}

function encrypt_usercard($world)
{
    $pwd = md5($world . usercard_token);
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    return $hash;
}

function decrypt_usercard($password, $hash)
{
    $pwd = md5($password . usercard_token);
    $boolean = password_verify($pwd, $hash);
    return $boolean;
}

function encrypt_adminrpass($world)
{
    $pwd = md5($world . adminpass_token);
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    return $hash;
}

function decrypt_adminpass($password, $hash)
{
    $pwd = md5($password . adminpass_token);
    $boolean = password_verify($pwd, $hash);
    return $boolean;
}

function encrypt_admintoken($world)
{
    $pwd = md5($world . admintoken_token);
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    return $hash;
}

function decrypt_admintoken($password, $hash)
{
    $pwd = md5($password . admintoken_token);
    $boolean = password_verify($pwd, $hash);
    return $boolean;
}

function encrypt_admincard($world)
{
    $pwd = md5($world . admincard_token);
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    return $hash;
}

function decrypt_admincard($password, $hash)
{
    $pwd = md5($password . admincard_token);
    $boolean = password_verify($pwd, $hash);
    return $boolean;
}

function json_error($msg = "操作失败", $orther = '')
{
    $json_arr = array('status' => '0', 'msg' => $msg, 'orther' => $orther,);
    return json_encode($json_arr);
}

function json_success($msg = "操作成功", $orther = '')
{
    $json_arr = array('status' => '1', 'msg' => $msg, 'orther' => $orther,);
    return json_encode($json_arr);
}

function getRandomString($len, $md5type = false)
{
    $chars = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    $charsLen = count($chars) - 1;
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    $output = substr(strtoupper(md5(md5(uniqid()) . md5(microtime()) . md5($output), $md5type)), 0, $len);
    return $output;
}

function trim_arr($arr)
{
    foreach ($arr as $k => $v) {
        $arr[$k] = htmlspecialchars(trim($v));
    }
    return $arr;
}

function define_filed_sort_arr($arr, $filed, $rule = 'desc')
{
    if ($rule == 'asc') {
        $arr_map = array_column($arr, $filed);
        array_multisort($arr_map, SORT_ASC, $arr);
        return $arr;
    }
    if ($rule == 'desc') {
        $arr_map = array_column($arr, $filed);
        array_multisort($arr_map, SORT_DESC, $arr);
        return $arr;
    }
}

function get_a_new_sessionid()
{
    if (PHP_SESSION_ACTIVE != session_status()) {
        session_start();
    }
    session_regenerate_id(true);
    return session_id();
}

function logout_user()
{
    if (session('?user')) {
        session('user', null);
    }
    Session::flush();
    session_regenerate_id();
}

function logout_admin()
{
    if (session('?admin')) {
        session('admin', null);
    }
    Session::flush();
    session_regenerate_id();
}

function dwz($url, $t = 'suoim')
{
    $Adminconfig_arr = cache('adminconfig');
    if (in_array($Adminconfig_arr['dwz'], array_keys(unserialize(dwz_arr)))) {
        $t = $Adminconfig_arr['dwz'];
    }
    if ($t == 'monarch') {
        $base_url = 'http://957ka.net/creat?url=' . $url . '&k=test123123';
        $res = http_curl_get($base_url);
        return $res;
    }
    if ($t == 'redis') {
        $base_url = 'http://957ka.net/redisc?url=' . $url . '&k=test123123';
        $res = http_curl_get($base_url);
        return $res;
    }
    if ($t == 'suoim') {
        $res = http_curl_get('http://suo.im/api.php?url=' . urlencode($url));
        return $res;
    }
    if ($t == 'ft12') {
        $res = http_curl_get('http://api.ft12.com/api.php?url=' . urlencode($url));
        return "http://" . substr($res, '16');
    }
    if ($t == 'weibo') {
        $res = curl_post('https://api.weibo.com/2/short_url/shorten.json', array('source' => '2474783709', 'url_long' => $url));
        $r = json_decode($res, true);
        if (!isset($r['error_code'])) {
            return $r['urls'][0]['url_short'];
        }
    }
    if ($t == 'dwz') {
        $res = curl_post('http://dwz.mn/create.aspx', array('url' => $url));
        $r = json_decode($res, true);
        if ($r['status'] == '0') {
            return $r['tinyurl'];
        }
    }
    return false;
}

function curl_post($url, $param, $second = 30)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    $data = curl_exec($ch);
    if ($data) {
        curl_close($ch);
        return $data;
    } else {
        curl_close($ch);
        echo("curl出错:$ch");
    }
}

function http_curl_get($url = "", $arr = '', $type = 'get', $res = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($type == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    if ($res == 'json') {
        return json_decode($output, true);
    } else {
        return $output;
    }
}

function unsert_null_value($arr)
{
    foreach ($arr as $k => $v) {
        if ("" == $v || is_null($v)) {
            unset($arr[$k]);
        }
    }
    return $arr;
}

function export_txt($content, $txtname)
{
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $ext = substr($txtname, -4);
    $encoded_filename = urlencode($txtname);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);
    header('Pragam:no-cache');
    header('Content-Transfer-Encoding: binary');
    header('Expires:0');
    header('Content-Type: application/octet-stream');
    if (preg_match("/MSIE/", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $txtname . '"');
    }
    if ($ext == '.xls') {
        echo $content;
    } else {
        echo mb_convert_encoding($content, 'gbk', 'utf-8');
    }
}

function getPageUrl()
{
    return request()->url(true);
}

function string_format($mysql_arr)
{
    foreach ($mysql_arr as $k => $v) {
        foreach ($v as $k2 => $v2) {
            if (strstr($v2, '.')) {
                $mysql_arr[$k][$k2] = number_format($v2, '2', '.', '');
            }
        }
    }
    return $mysql_arr;
}

function get_weeks($format = 'Y-m-d', $time = '')
{
    $time = $time != '' ? $time : time();
    $date = [];
    for ($i = 1; $i <= 7; $i++) {
        $date[$i] = date($format, strtotime('+' . $i - 7 . ' days', $time));
    }
    return $date;
}

function getIp()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else {
            if ($_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown")) {
                $ip = $_SERVER["REMOTE_ADDR"];
            } else {
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $ip = "unknown";
                }
            }
        }
    }
    $index = strpos($ip, ',');
    if($index){
        $ip = substr($ip, 0, $index);
    }
    return ($ip);
}

function isMobile()
{
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (isset ($_SERVER['HTTP_VIA'])) {
    }
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'ios');
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

function lock_url($txt, $key = 'www.zhuoyuexiazai.com')
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
    $nh = rand(0, 64);
    $ch = $chars[$nh];
    $mdKey = md5($key . $ch);
    $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
    $txt = base64_encode($txt);
    $tmp = '';
    $i = 0;
    $j = 0;
    $k = 0;
    for ($i = 0; $i < strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = ($nh + strpos($chars, $txt[$i]) + ord($mdKey[$k++])) % 64;
        $tmp .= $chars[$j];
    }
    return urlencode($ch . $tmp);
}

function unlock_url($txt, $key = 'www.zhuoyuexiazai.com')
{
    $txt = urldecode($txt);
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
    $ch = $txt[0];
    $nh = strpos($chars, $ch);
    $mdKey = md5($key . $ch);
    $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
    $txt = substr($txt, 1);
    $tmp = '';
    $i = 0;
    $j = 0;
    $k = 0;
    for ($i = 0; $i < strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
        while ($j < 0) $j += 64;
        $tmp .= $chars[$j];
    }
    return base64_decode($tmp);
}

function de_build_query($string)
{
    $arr = explode('&', $string);
    $arr_submit = [];
    foreach ($arr as $k => $v) {
        list($k2, $v2) = explode('=', $v);
        $arr_submit[$k2] = $v2;
    }
    return $arr_submit;
}

function strlen_utf8($str)
{
    $i = 0;
    $count = 0;
    $len = strlen($str);
    while ($i < $len) {
        $chr = ord($str[$i]);
        $count++;
        $i++;
        if ($i >= $len) {
            break;
        }
        if ($chr & 0x80) {
            $chr <<= 1;
            while ($chr & 0x80) {
                $i++;
                $chr <<= 1;
            }
        }
    }
    return $count;
}

function writelog($str)
{
    $file = fopen(ROOT_PATH . "runtime/" . date('YmdH') . "_log.txt", "a");
    fwrite($file, date('Y-m-d H:i:s') . "：" . $str . "\r\n");
    fclose($file);
}

function write_wx_log($str)
{
    $file = fopen(WX_LOG . "" . date('YmdH') . "_log.txt", "a");
    fwrite($file, date('Y-m-d H:i:s') . "：" . $str . "\r\n");
    fclose($file);
}