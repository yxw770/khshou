<?php

namespace app\user\controller;

use app\user\model\Orderlist;

class Seo extends UserBase
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
        $condition = ['addtime' => ['between', [date('Y-m-d'), date('Y-m-d H:i:s')]],];
        $orderlist_arr = $orderlist_model->get_seo_list_on_user($condition);
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search' && !empty($get['from_time']) && !empty($get['to_time'])) {
                if ($get['from_time'] == $get['to_time']) {
                    $get['to_time'] = date('Y-m-d H:i:s', strtotime($get['to_time']) + 60 * 60 * 24 - 1);
                }
                $condition = ['addtime' => ['between', [$get['from_time'], $get['to_time']]],];
                $orderlist_arr = $orderlist_model->get_seo_list_on_user($condition);
            }
        }
        $assign_arr = array('title' => "渠道分析", 'list' => $orderlist_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function exportseo()
    {
        if (request()->isGet()) {
            $from_time = input('from_time');
            $to_time = input('to_time');
            if (!empty($from_time) && !empty($to_time)) {
                if ($from_time == $to_time) {
                    $to_time = date('Y-m-d H:i:s', strtotime($to_time) + 60 * 60 * 24 - 1);
                }
                $condition = ['addtime' => ['between', [$from_time, $to_time]],];
                $orderlist_model = new Orderlist();
                $orderlist_arr = $orderlist_model->get_seo_list_on_user($condition);
                if (!empty($orderlist_arr)) {
                    $txtname = date('Y') . date('m') . date('d') . " （渠道分析）" . ".txt";
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
        $string = "支付方式" . "\t" . "提交订单数" . "\t" . "已付订单数" . "\t" . "未付订单数" . "\t" . "订单总金额" . "\t" . "订单实收金额" . "\t" . "订单总收入" . "\t" . "\r\n";
        foreach ($content as $k => $val) {
            $channelname = $val['channelname'];
            $order_count = $val['order_count'];
            $pay_count = $val['pay_count'];
            $unpay_count = $val['unpay_count'];
            $ordermoney_sum = (float)$val['ordermoney_sum'];
            $paymoney_sum = (string)$val['paymoney_sum'];
            $usergain_sum = $val['usergain_sum'];
            $string .= $channelname . "\t" . $order_count . "\t" . $pay_count . "\t" . $unpay_count . "\t" . $ordermoney_sum . "\t" . $paymoney_sum . "\t" . $usergain_sum . "\t" . "\r\n";
        }
        return $string;
    }
} 