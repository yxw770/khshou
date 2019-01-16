<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class Usersettlement extends BaseModel
{
    public function get_one_by_condition_on_admin($condition = [])
    {
        $res = $this->get_one_by_condition_on_base($condition);
        return $res;
    }

    public function get_value_by_condition_on_admin($field, $condition = [])
    {
        $res = $this->get_value_by_condition_on_base($condition, $field);
        return $res;
    }

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

    public function get_column_by_condition_on_admin($field, $condition = [])
    {
        $res = $this->get_column_by_condition_on_base($condition, $field);
        return $res;
    }

    public function get_settlementsum_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
    {
        $table_name = $this->getPk();
        if (empty($order)) {
            $order = [$table_name => 'desc'];
        }
        if (!empty($limit)) {
            if (!isset($offset) || empty($offset)) {
                $offset = request()->get('page') ? request()->get('page') : '1';
            }
            $where = '';
            if (isset($condition['userid']) && $condition['userid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 's.userid=' . $condition['userid'];
            }
            if (isset($condition['username']) && $condition['username'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "u.username like '%" . $condition['username'] . "%'";
            }
            $field = "
            s.*,
            p.*,
            u.username,
            u.addtime,
            u.flag
            ";
            $object_list = $this->alias('s')->join('userpay p', 'p.userid=s.userid', 'left')->join('user u', 'u.userid=s.userid', 'left')->field($field)->where($where)->order('totlepay desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->alias('s')->join('userpay p', 'p.userid=s.userid', 'left')->join('user u', 'u.userid=s.userid', 'left')->field($field)->where($where)->count();
            $page = $page_model->get_page($count, $limit);
        } else {
            $object_list = $this->where($condition)->field(true)->order($order)->select();
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