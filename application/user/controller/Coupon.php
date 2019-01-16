<?php

namespace app\user\controller;

use app\user\model\Usercoupon;
use app\user\model\Usergoodlist;
use think\Db;

class Coupon extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                $condition = [];
                if (!empty($get['goodlistid'])) {
                    $condition = ['goodlistid' => $get['goodlistid'],];
                }
                if (isset($get['is_state']) && $get['is_state'] != '') {
                    $condition = ['is_state' => $get['is_state'],];
                }
                if (!empty($get['keyword'])) {
                    $condition['couponcode'] = $get['keyword'];
                }
                $usercoupon_model = new Usercoupon();
                $usercoupon_arr = $usercoupon_model->get_list_by_condition_on_user($condition);
                $list = $usercoupon_arr[0];
                $page = $usercoupon_arr[1];
            } else {
                $usercoupon_model = new Usercoupon();
                $condition = [];
                $usercoupon_arr = $usercoupon_model->get_list_by_condition_on_user($condition);
                $list = $usercoupon_arr[0];
                $page = $usercoupon_arr[1];
            }
        }
        $goodlist_model = new Usergoodlist();
        $condition = ['is_trush' => '0',];
        $goodlist_arr = $goodlist_model->get_column_by_session_userid_on_user('goodlistid,goodname', $condition);
        $assign_arr = array('title' => "查优惠券", 'goodlist_arr' => $goodlist_arr, 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function delall()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $listid_arr = $post['listid'];
            Db::startTrans();
            $arr_in = [];
            foreach ($listid_arr as $k => $v) {
                $arr_in[] = $v;
            }
            $condition = ['userid' => session('user.userid'), 'couponid' => ['in', $arr_in],];
            $usercoupon_model = new Usercoupon();
            $res1 = $usercoupon_model->del_list_by_condition_on_user($condition);
            if (!$res1) {
                Db::rollback();
                return json_error($res1);
            }
            Db::commit();
            return json_success();
        }
    }

    public function del()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $couponid = $post['couponid'];
            Db::startTrans();
            $usercoupon_model = new Usercoupon();
            $condition = array('userid' => session('user.userid'), 'couponid' => $couponid,);
            $res1 = $usercoupon_model->del_list_by_condition_on_user($condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 