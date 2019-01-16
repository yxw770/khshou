<?php

namespace app\admin\controller;

use app\admin\model\Usergoodlist;
use think\Db;

class Goodslist extends AdminBase
{
    public function index()
    {
        $usergoodlist_model = new Usergoodlist();
        $condition = [];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (!empty($get['to_time']) && !empty($get['from_time'])) {
                    $from_time = date('Y-m-d', strtotime($get['from_time']));
                    $to_time = date('Y-m-d H:i:s', strtotime($get['to_time']) + 60 * 60 * 24 - 1);
                    $condition = ['addtime' => ['between', [$from_time, $to_time]],];
                }
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'goodname') {
                        $condition['goodname'] = ['like', '%' . $get['keyword'] . '%'];
                    } elseif ($get['ktype'] == 'catename') {
                        $condition['catename'] = ['like', '%' . $get['keyword'] . '%'];
                    }
                }
                $usergoodlist_arr = $usergoodlist_model->get_list_by_condition_on_admin($condition);
                $list = $usergoodlist_arr[0];
                $page = $usergoodlist_arr[1];
            } else {
                $usergoodlist_arr = $usergoodlist_model->get_list_by_condition_on_admin($condition);
                $list = $usergoodlist_arr[0];
                $page = $usergoodlist_arr[1];
            }
        }
        $assign_arr = array('title' => "商品管理", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function togglestate()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $goodlist_model = new Usergoodlist();
            $condition = array('goodlistid' => $post['goodlistid']);
            $load_arr = array('is_state' => $post['is_state'],);
            $res1 = $goodlist_model->update_one_by_condition_on_admin($load_arr, $condition);
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
            $goodlist_model = new Usergoodlist();
            $condition = array('goodlistid' => $post['goodlistid']);
            $load_arr = array('is_trush' => $post['is_trush'],);
            $res1 = $goodlist_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 