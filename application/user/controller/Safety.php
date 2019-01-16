<?php

namespace app\user\controller;

use app\user\model\User;
use think\Db;

class Safety extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $assign_arr = array('title' => "修改密码",);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function retsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $old_password = $post['old_password'];
            $password = $post['password'];
            $confirm_password = $post['confirm_password'];
            if (empty($old_password)) {
                return json_error('请输入原密码');
            }
            if (empty($password)) {
                return json_error('请输入新密码');
            }
            if (empty($confirm_password)) {
                return json_error('请输入确认新密码');
            }
            if (strlen($password) < 6) {
                return json_error('新密码至少6位');
            }
            if ($password != $confirm_password) {
                return json_error('新密码输入和确认新密码不一致');
            }
            $user_model = new User();
            $row_password = $user_model->get_value_by_session_userid_on_user('password');
            if (!$res = decrypt_userpass($old_password, $row_password)) {
                return json_error('原密码错误');
            }
            Db::startTrans();
            $res1 = $user_model->update_one_by_session_userid_on_user(['password' => encrypt_userpass($password)]);
            if (!$res1) {
                Db::rollback();
                return json_error("重置密码失败。");
            }
            Db::commit();
            return json_success('重置密码成功');
        }
    }
} 