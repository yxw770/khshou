<?php

namespace app\admin\controller;

use app\admin\model\Admincheck;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpay;
use app\admin\model\Userpaychannel;
use app\admin\model\Userpayment;
use app\admin\model\Usersettlement;
use think\Db;

class Settlementlog extends AdminBase
{
    public function index()
    {
        $userpayment_model = new Userpayment();
        $condition = [];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'username') {
                        $condition['username'] = $get['keyword'];
                    }
                }
                $usermentlist_arr = $userpayment_model->get_settlementlog_by_condition_on_admin($condition);
                $list = $usermentlist_arr[0];
                $page = $usermentlist_arr[1];
            } else {
                $usermentlist_arr = $userpayment_model->get_settlementlog_by_condition_on_admin($condition);
                $list = $usermentlist_arr[0];
                $page = $usermentlist_arr[1];
            }
        }
        $assign_arr = array('title' => "结算记录", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $userpayment_model = new Userpayment();
            $load_arr = ['is_state' => '1', 'addtime' => date('Y-m-d H:i:s'),];
            if (empty($post['id'])) {
                return json_error();
            }
            $condition = ['id' => $post['id'],];
            $res1 = $userpayment_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
}