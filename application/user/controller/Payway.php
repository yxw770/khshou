<?php

namespace app\user\controller;

use app\user\model\Userpaychannel;

class Payway extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $userpaychennel_model = new Userpaychannel();
        $condition = ['is_trush' => '0',];
        $userpaychennel_arr = $userpaychennel_model->get_list_by_session_userid_on_user($condition, [], [], '');
        $assign_arr = array('title' => "付款方式", 'list' => $userpaychennel_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
} 