<?php

namespace app\common\controller;

use think\Controller;

class Webconfig extends Controller
{
    function _initialize()
    {
        $adminconfig_cache = cache('adminconfig');
        $is_sitestate = $adminconfig_cache['is_sitestate'];
        $closesitemsg = $adminconfig_cache['closesitemsg'];
        if ($is_sitestate == '0') {
            header("Content-type: text/html; charset=utf-8");
            echo $closesitemsg;
            exit();
        }
    }
}