<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class Userpay extends BaseModel
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

    public function get_userpaylist_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            if (!empty($condition['userid'])) {
                $where = 'u.userid=' . $condition['userid'];
            }
            if (!empty($condition['username'])) {
                $where = "u.username like '%" . $condition['username'] . "%'";
            }
            $object_list = $this->alias('p')->join('user u', 'u.userid=p.userid', 'left')->where($where)->order('u.userid desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $user_model = new User();
            $where = '';
            if (!empty($condition['userid'])) {
                $where = 'userid=' . $condition['userid'];
            }
            if (!empty($condition['username'])) {
                $where = "username like '%" . $condition['username'] . "%'";
            }
            $count = $user_model->get_count_by_condition_on_base($where);
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

    public function get_settlement_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            if (!empty($condition['addtime'])) {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "o.addtime < '" . $condition['addtime'] . "'";
            }
            if (isset($condition['userid']) && $condition['userid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 'o.userid=' . $condition['userid'];
            }
            if (isset($condition['username']) && $condition['username'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "u.username like '%" . $condition['username'] . "%'";
            }
            $field = "
            p.*,
            SUM(o.paymoney*o.userproportion) AS sum_up_paymoney,
            SUM(o.paymoney) AS sum_paymoney,
            s.totlepay,
            u.username,
            u.flag
            ";
            $object_list = $this->alias('p')->join('orderlist o', 'o.userid=p.userid', 'left')->join('usersettlement s', 's.userid=p.userid', 'left')->join('user u', 'u.userid=p.userid', 'left')->field($field)->where('o.is_buylist=1 AND o.is_payment=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($where)->group('p.userid')->order('sum_up_paymoney,totlepay desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->alias('p')->join('orderlist o', 'o.userid=p.userid', 'left')->join('usersettlement s', 's.userid=p.userid', 'left')->join('user u', 'u.userid=p.userid', 'left')->field($field)->where('o.is_buylist=1 AND o.is_payment=0')->where($where)->group('p.userid')->count();
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

    public function get_one_settlement_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            if (!empty($condition['addtime'])) {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "o.addtime < '" . $condition['addtime'] . "'";
            }
            $field = "
            p.*,
            SUM(o.paymoney*o.userproportion) AS sum_up_paymoney,
            s.totlepay,
            u.username
            ";
            $object_list = $this->alias('p')->join('orderlist o', 'o.userid=p.userid', 'left')->join('usersettlement s', 's.userid=p.userid', 'left')->join('user u', 'u.userid=p.userid', 'left')->field($field)->where('o.is_buylist=1 AND o.is_payment=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($where)->find();
        } else {
            $object_list = $this->where($condition)->field(true)->order($order)->select();
        }
        $list = $object_list->toArray();
        return $list;
    }

    public function get_add_payment_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            if (!empty($condition['addtime'])) {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "o.addtime < '" . $condition['addtime'] . "'";
            }
            if (isset($condition['userid']) && $condition['userid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 'o.userid=' . $condition['userid'];
            }
            $field = "
            p.*,
            SUM(o.paymoney*o.userproportion) AS sum_up_paymoney
            ";
            $object_list = $this->alias('p')->join('orderlist o', 'o.userid=p.userid', 'left')->field($field)->where('o.is_buylist=1 AND o.is_payment=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($where)->find();
        } else {
            $object_list = $this->where($condition)->field(true)->order($order)->select();
        }
        $list = $object_list->toArray();
        return $list;
    }
} 