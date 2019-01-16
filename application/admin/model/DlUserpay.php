<?php

namespace app\admin\model;

use app\common\model\AdminPage;
use app\common\model\BaseModel;

class DlUserpay extends BaseModel
{
    protected $table = '1106111321_userpay';

    public function get_add_dlpayment_by_condition_on_admin($condition = [], $order = [], $offset = '', $limit = '20')
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
                $where .= 'o.sub_userid=' . $condition['userid'];
            }
            $field = "
            p.*,
            SUM(o.sub_money*o.userproportion) AS sum_up_paymoney
            ";
            $object_list = $this->alias('p')->join('orderlist o', 'o.sub_userid=p.userid', 'left')->field($field)->where('o.is_buylist=1 AND o.is_sub_ship=0 AND o.is_frozen=0 AND o.is_selllist=1')->where($where)->find();
        } else {
            $object_list = $this->where($condition)->field(true)->order($order)->select();
        }
        $list = $object_list->toArray();
        return $list;
    }
}