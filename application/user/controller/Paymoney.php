<?php

namespace app\user\controller;

use app\user\model\Userpayment;

class Paymoney extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $userpaymoent_model = new Userpayment();
        $reserpayment_arr = $userpaymoent_model->get_list_by_session_userid_on_user();
        $reserpayment_list = $reserpayment_arr[0];
        $page = $reserpayment_arr[1];
        $assign_arr = array('title' => "结算记录", 'list' => $reserpayment_list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
} 