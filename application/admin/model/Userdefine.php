<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class Userdefine extends BaseModel
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

    public function get_column_by_condition_on_admin($field, $condition = [])
    {
        $res = $this->get_column_by_condition_on_base($condition, $field);
        return $res;
    }

    public function get_userlist_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
    {
        $table_name = $this->getPk();
        if (empty($order)) {
            $order = [$table_name => 'desc'];
        }
        if (!empty($limit)) {
            if (!isset($offset) || empty($offset)) {
                $offset = request()->get('page') ? request()->get('page') : '1';
            }
            $object_list = $this->alias('u')->join('userpay p', 'u.userid=p.userid', 'left')->join('userdefine f', 'u.userid=f.userid', 'left')->where($condition)->order('u.' . $table_name . ' desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->get_count_by_condition_on_base($condition);
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

    public function get_one_user_by_condition_on_admin($userid, $order = [], $field = '', $append = [])
    {
        $table_name = $this->getPk();
        $object_row = $this->alias('u')->join('userpay p', 'u.userid=p.userid', 'left')->join('userdefine f', 'u.userid=f.userid', 'left')->where('u.userid=' . $userid)->find();
        $return = [];
        if (!empty($object_row)) {
            if (!empty($append)) {
                $return = $object_row->append($append)->toArray();
            } else {
                $return = $object_row->toArray();
            }
        }
        return $return;
    }
} 