<?php

namespace app\index\controller;

use app\index\model\User;
use app\wx\controller\Respone;
use think\Db;

class Login extends IndexBase
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
        $assign_arr = array('title' => '用户登录',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function login()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $username = $post_arr['username'];
            $password = $post_arr['password'];
            $validate = \think\Loader::validate('Uservalidate');
            $validate_arr = array('username' => $username, 'password' => $password,);
            if (!$validate->scene('login')->check($validate_arr)) {
                return json_error($validate->getError());
            }
            $user_model = new User();
            $row = $user_model->get_one_by_username_on_home($username);
            if (empty($row)) {
                return json_error('用户名不存在');
            }
            $password_verify = $row['password'];
            if (!$res = decrypt_userpass($password, $password_verify)) {
                return json_error('用户名或密码错误');
            }
            if ($row['is_state'] == '0') {
                return json_error('该账户后台正在审核中..');
            }
            if ($row['is_state'] == '2') {
                return json_error('该账户已冻结');
            }
            $userid = $row['userid'];
            $login_success_res = $this->login_success($userid, $username, $password_verify);
            if ($login_success_res) {
                return json_success("登录成功！");
            } else {
                return json_error("登录失败，请联系客服！");
            }
        }
    }

    private function login_success($userid, $username, $password)
    {
        if (session('?user')) {
            session('user', null);
        }
        $useragent = md5(request()->header('user-agent'));
        $ip = getIp();
        $sessionid = session_id();
        $login_time = date('Y-m-d H:i:s');
        $useruniqid = uniqid();
        $passkey = substr($password, -20);
        $usertoken_string = $useragent . $ip . $sessionid . $login_time . $useruniqid . $passkey;
        $usertoken = encrypt_usertoken($usertoken_string);
        Db::startTrans();
        $usertheme = db('userdefine')->where('userid', $userid)->value('usertheme');
        $load_arr_session = array('userid' => $userid, 'username' => $username, 'sessionid' => $sessionid, 'usertoken' => $usertoken, 'login_time' => $login_time, 'login_ip' => $ip, 'userleave' => $login_time, 'usertheme' => $usertheme,);
        session('user', $load_arr_session);
        if (!session('?user')) {
            Db::rollback();
            return false;
        }
        $load_arr_mysql = array('userid' => $userid, 'username' => $username, 'sessionid' => $sessionid, 'useragent' => $useragent, 'usertoken' => $usertoken, 'useruniqid' => $useruniqid, 'passkey' => $passkey, 'login_time' => $login_time, 'login_ip' => $ip, 'userleave' => $login_time,);
        $update_res = db('usercheck')->where('userid', $userid)->update($load_arr_mysql);
        if (!$update_res) {
            Db::rollback();
            return false;
        }
        $load_arr_userlog = ['userid' => $userid, 'username' => $username, 'logip' => getIp(), 'logtime' => $login_time,];
        $res_userlog = db('userlog')->insertGetId($load_arr_userlog);
        if (!$res_userlog) {
            Db::rollback();
            return false;
        }
        Db::commit();
        $load_arr_user = ['ip' => $ip];
        $res_user = db('user')->where('userid', $userid)->update($load_arr_user);
        return true;
    }
}