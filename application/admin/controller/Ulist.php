<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\Admincheck;
use app\admin\model\Adminpaychannel;
use app\admin\model\User;
use app\admin\model\Userpaychannel;
use think\Db;

class Ulist extends AdminBase
{
    public function index()
    {
        $viewlimit_json = unserialize(viewlimit_json);
        $admin_model = new Admin();
        $condition = ['is_trush' => '0',];
        $admin_arr = $admin_model->get_list_by_condition_on_admin($condition);
        $list = $admin_arr[0];
        $page = $admin_arr[1];
        $assign_arr = array('title' => "管理员列表", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        $assign_arr = array('title' => "新建管理员", 'viewlimit_json' => unserialize(viewlimit_json),);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $admin_model = new Admin();
            $load_arr = ['utype' => $post['utype'], 'adminname' => $post['adminname'], 'realname' => $post['realname'], 'is_safe' => $post['is_safe'], 'safekey' => $post['safekey'], 'is_whiteip' => $post['is_whiteip'], 'whiteip' => $post['whiteip'], 'is_state' => $post['is_state'], 'addtime' => date('Y-m-d H:i:s'),];
            if (empty($post['adminname'])) {
                return json_error("请您填写用户名");
            }
            if (!empty($post['is_safe']) && empty($post['safekey'])) {
                return json_error("请您填写安全码");
            }
            if (!empty($post['adminpass'])) {
                $load_arr['adminpass'] = encrypt_adminrpass($post['adminpass']);
            } else {
                return json_error("请您填写密码");
            }
            if (!empty($post['viewlimit'])) {
                $string = '';
                foreach ($post['viewlimit'] as $k => $v) {
                    $string .= $v . "|";
                }
                $string = trim($string, '|');
                $load_arr['viewlimit'] = $string;
            }
            $res1 = $admin_model->insert_one_by_arr_on_admin($load_arr);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            $admincheck_model = new Admincheck();
            $load_arr = ['adminid' => $res1, 'adminname' => $post['adminname'],];
            $res2 = $admincheck_model->insert_one_by_arr_on_admin($load_arr);
            if (!$res2) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function edit()
    {
        $adminid = input('param.adminid');
        $admin_model = new Admin();
        $condition = ['is_trush' => '0', 'adminid' => $adminid,];
        $admin_row = $admin_model->get_one_by_condition_on_login($condition);
        $admin_row['viewlimit'] = explode('|', $admin_row['viewlimit']);
        $assign_arr = array('title' => "编辑管理员", 'list' => $admin_row, 'viewlimit_json' => unserialize(viewlimit_json),);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            if ($post['adminid'] == '1' && session('admin.adminid') != '1') {
                return json_error("该管理员为系统超级管理员，仅有自己可以操作");
            }
            Db::startTrans();
            $admin_model = new Admin();
            $load_arr = ['utype' => $post['utype'], 'adminname' => $post['adminname'], 'realname' => $post['realname'], 'is_safe' => $post['is_safe'], 'safekey' => $post['safekey'], 'is_whiteip' => $post['is_whiteip'], 'whiteip' => $post['whiteip'], 'is_state' => $post['is_state'],];
            if (!empty($post['is_safe']) && empty($post['safekey'])) {
                return json_error("请您填写安全码");
            }
            if (!empty($post['adminpass'])) {
                $load_arr['adminpass'] = encrypt_adminrpass($post['adminpass']);
            }
            if (!empty($post['viewlimit'])) {
                $string = '';
                foreach ($post['viewlimit'] as $k => $v) {
                    $string .= $v . "|";
                }
                $string = trim($string, '|');
                $load_arr['viewlimit'] = $string;
            }
            $condition = ['adminid' => $post['adminid'],];
            $res1 = $admin_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function del()
    {
        if (request()->isPost()) {
            $post = input('post.');
            if ($post['adminid'] == '1') {
                return json_error("该管理员为唯一不能删除的管理员");
            }
            Db::startTrans();
            $admin_model = new Admin();
            $load_arr = array('is_trush' => '1',);
            $condition = array('adminid' => $post['adminid']);
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