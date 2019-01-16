<?php

namespace app\index\controller;
class Pwd extends IndexBase
{
    function _initialize()
    {
        parent::_initialize();
        $gusetconfig_arr = cache('guestconfig');
        $assign_arr = array('qtarr' => $gusetconfig_arr,);
        $this->assign($assign_arr);
    }

    public function index()
    {
        if (request()->isGet()) {
            $get_arr = trim_arr(input('get.'));
            $orderid = isset($get_arr['orderid']) ? $get_arr['orderid'] : '';
            $continue = false;
            if (!empty($orderid)) {
            } else {
                echo "<script>window.onload=function (){layer.msg('请搜索订单编号',{},function() {window.location.href='" . url('/orderquery') . "'});}</script>";
            }
            $assign_arr = array('title' => "查询密码",);
            $this->assign($assign_arr);
            return $this->fetch();
        }
    }

    public function chkpwd()
    {
        if (request()->isPost()) {
            $get_arr = trim_arr(input('post.'));
            $kw = isset($get_arr['kw']) ? $get_arr['kw'] : '';
            $pwd = isset($get_arr['pwd']) ? $get_arr['pwd'] : '';
            $continue = false;
            $lenth = strlen_utf8($kw);
            if ($lenth == '16' && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $kw)) {
                if (!preg_match('/^[A-Za-z0-9]{16}$/', $kw)) {
                    return json_error('订单编号不能包含特殊字符');
                } else {
                    $continue = true;
                }
            } else {
                return json_error('请输入16位订单编号');
            }
            if ($continue) {
                $orderlist_row = db('orderlist')->where('orderid', $kw)->field("is_buylist,is_selllist,is_pwd,orderpwd")->find();
                $is_pwd = $orderlist_row['is_pwd'];
                if ($is_pwd == '1' || ($is_pwd == '2' && !empty($pwd))) {
                    if (!$pwd) {
                        return json_error('请填写取卡密码');
                    }
                    if ($pwd != $orderlist_row['orderpwd']) {
                        return json_error('取卡密码填写错误');
                    }
                    if ($pwd == $orderlist_row['orderpwd']) {
                        session('pwd', $pwd);
                        return json_success('填写正确');
                    }
                }
            } else {
                return json_error();
            }
        }
    }
}