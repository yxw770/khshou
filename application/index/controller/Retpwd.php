<?php

namespace app\index\controller;

use app\index\model\User;
use think\Db;

class Retpwd extends IndexBase
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
        $assign_arr = array('title' => '找回密码',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function chkuser()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $username = $post_arr['username'];
            $email = $post_arr['email'];
            $tel = $post_arr['tel'];
            if (empty($username)) {
                return json_error('账号必填');
            }
            if (empty($email)) {
                return json_error('邮箱必填');
            }
            if (empty($tel)) {
                return json_error('手机号必填');
            }
            if (strlen($username) > 20) {
                return json_error('账号长度不能大于20位');
            }
            if (strlen($username) < 6) {
                return json_error('账号长度不能小于6位');
            }
            if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $email)) {
                return json_error('邮箱填写不正确');
            }
            if (!preg_match('/^(\d){11}$/', $tel)) {
                return json_error('手机号码填写不正确');
            }
            $user_model = new User();
            $row = $user_model->get_one_by_username_on_home($username);
            if (empty($row)) {
                return json_error('用户名不存在');
            }
            $userdefine_row = db('userdefine')->where('userid', $row['userid'])->field(true)->find();
            if ($email != $userdefine_row['email']) {
                return json_error('邮箱不匹配');
            }
            if ($tel != $userdefine_row['tel']) {
                return json_error('手机号不匹配');
            }
            $load_arr = ['userid' => $row['userid'], 'token' => md5($row['userid'] . time() . uniqid()), 'is_state' => '1', 'addtime' => date('Y-m-d H:i:s'),];
            Db::startTrans();
            $retpwdid = db('retpwd')->insertGetId($load_arr);
            if (!$retpwdid) {
                Db::rollback();
                return json_success("创建邮件发送失败。");
            }
            if (!empty($email)) {
                $site_arr = cache('guestconfig');
                $url = '<a href="http://' . $site_arr['siteurl'] . '/retpwd/ret?id=' . $retpwdid . '&token=' . $load_arr['token'] . '">http://' . $site_arr['siteurl'] . '/retpwd/ret?id=' . $retpwdid . '&token=' . $load_arr['token'] . '</a>';
                $message = "恭喜您！您的找回密码申请通过，请您点击下方链接重置密码。<br />";
                $message .= "重置密码链接：{$url}<br />";
                $message .= "温馨提示：该连接10分钟内有效，重置一次密码后失效。<br />";
                $message .= "(点击打开以上链接可重置密码)";
                $email_data = ['email' => $email, 'title' => $site_arr['sitename'] . ' 用户名：' . $username . '，找回密码邮件！', 'msg' => $message,];
                \think\Queue::push('app\common\queue\QueueClient@sendMAIL', $email_data, $queue = null);
            }
            Db::commit();
            return json_success('邮件已发送至您的邮箱：' . $email . '，请您登陆邮箱重置密码');
        }
    }

    public function ret()
    {
        if (request()->isGet()) {
            $get = input('get.');
            $retpwdid = $get['id'];
            $token = $get['token'];
            if (empty($retpwdid)) {
                return json_error('密钥不正确');
            }
            if (empty($token)) {
                return json_error('密钥不正确');
            }
            $retpwd_row = db('retpwd')->where('retpwdid', $retpwdid)->field(true)->find();
            if (empty($retpwd_row)) {
                return json_error('密钥不正确');
            }
            if ($token != $retpwd_row['token']) {
                return json_error('密钥不正确');
            }
            $assign_arr = array('title' => '重置密码', 'id' => $retpwdid, 'token' => $token,);
            $this->assign($assign_arr);
            return $this->fetch();
        }
    }

    public function retsave()
    {
        if (request()->isPost()) {
            $get = input('post.');
            $retpwdid = $get['id'];
            $token = $get['token'];
            $password = $get['password'];
            $confirm_password = $get['confirm_password'];
            if (empty($retpwdid)) {
                return json_error('密钥不正确');
            }
            if (empty($token)) {
                return json_error('密钥不正确');
            }
            if (empty($password)) {
                return json_error('请填写新密码');
            }
            if (empty($confirm_password)) {
                return json_error('请填写确认新密码');
            }
            if (strlen($password) < 6) {
                return json_error('请输入6位以上新密码');
            }
            if ($password != $confirm_password) {
                return json_error('两次填写密码不相同');
            }
            $retpwd_row = db('retpwd')->where('retpwdid', $retpwdid)->field(true)->find();
            if (empty($retpwd_row)) {
                return json_error('密钥不正确');
            }
            if ($token != $retpwd_row['token']) {
                return json_error('密钥不正确');
            }
            if ((time() - strtotime($retpwd_row['addtime'])) > 10 * 60) {
                return json_error('该链接超过10分钟已经自动失效，请重试。');
            }
            if ($retpwd_row['is_state'] == '0') {
                return json_error('该链接已经使用，请重新申请找回密码。');
            }
            Db::startTrans();
            $load_arr = ['password' => encrypt_userpass($password),];
            $condition = ['userid' => $retpwd_row['userid'],];
            $res1 = db('user')->where($condition)->update($load_arr);
            if (!$res1) {
                Db::rollback();
                return json_error("重置密码失败。");
            }
            $load_arr = ['is_state' => '0', 'updatetime' => date('Y-m-d H:i:s'),];
            $condition = ['retpwdid' => $retpwdid,];
            $res2 = db('retpwd')->where($condition)->update($load_arr);
            if (!$res2) {
                Db::rollback();
                return json_error("重置密码失败。");
            }
            Db::commit();
            return json_success('重置密码成功');
        }
    }
}