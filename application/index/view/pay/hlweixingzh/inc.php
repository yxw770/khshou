<?php
/**
 * Created by PhpStorm.
 * User: admin123123
 * Date: 2018/8/26
 * Time: 16:56
 */
$adminpayprovider_arr = cache('adminpayprovider');
foreach ($adminpayprovider_arr as $k => $v) {
    if ($v['directory'] == 'hlweixingzh') {
        $adminpayprovider_row = $v;
    }
}
$apiurl = $adminpayprovider_row['apiurl'];
$accountid = $adminpayprovider_row['accountid'];
$password = $adminpayprovider_row['password'];
