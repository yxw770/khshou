<?php

namespace app\user\controller;

use app\admin\model\Adminpaychannel;
use app\user\model\Orderlist;
use app\user\model\Usergoodlist;

class Income extends UserBase
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
        $orderlist_arr = $orderlist_model->get_income_list_on_user($condition);
        if (isMobile()) {
            $sum_arr = $orderlist_model->get_sum_income_by_condition_on_user($condition);
        }
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
                        $condition['orderid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'contact') {
                        $condition['contact'] = $get['keyword'];
                    }
                }
                if (!empty($get['channelid'])) {
                    $condition['channelid'] = $get['channelid'];
                }
                $orderlist_arr = $orderlist_model->get_income_list_on_user($condition);
                $list = $orderlist_arr[0];
                $page = $orderlist_arr[1];
            }
        }
        $assign_arr = array('title' => "渠道分析", 'list' => $list, 'goodlist_arr' => $goodlist_arr, 'paychannel_arr' => $adminpaychennel_arr, 'page' => $page, 'sum_arr' => [],);
        if (isMobile()) {
            $assign_arr['sum_arr'] = $sum_arr;
        }
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function exportincome()
    {
        if (request()->isGet()) {
            $from_time = input('from_time');
            $to_time = input('to_time');
            $goodlistid = input('goodlistid');
            $ktype = input('ktype');
            $keyword = input('keyword');
            if (!empty($from_time) && !empty($to_time)) {
                $condition = [];
                if (!empty($from_time) && !empty($to_time)) {
                    if ($from_time == $to_time) {
                        $to_time = date('Y-m-d H:i:s', strtotime($to_time) + 60 * 60 * 24 - 1);
                    }
                    $condition['addtime'] = ['between', [$from_time, $to_time]];
                }
                if (!empty($goodlistid)) {
                    $condition['goodlistid'] = $goodlistid;
                }
                if (!empty($keyword) && !empty($ktype)) {
                    if ($ktype == 'order') {
                        $condition['orderid'] = $keyword;
                    } elseif ($ktype == 'contact') {
                        $condition['contact'] = $keyword;
                    }
                }
                $orderlist_model = new Orderlist();
                $orderlist_arr = $orderlist_model->get_export_income_on_user($condition, [], [], []);
                if (!empty($orderlist_arr)) {
                    $txtname = date('Y') . date('m') . date('d') . " （收益统计）" . ".txt";
                    $content = $this->get_export_content($orderlist_arr);
                    export_txt($content, $txtname);
                    exit();
                } else {
                    export_txt('暂无可以导出的订单', '渠道分析.txt');
                    exit();
                }
            } else {
                export_txt('请选择日期范围进行导出渠道分析', '渠道分析.txt');
                exit();
            }
        }
    }

    public function get_export_content($content)
    {
        $string = "商品名称" . "\t" . "数量" . "\t" . "成本" . "\t" . "单价" . "\t" . "总价" . "\t" . "收入" . "\t" . "利润" . "\t" . "\r\n";
        foreach ($content as $k => $val) {
            $goodname = $val['goodname'];
            $quantity = $val['quantity'];
            $cost = $val['cost'] ? $val['cost'] : '0.00';
            $price = $val['price'];
            $paymoney = $val['paymoney'];
            $userproportion_sum = (float)($val['paymoney'] * $val['userproportion']);
            $usergain_sum = (string)($val['paymoney'] * $val['userproportion'] - $val['cost'] * $val['quantity']);
            $channelname = $val['channelname'];
            $addtime = $val['addtime'];
            $string .= $goodname . "\t" . $quantity . "\t" . $cost . "\t" . $price . "\t" . $paymoney . "\t" . $userproportion_sum . "\t" . $usergain_sum . "\t" . $channelname . "\t" . $addtime . "\t" . "\r\n";
        }
        return $string;
    }
} 