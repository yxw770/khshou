<?php
$adminpayprovider_arr = cache('adminpayprovider');
foreach ($adminpayprovider_arr as $k => $v) {
    if ($v['directory'] == 'wftweixin') {
        $adminpayprovider_row = $v;
    }
}
$apiurl = $adminpayprovider_row['apiurl'];
$accountid = $adminpayprovider_row['accountid'];
$password = $adminpayprovider_row['password'];