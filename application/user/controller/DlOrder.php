<?php

namespace app\user\controller;

use app\admin\model\Adminpaychannel;
use app\user\model\DlOrderlist;
use app\user\model\Orderlist;
use app\user\model\Usergoodlist;
use think\Db;

class DlOrder extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
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
        $dl_orderlist_model = new DlOrderlist();
        $condition = ['is_sub' => 1, 'sub_userid' => session('user.userid'),];
        $orderlist_arr = $dl_orderlist_model->get_order_list_on_daili($condition);
        $list = $orderlist_arr[0];
        $page = $orderlist_arr[1];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                $condition = ['is_sub' => 1, 'sub_userid' => session('user.userid'),];
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
                $orderlist_arr = $dl_orderlist_model->get_order_list_on_daili($condition);
                $list = $orderlist_arr[0];
                $page = $orderlist_arr[1];
            }
        }
        $assign_arr = array('title' => "代理订单交易", 'list' => $list, 'goodlist_arr' => $goodlist_arr, 'paychannel_arr' => $adminpaychennel_arr, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }
} 