<?php

namespace app\user\controller;

use app\user\model\Userlog;

class Log extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $userlog = new Userlog();
        $condition = [];
        $userlog_row = $userlog->get_list_by_session_userid_on_user($condition);
        $list = $userlog_row[0];
        $page = $userlog_row[1];
        $assign_arr = array('title' => "登陆日志", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
} 