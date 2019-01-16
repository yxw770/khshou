<?php

namespace app\user\model;

use app\common\model\BaseModel;

class Userpayment extends BaseModel
{
    public function get_one_by_session_userid_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_one_by_condition_on_base($condition);
        return $res;
    }

    public function get_value_by_session_userid_on_user($field, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_value_by_condition_on_base($condition, $field);
        return $res;
    }

    public function update_one_by_session_userid_on_user($post, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->update_one_by_condition_on_base($post, $condition);
        return $res;
    }

    public function insert_one_by_arr_on_user($arr)
    {
        $res = $this->insert_one_by_arr_on_base($arr);
        return $res;
    }

    public function get_list_by_session_userid_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition);
        return $res;
    }
} 