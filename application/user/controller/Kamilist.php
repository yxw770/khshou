<?php

namespace app\user\controller;

use app\user\model\Usercheck;
use app\user\model\Usergoodlist;
use app\user\model\Userproduct;
use think\Db;

class Kamilist extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $safekey_model = new Safekey();
        $safekey_model->checkSafeKey();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $kami_model = new Userproduct();
        $goodlist_model = new Usergoodlist();
        $condition = ['is_trush' => '0', 'is_sub' => '0',];
        $goodlist_arr = $goodlist_model->get_column_by_session_userid_on_user('goodlistid,goodname', $condition);
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search' && !empty($get['goodlistid'])) {
                $condition = ['goodlistid' => $get['goodlistid'], 'is_state' => '1', 'is_trush' => '0', 'is_sub' => '0',];
                $kami_arr = $kami_model->get_kami_list_on_user($condition);
                $list = $kami_arr[0];
                $page = $kami_arr[1];
            } else {
                $condition = ['is_state' => '1', 'is_trush' => '0', 'is_sub' => '0',];
                $kami_arr = $kami_model->get_kami_list_on_user($condition);
                $list = $kami_arr[0];
                $page = $kami_arr[1];
            }
        }
        $assign_arr = array('title' => "库存卡密", 'list' => $list, 'page' => $page, 'goodlist_arr' => $goodlist_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function del()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $kami_model = new Userproduct();
            $condition = array('productid' => $post['productid']);
            $load_arr = array('is_trush' => '1',);
            $res1 = $kami_model->update_one_by_session_userid_on_user($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function exportkami()
    {
        if (request()->isGet()) {
            $goodlistid = input('goodlistid');
            if (empty($goodlistid)) {
                return json_error('请先选择商品再进行导出卡密');
            }
            $kami_model = new Userproduct();
            $condition = array('goodlistid' => $goodlistid, 'is_trush' => '0', 'is_state' => '1',);
            $kami_arr = $kami_model->get_export_kami_on_user($condition, $goodlistid);
            if (!empty($kami_arr)) {
                $txtname = date('Y') . date('m') . date('d') . " " . $kami_arr[0] . "（库存卡密）" . ".txt";
                $content = $this->get_export_kami($kami_arr[1]);
                export_txt($content, $txtname);
                exit();
            } else {
                export_txt('暂无可以导出的卡密', '库存卡密.txt');
                exit();
            }
        }
    }

    public function get_export_kami($arr)
    {
        $content = '';
        foreach ($arr as $k => $v) {
            $cardpwd = $v['cardpassword'] ? $v['cardpassword'] : '';
            $content .= "" . $v['cardnumber'] . " " . $cardpwd . "\r\n";
        }
        return $content;
    }

    public function delall()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $listid_arr = $post['listid'];
            Db::startTrans();
            $load_arr = array('is_trush' => '1',);
            foreach ($listid_arr as $k => $v) {
                $kami_model = new Userproduct();
                $condition = array('productid' => $v);
                $res1 = $kami_model->update_one_by_session_userid_on_user($load_arr, $condition);
                if (!$res1) {
                    Db::rollback();
                    return json_error($res1);
                }
            }
            Db::commit();
            return json_success();
        }
    }
}