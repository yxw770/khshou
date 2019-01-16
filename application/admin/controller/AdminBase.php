<?php

namespace app\admin\controller;

use app\admin\model\Admincheck;
use think\Controller;
use think\Session;
use think\Url;

class AdminBase extends Controller
{
    function _initialize()
    {
        $qttheme = "monarch";
        $this->view->config('view_path', APP_PATH_ADMIN . $qttheme . "/");
        $assign_arr = array('css' => '/admin/' . $qttheme . '/css', 'flag' => '/admin/' . $qttheme . '/flag', 'js' => '/admin/' . $qttheme . '/js', 'images' => '/admin/' . $qttheme . '/images', 'layer' => '/admin/' . $qttheme . '/layer', 'layui' => '/admin/' . $qttheme . '/layui', 'open' => '/admin/' . $qttheme . '/open', 'laydate' => '/admin/' . $qttheme . '/laydate',);
        $this->assign($assign_arr);
        Url::root('/' . urlRoot);
        $res = $this->check_admin();
        if ($res['status'] == '0') {
            logout_admin();
            if (!empty($res['msg'])) {
                Session::flash('login_msg', $res['msg']);
            }
            return $this->redirect('login/index');
        }
        $viewlimit = \session('admin.viewlimit');
        $viewlimit_arr = explode("|", $viewlimit);
        if (!in_array(request()->controller(), $viewlimit_arr)) {
            echo '<meta charset="utf-8"><script>
//                layer.msg("没有操作该页面的权限",{icon:3,time:3000},function() {
//                  javascript:history.back(-1);
//                })
                alert("没有操作该页面的权限");
                javascript:history.back(-1);
//                setTimeout("javascript:history.back(-1)",3000);
                </script>';
            exit();
        }
    }

    private function check_admin()
    {
        $session_arr = session('admin');
        $session_adminid = $session_arr['adminid'];
        $session_adminname = $session_arr['adminname'];
        $session_sessionid = $session_arr['sessionid'];
        $session_admintoken = $session_arr['admintoken'];
        $session_login_time = $session_arr['login_time'];
        $session_ip = $session_arr['login_ip'];
        $session_adminleave = $session_arr['adminleave'];
        if (empty($session_arr)) {
            return ['status' => 0, 'msg' => '检查到session为空，请先登录'];
        }
        if (empty($session_adminid)) {
            return ['status' => 0, 'msg' => '检查到session中adminid为空，请先登录'];
        }
        if (empty($session_adminname)) {
            return ['status' => 0, 'msg' => '检查到session中adminname为空，请先登录'];
        }
        if (empty($session_sessionid)) {
            return ['status' => 0, 'msg' => '检查到session中sessionid为空，请先登录'];
        }
        if (empty($session_admintoken)) {
            return ['status' => 0, 'msg' => '检查到session中admintoken为空，请先登录'];
        }
        if (empty($session_login_time)) {
            return ['status' => 0, 'msg' => '检查到session中login_time为空，请先登录'];
        }
        if (empty($session_ip)) {
            return ['status' => 0, 'msg' => '检查到session中ip为空，请先登录'];
        }
        if (empty($session_adminleave)) {
            return ['status' => 0, 'msg' => '检查到session中adminleave为空，请先登录'];
        }
        $model = new Admincheck();
        $condition = ['adminid' => $session_adminid,];
        $row = $model->get_one_by_condition_on_check($condition);
        $row_adminid = $row['adminid'];
        $row_adminname = $row['adminname'];
        $row_sessionid = $row['sessionid'];
        $row_adminagent = $row['adminagent'];
        $row_admintoken = $row['admintoken'];
        $row_adminuniqid = $row['adminuniqid'];
        $row_passkey = $row['passkey'];
        $row_login_time = $row['login_time'];
        $row_ip = $row['login_ip'];
        $row_adminleave = $row['adminleave'];
        if (empty($row)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不到该 adminid ，请先登录'];
        }
        if (empty($row_adminid)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 adminid ，请先登录'];
        }
        if (empty($row_adminname)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 adminname ，请先登录'];
        }
        if (empty($row_sessionid)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 sessionid ，请先登录'];
        }
        if (empty($row_adminagent)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 adminagent ，请先登录'];
        }
        if (empty($row_admintoken)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 admintoken ，请先登录'];
        }
        if (empty($row_adminuniqid)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 adminuniqid ，请先登录'];
        }
        if (empty($row_passkey)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 passkey ，请先登录'];
        }
        if (empty($row_login_time)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 login_time ，请先登录'];
        }
        if (empty($row_ip)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 login_ip ，请先登录'];
        }
        if (empty($row_adminleave)) {
            return ['status' => 0, 'msg' => '检查到数据库查询不存在该 adminleave ，请先登录'];
        }
        if ($session_adminid != $row_adminid) {
            return ['status' => 0, 'msg' => '检查到数据库中 adminid 与session中不相同，发现一个异常，请联系管理人员！'];
        }
        if ($session_adminname != $row_adminname) {
            return ['status' => 0, 'msg' => '检查到数据库中 adminname 与session中不相同，发现一个异常，请联系管理人员！'];
        }
        if ($session_sessionid != $row_sessionid) {
            return ['status' => 0, 'msg' => '检查到数据库中 sessionid 与session中不相同，可能在其他地方登陆，请联系管理人员！'];
        }
        if ($session_admintoken != $row_admintoken) {
            return ['status' => 0, 'msg' => '检查到数据库中 admintoken 与session中不相同，发现一个异常，请联系管理人员！'];
        }
        if ($session_login_time != $row_login_time) {
            return ['status' => 0, 'msg' => '检查到数据库中 login_time 与session中不相同，发现一个异常，请联系管理人员！'];
        }
        if ($session_ip != $row_ip) {
            return ['status' => 0, 'msg' => '检查到数据库中 ip 与session中不相同，可能在其他地方登陆，请联系管理人员！'];
        }
        $adminagent = md5(request()->header('user-agent'));
        $ip = request()->ip();
        $sessionid = session_id();
        if ($adminagent != $row_adminagent) {
            return ['status' => 0, 'msg' => '检查到本机浏览器 agent 与数据库中不相同，可能在其他地方登陆，请联系管理人员！'];
        }
        if ($ip != $row_ip) {
            return ['status' => 0, 'msg' => '检查到本机 ip 与数据库中不相同，可能在其他地方登陆，请联系管理人员！'];
        }
        if ($sessionid != $row_sessionid) {
            return ['status' => 0, 'msg' => '检查到本机 sessionid 与数据库中不相同，可能在其他地方登陆，请联系管理人员！'];
        }
        if (((time() - strtotime($row_adminleave)) / 60) >= 120) {
            return ['status' => 0, 'msg' => '检测到长时间没有活动，系统已经自动退出，请重新登录'];
        } elseif (((time() - strtotime($row_adminleave)) / 60) >= 10) {
            $model->update_one_by_condition_on_admin(['adminleave' => date('Y-m-d H:i:s')], $condition);
            return ['status' => 1, 'msg' => '更新成功'];
        }
        return ['status' => 1, 'msg' => '完成。。'];
    }
} 