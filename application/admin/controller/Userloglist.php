<?php

namespace app\admin\controller;

use app\admin\model\Userlog;

class Userloglist extends AdminBase
{
    public function index()
    {
        $userloglist_model = new Userlog();
        $condition = [];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['username'])) {
                    $condition['username'] = $get['username'];
                }
                if (!empty($get['logip'])) {
                    $condition['logip'] = $get['logip'];
                }
                if (!empty($get['to_time'])) {
                    $from_time = date('Y-m-d', strtotime($get['to_time']));
                    $to_time = date('Y-m-d H:i:s', strtotime($get['to_time']) + 60 * 60 * 24 - 1);
                    $condition['logtime'] = ['between', [$from_time, $to_time]];
                }
                $userloglist_arr = $userloglist_model->get_list_by_condition_on_admin($condition);
                $list = $userloglist_arr[0];
                $page = $userloglist_arr[1];
            } else {
                $userloglist_arr = $userloglist_model->get_list_by_condition_on_admin($condition);
                $list = $userloglist_arr[0];
                $page = $userloglist_arr[1];
            }
        }
        $assign_arr = array('title' => "登录日志", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
}