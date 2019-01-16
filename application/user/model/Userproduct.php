<?php

namespace app\user\model;

use app\common\model\BaseModel;

class Userproduct extends BaseModel
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

    public function insert_all_by_arr_on_user($arr)
    {
        $res = $this->inser_all_by_arr_on_base($arr);
        return $res;
    }

    public function get_kami_list_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        unset($condition['is_sub']);
        $res = $this->get_list_by_condition_on_base($condition);
        $list = $res[0];
        $goodlist_model = new Usergoodlist();
        foreach ($list as $k => $v) {
            $list[$k]['goodname'] = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $v['goodlistid']]);
            $list[$k]['price'] = $goodlist_model->get_value_by_session_userid_on_user('price', ['goodlistid' => $v['goodlistid']]);
        }
        $res[0] = $list;
        return $res;
    }

    public function get_export_kami_on_user($condition = [], $goodlistid, $offset = '', $limit = '20')
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, [], [], []);
        if (!empty($res)) {
            $goodlist_model = new Usergoodlist();
            $goodname = $goodlist_model->get_value_by_session_userid_on_user('goodname', ['goodlistid' => $goodlistid]);
            $return[0] = $goodname;
            $return[1] = $res;
            return $return;
        } else {
            return [];
        }
    }
}