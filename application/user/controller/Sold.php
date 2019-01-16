<?php

namespace app\user\controller;

use app\user\model\Orderlist;
use app\user\model\Selllist;
use app\user\model\Usergoodlist;

class Sold extends UserBase
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
        $orderlist_model = new Orderlist();
        $condition = ['is_buylist' => '1',];
        $orderlist_arr = $orderlist_model->get_sold_list_on_user($condition);
        $list = $orderlist_arr[0];
        $page = $orderlist_arr[1];
        $goodlist_model = new Usergoodlist();
        $condition = ['is_trush' => '0',];
        $goodlist_arr = $goodlist_model->get_column_by_session_userid_on_user('goodlistid,goodname', $condition);
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                $condition = ['is_buylist' => '1',];
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
                $orderlist_arr = $orderlist_model->get_sold_list_on_user($condition);
                $list = $orderlist_arr[0];
                $page = $orderlist_arr[1];
            }
        }
        $assign_arr = array('title' => "最近卖出", 'list' => $list, 'page' => $page, 'goodlist_arr' => $goodlist_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function link()
    {
        $route = request()->route();
        $orderlistid = input('param.orderlistid');
        $selllist_model = new Selllist();
        $condition = ['orderlistid' => $orderlistid,];
        $selllist_arr = $selllist_model->get_one_list_kemi_on_user($condition);
        $assign_arr = array('title' => "查看卡密", 'list' => $selllist_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function exportsold()
    {
        if (request()->isGet()) {
            $goodlistid = input('goodlistid');
            $ktype = input('ktype');
            $keyword = input('keyword');
            $condition = ['is_buylist' => '1',];
            if (!empty($get['goodlistid'])) {
                $condition['goodlistid'] = $goodlistid;
            }
            if (!empty($keyword) && !empty($ktype)) {
                if ($ktype == 'order') {
                    $condition['orderid'] = ['like', '%' . $keyword . '%'];
                } elseif ($ktype == 'contact') {
                    $condition['contact'] = ['like', '%' . $keyword . '%'];
                }
            }
            $orderlist_model = new Orderlist();
            $orderlist_arr = $orderlist_model->get_export_sold_on_user($condition);
            if (!empty($orderlist_arr)) {
                $txtname = date('Y') . date('m') . date('d') . " （最近卖出）" . ".txt";
                $content = $this->get_export_content($orderlist_arr);
                export_txt($content, $txtname);
                exit();
            } else {
                export_txt('暂无可以导出的订单', '最近卖出.txt');
                exit();
            }
        }
    }

    public function get_export_content($content)
    {
        $string = "订单编号" . "\t" . "交易时间" . "\t" . "商品名称" . "\t" . "支付方式" . "\t" . "金额" . "\t" . "购买者信息" . "\t" . "状态" . "\t" . "\r\n";
        foreach ($content as $k => $val) {
            switch ($val['is_buylist']) {
                case '0';
                    $is_status = '未付款';
                    break;
                case '1';
                    $is_status = '已付款';
                    break;
                case '2';
                    $is_status = '部分付款';
                    break;
                case '4';
                    $is_status = '已退款';
                    break;
            }
            $orderid = $val['orderid'];
            $addtime = $val['addtime'];
            $goodname = $val['goodname'];
            $channelname = $val['channelname'];
            $ordermoney = (float)$val['ordermoney'];
            $contact = (string)$val['contact'];
            $status = $val['is_frozen'] ? '已冻结' : $is_status;
            $string .= $orderid . "\t" . $addtime . "\t" . $goodname . "\t" . $channelname . "\t" . $ordermoney . "\t" . $contact . "\t" . $status . "\t" . "\r\n";
        }
        return $string;
    }
}