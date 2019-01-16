<?php

namespace app\user\model;

use app\common\model\BaseModel;

class Usergoodlist extends BaseModel
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

    public function get_one_by_session_userid_on_daili($condition = [])
    {
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

    public function del_list_by_condition_on_user($condition)
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
            exit();
        } else {
            $condition['userid'] = session('user.userid');
        }
        $del = $this->del_list_by_condition_on_base($condition);
        return $del;
    }

    public function get_column_by_session_userid_on_user($field, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_column_by_condition_on_base($condition, $field);
        return $res;
    }

    public function get_column_by_condition_on_index($field, $condition = [])
    {
        if (empty($condition) || $condition == '') {
            exit();
        }
        $res = $this->get_column_by_condition_on_base($condition, $field);
        return $res;
    }

    public function get_count_by_condition_on_daili($condition)
    {
        $count = $this->get_count_by_condition_on_base($condition);
        return $count ? $count : 0;
    }

    public function get_goodlist_list_on_user($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, 'sortid,goodlistid desc');
        $list = $res[0];
        $goodlistid_in = [];
        foreach ($list as $k => $v) {
            $goodlistid_in[$k] = $v['goodlistid'];
        }
        $product_model = new Userproduct();
        unset($condition['cateid']);
        unset($condition['is_sub']);
        $count_onsell_kami_in = $product_model->group_field_by_condition_on_base(array_merge($condition, ['goodlistid' => ['in', $goodlistid_in], 'is_state' => '1', 'is_trush' => '0']), 'goodlistid,count(*) AS count', 'goodlistid');
        $count_selled_kami_in = $product_model->group_field_by_condition_on_base(array_merge($condition, ['goodlistid' => ['in', $goodlistid_in], 'is_state' => '0', 'is_trush' => '0']), 'goodlistid,count(*) AS count', 'goodlistid');
        foreach ($list as $k => $v) {
            $list[$k]['count_onsell_kami'] = '0';
            $list[$k]['count_selled_kami'] = '0';
            if (!empty($count_onsell_kami_in)) {
                foreach ($count_onsell_kami_in as $k2 => $v2) {
                    if ($v['goodlistid'] == $v2['goodlistid']) {
                        $list[$k]['count_onsell_kami'] = $v2['count'];
                    }
                }
            }
            if (!empty($count_selled_kami_in)) {
                foreach ($count_selled_kami_in as $k2 => $v2) {
                    if ($v['goodlistid'] == $v2['goodlistid']) {
                        $list[$k]['count_selled_kami'] = $v2['count'];
                    }
                }
            }
        }
        $res[0] = $list;
        return $res;
    }

    public function get_dlgoodlist_list_on_daili($condition = [])
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, 'sortid,goodlistid desc');
        $list = $res[0];
        $goodlistid_in = [];
        foreach ($list as $k => $v) {
            $goodlistid_in[$k] = $v['parent_id'];
        }
        $product_model = new Userproduct();
        unset($condition['cateid']);
        unset($condition['is_sub']);
        unset($condition['userid']);
        $count_onsell_kami_in = $product_model->group_field_by_condition_on_base(array_merge($condition, ['goodlistid' => ['in', $goodlistid_in], 'is_state' => '1', 'is_trush' => '0']), 'goodlistid,count(*) AS count', 'goodlistid');
        $count_selled_kami_in = $product_model->group_field_by_condition_on_base(array_merge($condition, ['goodlistid' => ['in', $goodlistid_in], 'is_state' => '0', 'is_trush' => '0']), 'goodlistid,count(*) AS count', 'goodlistid');
        foreach ($list as $k => $v) {
            $list[$k]['count_onsell_kami'] = '0';
            $list[$k]['count_selled_kami'] = '0';
            if (!empty($count_onsell_kami_in)) {
                foreach ($count_onsell_kami_in as $k2 => $v2) {
                    if ($v['goodlistid'] == $v2['goodlistid']) {
                        $list[$k]['count_onsell_kami'] = $v2['count'];
                    }
                }
            }
            if (!empty($count_selled_kami_in)) {
                foreach ($count_selled_kami_in as $k2 => $v2) {
                    if ($v['goodlistid'] == $v2['goodlistid']) {
                        $list[$k]['count_selled_kami'] = $v2['count'];
                    }
                }
            }
        }
        $res[0] = $list;
        return $res;
    }
}