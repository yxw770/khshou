<?php

namespace app\user\controller;

use app\common\controller\Webconfig;
use app\user\model\Usercheck;
use think\Session;
use think\Url;

class UserBase extends Webconfig
{
    function _initialize()
    {
        parent::_initialize();
        $gusetconfig_arr = cache('guestconfig');
        if (isMobile()) {
            $qttheme = 'mobile';
        } else {
            $qttheme = session('user.usertheme');
        }
        $this->view->config('view_path', APP_PATH_USER . $qttheme . "/");
        $assign_arr = array('css' => '/user/' . $qttheme . '/css', 'js' => '/user/' . $qttheme . '/js', 'images' => '/user/' . $qttheme . '/images', 'layer' => '/user/' . $qttheme . '/layer', 'open' => '/user/' . $qttheme . '/open', 'laydate' => '/user/' . $qttheme . '/laydate', 'layertip' => '/user/' . $qttheme . '/layertip', 'qtarr' => $gusetconfig_arr,);
        $this->assign($assign_arr);
        Url::root('/center');
        $res = [];
        if (session('?user') && !session('?admin')) {
            $res = $this->check_user();
        } elseif (session('?user') && session('?admin')) {
            $res = $this->check_admin();
        }
        if (!isset($res) || empty($res)) {
            logout_user();
            return $this->redirect('/login/index');
        }
        if ($res['status'] == '0') {
            logout_user();
            Session::flash('login_msg', $res['msg']);
            return $this->redirect('/login/index');
        }
    }

    private function check_user()
    {
        $session_arr = session('user');
        $session_userid = $session_arr['userid'];
        $session_username = $session_arr['username'];
        $session_sessionid = $session_arr['sessionid'];
        $session_usertoken = $session_arr['usertoken'];
        $session_login_time = $session_arr['login_time'];
        $session_ip = $session_arr['login_ip'];
        $session_userleave = $session_arr['userleave'];
        if (empty($session_arr)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_userid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_username)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_sessionid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_usertoken)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_login_time)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_ip)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_userleave)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        $model = new Usercheck();
        $row = $model->get_one_by_id_on_user($session_userid);
        $row_userid = $row['userid'];
        $row_username = $row['username'];
        $row_sessionid = $row['sessionid'];
        $row_useragent = $row['useragent'];
        $row_usertoken = $row['usertoken'];
        $row_useruniqid = $row['useruniqid'];
        $row_passkey = $row['passkey'];
        $row_login_time = $row['login_time'];
        $row_ip = $row['login_ip'];
        $row_userleave = $row['userleave'];
        if (empty($row)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_userid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_username)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_sessionid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_useragent)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_usertoken)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_useruniqid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_passkey)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_login_time)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_ip)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_userleave)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if ($session_userid != $row_userid) {
            return ['status' => 0, 'msg' => '检测到账号存在异常，该账号在其他地方登陆，异常编号：453'];
        }
        if ($session_username != $row_username) {
            return ['status' => 0, 'msg' => '检测到账号存在异常，该账号在其他地方登陆，异常编号：454'];
        }
        if ($session_sessionid != $row_sessionid) {
            return ['status' => 0, 'msg' => '检测到登陆状态存在异常，该账号在其他地方登陆，异常编号：455'];
        }
        if ($session_usertoken != $row_usertoken) {
            return ['status' => 0, 'msg' => '检测到登陆状态存在异常，该账号在其他地方登陆，异常编号：456'];
        }
        if ($session_login_time != $row_login_time) {
            return ['status' => 0, 'msg' => '检测到登陆状态存在异常，该账号在其他地方登陆，异常编号：457'];
        }
        if ($session_ip != $row_ip) {
            return ['status' => 0, 'msg' => '检测到登陆IP存在异常，该账号在其他地方登陆，异常编号：453'];
        }
        $useragent = md5(request()->header('user-agent'));
        $ip = getIp();
        $sessionid = session_id();
        if ($ip != $row_ip) {
            return ['status' => 0, 'msg' => '检测到登陆IP存在异常，，该账号在其他地方登陆，异常编号：462'];
        }
        if ($sessionid != $row_sessionid) {
            return ['status' => 0, 'msg' => '检测到登陆环境存在异常，异常编号：463'];
        }
        if (((time() - strtotime($row_userleave)) / 60) >= 120) {
            return ['status' => 0, 'msg' => '检测到长时间没有活动，系统已经自动退出，请重新登录'];
        } elseif (((time() - strtotime($row_userleave)) / 60) >= 10) {
            $model->update_one_by_id_on_user(['userleave' => date('Y-m-d H:i:s')], $session_userid);
            return ['status' => 1, 'msg' => ''];
        }
        return ['status' => 1, 'msg' => ''];
    }

    private function check_admin()
    {
        $session_arr = session('user');
        $session_userid = $session_arr['userid'];
        $session_username = $session_arr['username'];
        $session_sessionid = $session_arr['sessionid'];
        $session_login_time = $session_arr['login_time'];
        $session_ip = $session_arr['login_ip'];
        $session_userleave = $session_arr['userleave'];
        if (empty($session_arr)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_userid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_username)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_sessionid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_login_time)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_ip)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($session_userleave)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        $model = new Usercheck();
        $row = $model->get_one_by_id_on_user($session_userid);
        $row_userid = $row['userid'];
        $row_username = $row['username'];
        $row_sessionid = $row['sessionid'];
        $row_useragent = $row['useragent'];
        $row_useruniqid = $row['useruniqid'];
        $row_login_time = $row['login_time'];
        $row_ip = $row['login_ip'];
        $row_userleave = $row['userleave'];
        if (empty($row)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_username)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_sessionid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_useragent)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_useruniqid)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_login_time)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_ip)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_userleave)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if (empty($row_userleave)) {
            return ['status' => 0, 'msg' => '请先登录账号'];
        }
        if ($session_userid != $row_userid) {
            return ['status' => 0, 'msg' => '检测到异常1，请重新登录'];
        }
        if ($session_username != $row_username) {
            return ['status' => 0, 'msg' => '检测到异常2，请重新登录'];
        }
        if ($session_sessionid != $row_sessionid) {
            return ['status' => 0, 'msg' => '检测到异常3，请重新登录'];
        }
        if ($session_login_time != $row_login_time) {
            return ['status' => 0, 'msg' => '检测到异常5，请重新登录'];
        }
        if ($session_ip != $row_ip) {
            return ['status' => 0, 'msg' => '检测到ip异常，请重新登录'];
        }
        return ['status' => 1, 'msg' => ''];
    }
}