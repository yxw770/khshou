<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/26
 * Time: 16:56
 */



//------------ 2018年8月26日12:56:41 不使用随机使用接入商家 开始 ------------//

function get_adminpayprovider_by_orderid($orderid)
{
    $row_order = db('orderlist')->where(['orderid' => $orderid])->find();
    $providerid = $row_order['providerid'];
    $adminpayprovider_arr = cache('adminpayprovider');
    foreach ($adminpayprovider_arr as $k => $v) {
        if ($v['providerid'] == $providerid) {
            $adminpayprovider_row = $v;
        }
    }
    $return['apiurl'] = $adminpayprovider_row['apiurl'];
    $return['accountid'] = $adminpayprovider_row['accountid'];
    $return['password'] = $adminpayprovider_row['password'];
    return $return;
}


//echo $apiurl = $adminpayprovider_row['apiurl'];
//echo "<hr>";
//echo $accountid = $adminpayprovider_row['accountid'];
//echo "<hr>";
//echo $password = $adminpayprovider_row['password'];
//echo "<hr>";
//------------ 2018年8月26日12:56:41 不使用随机使用接入商家 结束 ------------//