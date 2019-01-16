<?php

namespace app\user\controller;

use app\user\model\Orderlist;
use app\user\model\Userpay;
use app\user\model\Userpayment;
use app\user\model\Usersettlement;
use think\Db;

class Withdraw extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $orderlist_model = new Orderlist();
        $zero_time = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $condition = ['is_buylist' => '1', 'is_payment' => '0', 'is_frozen' => '0', 'is_selllist' => '1', 'addtime' => ['<', $zero_time,]];
        $orderlist_payment = $orderlist_model->sum_by_condition_on_user($condition, 'paymoney*userproportion');
        $adminconfig_cache = cache('adminconfig');
        $takecashlowest = $adminconfig_cache['takecashlowest'];
        $userpay_model = new Userpay();
        $userpay_row = $userpay_model->get_one_by_session_userid_on_user();
        $assign_arr = array('title' => "申请提现", 'list' => $userpay_row, 'unpayment' => $orderlist_payment, 'takecashlowest' => $takecashlowest,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        if (request()->isPost()) {
            $orderlist_model = new Orderlist();
            $zero_time = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $condition = ['is_buylist' => '1', 'is_payment' => '0', 'is_frozen' => '0', 'is_selllist' => '1', 'addtime' => ['<', $zero_time,]];
            $orderlist_payment = $orderlist_model->sum_by_condition_on_user($condition, 'paymoney*userproportion');
            $fee = cache('adminconfig')['fee'];
            $orderlist_payment = $orderlist_payment - $fee;
            $adminconfig_cache = cache('adminconfig');
            $takecashfrom = $adminconfig_cache['takecashfrom'];
            $takecashto = $adminconfig_cache['takecashto'];
            if (!empty($takecashfrom)) {
                if (date('H') < $takecashfrom) {
                    return json_error('允许提现开始时间：' . $takecashfrom . "点整");
                }
            }
            if (!empty($takecashto)) {
                if (date('H') > $takecashto) {
                    return json_error('允许提现结束时间：' . $takecashto . "点整");
                }
            }
            if ($orderlist_payment <= 0) {
                return json_error('可用余额不足');
            }
            $takecashlowest = cache('adminconfig')['takecashlowest'];
            if ($orderlist_payment <= $takecashlowest) {
                return json_error('可用余额不足' . $takecashlowest . '元');
            }
            $userpay_model = new Userpay();
            $realname = $userpay_model->get_value_by_session_userid_on_user('realname');
            $ptype = $userpay_model->get_value_by_session_userid_on_user('ptype');
            if ($ptype == '1') {
                $account = $userpay_model->get_value_by_session_userid_on_user('alipay');
                $ptype_name = '支付宝';
            } elseif ($ptype == '2') {
                $account = $userpay_model->get_value_by_session_userid_on_user('card');
                $ptype_name = '银行卡';
            } elseif ($ptype == '3') {
                $account = $userpay_model->get_value_by_session_userid_on_user('weixin');
                $ptype_name = '微信';
            }
            Db::startTrans();
            $userpayment_model = new Userpayment();
            $load_arr_userpayment = array('userid' => session('user.userid'), 'username' => session('user.username'), 'usermoney' => $orderlist_payment, 'addtime' => date('Y-m-d H:i:s'), 'is_user' => '1', 'fee' => $fee, 'ptype' => $ptype, 'remark' => "姓名：" . $realname . " 收款方式：" . $ptype_name . " 收款账号：" . $account,);
            $res1 = $userpayment_model->insert_one_by_arr_on_user($load_arr_userpayment);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            $usersettlement_model = new Usersettlement();
            $totlepay = $usersettlement_model->get_value_by_session_userid_on_user('totlepay');
            $totlepay = $totlepay + $orderlist_payment;
            $load_arr_usersettlement = array('totlepay' => $totlepay,);
            $res2 = $usersettlement_model->update_one_by_session_userid_on_user($load_arr_usersettlement);
            if (!$res2) {
                Db::rollback();
                return json_error();
            }
            $load_arr_orderlist = array('is_payment' => '1',);
            $condition = ['addtime' => ['<', $zero_time,], 'is_selllist' => 1, 'is_frozen' => 0,];
            $res3 = $orderlist_model->update_one_by_session_userid_on_user($load_arr_orderlist, $condition);
            if (!$res3) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
}