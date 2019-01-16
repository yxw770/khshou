<?php

namespace app\user\controller;

use app\user\model\Userreportresult;

class Complain extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $userreport_model = new Userreportresult();
        $report_arr = $userreport_model->get_list_by_session_userid_on_user();
        $report_list = $report_arr[0];
        $page = $report_arr[1];
        $count = 0;
        foreach ($report_list as $k => $v) {
            if ($v['report_status'] == '1') {
                $count++;
            }
        }
        $sitename = cache('guestconfig')['sitename'];
        $assign_arr = array('title' => "投诉管理", 'list' => $report_list, 'page' => $page, 'count' => $count, 'sitename' => $sitename,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    function refund()
    {
        $id = (int)input('id');
        $orderid = input('orderid');
        $report_arr = db('userreportresult')->where(['seller_userid' => session('user.userid'), 'report_orderid' => $orderid])->find();
        if (!$report_arr || $id != $report_arr['id']) {
            return json_error('参数错误');
        }
        $report_status = $report_arr['report_status'];
        if ($report_status == '1') {
            return json_error('该笔订单已经处理完成！');
        }
        $arr_update = array('report_finishtime' => date('Y-m-d H:i:s', time()), 'report_status' => '1', 'result_status' => '1', 'refund_status' => '0',);
        $affected_row_number = db('userreportresult')->where(['id' => $id, 'report_orderid' => $orderid])->update($arr_update);
        if ($affected_row_number) {
            return json_success('确认退款成功！');
        } else {
            return json_error('系统出错！');
        }
    }
}