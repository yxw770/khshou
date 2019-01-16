<?php

namespace app\common\oss;

use think\queue\Job;
use Mailermaster\PHPMailer;

class Oss
{
    public function upload_oss($filename, $filepath)
    {
        $erweima_url = '';
        $file = $_FILES[$filename];
        $file_type = $file['type'];
        if (!empty($file['size'])) {
            $extArray = array('image/png' => 'png', 'image/x-png' => 'png', 'image/gif' => 'gif', 'image/jpeg' => 'jpg', 'image/pjpeg' => 'jpg');
            if (!array_key_exists($file_type, $extArray)) {
                $msg = '仅支持jpg/bmp/png/gif格式的文件上传';
                return json_error($msg);
            }
            $fp = fopen($file['tmp_name'], 'r');
            $bin = fread($fp, 2);
            fclose($fp);
            $str_info = @unpack("C2chars", $bin);
            $type_code = intval($str_info['chars1'] . $str_info['chars2']);
            $file_type_erjinzhi = '';
            switch ($type_code) {
                case 7790:
                    $file_type_erjinzhi = 'exe';
                    break;
                case 7784:
                    $file_type_erjinzhi = 'midi';
                    break;
                case 8075:
                    $file_type_erjinzhi = 'zip';
                    break;
                case 8297:
                    $file_type_erjinzhi = 'rar';
                    break;
                case 255216:
                    $file_type_erjinzhi = 'jpg';
                    break;
                case 7173:
                    $file_type_erjinzhi = 'gif';
                    break;
                case 6677:
                    $file_type_erjinzhi = 'bmp';
                    break;
                case 13780:
                    $file_type_erjinzhi = 'png';
                    break;
                default:
                    $file_type_erjinzhi = 'unknown';
                    break;
            }
            $wejinzhiArray = array('png', 'gif', 'bmp', 'jpg');
            if (!in_array($file_type_erjinzhi, $wejinzhiArray)) {
                $msg = '仅支持jpg/bmp/png/gif格式的文件上传';
                return json_error($msg);
            }
            $file_size = $file['size'];
            if ($file_size <= 0 || $file_size > 1024 * 1024 * 3) {
                $msg = '上传的文件最大支持3MB';
                return json_error($msg);
            }
            $file_error = $file['error'];
            $file_tmp = $file['tmp_name'];
            if ($file_error <> 0) {
                $msg = '上传出现意外错误，请稍候重试...';
                return json_error($msg);
            }
        }
        if (!empty($file['size'])) {
            $filename_arr = explode('.', $file['name']);
            $filename = $filename_arr[0];
            $prefix = $filename_arr[1];
            $prefix = $extArray[$file_type];
            $new_filename = md5(time() . $filename);
            $upload_pathName = $filepath;
            require_once EXTEND_PATH . "aliyun-oss-php-sdk-master/autoload.php";
            $ossClient = new \OSS\OssClient("LTAI4vCVM93Qnkln", "hmq4NVZD76W2gLkiUSOeJVxsb3YQ1k", "http://oss-cn-shanghai.aliyuncs.com");
            $result = $ossClient->uploadFile("957ka", "upload/" . $upload_pathName . "/" . date("Y-m-d", time()) . "/" . $new_filename . "." . $prefix, $file['tmp_name']);
            $erweima_url = $result['oss-request-url'];
        }
        return json_success('', $erweima_url);
    }
}