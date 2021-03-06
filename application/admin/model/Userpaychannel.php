<?php

namespace app\admin\model;

use app\common\model\BaseModel;

class Userpaychannel extends BaseModel
{
    public function update_one_by_condition_on_admin($post, $condition = [])
    {
        $res = $this->update_one_by_condition_on_base($post, $condition);
        return $res;
    }

    public function insert_one_by_arr_on_admin($arr, $allow = true, $admin = true)
    {
        $res = $this->insert_one_by_arr_on_base($arr, $allow, $admin);
        return $res;
    }

    public function get_list_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = 'admin')
    {
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        return $res;
    }

    public function del_list_by_condition_on_admin($condition)
    {
        if (empty($condition) || $condition == '') {
            exit();
        }
        $del = $this->del_list_by_condition_on_base($condition);
        return $del;
    }
} 