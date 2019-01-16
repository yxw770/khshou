<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class User extends BaseModel
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
            $where = '';
            if (!empty($condition['userid'])) {
                $where = 'u.userid=' . $condition['userid'];
            }
            if (!empty($condition['username'])) {
                $where = "u.username like '%" . $condition['username'] . "%'";
            }
            if (!empty($condition['keyword'])) {
                $where = "u.userid='" . $condition['keyword'] . "'";
                $where .= "OR u.username like '%" . $condition['keyword'] . "%'";
                $where .= "OR f.qq like '%" . $condition['keyword'] . "%'";
                $where .= "OR f.email like '%" . $condition['keyword'] . "%'";
                $where .= "OR f.tel like '%" . $condition['keyword'] . "%'";
            }
            if (isset($condition['is_state'])) {
                $where = 'u.is_state=' . $condition['is_state'];
            }
            $object_list = $this->alias('u')->join('userpay p', 'u.userid=p.userid', 'left')->join('userdefine f', 'u.userid=f.userid', 'left')->where($where)->order('u.' . $table_name . ' desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $where = '';
            if (!empty($condition['userid'])) {
                $where = 'userid=' . $condition['userid'];
            }
            if (!empty($condition['username'])) {
                $where = "username like '%" . $condition['username'] . "%'";
            }
            if (isset($condition['is_state'])) {
                $where = 'is_state=' . $condition['is_state'];
            }
            $count = $this->get_count_by_condition_on_base($where);
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
        $object_row = $this->alias('u')->join('userdefine f', 'u.userid=f.userid', 'left')->where('u.userid=' . $userid)->find();
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

    public function get_busanalysis_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            if (!empty($condition['from_time']) && !empty($condition['to_time'])) {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "o.addtime between '" . $condition['from_time'] . "' and '" . $condition['to_time'] . "'";
            }
            if (isset($condition['channelid']) && $condition['channelid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 'o.channelid=' . $condition['channelid'];
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
            u.userid,
            u.username,
            COUNT(*) as count_all,
            COUNT(IF(is_buylist=1,TRUE,NULL)) AS count_success, 
            COUNT(IF(is_buylist=0,TRUE,NULL)) AS count_failed, 
            SUM(ordermoney) as sum_ordermoney,
            SUM(paymoney) as sum_paymoney,
            SUM(paymoney*userproportion) as sum_usergain,
            SUM(paymoney*ptproportion) as sum_ptgain
            ";
            $object_list = $this->alias('u')->join('orderlist o', 'u.userid=o.userid', 'left')->field($field)->where($where)->group('u.userid')->order('sum_paymoney desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->alias('u')->join('orderlist o', 'u.userid=o.userid', 'left')->field($field)->where($where)->group('u.userid')->where($where)->count();
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

    public function get_all_settlement_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            $sub_where = '';
            if (!empty($condition['addtime'])) {
                if ($sub_where != '') {
                    $sub_where = $sub_where . " and ";
                }
                $sub_where .= "o.addtime < '" . $condition['addtime'] . "'";
            }
            if (isset($condition['userid']) && $condition['userid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                if ($sub_where != '') {
                    $sub_where = $sub_where . " and ";
                }
                $where .= 'o.userid=' . $condition['userid'];
                $sub_where .= 'o.userid=' . $condition['userid'];
            }
            if (isset($condition['username']) && $condition['username'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "u.username like '%" . $condition['username'] . "%'";
            }
            $sub_exists = '';
            if (!empty($condition['keyword'])) {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "u.userid='" . $condition['keyword'] . "'";
                $where .= "OR u.username like '%" . $condition['keyword'] . "%'";
                $sub_exists = "o.userid=" . $condition['keyword'] . " or exists (SELECT * FROM `1106111321_user` WHERE username LIKE '%" . $condition['keyword'] . "%')";
            }
            $sub_field = "
            SUM(o.paymoney*o.userproportion) AS sum_up_paymoney,
            SUM(o.paymoney) AS sum_paymoney,
            o.userid
            ";
            $subsql = db('orderlist')->alias('o')->where('o.is_buylist=1 AND o.is_payment=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($sub_where)->where($sub_exists)->field($sub_field)->group('o.userid')->buildSql();
            $field = "
            p.*,
            s.totlepay,
            u.username,
            u.flag,
            sum_up_paymoney,
            sum_paymoney
            ";
            $object_list = $this->alias('u')->join([$subsql => 'o'], 'o.userid=u.userid', 'left')->join('usersettlement s', 's.userid=u.userid', 'left')->join('userpay p', 'p.userid=u.userid', 'left')->field($field)->where($where)->group('u.userid')->order('sum_up_paymoney desc,totlepay desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->alias('u')->join([$subsql => 'o'], 'o.userid=u.userid', 'left')->join('usersettlement s', 's.userid=u.userid', 'left')->join('userpay p', 'p.userid=u.userid', 'left')->field($field)->where($where)->group('u.userid')->count();
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
            $sub_where = '';
            if (!empty($condition['addtime'])) {
                if ($sub_where != '') {
                    $sub_where = $sub_where . " and ";
                }
                $sub_where .= "o.addtime < '" . $condition['addtime'] . "'";
            }
            if (isset($condition['userid']) && $condition['userid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                if ($sub_where != '') {
                    $sub_where = $sub_where . " and ";
                }
                $where .= 'u.userid=' . $condition['userid'];
                $sub_where .= 'o.userid=' . $condition['userid'];
            }
            $sub_field = "
            SUM(o.paymoney*o.userproportion) AS sum_up_paymoney,
            o.userid
            ";
            $subsql = db('orderlist')->alias('o')->where('o.is_buylist=1 AND o.is_payment=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($sub_where)->field($sub_field)->group('o.userid')->buildSql();
            $field = "
            p.*,
            s.totlepay,
            u.username,
            sum_up_paymoney
            ";
            $object_list = $this->alias('u')->join([$subsql => 'o'], 'o.userid=u.userid', 'left')->join('usersettlement s', 's.userid=u.userid', 'left')->join('userpay p', 'p.userid=u.userid', 'left')->field($field)->where($where)->find();
        } else {
            $object_list = $this->where($condition)->field(true)->order($order)->select();
        }
        $list = $object_list->toArray();
        return $list;
    }
} 