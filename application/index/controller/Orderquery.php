<?php

namespace app\index\controller;

use app\index\model\Notifyhost;

class Orderquery extends IndexBase
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
            $kw = isset($get_arr['kw']) ? $get_arr['kw'] : '';
            $pwd = isset($get_arr['pwd']) ? $get_arr['pwd'] : '';
            $continue = false;
            $flag = '';
            $list = [];
            $tousu = 0;
            $userproduct_row = [];
            if (!empty($kw)) {
                if (!session('?chk_tip')) {
                    session('chk_tip', ['ip' => getIp(), 'time' => time(), 'search_counts' => 0]);
                } else {
                    $chk_tip = session('chk_tip');
                    $ip = $chk_tip['ip'];
                    $time = $chk_tip['time'];
                    $search_counts = $chk_tip['search_counts'];
                    if ((time() - $time) < 0.5) {
                        echo "<script>window.onload=function (){layer.msg('请不要搜索过快');}</script>";
                        session('chk_tip', ['ip' => getIp(), 'time' => time(), 'search_counts' => $search_counts]);
                    } else {
                        $search_counts = $chk_tip['search_counts'] + 1;
                        session('chk_tip', ['ip' => getIp(), 'time' => time(), 'search_counts' => $search_counts]);
                    }
                }
                $lenth = strlen_utf8($kw);
                if ($lenth == '16' && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $kw)) {
                    if (!preg_match('/^[A-Za-z0-9]{16}$/', $kw)) {
                        echo "<script>window.onload=function (){layer.msg('订单编号不能包含特殊字符');}</script>";
                    } else {
                        $flag = 'orderid';
                        $continue = true;
                    }
                } elseif ($lenth > 20) {
                    echo "<script>window.onload=function (){layer.msg('输入长度有误，不能超过20位');}</script>";
                } elseif ($lenth < 6) {
                    echo "<script>window.onload=function (){layer.msg('输入长度有误，不能小于6位');}</script>";
                } else {
                    $flag = 'contact';
                    $continue = true;
                }
                if ($flag == 'orderid') {
                    $orderlist_row = db('orderlist')->where($flag, $kw)->field("orderid,paymoney,is_buylist,is_selllist,is_frozen,is_pwd,orderpwd")->find();
                    if (empty($orderlist_row)) {
                        echo "<script>window.onload=function (){layer.msg('没有此订单号');}</script>";
                        $continue = false;
                    } elseif ($orderlist_row['is_buylist'] != 1 && $orderlist_row['is_buylist'] != '11') {
                    } elseif ($orderlist_row['is_buylist'] == 1 || $orderlist_row['is_buylist'] == '11') {
                        $is_pwd = $orderlist_row['is_pwd'];
                        $orderpwd = $orderlist_row['orderpwd'];
                        if ($is_pwd == '1' || ($is_pwd == '2' && !empty($orderpwd))) {
                            if (!session('?pwd')) {
                                echo "<script>var time=3;setInterval('timedesc()',1000);function timedesc(){if(time>0){time=time-1;$('#time').text(time)};};</script><script>window.onload=function (){layer.msg('订单编号：" . $kw . " <br>需要填写取卡密码<br><span id=\"time\">'+time+'</span>秒钟将为您自动跳转输入取卡密码页面',{time:3000},function() {window.location.href='/pwd?orderid=" . $kw . "'});}</script>";
                                $continue = false;
                            } elseif (session('?pwd') != $orderlist_row['orderpwd']) {
                                echo "<script>window.onload=function (){layer.msg('取卡密码填写错误',{},function() {window.location.href='/pwd?orderid=" . $kw . "'});}</script>";
                                $continue = false;
                            } elseif (session('?pwd') == $orderlist_row['orderpwd']) {
                                $continue = true;
                                session('pwd', null);
                            }
                        }
                    }
                }
                if ($continue) {
                    if ($flag == 'orderid') {
                        if ($orderlist_row['is_buylist'] == '11' && $orderlist_row['is_selllist'] == '3' && $orderlist_row['is_frozen'] == '0') {
                            $notifyhost_model = new Notifyhost();
                            $res = $notifyhost_model->updateOrderStatusForBank($kw, $orderlist_row['paymoney']);
                        }
                        $field = "o.orderid,o.orderlistid,o.goodlistid,o.userid,o.ordermoney,o.quantity,o.paymoney,o.userqq,o.channelname,o.is_buylist,o.is_selllist,o.is_frozen,o.addtime,o.is_pwd,o.is_sub,o.sub_userid,o.sub_money,o.is_sub_ship,g.instrucation";
                        $orderlist_row = db('orderlist')->alias('o')->join('usergoodlist g', 'o.goodlistid=g.goodlistid', 'left')->where($flag, $kw)->field($field)->find();
                        if ($orderlist_row['is_buylist'] == '1' && $orderlist_row['is_selllist'] == '1') {
                            $userproduct_row = db('userproduct')->where('orderlistid', $orderlist_row['orderlistid'])->field('cardnumber,cardpassword')->select();
                            db('orderlist')->where('orderlistid', $orderlist_row['orderlistid'])->setInc('search_tips');
                            $tousu = 1;
                        }
                        if ($orderlist_row['is_sub'] == 1) {
                            $orderlist_row['ordermoney'] += $orderlist_row['sub_money'];
                            $orderlist_row['paymoney'] += $orderlist_row['paymoney'] > 0 ? $orderlist_row['sub_money'] : 0;
                        }
                        $list[0] = $orderlist_row;
                        $list[0]['kami'] = $userproduct_row;
                    }
                    if ($flag == 'contact') {
                        $field = "o.orderid,o.orderlistid,o.goodlistid,o.userid,o.ordermoney,o.quantity,o.paymoney,o.userqq,o.channelname,o.is_buylist,o.is_selllist,o.is_frozen,o.addtime,o.is_pwd,o.is_sub,o.sub_userid,o.sub_money,o.is_sub_ship,g.instrucation";
                        $orderlist_row = db('orderlist')->alias('o')->join('usergoodlist g', 'o.goodlistid=g.goodlistid', 'left')->where($flag, $kw)->order('orderlistid', 'desc')->field($field)->limit(10)->select();
                        foreach ($orderlist_row as $k => $v) {
                            if ($v['is_sub'] == 1) {
                                $v['ordermoney'] += $v['sub_money'];
                                $v['paymoney'] += $v['paymoney'] > 0 ? $v['sub_money'] : 0;
                            }
                            $list[$k] = $v;
                            if ($v['is_pwd'] != 0) {
                                $list[$k]['kami'] = [['cardnumber' => '该卡密仅支持通过订单编号进行查询', 'cardpassword' => '',]];
                            } else {
                                $userproduct_row = db('userproduct')->where('orderlistid', $v['orderlistid'])->field('cardnumber,cardpassword')->select();
                                $list[$k]['kami'] = $userproduct_row;
                            }
                            $userproduct_row = [];
                        }
                    }
                }
            } else {
                if (session('?chk_tip')) {
                    session('chk_tip', null);
                }
            }
            foreach ($list as $k => $v) {
                $list[$k]['userqq'] = db('userdefine')->where(['userid' => $v['userid']])->value('qq');
            }
            $assign_arr = array('title' => "订单查询", 'list' => $list, 'tousu' => $tousu,);
            $this->assign($assign_arr);
            return $this->fetch();
        }
    }

    public function help()
    {
        return $this->fetch();
    }
} 