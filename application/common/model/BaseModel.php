<?php

namespace app\common\model;

use think\Model;

class BaseModel extends Model
{
    public function get_one_by_condition_on_base($where = [], $order = [], $field = true, $append = [])
    {
        $table_name = $this->getPk();
        if (empty($order)) {
            $order = [$table_name => 'desc'];
        }
        $object = $this->where($where)->field($field)->order($order)->find();
        if (!empty($object) && !empty($append)) {
            $return = $object->append($append);
        } else {
            $return = $object;
        }
        return empty($return) ? [] : $return->toArray();
    }

    public function get_value_by_condition_on_base($condition, $value)
    {
        $return = $this->where($condition)->value($value);
        return is_null($return) ? '' : $return;
    }

    public function update_one_by_condition_on_base($arr, $condition, $allow = true)
    {
        if (isset($arr['userid'])) {
            unset($arr['userid']);
        }
        $save = $this->allowField($allow)->save($arr, $condition);
        return $save;
    }

    public function insert_one_by_arr_on_base($arr, $allow = true, $admin = false)
    {
        if (!$admin) {
            if (isset($arr['userid'])) {
                unset($arr['userid']);
                $arr['userid'] = session('user.userid');
            }
        }
        $table_name = $this->getPk();
        $res = self::create($arr, $allow);
        $save = $res->getData();
        return $save[$table_name];
    }

    public function get_list_by_condition_on_base($where = [], $order = [], $offset = '', $limit = '20', $field = true, $append = [])
    {
        $table_name = $this->getPk();
        if (empty($order)) {
            $order = [$table_name => 'desc'];
        }
        if (!empty($limit)) {
            if (!isset($offset) || empty($offset)) {
                $offset = request()->get('page') ? request()->get('page') : '1';
            }
            $object_list = $this->where($where)->field($field)->order($order)->page($offset, $limit)->select();
            if ($limit == 'admin') {
                $limit = '20';
                $page_model = new AdminPage();
                $count = $this->get_count_by_condition_on_base($where);
                $page = $page_model->get_page($count, $limit);
            } else {
                $page_model = new BasePage();
                $count = $this->get_count_by_condition_on_base($where);
                $page = $page_model->get_page($count, $limit);
            }
        } else {
            $object_list = $this->where($where)->field($field)->order($order)->select();
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

    public function get_count_by_condition_on_base($where = [])
    {
        return $this->where($where)->count();
    }

    public function group_field_by_condition_on_base($where = [], $field, $group)
    {
        if (!empty($group)) {
            $object_list = $this->field($field)->group($group)->where($where)->select();
        } else {
            $object_list = $this->field($field)->where($where)->select();
        }
        $list = [];
        if (!empty($object_list)) {
            foreach ($object_list as $item => $value) {
                $list[] = $value->toArray();
            }
        }
        return is_null($list) ? '' : $list;
    }

    public function get_column_by_condition_on_base($condition, $value)
    {
        $object_list = $this->where($condition)->field($value)->select();
        $list = [];
        if (!empty($object_list)) {
            foreach ($object_list as $item => $value) {
                $list[] = $value->toArray();
            }
        }
        return is_null($list) ? '' : $list;
    }

    public function del_list_by_condition_on_base($condition)
    {
        $del = $this->where($condition)->delete();
        return is_null($del) ? '' : $del;
    }

    public function inser_all_by_arr_on_base($arr, $allow = true)
    {
        foreach ($arr as $k => $v) {
            if (isset($v['userid'])) {
                unset($arr[$k]['userid']);
                $arr[$k]['userid'] = session('user.userid');
            }
        }
        $save = $this->allowField($allow)->saveAll($arr);
        return $save;
    }

    public function sum_by_condition_on_base($condition, $sumfield)
    {
        $res = $this->where($condition)->sum($sumfield);
        return $res;
    }
}