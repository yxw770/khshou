<?php

namespace app\index\controller;

use think\Controller;

class Pay extends Controller
{
    public function index()
    {
        if (request()->isGet()) {
            $get_arr = trim_arr(input('get.'));
            $orderid = $get_arr['orderid'];
            $price = $get_arr['price'];
            $providerid = $get_arr['pid'];
            $directory = $get_arr['payway'];
            $adminpayprovider_row = $this->getprovider($providerid);
            $apiurl = $adminpayprovider_row['apiurl'];
            $accountid = $adminpayprovider_row['accountid'];
            $password = $adminpayprovider_row['password'];
            $assign_arr = array('orderid' => $orderid, 'price' => $price, 'directory' => $directory, 'apiurl' => $apiurl, 'accountid' => $accountid, 'password' => $password,);
            $this->assign($assign_arr);
            return $this->fetch(APP_PATH_INDEX . 'pay/' . $directory . "/send.php");
        }
        return false;
    }

    private function getprovider($providerid)
    {
        $adminpayprovider_arr = cache('adminpayprovider');
        foreach ($adminpayprovider_arr as $k => $v) {
            if ($v['providerid'] == $providerid) {
                $adminpayprovider_row = $v;
            }
        }
        if (!empty($adminpayprovider_row)) {
            return $adminpayprovider_row;
        } else {
            return [];
        }
    }
}