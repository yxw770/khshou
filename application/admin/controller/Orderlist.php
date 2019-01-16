<?php

namespace app\admin\controller;

use app\admin\model\Adminpaychannel;
use app\admin\model\User;
use app\admin\model\Userpay;
use think\Db;

class Orderlist extends AdminBase
{
    public function index()
    {
        $orderlist_model = new \app\admin\model\Orderlist();
        $condition = [];
        $adminpaychennel_model = new Adminpaychannel();
        $adminpaychennel_arr = $adminpaychennel_model->get_list_by_condition_on_admin([], [], [], []);
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['to_time']) && !empty($get['from_time'])) {
                    $from_time = date('Y-m-d', strtotime($get['from_time']));
                    $to_time = date('Y-m-d H:i:s', strtotime($get['to_time']) + 60 * 60 * 24 - 1);
                    $condition['from_time'] = $from_time;
                    $condition['to_time'] = $to_time;
                }
                if (isset($get['is_payment']) && $get['is_payment'] != '') {
                    $condition['is_payment'] = $get['is_payment'];
                }
                if (isset($get['is_buylist']) && $get['is_buylist'] != '') {
                    $condition['is_buylist'] = $get['is_buylist'];
                }
                if (isset($get['channelid']) && $get['channelid'] != '') {
                    $condition['channelid'] = $get['channelid'];
                }
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'username') {
                        $condition['username'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'orderid') {
                        $condition['orderid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'contact') {
                        $condition['contact'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'goodname') {
                        $condition['goodname'] = $get['keyword'];
                    }
                }
                if (isset($get['search_tips']) && $get['search_tips'] != '') {
                    $condition['search_tips'] = $get['search_tips'];
                }
                if (isset($get['is_frozen']) && $get['is_frozen'] != '') {
                    $condition['is_frozen'] = $get['is_frozen'];
                }
                $orderlist_arr = $orderlist_model->get_orderlist_by_condition_on_admin($condition);
                $list = $orderlist_arr[0];
                $page = $orderlist_arr[1];
            } else {
                $orderlist_arr = $orderlist_model->get_orderlist_by_condition_on_admin($condition);
                $list = $orderlist_arr[0];
                $page = $orderlist_arr[1];
            }
        }
        $assign_arr = array('title' => "订单管理", 'list' => $list, 'paychannel_arr' => $adminpaychennel_arr, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function refund()
    {
        $userid = input('param.userid');
        $orderlistid = input('param.orderlistid');
        $user_model = new User();
        $condition = ['userid' => $userid,];
        $username = $user_model->get_value_by_condition_on_admin('username', $condition);
        $userpaylist_model = new Userpay();
        $condition = ['userid' => $userid,];
        $userpaylist_arr = $userpaylist_model->get_one_by_condition_on_admin($condition);
        $userpaylist_arr = array_merge($userpaylist_arr, ['username' => $username]);
        $assign_arr = array('title' => "退款", 'list' => $userpaylist_arr, 'orderlistid' => $orderlistid,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function refundsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $orderlist_model = new \app\admin\model\Orderlist();
            $condition = ['orderlistid' => $post['orderlistid'],];
            $load_arr = ['is_buylist' => '7',];
            $res1 = $orderlist_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function togglefrozen()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $orderlist_model = new \app\admin\model\Orderlist();
            $condition = ['orderlistid' => $post['orderlistid'],];
            $load_arr = array('is_frozen' => $post['is_frozen'],);
            $res1 = $orderlist_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 