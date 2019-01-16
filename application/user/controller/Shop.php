<?php

namespace app\user\controller;

use app\user\model\Userdefine;
use think\Db;

class Shop extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $userdefine_model = new Userdefine();
        $linkid = $userdefine_model->get_value_by_session_userid_on_user('linkid');
        $link_state = $userdefine_model->get_value_by_session_userid_on_user('link_state');
        if (empty($linkid)) {
            $linkid = getRandomString(16);
            $res1 = $userdefine_model->update_one_by_session_userid_on_user(['linkid' => $linkid]);
            if (!empty($res1)) {
                json_error('系统生成店铺链接出现错误，请联系客服');
            }
        }
        $base_url = cache('guestconfig')['linkurl'];
        $long_url = "http://" . $base_url . "/links/" . $linkid;
        $short_url = dwz($long_url);
        $assign_arr = array('title' => "店铺链接", 'long_url' => $long_url, 'short_url' => $short_url, 'link_state' => $link_state,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function togglestate()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $userdefine_model = new Userdefine();
            $load_arr = array('link_state' => $post['to_state'],);
            $res1 = $userdefine_model->update_one_by_session_userid_on_user($load_arr);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function resetlink()
    {
        if (request()->isPost()) {
            Db::startTrans();
            $userdefine_model = new Userdefine();
            $load_arr = array('linkid' => getRandomString(16),);
            $res1 = $userdefine_model->update_one_by_session_userid_on_user($load_arr);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 