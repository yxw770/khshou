<?php

namespace app\index\controller;

use app\common\controller\Webconfig;

class Bank extends Webconfig
{
    function _initialize()
    {
        parent::_initialize();
        $gusetconfig_arr = cache('guestconfig');
        $assign_arr = array('qtarr' => $gusetconfig_arr,);
        $this->assign($assign_arr);
    }

    public function index()
    {
        $url = urldecode(input('url'));
        $orderid = input('orderid');
        $assign_arr = array('title' => '微信支付', 'js' => '/paytheme/bank/js', 'layer' => '/paytheme/bank/layer', 'orderid' => $orderid, 'url' => $url,);
        $this->assign($assign_arr);
        $this->view->config('view_path', APP_PATH_INDEX . "/");
        return $this->fetch('paytheme/bank');
    }
}