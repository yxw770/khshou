<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Userpay;
use think\Db;

class Userpaylist extends AdminBase
{
    public function index()
    {
        $userpaylist_model = new Userpay();
        $condition = [];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (isset($get['is_state'])) {
                    $condition['is_state'] = $get['is_state'];
                }
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'username') {
                        $condition['username'] = $get['keyword'];
                    }
                }
                $userpaylist_arr = $userpaylist_model->get_userpaylist_by_condition_on_admin($condition);
                $list = $userpaylist_arr[0];
                $page = $userpaylist_arr[1];
            } else {
                $userpaylist_arr = $userpaylist_model->get_userpaylist_by_condition_on_admin($condition);
                $list = $userpaylist_arr[0];
                $page = $userpaylist_arr[1];
            }
        }
        $assign_arr = array('title' => "用户列表", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function edit()
    {
        $userid = input('param.userid');
        $user_model = new User();
        $condition = ['userid' => $userid,];
        $username = $user_model->get_value_by_condition_on_admin('username', $condition);
        $userpaylist_model = new Userpay();
        $condition = ['userid' => $userid,];
        $userpaylist_arr = $userpaylist_model->get_one_by_condition_on_admin($condition);
        $userpaylist_arr = array_merge($userpaylist_arr, ['username' => $username]);
        $assign_arr = array('title' => "编辑用户", 'list' => $userpaylist_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $userpaylist_model = new Userpay();
            $condition = ['userid' => $post['userid'],];
            $res1 = $userpaylist_model->update_one_by_condition_on_admin($post, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
}