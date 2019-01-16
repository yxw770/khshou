<?php

namespace app\index\model;

use app\common\model\BaseModel;

class Guestconfig extends BaseModel
{
    public function get_one_on_home()
    {
        $res = $this->where('id', '1')->find()->toArray();
        return $res;
    }
}