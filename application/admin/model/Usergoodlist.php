<?php

namespace app\admin\model;

use app\common\model\BaseModel;

class Usergoodlist extends BaseModel
{
    public function get_one_by_condition_on_admin($condition = [])
    {
        $res = $this->get_one_by_condition_on_base($condition);
        return $res;
    }

    public function update_one_by_condition_on_admin($post, $condition = [])
    {
        $res = $this->update_one_by_condition_on_base($post, $condition);
        return $res;
    }

    public function get_list_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = 'admin')
    {
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        return $res;
    }
} 