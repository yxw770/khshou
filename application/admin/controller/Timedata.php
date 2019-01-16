<?php

namespace app\admin\controller;

use app\admin\model\Adminpaychannel;
use app\admin\model\User;
use app\admin\model\Userpay;
use think\Db;

class Timedata extends AdminBase
{
    protected $table = false;

    public function index()
    {
        $orderlist_model = new \app\admin\model\Orderlist();
        $condition = ['date' => date('Y-m-d'),];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['to_time'])) {
                    $condition['date'] = $get['to_time'];
                }
                $paychennel_arr = $orderlist_model->get_timedata_by_condition_on_admin($condition);
                $list = string_format($paychennel_arr);
            } else {
                $paychennel_arr = $orderlist_model->get_timedata_by_condition_on_admin($condition);
                $list = string_format($paychennel_arr);
            }
        }
        $assign_arr = array('title' => "实时数据", 'list' => $list,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
} 