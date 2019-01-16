<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class Orderlist extends BaseModel
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

    public function sum_by_condition_on_admin($condition = [], $sumfield)
    {
        $res = $this->sum_by_condition_on_base($condition, $sumfield);
        if (!empty($res)) {
            return $res;
        } else {
            return 0;
        }
    }

    public function get_orderlist_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            if (isset($condition['is_payment']) && $condition['is_payment'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 'o.is_payment=' . $condition['is_payment'];
            }
            if (isset($condition['is_buylist']) && $condition['is_buylist'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 'o.is_buylist=' . $condition['is_buylist'];
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
            if (isset($condition['orderid']) && $condition['orderid'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "o.orderid='" . $condition['orderid'] . "'";
            }
            if (isset($condition['contact']) && $condition['contact'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "o.contact like '%" . $condition['contact'] . "%'";
            }
            if (isset($condition['goodname']) && $condition['goodname'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= "g.goodname like '%" . $condition['goodname'] . "%'";
            }
            if (isset($condition['search_tips']) && $condition['search_tips'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 'o.search_tips=' . $condition['search_tips'];
            }
            if (isset($condition['is_frozen']) && $condition['is_frozen'] != '') {
                if ($where != '') {
                    $where = $where . " and ";
                }
                $where .= 'o.is_frozen=' . $condition['is_frozen'];
            }
            $field = 'o.*,u.username,d.ip,g.goodname';
            $object_list = $this->alias('o')->join('user u', 'u.userid=o.userid', 'left')->join('orderdefine d', 'd.orderlistid=o.orderlistid', 'left')->join('usergoodlist g', 'g.goodlistid=o.goodlistid', 'left')->where($where)->field($field)->order('o.' . $table_name . ' desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->alias('o')->join('user u', 'u.userid=o.userid', 'left')->join('usergoodlist g', 'g.goodlistid=o.goodlistid', 'left')->where($where)->count();
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

    public function get_timedata_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            $field = "
            COUNT(*) as count_all,
            COUNT(IF(is_buylist=1,TRUE,NULL)) AS count_success, 
            COUNT(IF(is_buylist=0,TRUE,NULL)) AS count_failed, 
            SUM(ordermoney) as sum_ordermoney,
            SUM(paymoney) as sum_paymoney,
            SUM(paymoney*userproportion) as sum_usergain,
            SUM(paymoney*ptproportion) as sum_ptgain
            ";
            $date = $condition['date'];
            $list = [];
            for ($i = 0; $i <= 22; $i = $i + 2) {
                $object_list = $this->alias('u')->field($field)->where($where)->where("addtime >= '" . $date . " " . ($i) . ":00:00' and addtime < '" . $date . " " . ($i + 2) . ":00:00'")->find();
                $arr = $object_list->toArray();
                if ($i < 10) {
                    $arr['time'] = '0' . $i . ':00:00';
                } else {
                    $arr['time'] = $i . ':00:00';
                }
                $list[] = $arr;
            }
        }
        return $list;
    }
} 