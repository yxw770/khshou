<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class DlUser extends BaseModel
{
    protected $table = '1106111321_user';

    public function get_all_dlsettlement_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            if (isset($condition['sub_userid']) && $condition['sub_userid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                if ($sub_where != '') {
                    $sub_where = $sub_where . " and ";
                }
                $where .= 'o.sub_userid=' . $condition['sub_userid'];
                $sub_where .= 'o.sub_userid=' . $condition['sub_userid'];
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
                $sub_exists = "o.sub_userid=" . $condition['keyword'] . " or exists (SELECT * FROM `1106111321_user` WHERE username LIKE '%" . $condition['keyword'] . "%')";
            }
            $sub_field = "
            SUM(o.sub_money*o.userproportion) AS sum_up_paymoney,
            SUM(o.sub_money) AS sum_paymoney,
            o.sub_userid
            ";
            $subsql = db('orderlist')->alias('o')->where('o.is_buylist=1 AND o.is_sub_ship=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($sub_where)->where($sub_exists)->field($sub_field)->group('o.sub_userid')->buildSql();
            $field = "
            p.*,
            s.totlepay,
            u.username,
            u.flag,
            sum_up_paymoney,
            sum_paymoney
            ";
            $object_list = $this->alias('u')->join([$subsql => 'o'], 'o.sub_userid=u.userid', 'left')->join('usersettlement s', 's.userid=u.userid', 'left')->join('userpay p', 'p.userid=u.userid', 'left')->field($field)->where($where)->group('u.userid')->order('sum_up_paymoney desc,totlepay desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->alias('u')->join([$subsql => 'o'], 'o.sub_userid=u.userid', 'left')->join('usersettlement s', 's.userid=u.userid', 'left')->join('userpay p', 'p.userid=u.userid', 'left')->field($field)->where($where)->group('u.userid')->count();
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

    public function get_one_dlsettlement_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
                $sub_where .= 'o.sub_userid=' . $condition['userid'];
            }
            $sub_field = "
            SUM(o.sub_money*o.userproportion) AS sum_up_paymoney,
            o.sub_userid
            ";
            $subsql = db('orderlist')->alias('o')->where('o.is_buylist=1 AND o.is_sub_ship=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($sub_where)->field($sub_field)->group('o.sub_userid')->buildSql();
            $field = "
            p.*,
            s.totlepay,
            u.username,
            sum_up_paymoney
            ";
            $object_list = $this->alias('u')->join([$subsql => 'o'], 'o.sub_userid=u.userid', 'left')->join('usersettlement s', 's.userid=u.userid', 'left')->join('userpay p', 'p.userid=u.userid', 'left')->field($field)->where($where)->find();
        } else {
            $object_list = $this->where($condition)->field(true)->order($order)->select();
        }
        $list = $object_list->toArray();
        return $list;
    }
}