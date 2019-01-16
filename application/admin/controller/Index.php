<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Userpayment;
use app\index\model\Notifyhost;

class Index extends AdminBase
{
    public function index()
    {
        $zero_timestapm = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $zero_time = date('Y-m-d H:i:s', $zero_timestapm);
        $yesterday_zero_time = date('Y-m-d H:i:s', $zero_timestapm - 60 * 60 * 24);
        $user_model = new User();
        $order_model = new \app\admin\model\Orderlist();
        $userpaymen_model = new Userpayment();
        $condition = ['addtime' => ['>', $zero_time],];
        $count_today_register = $user_model->get_count_by_condition_on_base(['addtime' => ['>', $zero_time]]);
        $count_yesterday_register = $user_model->get_count_by_condition_on_base(['addtime' => [['>', $yesterday_zero_time], ['<', $zero_time]]]);
        $un_user = $user_model->get_count_by_condition_on_base(['is_state' => '0']);
        $fr_user = $user_model->get_count_by_condition_on_base(['is_state' => '2']);
        $count_today_submit = $order_model->get_count_by_condition_on_base(['addtime' => ['>', $zero_time]]);
        $count_today_unpay = $order_model->get_count_by_condition_on_base(['is_buylist' => ['<>', '1'], 'addtime' => ['>', $zero_time]]);
        $count_today_ispay = $order_model->get_count_by_condition_on_base(['is_buylist' => ['=', '1'], 'addtime' => ['>', $zero_time]]);
        $count_yesterday_ispay = $order_model->get_count_by_condition_on_base(['is_buylist' => ['=', '1'], 'addtime' => [['>', $yesterday_zero_time], ['<', $zero_time]]]);
        $sum_today_ispay = $order_model->sum_by_condition_on_admin(['is_buylist' => ['=', '1'], 'addtime' => ['>', $zero_time]], 'paymoney');
        $sum_today_usergain = $order_model->sum_by_condition_on_admin(['is_buylist' => ['=', '1'], 'addtime' => ['>', $zero_time]], 'paymoney*userproportion');
        $sum_yesterday_ispay = $order_model->sum_by_condition_on_admin(['is_buylist' => ['=', '1'], 'addtime' => [['>', $yesterday_zero_time], ['<', $zero_time]]], 'paymoney');
        $sum_yesterday_usergain = $order_model->sum_by_condition_on_admin(['is_buylist' => ['=', '1'], 'addtime' => [['>', $yesterday_zero_time], ['<', $zero_time]]], 'paymoney*userproportion');
        $sum_today_isuser = $userpaymen_model->sum_by_condition_on_admin(['is_user' => ['=', '1'], 'addtime' => ['>', $zero_time]], 'usermoney');
        $sum_yesterday_isuser = $userpaymen_model->sum_by_condition_on_admin(['is_user' => ['=', '1'], 'addtime' => [['>', $yesterday_zero_time], ['<', $zero_time]]], 'usermoney');
        $sum_today_isst = $userpaymen_model->sum_by_condition_on_admin(['is_state' => ['=', '1'], 'addtime' => ['>', $zero_time]], 'settlement');
        $sum_yesterday_isst = $userpaymen_model->sum_by_condition_on_admin(['is_state' => ['=', '1'], 'addtime' => [['>', $yesterday_zero_time], ['<', $zero_time]]], 'settlement');
        $week = get_weeks('Y-m-d');
        $week2 = get_weeks('m-d');
        foreach ($week2 as $k => $v) {
            $list_week[$k]['time'] = $v;
        }
        foreach ($week as $k => $v) {
            if (in_array($k, ['7', '6'])) {
                continue;
            }
            $list_week[$k]['sum_isst'] = $userpaymen_model->sum_by_condition_on_admin(['is_state' => ['=', '1'], 'addtime' => [['>', $v], ['<', $week[$k + 1]]]], 'settlement');
            $list_week[$k]['sum_ispay'] = $order_model->sum_by_condition_on_admin(['is_buylist' => ['=', '1'], 'addtime' => [['>', $v], ['<', $week[$k + 1]]]], 'paymoney');
        }
        $list_week['6']['sum_isst'] = $sum_yesterday_isst;
        $list_week['7']['sum_isst'] = $sum_today_isst;
        $list_week['6']['sum_ispay'] = $sum_yesterday_ispay;
        $list_week['7']['sum_ispay'] = $sum_today_ispay;
        $field = "
            COUNT(IF(channelid=1,TRUE,NULL)) AS count_1, 
            COUNT(IF(channelid=2,TRUE,NULL)) AS count_2, 
            COUNT(IF(channelid=3,TRUE,NULL)) AS count_3, 
            COUNT(IF(channelid=4,TRUE,NULL)) AS count_4, 
            COUNT(IF(channelid=5,TRUE,NULL)) AS count_5, 
            COUNT(IF(channelid=6,TRUE,NULL)) AS count_6, 
            COUNT(IF(channelid=7,TRUE,NULL)) AS count_7, 
            COUNT(IF(channelid=8,TRUE,NULL)) AS count_8, 
            COUNT(IF(channelid=9,TRUE,NULL)) AS count_9
            ";
        $sum_channel = $order_model->get_column_by_condition_on_admin($field, ['addtime' => ['>', $zero_time]]);
        $sum_channel = $sum_channel[0];
        $count = 0;
        foreach ($sum_channel as $k => $v) {
            $count += $v;
        }
        $assign_arr = array('title' => "后台主页", 'count_today_register' => $count_today_register, 'count_yesterday_register' => $count_yesterday_register, 'un_user' => $un_user, 'fr_user' => $fr_user, 'count_today_submit' => $count_today_submit, 'count_today_unpay' => $count_today_unpay, 'count_today_ispay' => $count_today_ispay, 'count_yesterday_ispay' => $count_yesterday_ispay, 'sum_today_ispay' => $sum_today_ispay, 'sum_today_usergain' => $sum_today_usergain, 'sum_yesterday_ispay' => $sum_yesterday_ispay, 'sum_yesterday_usergain' => $sum_yesterday_usergain, 'sum_today_isuser' => $sum_today_isuser, 'sum_yesterday_isuser' => $sum_yesterday_isuser, 'sum_today_isst' => $sum_today_isst, 'sum_yesterday_isst' => $sum_yesterday_isst, 'list_week' => $list_week, 'sum_channel' => $sum_channel, 'count' => $count,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function logout()
    {
        logout_admin();
        return $this->redirect(url('/login'));
    }

    public function refreshCache()
    {
        if (request()->isPost()) {
            $adminpayprovider_arr = db('adminpayprovider')->where(['is_state' => '1', 'is_trush' => '0',])->field(true)->select();
            cache('adminpayprovider', $adminpayprovider_arr);
            $adminpaychannel_arr = db('adminpaychannel')->where(['is_state' => '1', 'is_trush' => '0',])->field(true)->select();
            cache('adminpaychannel', $adminpaychannel_arr);
            $adminconfig_arr = db('adminconfig')->where(['id' => '1'])->field(true)->find();
            cache('adminconfig', $adminconfig_arr);
            $guestconfig_arr = db('guestconfig')->where(['id' => '1'])->field(true)->find();
            cache('guestconfig', $guestconfig_arr);
            return json_success("更新缓存成功");
        }
    }

    public function notifyhost()
    {
        return $this->fetch();
    }

    public function notifyhostsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $notifuhost_model = new Notifyhost();
            $res = $notifuhost_model->updateOrderStatusForBank($post['orderid'], $post['paymoney']);
            if ($res) {
                return json_success();
            } else {
                return json_error();
            }
        }
    }
} 