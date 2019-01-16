<?php

namespace app\user\controller;

use app\admin\model\Adminarticlelist;
use app\user\model\Orderlist;
use app\user\model\Userdefine;
use app\user\model\Userpayment;
use think\Session;

class Index extends UserBase
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
        $zero_timestapm = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $zero_time = date('Y-m-d H:i:s', $zero_timestapm);
        $yestaerday_zero_time = date('Y-m-d H:i:s', $zero_timestapm - 60 * 60 * 24);
        $condition = ['is_frozen' => '0', 'is_buylist' => '1', 'is_payment' => '0', 'addtime' => ['<', $zero_time,]];
        $unpaied_payment_arr = $orderlist_model->group_field_by_condition_on_user($condition, 'sum(paymoney*userproportion)');
        $unpaied_payment = number_format($unpaied_payment_arr[0]['sum(paymoney*userproportion)'], '2', '.', '');
        $condition = ['is_frozen' => '0', 'is_buylist' => '1', 'is_payment' => '0', 'addtime' => ['>', $zero_time,]];
        $today_income_arr = $orderlist_model->group_field_by_condition_on_user($condition, 'sum(paymoney)');
        $today_income = number_format($today_income_arr[0]['sum(paymoney)'], '2', '.', '');
        $userpaymoent_model = new Userpayment();
        $reserpayment_arr = $userpaymoent_model->get_one_by_session_userid_on_user();
        if (empty($reserpayment_arr)) {
            $last_usermoney = '0.00';
            $last_t = '暂无结算';
        } else {
            $last_usermoney = $reserpayment_arr['usermoney'];
            $last_t = $reserpayment_arr['updatetime'];
        }
        $orderlist_model = new Orderlist();
        $condition = ['is_buylist' => '1',];
        $is_buylist_count = $orderlist_model->get_count_by_condition_on_user($condition);
        $today_buylist_count = $orderlist_model->get_count_by_condition_on_user(array_merge($condition, ['addtime' => ['>', $zero_time]]));
        $yesterday_buylist_count = $orderlist_model->get_count_by_condition_on_user(array_merge($condition, ['addtime' => ['between', [$yestaerday_zero_time, $zero_time]]]));
        $yesterday_income_arr = $orderlist_model->group_field_by_condition_on_user(array_merge($condition, ['addtime' => ['between', [$yestaerday_zero_time, $zero_time]]]), 'sum(paymoney*userproportion) as sum');
        $yesterday_income = number_format($yesterday_income_arr[0]['sum'], '2', '.', '');
        $today_quantity_arr = $orderlist_model->group_field_by_condition_on_user(array_merge($condition, ['addtime' => ['>', $zero_time]]), 'sum(quantity) as sum_quantity,sum(paymoney) as sum_paymoney,sum(paymoney*userproportion) as sum_usergain');
        $today_quantity = number_format($today_quantity_arr[0]['sum_quantity'], '0', '.', '');
        $today_paymoney = number_format($today_quantity_arr[0]['sum_paymoney'], '2', '.', '');
        $today_sum_usergain = number_format($today_quantity_arr[0]['sum_usergain'], '2', '.', '');
        $articlelist_model = new Adminarticlelist();
        $condition = ['articlecateid' => '1', 'is_trush' => '0',];
        $articlelist_arr = $articlelist_model->get_list_by_condition_on_admin($condition, [], [], 12);
        $articlelist_arr = $articlelist_arr[0];
        $condition = ['addtime' => ['between', [date('Y-m-d'), date('Y-m-d H:i:s')]],];
        $orderlist_arr = $orderlist_model->get_seo_list_on_user($condition);
        $reserpayment_arr = $userpaymoent_model->get_list_by_session_userid_on_user([], [], '', 15);
        $reserpayment_list = $reserpayment_arr[0];
        $assign_arr = array('title' => "网站首页", 'unpaied_payment' => $unpaied_payment, 'today_income' => $today_income, 'last_usermoney' => $last_usermoney, 'last_t' => $last_t, 'is_buylist_count' => $is_buylist_count, 'today_buylist_count' => $today_buylist_count, 'yesterday_buylist_count' => $yesterday_buylist_count, 'yesterday_income' => $yesterday_income, 'today_quantity' => $today_quantity, 'today_paymoney' => $today_paymoney, 'today_sum_usergain' => $today_sum_usergain, 'articlelist_arr' => $articlelist_arr, 'orderlist_arr' => $orderlist_arr, 'reserpayment_list' => $reserpayment_list,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function help()
    {
        return $this->fetch();
    }

    public function baike()
    {
        return $this->fetch();
    }

    public function notice()
    {
        return $this->fetch();
    }

    public function link()
    {
        $route = request()->route();
        $articlelistid = input('param.articlelistid');
        $articlelist_model = new Adminarticlelist();
        $condition = ['articlelistid' => $articlelistid,];
        $list = $articlelist_model->get_one_by_condition_on_admin($condition);
        $assign_arr = array('title' => "平台公告", 'list' => $list,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    function make_root_password_for_centos($length = 64)
    {
        $base_chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $string = "( ) ` ~ ! @ # $ % ^ & * - + = | { } [ ] : ; ' < > , . ? / ";
        $chars = explode(" ", $string);
        $chars += $base_chars;
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, count($chars) - 1)];
        }
        return $password;
    }

    public function changeTheme()
    {
        $them_arr = array_keys(unserialize(qttheme));
        session('user.usertheme', in_array(input('theme'), $them_arr) ? input('theme') : 'monarch');
        (new Userdefine())->update_one_by_session_userid_on_user(['usertheme' => in_array(input('theme'), $them_arr) ? input('theme') : 'monarch']);
        echo "<script>history.go(-1)</script>";
        exit();
    }

    public function logout()
    {
        logout_user();
        Session::flash('login_msg', '退出成功');
        return $this->redirect('/login/index');
    }
}