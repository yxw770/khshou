<?php

namespace app\admin\model;

use app\common\model\BaseModel;

class Admin extends BaseModel
{
    public function get_one_by_condition_on_login($condition = [])
    {
        $res = $this->get_one_by_condition_on_base($condition);
        return $res;
    }

    public function get_one_by_session_on_admin($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['adminid' => session('admin.adminid'),];
        } else {
            $condition['adminid'] = session('admin.adminid');
        }
        $res = $this->get_one_by_condition_on_base($condition);
        return $res;
    }

    public function update_one_by_condition_on_admin($post, $condition = [])
    {
        $res = $this->update_one_by_condition_on_base($post, $condition);
        return $res;
    }

    public function insert_one_by_arr_on_admin($arr)
    {
        $res = $this->insert_one_by_arr_on_base($arr, true, true);
        return $res;
    }

    public function get_list_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = 'admin')
    {
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        return $res;
    }

    public function get_column_by_condition_on_admin($field, $condition = [])
    {
        $res = $this->get_column_by_condition_on_base($condition, $field);
        return $res;
    }
} 