<?php

namespace app\admin\controller;

use app\admin\model\Adminpaychannel;
use app\admin\model\User;
use app\admin\model\Userpay;
use think\Db;

class Busanalysis extends AdminBase
{
    public function index()
    {
        $user_model = new User();
        $condition = [];
        $adminpaychennel_model = new Adminpaychannel();
        $adminpaychennel_arr = $adminpaychennel_model->get_list_by_condition_on_admin([], [], [], []);
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['to_time']) && !empty($get['from_time'])) {
                    $from_time = date('Y-m-d', strtotime($get['from_time']));
                    $to_time = date('Y-m-d H:i:s', strtotime($get['to_time']) + 60 * 60 * 24 - 1);
                    $condition['from_time'] = $from_time;
                    $condition['to_time'] = $to_time;
                }
                if (isset($get['channelid']) && $get['channelid'] != '') {
                    $condition['channelid'] = $get['channelid'];
                }
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'username') {
                        $condition['username'] = $get['keyword'];
                    }
                }
                $userlist_arr = $user_model->get_busanalysis_by_condition_on_admin($condition);
                $list = string_format($userlist_arr[0]);
                $page = $userlist_arr[1];
            } else {
                $zero_timestapm = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $from_time = date('Y-m-d H:i:s', $zero_timestapm);
                $to_time = date('Y-m-d H:i:s');
                $condition['from_time'] = $from_time;
                $condition['to_time'] = $to_time;
                $userlist_arr = $user_model->get_busanalysis_by_condition_on_admin($condition);
                $list = string_format($userlist_arr[0]);
                $page = $userlist_arr[1];
            }
        }
        $assign_arr = array('title' => "商户分析", 'list' => $list, 'paychannel_arr' => $adminpaychennel_arr, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
}