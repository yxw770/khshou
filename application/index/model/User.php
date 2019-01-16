<?php

namespace app\index\model;

use app\common\model\BaseModel;

class User extends BaseModel
{
    public function get_one_by_username_on_home($username)
    {
        $res = $this->getByUsername($username);
        return $res;
    }
}