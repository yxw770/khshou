<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class Adminpaychannel extends BaseModel
{
    public function get_one_by_session_adminid_on_admin($condition = [])
    {
        $res = $this->get_one_by_condition_on_base($condition);
        return $res;
    }

    public function update_one_by_session_adminid_on_admin($post, $condition = [])
    {
        $res = $this->update_one_by_condition_on_base($post, $condition);
        return $res;
    }

    public function insert_one_by_arr_on_admin($arr, $allow = true, $admin = true)
    {
        $res = $this->insert_one_by_arr_on_base($arr, $allow, $admin);
        return $res;
    }

    public function get_list_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
    {
        $res = $this->get_list_by_condition_on_base($condition, $order, $offset, $limit);
        return $res;
    }

    public function get_channelanalysis_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
            $field = "
              p.channelid,
              p.channelname,
              COUNT(*) AS count_all,
              COUNT(IF(is_buylist = 1, TRUE, NULL)) AS count_success,
              COUNT(IF(is_buylist = 0, TRUE, NULL)) AS count_failed,
              SUM(ordermoney) AS sum_ordermoney,
              SUM(paymoney) AS sum_paymoney,
              SUM(paymoney * o.userproportion) AS sum_usergain,
              SUM(paymoney * o.ptproportion) AS sum_ptgain 
            ";
            $object_list = $this->alias('p')->join('orderlist o', 'p.channelid=o.channelid', 'left')->field($field)->where($where)->group('p.channelid')->order('sum_paymoney desc')->page($offset, $limit)->select();
            $page_model = new AdminPage();
            $count = $this->alias('p')->join('orderlist o', 'p.channelid=o.channelid', 'left')->field($field)->where($where)->group('p.channelid')->count();
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