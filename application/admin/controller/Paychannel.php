<?php

namespace app\admin\controller;

use app\admin\model\Adminpaychannel;
use app\admin\model\User;
use app\admin\model\Userpaychannel;
use think\Controller;
use think\Db;

class Paychannel extends Controller
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function updateone()
    {
        $user_model = new User();
        $user_arr = $user_model->get_column_by_condition_on_admin('userid');
        $adminpaychennel_model = new Adminpaychannel();
        $condition = ['channelid' => '9',];
        $adminpaychennel_arr = $adminpaychennel_model->get_one_by_session_adminid_on_admin($condition);
        if (!empty($user_arr)) {
            $userpaychennel_model = new Userpaychannel();
            Db::startTrans();
            foreach ($user_arr as $k => $v) {
                $condition['userid'] = $v['userid'];
                $userpaychennel_model->del_list_by_condition_on_admin($condition);
                $load_arr = [];
                $load_arr['userid'] = $v['userid'];
                $load_arr['channelid'] = $adminpaychennel_arr['channelid'];
                $load_arr['channelname'] = $adminpaychennel_arr['channelname'];
                $load_arr['userrate'] = $adminpaychennel_arr['userproportion'];
                $load_arr['is_state'] = $adminpaychennel_arr['is_state'];
                $load_arr['payway'] = $adminpaychennel_arr['payway'];
                $res1 = $userpaychennel_model->insert_one_by_arr_on_admin($load_arr);
                if (!$res1) {
                    Db::rollback();
                    return json_error('保存失败');
                }
                unset($load_arr);
            }
            Db::commit();
            return json_success();
        }
    }

    public function updateAll()
    {
        $user_model = new User();
        $user_arr = $user_model->get_column_by_condition_on_admin('userid');
        $adminpaychennel_model = new Adminpaychannel();
        $condition = [];
        $adminpaychennel_arr = $adminpaychennel_model->get_list_by_condition_on_admin($condition);
        Db::startTrans();
        $adminpaychennel_arr = $adminpaychennel_arr[0];
        foreach ($adminpaychennel_arr as $k_chennel => $v_chennel) {
            if (!empty($user_arr)) {
                $userpaychennel_model = new Userpaychannel();
                foreach ($user_arr as $k => $v) {
                    $condition['userid'] = $v['userid'];
                    $condition['channelid'] = $v_chennel['channelid'];
                    $userpaychennel_model->del_list_by_condition_on_admin($condition);
                    $load_arr = [];
                    $load_arr['userid'] = $v['userid'];
                    $load_arr['channelid'] = $v_chennel['channelid'];
                    $load_arr['channelname'] = $v_chennel['channelname'];
                    $load_arr['userrate'] = $v_chennel['userproportion'];
                    $load_arr['is_state'] = $v_chennel['is_state'];
                    $load_arr['payway'] = $v_chennel['payway'];
                    $res1 = $userpaychennel_model->insert_one_by_arr_on_admin($load_arr);
                    if (!$res1) {
                        Db::rollback();
                        return json_error('保存失败');
                    }
                    unset($load_arr);
                }
            }
        }
        Db::commit();
        return json_success();
    }
} 