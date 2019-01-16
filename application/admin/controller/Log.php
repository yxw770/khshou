<?php

namespace app\admin\controller;

use app\admin\model\Adminlog;
use app\admin\model\Userlog;

class Log extends AdminBase
{
    public function index()
    {
        $adminlog_model = new Adminlog();
        $condition = ['adminid' => session('admin.adminid'),];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['adminname'])) {
                    $condition['adminname'] = $get['adminname'];
                }
                if (!empty($get['loginip'])) {
                    $condition['loginip'] = $get['loginip'];
                }
                if (!empty($get['to_time'])) {
                    $from_time = date('Y-m-d', strtotime($get['to_time']));
                    $to_time = date('Y-m-d H:i:s', strtotime($get['to_time']) + 60 * 60 * 24 - 1);
                    $condition['logintime'] = ['between', [$from_time, $to_time]];
                }
                $userloglist_arr = $adminlog_model->get_list_by_condition_on_admin($condition);
                $list = $userloglist_arr[0];
                $page = $userloglist_arr[1];
            } else {
                $userloglist_arr = $adminlog_model->get_list_by_condition_on_admin($condition);
                $list = $userloglist_arr[0];
                $page = $userloglist_arr[1];
            }
        }
        $assign_arr = array('title' => "登录日志", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
} 