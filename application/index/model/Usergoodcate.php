<?php

namespace app\index\model;

use app\common\model\BaseModel;

class Usergoodcate extends BaseModel
{
    public function get_column_by_condition_on_index($field, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            exit();
        }
        $res = $this->get_column_by_condition_on_base($condition, $field);
        return $res;
    }
}