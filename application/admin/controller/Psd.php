<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\Adminpaychannel;
use app\admin\model\User;
use app\admin\model\Userpaychannel;
use think\Db;

class Psd extends AdminBase
{
    public function index()
    {
        $admin_model = new Admin();
        $condition = ['is_trush' => '0',];
        $admin_arr = $admin_model->get_one_by_session_on_admin($condition);
        $assign_arr = array('title' => "通道列表", 'list' => $admin_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $admin_model = new Admin();
            $admin_arr = $admin_model->get_one_by_session_on_admin();
            $adminpass_old_mysql = $admin_arr['adminpass'];
            if (!decrypt_adminpass($post['adminpass_old'], $adminpass_old_mysql)) {
                return json_error('密码错误');
            }
            if ($post['adminpass'] != $post['adminpass_verify']) {
                return json_error('两次密码不一致');
            }
            if ($post['adminpass'] == '' || $post['adminpass_verify'] == '') {
                return json_error('密码为空');
            }
            $load_arr = ['adminpass' => encrypt_adminrpass($post['adminpass']),];
            $condition = ['adminid' => session('admin.adminid'),];
            $res1 = $admin_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 