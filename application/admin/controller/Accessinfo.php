<?php

namespace app\admin\controller;

use app\admin\model\Admincheck;
use app\admin\model\Adminpaychannel;
use app\admin\model\Adminpayprovider;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpay;
use app\admin\model\Userpaychannel;
use app\admin\model\Userpayment;
use app\admin\model\Usersettlement;
use think\Db;

class Accessinfo extends AdminBase
{
    public function index()
    {
        $adminpayprovider_model = new Adminpayprovider();
        $condition = ['is_trush' => '0',];
        $adminpayprovider_arr = $adminpayprovider_model->get_list_by_condition_on_admin($condition);
        $list = $adminpayprovider_arr[0];
        $page = $adminpayprovider_arr[1];
        $assign_arr = array('title' => "接入商信息", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        $adminpaychannel_model = new Adminpaychannel();
        $adminpaychannel_arr = $adminpaychannel_model->get_list_by_condition_on_admin([], [], [], '');
        $assign_arr = array('title' => "添加新的接入商", 'arr' => $adminpaychannel_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $adminpayprovider_model = new Adminpayprovider();
            $load_arr = ['providername' => $post['providername'], 'directory' => $post['directory'], 'payway' => $post['payway'], 'apiurl' => $post['apiurl'], 'accountid' => $post['accountid'], 'password' => $post['password'], 'is_state' => '1',];
            $res1 = $adminpayprovider_model->insert_one_by_arr_on_admin($load_arr);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $adminpayprovider_model = new Adminpayprovider();
            $load_arr = ['apiurl' => $post['apiurl'], 'accountid' => $post['accountid'], 'password' => $post['password'],];
            if (empty($post['providerid'])) {
                return json_error();
            }
            $condition = ['providerid' => $post['providerid'],];
            $res1 = $adminpayprovider_model->update_one_by_condition_on_admin($load_arr, $condition);
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
            Db::startTrans();
            $adminpayprovider_model = new Adminpayprovider();
            $load_arr = array('is_trush' => '1',);
            $condition = ['providerid' => $post['providerid'],];
            $res1 = $adminpayprovider_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function toggleState()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $adminpayprovider_model = new Adminpayprovider();
            $load_arr = array('is_state' => $post['is_state'],);
            $condition = ['providerid' => $post['providerid'],];
            $res1 = $adminpayprovider_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 