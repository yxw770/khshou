<?php

namespace app\admin\controller;

use app\admin\model\Userreportresult;
use think\Db;

class Tousu extends AdminBase
{
    public function index()
    {
        if (request()->isGet()) {
            $get = input('get.');
            $condition = [];
            if (isset($get['do']) && $get['do'] == 'search') {
                if (isset($get['result_status']) && $get['result_status'] != '') {
                    $condition['result_status'] = $get['result_status'];
                }
                if (isset($get['report_status']) && $get['report_status'] != '') {
                    $condition['report_status'] = $get['report_status'];
                }
                if (isset($get['report_type']) && $get['report_type'] != '') {
                    $condition['report_type'] = $get['report_type'];
                }
                if (!empty($get['keyword'])) {
                    if (strlen($get['keyword']) == '16') {
                        $condition['report_orderid'] = $get['keyword'];
                    } else {
                        $condition['seller_username'] = ['like', '%' . $get['keyword'] . '%'];
                    }
                }
                $report_model = new Userreportresult();
                $report_arr = $report_model->get_list_by_condition_on_admin($condition);
                $list = $report_arr[0];
                $page = $report_arr[1];
            } else {
                $report_model = new Userreportresult();
                $report_arr = $report_model->get_list_by_condition_on_admin($condition);
                $list = $report_arr[0];
                $page = $report_arr[1];
            }
            $id_map = [];
            foreach ($list as $k => $v) {
                if ((time() - strtotime($v['report_creattime'])) > 60 * 60 && $v['report_status'] == '0') {
                    $id_map[] = $v['id'];
                    $list[$k]['report_status'] = '2';
                }
            }
            if (!empty($id_map)) {
                $ret = $report_model->update_one_by_condition_on_admin(['report_status' => '2'], ['id' => ['in', $id_map]]);
            }
            $assign_arr = array('title' => "订单投诉", 'list' => $list, 'page' => $page,);
            $this->assign($assign_arr);
            return $this->fetch();
        }
    }

    public function shenhe()
    {
        $id = input('param.id');
        $report_model = new Userreportresult();
        $condition = ['id' => $id,];
        $report_arr = $report_model->get_one_by_condition_on_admin($condition);
        $assign_arr = array('title' => "平台审核", 'list' => $report_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function shenhesave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $id = $post['id'];
            $orderid = $post['report_orderid'];
            Db::startTrans();
            $report_model = new Userreportresult();
            $condition = ['id' => $id,];
            $load_arr = ['result_remark' => $post['result_remark'], 'result_status' => $post['result_status'],];
            if ($post['result_status'] == '1') {
                $load_arr['report_status'] = '1';
                $load_arr['refund_status'] = '2';
                $load_arr['report_finishtime'] = date('Y-m-d H:i:s');
            } elseif ($post['result_status'] == '2') {
                $load_arr['report_status'] = '1';
                $load_arr['refund_status'] = '0';
                $load_arr['report_finishtime'] = date('Y-m-d H:i:s');
            } elseif ($post['result_status'] == '0') {
                $load_arr['report_status'] = '2';
                $load_arr['refund_status'] = '0';
                $load_arr['report_finishtime'] = null;
            }
            $res1 = $report_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            if ($post['result_status'] == '2') {
                $orderlist_model = new \app\admin\model\Orderlist();
                $condition = ['orderid' => $orderid,];
                $load_arr = ['is_frozen' => '0',];
                $res2 = $orderlist_model->update_one_by_condition_on_admin($load_arr, $condition);
                if (!$res2) {
                    Db::rollback();
                    return json_error();
                }
            } elseif ($post['result_status'] == '1' || $post['result_status'] == '0') {
                $orderlist_model = new \app\admin\model\Orderlist();
                $condition = ['orderid' => $orderid,];
                $load_arr = ['is_frozen' => '1',];
                $res2 = $orderlist_model->update_one_by_condition_on_admin($load_arr, $condition);
            }
            Db::commit();
            return json_success();
        }
    }

    public function tuikuan()
    {
        $id = input('param.id');
        $report_model = new Userreportresult();
        $condition = ['id' => $id,];
        $report_arr = $report_model->get_one_by_condition_on_admin($condition);
        $assign_arr = array('title' => "处理退款", 'list' => $report_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function tuikuansave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $id = $post['id'];
            Db::startTrans();
            $report_model = new Userreportresult();
            $condition = ['id' => $id,];
            $load_arr = ['refund_status' => $post['refund_status'], 'refund_account' => $post['refund_account'], 'refund_price' => $post['refund_price'], 'refund_remark' => $post['refund_remark'], 'refund_time' => date('Y-m-d H:i:s'), 'report_status' => '1', 'result_status' => '1', 'report_finishtime' => date('Y-m-d H:i:s'),];
            $res1 = $report_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 