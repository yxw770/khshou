<?php

namespace app\user\model;

use app\common\model\BaseModel;
use app\common\model\BasePage;

class Usercoupon extends BaseModel
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

    public function get_list_by_session_userid_on_user($condition = [], $order = [], $offset = '', $limit = '20')
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        return $res;
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

    public function del_list_by_condition_on_user($condition)
    {
        $del = $this->del_list_by_condition_on_base($condition);
        return $del;
    }

    public function insert_all_by_arr_on_user($arr)
    {
        $res = $this->inser_all_by_arr_on_base($arr);
        return $res;
    }

    public function get_list_by_condition_on_user($condition = [], $order = [], $offset = '', $limit = '20')
    {
        if (empty($condition) || $condition == '') {
            $condition = ['userid' => session('user.userid'),];
        } else {
            $condition['userid'] = session('user.userid');
        }
        if (!empty($limit)) {
            if (!isset($offset) || empty($offset)) {
                $offset = request()->get('page') ? request()->get('page') : '1';
            }
            $where = [];
            if (isset($condition['userid']) && $condition['userid'] != '') {
                $where['c.userid'] = $condition['userid'];
            }
            if (isset($condition['goodlistid']) && $condition['goodlistid'] != '') {
                $where['c.goodlistid'] = $condition['goodlistid'];
            }
            if (isset($condition['is_state']) && $condition['is_state'] != '') {
                $where['c.is_state'] = $condition['is_state'];
            }
            if (isset($condition['couponcode']) && $condition['couponcode'] != '') {
                $where['c.couponcode'] = ['like', '%' . $condition['couponcode'] . '%'];
            }
            $field = 'c.*,g.goodname';
            $object_list = $this->alias('c')->join('usergoodlist g', ['c.goodlistid=g.goodlistid'], 'left')->where($where)->field($field)->order('c.couponid desc')->page($offset, $limit)->select();
            $page_model = new BasePage();
            $count = $this->alias('c')->join('usergoodlist g', ['c.goodlistid=g.goodlistid'], 'left')->where($where)->count();
            $page = $page_model->get_page($count, $limit);
        }
        $list = [];
        if (!empty($object_list)) {
            foreach ($object_list as $item => $value) {
                if (!empty($append)) {
                    $list[] = $value->append($append)->toArray();
                } else {
                    $list[] = $value->toArray();
                }
            }
        }
        if (!empty($limit)) {
            $return[] = $list;
            $return[] = $page;
            return $return;
        } else {
            return $list;
        }
    }
} 