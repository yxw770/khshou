<?php

namespace app\user\model;

use app\common\model\BaseModel;

class Usercheck extends BaseModel
{
    public function get_one_by_id_on_user($userid)
    {
        $res = $this->where('userid', $userid)->find();
        if (!empty($res)) {
            return $res->toArray();
        } else {
            return false;
        }
    }

    public function update_one_by_id_on_user($data, $userid)
    {
        $number_id = $this->save($data, ['userid' => $userid]);
        return $number_id;
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
} 