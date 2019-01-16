<?php

namespace app\index\controller;

use app\common\oss\Oss;
use app\wx\controller\Respone;
use think\Db;

class Tousu extends IndexBase
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
                $lenth = strlen_utf8($orderid);
                if ($lenth == '16' && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $orderid)) {
                    if (!preg_match('/^[A-Za-z0-9]{16}$/', $orderid)) {
                        echo "<script>window.onload=function (){layer.msg('订单编号不能包含特殊字符');}</script>";
                    } else {
                        $continue = true;
                    }
                } elseif ($lenth > 20) {
                    echo "<script>window.onload=function (){layer.msg('订单编号输入长度有误，不能超过20位');}</script>";
                } elseif ($lenth < 6) {
                    echo "<script>window.onload=function (){layer.msg('订单编号输入长度有误，不能小于6位');}</script>";
                } else {
                    echo "<script>window.onload=function (){layer.msg('订单编号输入长度有误，必须是16位');}</script>";
                }
            }
            $assign_arr = array('title' => "订单投诉",);
            $this->assign($assign_arr);
            return $this->fetch();
        }
    }

    public function toususave()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $orderid = $post_arr['report_orderid'];
            $report_type = $post_arr['report_type'];
            $report_contact = $post_arr['report_contact'];
            $report_telephone = $post_arr['report_telephone'];
            $maijia_remark = $post_arr['maijia_remark'];
            $continue = false;
            $lenth = strlen_utf8($orderid);
            if ($lenth == '16' && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $orderid)) {
                if (!preg_match('/^[A-Za-z0-9]{16}$/', $orderid)) {
                    return json_error('订单编号不能包含特殊字符');
                } else {
                    $continue = true;
                }
            } else {
                return json_error('订单编号输入长度有误，不是16位订单编号');
            }
            if (empty($report_type)) {
                return json_error('--请选择投诉原因--');
            }
            if (empty($report_contact) || !preg_match('/^(\w){6,20}$/', $report_contact)) {
                return json_error('请填写您的联系方式，推荐QQ号');
            }
            if (empty($report_telephone) || !preg_match('/^(\d){11}$/', $report_telephone)) {
                return json_error('请输入11位手机号');
            }
            if (empty($maijia_remark)) {
                return json_error('请填写详情说明');
            }
            $orderlist_row = db('orderlist')->where('orderid', $orderid)->field(true)->find();
            $gusetconfig_arr = cache('guestconfig');
            if ($orderlist_row['is_payment'] == '1') {
                return json_error('该笔订单未在当天投诉，平台已自动结算<br>请联系平台客服对该笔订单重新人工审核<br>平台客服QQ：' . $gusetconfig_arr['qq']);
            }
            if ($orderlist_row['is_buylist'] == '7') {
                return json_error('该笔订单已经退款完成，请勿重复投诉');
            }
            if ($orderlist_row['is_frozen'] == '1') {
                return json_error('该笔订单已经操作冻结，请勿重复投诉');
            }
            if ($orderlist_row['is_buylist'] != '1' && $orderlist_row['is_buylist'] != '11') {
                return json_error('该笔订单未付款，未付款的订单不能投诉');
            }
            $zero_timestapm = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            if (($zero_timestapm - strtotime($orderlist_row['addtime'])) > 0) {
                return json_error('请在购买当天投诉订单<br>超过当天系统不再支持<br>如需帮助<br>请联系平台客服QQ：' . $gusetconfig_arr['qq'] . '<br>申请人工重新审核');
            }
            $oss_model = new Oss();
            $json_arr = $oss_model->upload_oss('report_pay_img', 'payimgs');
            $arr_arr = json_decode($json_arr, true);
            if ($arr_arr['status'] == '1') {
                $img_url = $arr_arr['orther'];
            } else {
                return $json_arr;
            }
            $seller_username = db('user')->where('userid', $orderlist_row['userid'])->value('username');
            $maijia_code = rand(000000, 999999);
            $load_arr = ['report_orderid' => $post_arr['report_orderid'], 'report_type' => $post_arr['report_type'], 'maijia_contact' => $post_arr['report_contact'], 'maijia_telephone' => $post_arr['report_telephone'], 'maijia_ip' => getIp(), 'maijia_pay' => $orderlist_row['paymoney'], 'maijia_contact_pay' => $orderlist_row['contact'], 'maijia_account' => '', 'seller_userid' => $orderlist_row['userid'], 'seller_username' => $seller_username, 'report_creattime' => date('Y-m-d H:i:s'), 'report_status' => '0', 'report_pay_img' => $img_url, 'maijia_remark' => $post_arr['maijia_remark'], 'maijia_code' => $maijia_code,];
            Db::startTrans();
            $res1 = db('userreportresult')->insertGetId($load_arr);
            if (!$res1) {
                Db::rollback();
            }
            $res3 = db('orderlist')->where('orderlistid', $orderlist_row['orderlistid'])->update(['is_frozen' => '1']);
            if (!$res3) {
                Db::rollback();
            }
            $email = db('userdefine')->where('userid', $orderlist_row['userid'])->value('email');
            if (!empty($email)) {
                $site_arr = cache('guestconfig');
                $url = '<a href="http://' . $site_arr['siteurl'] . '/orderquery?kw=' . $orderid . '">http://' . $site_arr['siteurl'] . '/orderquery?kw=' . $orderid . '</a>';
                $message = "投诉通知！您的订单编号：“" . $orderid . "” 已有买家投诉，请您及时登陆后台完成处理。<br />";
                $message .= "交易订单号：{$orderid}<br />";
                $message .= "商品单价：{$orderlist_row['price']}<br />";
                $message .= "购买数量：{$orderlist_row['quantity']}<br />";
                $message .= "订单连接：{$url}<br />";
                $message .= "(点击打开以上链接可查看到投诉订单当前状态)";
                $email_data = ['email' => $email, 'title' => "投诉通知！ " . $site_arr['sitename'] . ' 提示您，您有订单被投诉', 'msg' => $message,];
                $ret = \think\Queue::push('app\common\queue\QueueClient@sendMAIL', $email_data, $queue = null);
            }
            Db::commit();
            if ($res1 && $res3) {
                $seller_tel = db('userdefine')->where('userid', $orderlist_row['userid'])->value('tel');
                $msg = ['code' => $orderlist_row['orderid'],];
                $this->send_sms_seller($seller_tel, $msg, 'SMS_152544200');
                $msg = ['code' => $orderlist_row['orderid'], 'password' => $maijia_code,];
                $this->send_sms($report_telephone, $msg, 'SMS_152549101');
                return json_success('投诉成功！<br>平台已经收到您的投诉，并且通知卖家<br>您可以继续联系平台客服');
            } else {
                return json_error();
            }
        }
    }

    private function send_sms($tel, $msg, $template)
    {
        $load_arr = ['tel' => $tel, 'content' => "发送投诉短信：" . $msg['code'], 'addtime' => date('Y-m-d H:i:s'),];
        $res1 = db('sms')->insertGetId($load_arr);
        if (empty($res1)) {
            exit();
        }
        require_once EXTEND_PATH . 'dysms/SmsDemo.php';
        $result = \dySms\SmsDemo::sendSms('', $template, $tel, $msg);
        $result_arr = json_decode(json_encode($result), true);
        if ($result_arr['Code'] == 'OK' || $result_arr['Message'] == 'OK') {
            $res2 = db('sms')->where('smsid', $res1)->update(['is_send' => '1', 'updatetime' => date('Y-m-d H:i:s')]);
            return true;
        } else {
            return false;
        }
    }

    private function send_sms_seller($tel, $msg, $template)
    {
        $load_arr = ['tel' => $tel, 'content' => "发送投诉提醒：" . $msg['code'], 'addtime' => date('Y-m-d H:i:s'),];
        $res1 = db('sms')->insertGetId($load_arr);
        if (empty($res1)) {
            exit();
        }
        require_once EXTEND_PATH . 'dysms/SmsDemo.php';
        $result = \dySms\SmsDemo::sendSms('', $template, $tel, $msg);
        $result_arr = json_decode(json_encode($result), true);
        if ($result_arr['Code'] == 'OK' || $result_arr['Message'] == 'OK') {
            $res2 = db('sms')->where('smsid', $res1)->update(['is_send' => '1', 'updatetime' => date('Y-m-d H:i:s')]);
            return true;
        } else {
            return false;
        }
    }

    function destroy_report()
    {
        $post_orderid = input('orderid');
        $post_pass = input('pass');
        $userreportresult_arr = db('userreportresult')->where(["report_orderid" => $post_orderid])->field(true)->find();
        if (!$userreportresult_arr) {
            return json_error("订单号输入错误，请重新输入。");
        }
        $mysql_pass = $userreportresult_arr['maijia_code'];
        if ($post_pass != $mysql_pass) {
            return json_error("密码错误，如需帮助请联系客服");
        }
        if ($userreportresult_arr['report_status'] == 1) {
            return json_error("该订单已撤销投诉，无需多次撤销");
        }
        if ($post_pass == $mysql_pass && $userreportresult_arr['report_status'] != 1) {
            $mysql_arr = array('report_finishtime' => date('Y-m-d H:i:s', time()), 'report_status' => '1', 'result_remark' => '买家撤销投诉', 'result_status' => '2', 'refund_status' => '0',);
            Db::startTrans();
            $res1 = db('userreportresult')->where(['id' => $userreportresult_arr['id']])->update($mysql_arr);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            $condition = ['orderid' => $userreportresult_arr['report_orderid'],];
            $load_arr = ['is_frozen' => '0',];
            $res2 = db('orderlist')->where($condition)->update($load_arr);
            if (!$res2) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function search()
    {
        $assign_arr = array('title' => "投诉查询",);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function searchResult()
    {
        $get_arr = trim_arr(input('get.'));
        $kw = isset($get_arr['kw']) ? $get_arr['kw'] : '';
        if (!empty($kw)) {
            $lenth = strlen_utf8($kw);
            if ($lenth == '16' && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $kw)) {
                if (!preg_match('/^[A-Za-z0-9]{16}$/', $kw)) {
                    echo "<script>window.onload=function (){layer.msg('订单编号不能包含特殊字符');}</script>";
                }
            } elseif ($lenth != 16) {
                echo "<script>window.onload=function (){layer.msg('输入长度有误，不能超过20位');}</script>";
            }
            $orderlist_row = db('orderlist')->where(['orderid' => $kw])->field("userid,paymoney,is_buylist,is_selllist,is_frozen,is_pwd,orderpwd")->find();
            if (empty($orderlist_row)) {
                echo "<script>window.onload=function (){layer.msg('没有此订单号');}</script>";
            } elseif ($orderlist_row['is_buylist'] == 0) {
                echo "<script>window.onload=function (){layer.msg('此订单未付款');}</script>";
            } else {
                $userreportresult_row = db('userreportresult')->where(['report_orderid' => $kw])->find();
                if (empty($userreportresult_row)) {
                    echo "<script>window.onload=function (){layer.msg('此订单未投诉');}</script>";
                } else {
                    $creattime = $userreportresult_row['report_creattime'];
                    $report_status = $userreportresult_row['report_status'];
                    $lesttime = strtotime($creattime) + 24 * 60 * 60 - time();
                    if ($lesttime <= 0 && $report_status == '0') {
                        $arr_update = array('report_status' => '2',);
                        $affected_row_number = db('userreportresult')->update($arr_update, ['report_orderid' => $userreportresult_row['report_orderid']]);
                    }
                    if ($lesttime <= 0) {
                        $lesttime = '0';
                    }
                    $userdefine_row = db('userdefine')->where(['userid' => $orderlist_row['userid']])->find();
                    $qq = $userdefine_row['qq'];
                    $lists['report_status'] = $userreportresult_row['report_status'];
                    $lists['result_status'] = $userreportresult_row['result_status'];
                    $lists['report_orderid'] = $userreportresult_row['report_orderid'];
                    $lists['report_type'] = $userreportresult_row['report_type'];
                    $lists['report_creattime'] = $userreportresult_row['report_creattime'];
                    $assign_arr = array('title' => "投诉结果",);
                    $this->assign('lesttime', $lesttime);
                    $this->assign('qq', $qq);
                    $this->assign('lists', $lists);
                    $this->assign($assign_arr);
                    return $this->fetch();
                }
            }
        }
        $assign_arr = array('title' => "投诉查询",);
        $this->assign($assign_arr);
        return $this->fetch('search');
    }
} 