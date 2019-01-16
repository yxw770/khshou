<?php

namespace app\admin\controller;

use app\admin\model\Admincheck;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpay;
use app\admin\model\Userpaychannel;
use app\admin\model\Userpayment;
use app\admin\model\Usersettlement;
use think\Db;

class Settlementsum extends AdminBase
{
    public function index()
    {
        $usersettlement_model = new Usersettlement();
        $condition = [];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'username') {
                        $condition['username'] = $get['keyword'];
                    }
                }
                $usersettlement_arr = $usersettlement_model->get_settlementsum_by_condition_on_admin($condition);
                $list = $usersettlement_arr[0];
                $page = $usersettlement_arr[1];
            } else {
                $usersettlement_arr = $usersettlement_model->get_settlementsum_by_condition_on_admin($condition);
                $list = $usersettlement_arr[0];
                $page = $usersettlement_arr[1];
            }
        }
        $assign_arr = array('title' => "结算统计", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
}