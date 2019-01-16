<?php

namespace app\admin\controller;

use app\admin\model\Adminpaychannel;
use app\admin\model\User;
use app\admin\model\Userpaychannel;
use think\Db;

class Channel extends AdminBase
{
    public function index()
    {
        $adminpaychennel_model = new Adminpaychannel();
        $condition = ['is_trush' => '0',];
        $adminpaychennel_arr = $adminpaychennel_model->get_list_by_condition_on_admin($condition, ['channelid' => 'asc']);
        $list = $adminpaychennel_arr[0];
        $page = $adminpaychennel_arr[1];
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['providername'] = \db('adminpayprovider')->where('providerid', $v['providerid'])->value('providername');
            }
        }
        $assign_arr = array('title' => "通道列表", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        $adminpayprovider_arr = cache('adminpayprovider');
        $assign_arr = array('title' => "新建通道", 'arr' => $adminpayprovider_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $adminpaychennel_model = new Adminpaychannel();
            $load_arr = ['providerid' => $post['providerid'], 'channelname' => $post['channelname'], 'payway' => $post['payway'], 'userproportion' => $post['userproportion'], 'is_state' => $post['is_state'], 'ptproportion' => $post['ptproportion'],];
            $res1 = $adminpaychennel_model->insert_one_by_arr_on_admin($load_arr);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            if (isset($post['all']) && $post['all'] == 'on') {
                $user_model = new User();
                $user_arr = $user_model->get_column_by_condition_on_admin('userid');
                $userpaychennel_model = new Userpaychannel();
                foreach ($user_arr as $k => $v) {
                    $load_arr = ['userid' => $v['userid'], 'channelid' => $res1, 'channelname' => $post['channelname'], 'payway' => $post['payway'], 'userrate' => $post['userproportion'], 'is_state' => $post['is_state'],];
                    $res2 = $userpaychennel_model->insert_one_by_arr_on_admin($load_arr);
                    if (!$res2) {
                        Db::rollback();
                        return json_error('保存失败');
                    }
                    unset($load_arr);
                }
            }
            Db::commit();
            return json_success();
        }
    }

    public function edit()
    {
        $channelid = input('param.channelid');
        $adminpaychennel_model = new Adminpaychannel();
        $condition = ['channelid' => $channelid,];
        $adminpaychennel_row = $adminpaychennel_model->get_one_by_session_adminid_on_admin($condition);
        $adminpayprovider_arr = cache('adminpayprovider');
        $assign_arr = array('title' => "编辑通道", 'list' => $adminpaychennel_row, 'arr' => $adminpayprovider_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $adminpaychennel_model = new Adminpaychannel();
            $load_arr = ['providerid' => $post['providerid'], 'channelname' => $post['channelname'], 'payway' => $post['payway'], 'userproportion' => $post['userproportion'], 'is_state' => $post['is_state'], 'ptproportion' => $post['ptproportion'],];
            $condition = ['channelid' => $post['channelid'],];
            $res1 = $adminpaychennel_model->update_one_by_session_adminid_on_admin($load_arr, $condition);
            if (!isset($post['all']) && !$res1) {
                Db::rollback();
                return json_error();
            }
            if (isset($post['all']) && $post['all'] == 'on') {
                $user_model = new User();
                $user_arr = $user_model->get_column_by_condition_on_admin('userid');
                $userpaychennel_model = new Userpaychannel();
                foreach ($user_arr as $k => $v) {
                    $condition['userid'] = $v['userid'];
                    $userpaychennel_model->del_list_by_condition_on_admin($condition);
                    $load_arr = ['userid' => $v['userid'], 'channelid' => $post['channelid'], 'channelname' => $post['channelname'], 'payway' => $post['payway'], 'userrate' => $post['userproportion'], 'is_state' => $post['is_state'],];
                    $res2 = $userpaychennel_model->insert_one_by_arr_on_admin($load_arr);
                    if (!$res2) {
                        Db::rollback();
                        return json_error('保存失败');
                    }
                    unset($load_arr);
                }
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
            $adminpaychennel_model = new Adminpaychannel();
            $load_arr = array('is_trush' => '1',);
            $condition = array('channelid' => $post['channelid']);
            $res1 = $adminpaychennel_model->update_one_by_session_adminid_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            $user_model = new User();
            $user_arr = $user_model->get_column_by_condition_on_admin('userid');
            foreach ($user_arr as $k => $v) {
                $userpaychennel_model = new Userpaychannel();
                $condition['userid'] = $v['userid'];
                $load_arr = ['is_trush' => '1',];
                $res2 = $userpaychennel_model->update_one_by_condition_on_admin($load_arr, $condition);
                if (!$res2) {
                    Db::rollback();
                    return json_error('保存失败2');
                }
                unset($load_arr);
            }
            Db::commit();
            return json_success();
        }
    }
} 