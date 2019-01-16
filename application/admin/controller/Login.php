<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use think\Controller;
use think\Db;
use think\Url;

class Login extends Controller
{
    public function index()
    {
        Url::root('/adminoperat');
        return $this->fetch('/monarch/login/index');
    }

    public function login()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $adminname = $post_arr['adminname'];
            $adminpass = $post_arr['adminpass'];
            $safekey = $post_arr['safekey'];
            $validate = \think\Loader::validate('Adminvalidate');
            if (!$validate->scene('login')->check($post_arr)) {
                return json_error($validate->getError());
            }
            $admin_model = new Admin();
            $condition = array('adminname' => $adminname,);
            $row = $admin_model->get_one_by_condition_on_login($condition);
            if ($row['is_state'] == '0') {
                return json_error('该账户已经关闭');
            }
            if ($row['is_trush'] == '1') {
                return json_error('该账户已被删除');
            }
            if (empty($row)) {
                return json_error('用户名不存在');
            }
            $password_verify = $row['adminpass'];
            if (!$res = decrypt_adminpass($adminpass, $password_verify)) {
                return json_error('密码错误');
            }
            if ($row['is_safe'] == '1') {
                if ($safekey != $row['safekey']) {
                    return json_error('安全码错误');
                }
            }
            $ip = getIp();
            $whiteip = $row['whiteip'];
            $is_whiteip = $row['is_whiteip'];
            if (!empty($whiteip)) {
                if ($is_whiteip == '1') {
                    if (strpos($whiteip, $ip) === false) {
                        return json_error('登陆IP限制');
                    }
                }
                if ($is_whiteip == '2') {
                    $ip_arr = explode('.', $ip);
                    $prefix_ip = $ip_arr[0] . '.' . $ip_arr[1];
                    if (strpos($whiteip, $prefix_ip) === false) {
                        return json_error('登陆IP限制');
                    }
                }
            }
            $adminid = $row['adminid'];
            $login_success_res = $this->login_success($adminid, $adminname, $password_verify, $row['viewlimit']);
            if ($login_success_res) {
                return json_success("登录成功！");
            } else {
                return json_error("登录失败，请联系客服！");
            }
        }
    }

    private function login_success($adminid, $adminname, $password, $viewlimit)
    {
        if (session('?admin')) {
            session('admin', null);
        }
        $adminagent = md5(request()->header('user-agent'));
        $ip = request()->ip();
        $sessionid = session_id();
        $login_time = date('Y-m-d H:i:s');
        $adminuniqid = uniqid();
        $passkey = substr($password, -20);
        $admintoken_string = $adminagent . $ip . $sessionid . $login_time . $adminuniqid . $passkey;
        $admintoken = encrypt_admintoken($admintoken_string);
        Db::startTrans();
        $admintheme = 'monarch';
        $load_arr_session = array('adminid' => $adminid, 'adminname' => $adminname, 'sessionid' => $sessionid, 'admintoken' => $admintoken, 'login_time' => $login_time, 'login_ip' => $ip, 'adminleave' => $login_time, 'admintheme' => $admintheme, 'viewlimit' => $viewlimit);
        session('admin', $load_arr_session);
        if (!session('?admin')) {
            Db::rollback();
            return false;
        }
        $load_arr_mysql = array('adminid' => $adminid, 'adminname' => $adminname, 'sessionid' => $sessionid, 'adminagent' => $adminagent, 'admintoken' => $admintoken, 'adminuniqid' => $adminuniqid, 'passkey' => $passkey, 'login_time' => $login_time, 'login_ip' => $ip, 'adminleave' => $login_time,);
        $update_res = db('admincheck')->where('adminid', $adminid)->update($load_arr_mysql);
        if (!$update_res) {
            Db::rollback();
            return false;
        }
        $load_arr_userlog = ['adminid' => $adminid, 'adminname' => $adminname, 'loginip' => getIp(), 'logintime' => $login_time,];
        $res_userlog = db('adminlog')->insertGetId($load_arr_userlog);
        if (!$res_userlog) {
            Db::rollback();
            return false;
        }
        Db::commit();
        return true;
    }
} 