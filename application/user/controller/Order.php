<?php

namespace app\user\controller;

use app\admin\model\Adminpaychannel;
use app\user\model\Orderlist;
use app\user\model\Usergoodlist;
use think\Db;

class Order extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $safekey_model = new Safekey();
        $safekey_model->checkSafeKey();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $adminpaychennel_model = new Adminpaychannel();
        $adminpaychennel_arr = $adminpaychennel_model->get_list_by_condition_on_admin([], [], [], []);
        $goodlist_model = new Usergoodlist();
        $condition = ['is_trush' => '0',];
        $goodlist_arr = $goodlist_model->get_column_by_session_userid_on_user('goodlistid,goodname', $condition);
        $orderlist_model = new Orderlist();
        $condition = [];
        $orderlist_arr = $orderlist_model->get_order_list_on_user($condition);
        $list = $orderlist_arr[0];
        $page = $orderlist_arr[1];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                $condition = [];
                if (!empty($get['from_time']) && !empty($get['to_time'])) {
                    if ($get['from_time'] == $get['to_time']) {
                        $get['to_time'] = date('Y-m-d H:i:s', strtotime($get['to_time']) + 60 * 60 * 24 - 1);
                    }
                    $condition['addtime'] = ['between', [$get['from_time'], $get['to_time']]];
                }
                if (!empty($get['goodlistid'])) {
                    $condition['goodlistid'] = $get['goodlistid'];
                }
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'order') {
                        $condition['orderid'] = ['like', '%' . $get['keyword'] . '%'];
                    } elseif ($get['ktype'] == 'contact') {
                        $condition['contact'] = ['like', '%' . $get['keyword'] . '%'];
                    }
                }
                if (!empty($get['channelid'])) {
                    $condition['channelid'] = $get['channelid'];
                }
                if (isset($get['is_buylist']) && $get['is_buylist'] != '') {
                    $condition['is_buylist'] = $get['is_buylist'];
                }
                $orderlist_arr = $orderlist_model->get_order_list_on_user($condition);
                $list = $orderlist_arr[0];
                $page = $orderlist_arr[1];
            }
        }
        $assign_arr = array('title' => "????????????", 'list' => $list, 'goodlist_arr' => $goodlist_arr, 'paychannel_arr' => $adminpaychennel_arr, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function exportorder()
    {
        if (request()->isGet()) {
            $orderlistid = input('orderlistid');
            if (!empty($orderlistid)) {
                $condition = [];
                if (!empty($orderlistid)) {
                    $condition['orderlistid'] = $orderlistid;
                }
                $orderlist_model = new Orderlist();
                $orderlist_arr = $orderlist_model->get_export_order_on_user($condition);
                if (!empty($orderlist_arr)) {
                    $txtname = date('Y') . date('m') . date('d') . " ??????????????????" . ".txt";
                    $content = $this->get_export_content($orderlist_arr);
                    export_txt($content, $txtname);
                    exit();
                } else {
                    export_txt('???????????????????????????', '????????????.txt');
                    exit();
                }
            } else {
                export_txt('???????????????', '????????????.txt');
                exit();
            }
        }
    }

    public function get_export_content($content)
    {
        $string = "????????????" . "\t" . "????????????" . "\t" . "????????????" . "\t" . "????????????" . "\t" . "??????" . "\t" . "???????????????" . "\t" . "??????" . "\t" . "\r\n";
        $orderid = $content['orderid'];
        $addtime = $content['addtime'];
        $goodname = $content['goodname'] . "???" . $content['quantity'] . "??????";
        $channelname = $content['channelname'];
        $paymoney = $content['paymoney'];
        $contact = $content['contact'];
        $is_buylist = $content['is_buylist'] == '1' ? '?????????' : '?????????';
        $string .= $orderid . "\t" . $addtime . "\t" . $goodname . "\t" . $channelname . "\t" . $paymoney . "\t" . $contact . "\t" . $is_buylist . "\t" . "\r\n";
        return $string;
    }

    public function togglestate()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $orderlist_model = new Orderlist();
            $condition = array('orderlistid' => $post['orderlistid']);
            $load_arr = array('is_state' => $post['is_state'],);
            $res1 = $orderlist_model->update_one_by_session_userid_on_user($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 