<?php

namespace app\index\model;

use app\common\model\BaseModel;
use app\common\queue\QueueClient;
use app\wx\controller\Respone;
use Mailermaster\PHPMailer;
use think\Cache;
use think\Db;

class Notifyhost extends BaseModel
{
    public function updateOrderStatusForBank($orderid, $receipt_amount, $is_buylist = 1)
    {
        $orderlist_row = db('orderlist')->where('orderid', $orderid)->field(true)->find();
        if (empty($orderlist_row)) {
            return false;
        }
        if ($orderlist_row['is_selllist'] == '1') {
            return false;
        }
        if ($orderlist_row['is_sub'] == 1) {
            return (new SubNotifyhost())->updateOrderStatusForBank($orderid, $receipt_amount, $is_buylist);
            exit();
        }
        if ($receipt_amount < $orderlist_row['ordermoney']) {
            return false;
        }
        if ($receipt_amount > $orderlist_row['ordermoney']) {
            $receipt_amount = $orderlist_row['ordermoney'];
        }
        $cukun = db('userproduct')->where(['goodlistid' => $orderlist_row['goodlistid'], 'is_state' => '1', 'is_trush' => '0'])->count();
        if ($cukun >= $orderlist_row['quantity']) {
            $row_userdefine = db('userdefine')->where(['userid' => $orderlist_row['userid']])->field(true)->limit(1)->orderRaw('rand()')->find();
            if (empty($row_userdefine)) {
                return false;
            }
            $check_goods = $row_userdefine['check_goods'] ? $row_userdefine['check_goods'] : 1;
            Db::startTrans();
            if ($check_goods == 1) {
                $kami = db('userproduct')->where(['goodlistid' => $orderlist_row['goodlistid'], 'is_state' => '1', 'is_trush' => '0'])->limit($orderlist_row['quantity'])->orderRaw('rand()')->lock(true)->select();
            } elseif ($check_goods == 2) {
                $kami = db('userproduct')->where(['goodlistid' => $orderlist_row['goodlistid'], 'is_state' => '1', 'is_trush' => '0'])->limit($orderlist_row['quantity'])->order('productid asc')->lock(true)->select();
            } elseif ($check_goods == 3) {
                $kami = db('userproduct')->where(['goodlistid' => $orderlist_row['goodlistid'], 'is_state' => '1', 'is_trush' => '0'])->limit($orderlist_row['quantity'])->order('productid desc')->lock(true)->select();
            } else {
                $kami = db('userproduct')->where(['goodlistid' => $orderlist_row['goodlistid'], 'is_state' => '1', 'is_trush' => '0'])->limit($orderlist_row['quantity'])->orderRaw('rand()')->lock(true)->select();
            }
            $kami_map = array_column($kami, 'productid');
            $is_orderlistid = db('userproduct')->where(['orderlistid' => $orderlist_row['orderlistid']])->count();
            if ($is_orderlistid > 0) {
                Db::rollback();
                return false;
            }
            $updatetime = date('Y-m-d H:i:s');
            $res1 = db('userproduct')->where(['productid' => ['in', $kami_map]])->update(['orderlistid' => $orderlist_row['orderlistid'], 'is_state' => '0', 'updatetime' => $updatetime]);
            if (!$res1 || $res1 != $orderlist_row['quantity']) {
                Db::rollback();
                return false;
            }
            $insert_arr = [];
            foreach ($kami_map as $k => $v) {
                $insert_arr[$k] = ['userid' => $orderlist_row['userid'], 'goodlistid' => $orderlist_row['goodlistid'], 'orderlistid' => $orderlist_row['orderlistid'], 'productid' => $v, 'orderid' => $orderlist_row['orderid'], 'addtime' => $updatetime,];
            }
            $res2 = db('selllist')->insertAll($insert_arr);
            if (!$res2 || $res2 != $orderlist_row['quantity']) {
                Db::rollback();
                return false;
            }
            $res3 = db('orderlist')->where('orderlistid', $orderlist_row['orderlistid'])->update(['paymoney' => $receipt_amount, 'is_buylist' => '1', 'is_selllist' => '1', 'updatetime' => $orderlist_row['updatetime'] ? $orderlist_row['updatetime'] : date('Y-m-d H:i:s')]);
            if (!$res3 && empty($orderlist_row['updatetime'])) {
                Db::rollback();
                return false;
            }
            $count_is_orderlistid = db('userproduct')->where(['orderlistid' => $orderlist_row['orderlistid']])->count();
            if ($count_is_orderlistid != $orderlist_row['quantity']) {
                Db::rollback();
                return false;
            }
            Db::commit();
            if ($res3) {
                $orderdefine_row = db('orderdefine')->where('orderlistid', $orderlist_row['orderlistid'])->field(true)->find();
                if ($orderdefine_row['is_tel'] == '1' && !empty($orderdefine_row['tel'])) {
                    $this->send_sms($orderdefine_row['tel'], ['orderid' => $orderid], 'SMS_153885184');
                }
                if ($orderdefine_row['is_email'] == '1' && !empty($orderdefine_row['email'])) {
                    $site_arr = cache('guestconfig');
                    $goodname = db('usergoodlist')->where(['goodlistid' => $orderlist_row['goodlistid']])->value('goodname');
                    $url = '<a href="http://' . $site_arr['siteurl'] . '/orderquery?kw=' . $orderid . '">http://' . $site_arr['siteurl'] . '/orderquery?kw=' . $orderid . '</a>';
                    $message = "?????????????????????????????????????????????????????????????????????<br />";
                    $message .= "??????????????????{$orderid}<br />";
                    $message .= "???????????????{$goodname}<br />";
                    $message .= "???????????????{$orderlist_row['price']}<br />";
                    $message .= "???????????????{$orderlist_row['quantity']}<br />";
                    $message .= "???????????????{$url}<br />";
                    $message .= "(??????????????????????????????????????????????????????????????????)";
                    $this->send_email($orderdefine_row['email'], $site_arr['sitename'] . ' ????????????[' . $goodname . ']???????????????', $message, $site_arr['siteurl']);
                }
            }
            return true;
        } elseif ($cukun < $orderlist_row['quantity']) {
            Db::startTrans();
            $res3 = db('orderlist')->where('orderlistid', $orderlist_row['orderlistid'])->update(['paymoney' => $receipt_amount, 'is_buylist' => '11', 'is_selllist' => '3', 'updatetime' => $orderlist_row['updatetime'] ? $orderlist_row['updatetime'] : date('Y-m-d H:i:s')]);
            if (!$res3 && empty($orderlist_row['updatetime'])) {
                Db::rollback();
            }
            $email = db('userdefine')->where('userid', $orderlist_row['userid'])->value('email');
            if ($res3) {
                if (!empty($email)) {
                    $load_arr = ['userid' => $orderlist_row['userid'], 'to' => $orderlist_row['userid'], 'content' => "?????????????????????" . $orderid, 'goodlistid' => $orderlist_row['goodlistid'], 'addtime' => date('Y-m-d H:i:s'),];
                    $res4 = db('email')->insertGetId($load_arr);
                    if (empty($res4)) {
                        Db::rollback();
                    }
                }
            }
            Db::commit();
            if ($res3) {
                if (!empty($email)) {
                    $site_arr = cache('guestconfig');
                    $goodname = db('usergoodlist')->where(['goodlistid' => $orderlist_row['goodlistid']])->value('goodname');
                    $url = '<a href="http://' . $site_arr['siteurl'] . '/orderquery?kw=' . $orderid . '">http://' . $site_arr['siteurl'] . '/orderquery?kw=' . $orderid . '</a>';
                    $message = "??????????????????????????????" . $goodname . "??? ????????????????????????????????????????????????????????????????????????????????????<br />";
                    $message .= "??????????????????????????????????????????????????????????????????????????????????????????????????????????????????<br />";
                    $message .= "??????????????????{$orderid}<br />";
                    $message .= "???????????????{$goodname}<br />";
                    $message .= "???????????????{$orderlist_row['price']}<br />";
                    $message .= "???????????????{$orderlist_row['quantity']}<br />";
                    $message .= "?????????????????????????????????<br />";
                    $message .= "???????????????{$url}<br />";
                    $message .= "(??????????????????????????????????????????????????????????????????)";
                    $email_data = ['email' => $email, 'title' => $site_arr['sitename'] . ' ????????????[' . $goodname . ']???????????????', 'msg' => $message,];
                    \think\Queue::push('app\common\queue\QueueClient@sendMAIL', $email_data, $queue = null);
                    \think\Queue::later(100, 'app\common\queue\QueueClient@sendMAIL', $email_data, $queue = null);
                }
            }
            return true;
        }
    }

    private function send_sms($tel, $msg, $template)
    {
        $load_arr = ['tel' => $tel, 'content' => "?????????????????????" . $msg['orderid'], 'addtime' => date('Y-m-d H:i:s'),];
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

    private function send_email($email, $title, $msg, $siteurl = '')
    {
        $smtp_arr = cache('adminconfig');
        $site_arr = cache('guestconfig');
        $mail = new PHPMailer();
        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            $mail->Username = $smtp_arr['stmpeemail'];
            $mail->Password = $smtp_arr['sttmppwd'];
            $mail->Host = $smtp_arr['stmp'];
            $mail->isHTML(true);
            $mail->setFrom($smtp_arr['stmpeemail'], $site_arr['siteurl']);
            $mail->Subject = $title;
            $mail->Body = $msg;
            $mail->addAddress($email);
            $res1 = $mail->send();
        } catch (Exception $e) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }
} 