<?php

namespace app\user\controller;

use app\user\model\User;
use app\user\model\Usercheck;
use think\Db;

class Safekey extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function checkSafeKey()
    {
        $usercheck_model = new Usercheck();
        $row = $usercheck_model->get_one_by_id_on_user(session('user')['userid']);
        $is_safekey = $row['is_safekey'];
        $usersafekey = isset($row['usersafekey']) ? $row['usersafekey'] : '';
        if ($is_safekey == '1') {
            if (!session('?user.usersafekey')) {
                $this->error('安全密码不能为空', url('safekey/enter'));
            }
            if (session('user')['usersafekey'] != $usersafekey) {
                $this->error('安全密码错误', url('safekey/enter'));
            }
        }
    }

    public function index()
    {
        $model = new Usercheck();
        $row = $model->get_one_by_id_on_user(session('user')['userid']);
        $assign_arr = array('list' => $row, 'title' => "修改安全密码",);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $password = $post['usersafekey'];
            $confirm_password = $post['confirm_usersafekey'];
            $is_safekey = $post['is_safekey'];
            if (empty($password)) {
                return json_error('请输入新安全密码');
            }
            if (empty($confirm_password)) {
                return json_error('请输入确认新安全密码');
            }
            if (strlen($password) < 6) {
                return json_error('新安全密码至少6位');
            }
            if ($password != $confirm_password) {
                return json_error('新安全密码输入和确认新安全密码不一致');
            }
            $usercheck_model = new Usercheck();
            $row = $usercheck_model->get_one_by_id_on_user(session('user')['userid']);
            $is_safekey = $row['is_safekey'];
            $usersafekey = isset($row['usersafekey']) ? $row['usersafekey'] : '';
            if ($is_safekey == '1') {
                return json_error('安全密码已经存在');
            }
            Db::startTrans();
            $res1 = $usercheck_model->update_one_by_session_userid_on_user(['is_safekey' => $post['is_safekey'], 'usersafekey' => $password]);
            if (!$res1) {
                Db::rollback();
                return json_error("保存安全密码失败。");
            }
            Db::commit();
            return json_success('保存安全密码成功');
        }
    }

    public function retsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $old_password = $post['old_usersafekey'];
            $password = $post['usersafekey'];
            $confirm_password = $post['confirm_usersafekey'];
            $is_safekey = $post['is_safekey'];
            if (empty($old_password)) {
                return json_error('请输入原安全密码');
            }
            if (empty($password)) {
                return json_error('请输入新安全密码');
            }
            if (empty($confirm_password)) {
                return json_error('请输入确认新安全密码');
            }
            if (strlen($password) < 6) {
                return json_error('新安全密码至少6位');
            }
            if ($password != $confirm_password) {
                return json_error('新安全密码输入和确认新安全密码不一致');
            }
            $usercheck_model = new Usercheck();
            $row = $usercheck_model->get_one_by_id_on_user(session('user')['userid']);
            $is_safekey = $row['is_safekey'];
            $usersafekey = isset($row['usersafekey']) ? $row['usersafekey'] : '';
            if ($usersafekey != $old_password) {
                return json_error('原安全密码错误');
            }
            Db::startTrans();
            $res1 = $usercheck_model->update_one_by_session_userid_on_user(['is_safekey' => $post['is_safekey'], 'usersafekey' => $password]);
            if (!$res1) {
                Db::rollback();
                return json_error("保存安全密码失败。");
            }
            Db::commit();
            return json_success('保存安全密码成功');
        }
    }

    public function enter()
    {
        $assign_arr = array('title' => "输入安全密码",);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function entersave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $old_password = $post['old_usersafekey'];
            if (empty($old_password)) {
                return json_error('请输入安全密码');
            }
            if (strlen($old_password) < 6) {
                return json_error('安全密码至少6位');
            }
            $usercheck_model = new Usercheck();
            $row = $usercheck_model->get_one_by_id_on_user(session('user')['userid']);
            $usersafekey = isset($row['usersafekey']) ? $row['usersafekey'] : '';
            if ($usersafekey != $old_password) {
                return json_error('原安全密码错误');
            }
            session('user.usersafekey', $old_password);
            return json_success('输入安全密码正确');
        }
    }
} 