<?php

namespace app\admin\controller;

use app\admin\model\Admincheck;
use app\admin\model\DlUser;
use app\admin\model\DlUserpay;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpay;
use app\admin\model\Userpaychannel;
use app\admin\model\Userpayment;
use app\admin\model\Usersettlement;
use think\Db;

class DlSettlement extends AdminBase
{
    public function index()
    {
        $dl_userp_model = new DlUser();
        $zero_timestapm = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $zero_time = date('Y-m-d H:i:s', $zero_timestapm);
        $condition = ['addtime' => $zero_time,];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['sub_userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'username') {
                        $condition['username'] = $get['keyword'];
                    }
                }
                if (!empty($get['keyword']) && empty($get['ktype'])) {
                    $condition['keyword'] = $get['keyword'];
                }
                $userlist_arr = $dl_userp_model->get_all_dlsettlement_by_condition_on_admin($condition);
                $list = $userlist_arr[0];
                $page = $userlist_arr[1];
            } else {
                $userlist_arr = $dl_userp_model->get_all_dlsettlement_by_condition_on_admin($condition);
                $list = $userlist_arr[0];
                $page = $userlist_arr[1];
            }
        }
        $assign_arr = array('title' => "商户结算", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        $userid = input('param.userid');
        $dl_userp_model = new DlUser();
        $zero_timestapm = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $zero_time = date('Y-m-d H:i:s', $zero_timestapm);
        $condition = ['userid' => $userid, 'addtime' => $zero_time,];
        $userpay_row = $dl_userp_model->get_one_dlsettlement_by_condition_on_admin($condition);
        $assign_arr = array('title' => "标记用户", 'list' => $userpay_row,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $dl_userpay_model = new DlUserpay();
            $zero_timestapm = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $zero_time = date('Y-m-d H:i:s', $zero_timestapm);
            $condition = ['userid' => $post['userid'], 'addtime' => $zero_time,];
            $userpay_row = $dl_userpay_model->get_add_dlpayment_by_condition_on_admin($condition);
            if ($userpay_row['sum_up_paymoney'] <= 0) {
                return json_error('可用余额不足');
            }
            Db::startTrans();
            $userpayment_model = new Userpayment();
            $remark2 = '';
            if ($userpay_row['ptype'] == '1') {
                $remark2 .= "姓名：" . $userpay_row['realname'] . " 收款方式：支付宝 收款账号：" . $userpay_row['alipay'];
            } elseif ($userpay_row['ptype'] == '2') {
                $remark2 .= "姓名：" . $userpay_row['realname'] . " 收款方式：银行卡 收款账号：" . $userpay_row['card'];
            } elseif ($userpay_row['ptype'] == '3') {
                $remark2 .= "姓名：" . $userpay_row['realname'] . " 收款方式：微信 收款账号：" . $userpay_row['weixin'];
            }
            $load_arr = ['userid' => $post['userid'], 'usermoney' => $userpay_row['sum_up_paymoney'], 'fee' => $post['fee'], 'settlement' => $userpay_row['sum_up_paymoney'] - $post['fee'], 'addtime' => date('Y-m-d H:i:s'), 'is_user' => '0', 'is_state' => '1', 'ptype' => $userpay_row['ptype'], 'remark_admin' => $post['remark_admin'], 'remark' => $remark2,];
            $res1 = $userpayment_model->insert_one_by_arr_on_admin($load_arr);
            if (!$res1) {
                Db::rollback();
                return json_error('新增用户结算记录信息失败');
            }
            $usersettlement_model = new Usersettlement();
            $condition2 = ['userid' => $post['userid'],];
            $usersettlement_data = $usersettlement_model->get_one_by_condition_on_admin($condition2);
            if (!empty($usersettlement_data)) {
                $totlepay = $usersettlement_model->get_value_by_condition_on_admin('totlepay', ['userid' => $post['userid']]);
                $totlepay = $totlepay + $userpay_row['sum_up_paymoney'];
                $load_arr_usersettlement = array('totlepay' => $totlepay,);
                $res2 = $usersettlement_model->update_one_by_condition_on_admin($load_arr_usersettlement, $condition2);
            } else {
                $load_arr_usersettlement = array('totlepay' => $userpay_row['sum_up_paymoney'], 'userid' => $post['userid'],);
                $res2 = $usersettlement_model->insert_one_by_arr_on_admin($load_arr_usersettlement);
            }
            if (!$res2) {
                Db::rollback();
                return json_error('更新用户结算总额失败');
            }
            $orderlist_model = new \app\admin\model\Orderlist();
            $load_arr_orderlist = array('is_sub_ship' => '1',);
            $condition3 = ['sub_userid' => $post['userid'], 'addtime' => ['<', $zero_time], 'is_selllist' => 1, 'is_frozen' => 0,];
            $res3 = $orderlist_model->update_one_by_condition_on_admin($load_arr_orderlist, $condition3);
            if (!$res3) {
                Db::rollback();
                return json_error('更新用户订单信息失败');
            }
            Db::commit();
            return json_success();
        }
    }
}