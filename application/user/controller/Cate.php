<?php

namespace app\user\controller;

use app\admin\model\Adminconfig;
use app\user\model\Userdefine;
use app\user\model\Usergoodcate;
use think\Db;

class Cate extends UserBase
{
    function _initialize()
    {
        parent::_initialize();
        $assign_arr = array();
        $this->assign($assign_arr);
    }

    public function index()
    {
        $goodcate_model = new Usergoodcate();
        $condition = ['is_trush' => '0',];
        $order = 'sortid,goodcateid desc';
        $goodcate_arr = $goodcate_model->get_list_by_session_userid_on_user($condition, $order);
        $list = $goodcate_arr[0];
        $page = $goodcate_arr[1];
        $assign_arr = array('title' => "商品分类", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function add()
    {
        if (request()->isPost()) {
            $post = input('post.');
            if (!empty($post)) {
                $catename_arr = $post['catename'];
                $sortid_arr = $post['sortid'];
                if (empty($catename_arr) || empty($sortid_arr) || count($catename_arr) != count($sortid_arr)) {
                    return json_error();
                }
                $adminconfig_model = new Adminconfig();
                $condition = ['id' => '1',];
                $is_filterkey = $adminconfig_model->get_value_by_condition_on_admin('is_filterkey', $condition);
                if ($is_filterkey == '1') {
                    $fileterkey_row = $adminconfig_model->get_value_by_condition_on_admin('filterkey', $condition);
                    if (!empty($fileterkey_row)) {
                        $fileterkey_arr = explode('|', $fileterkey_row);
                        foreach ($fileterkey_arr as $k => $v) {
                            foreach ($catename_arr as $kc => $vc) {
                                if (stripos($vc, $v) !== false) {
                                    $catename_arr[$kc] = str_replace($v, '', $vc);
                                }
                                if ($catename_arr[$kc] == '' || !$catename_arr[$kc] || empty($catename_arr[$kc])) {
                                    return json_error('分类名称检测是被屏蔽的关键字，已拒绝操作。');
                                }
                            }
                        }
                    }
                }
                $userdefine_model = new Userdefine();
                $paytheme = $userdefine_model->get_value_by_session_userid_on_user('paytheme', ['userid' => session('user.userid')]);
                for ($i = 0; $i < count($catename_arr); $i++) {
                    $goodcate_model = new Usergoodcate();
                    $catename = $catename_arr[$i];
                    $sortid = $sortid_arr[$i];
                    if ($catename != '' && $sortid != '' && strlen($catename) <= 100) {
                        $load_arr_goodcate = array('userid' => session('user.userid'), 'catename' => $catename, 'sortid' => $sortid, 'paytheme' => $paytheme, 'linkid' => getRandomString(16),);
                        Db::startTrans();
                        $res1 = $goodcate_model->insert_one_by_arr_on_user($load_arr_goodcate);
                        if (!$res1) {
                            Db::rollback();
                            return json_error('保存失败');
                        }
                        Db::commit();
                    }
                    if ($catename == '' || $sortid == '' || strlen($catename) >= 100) {
                        return json_error('分类名称、排序为空或包含不合法的关键字');
                    }
                }
                return json_success();
            }
        }
    }

    public function edit()
    {
        $route = request()->route();
        $goodcateid = input('param.goodcateid');
        $goodcate_model = new Usergoodcate();
        $condition = array('goodcateid' => $goodcateid,);
        $goodcate_row = $goodcate_model->get_one_by_session_userid_on_user($condition);
        $assign_arr = array('title' => "修改商品分类", 'list' => $goodcate_row,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $catename = $post['catename'];
            $adminconfig_model = new Adminconfig();
            $condition = ['id' => '1',];
            $is_filterkey = $adminconfig_model->get_value_by_condition_on_admin('is_filterkey', $condition);
            if ($is_filterkey == '1') {
                $fileterkey_row = $adminconfig_model->get_value_by_condition_on_admin('filterkey', $condition);
                if (!empty($fileterkey_row)) {
                    $fileterkey_arr = explode('|', $fileterkey_row);
                    foreach ($fileterkey_arr as $k => $v) {
                        if (stripos($catename, $v) !== false) {
                            $catename = str_replace($v, '', $catename);
                        }
                    }
                    if ($catename == '' || !$catename || empty($catename)) {
                        return json_error('分类名称检测是被屏蔽的关键字，已拒绝操作。');
                    }
                    $post['catename'] = $catename;
                }
            }
            $userdefine_model = new Userdefine();
            $paytheme = $userdefine_model->get_value_by_session_userid_on_user('paytheme', ['userid' => session('user.userid')]);
            $post['paytheme'] = $paytheme;
            Db::startTrans();
            $goodcate_model = new Usergoodcate();
            $condition = array('goodcateid' => $post['goodcateid'],);
            $res1 = $goodcate_model->update_one_by_session_userid_on_user($post, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function link()
    {
        $route = request()->route();
        $goodcateid = input('param.goodcateid');
        $goodcate_model = new Usergoodcate();
        $condition = array('goodcateid' => $goodcateid,);
        $goodcate_row = $goodcate_model->get_one_by_session_userid_on_user($condition);
        if (empty($goodcate_row['linkid'])) {
            $load_arr['linkid'] = getRandomString(16);
            $goodcate_model = new Usergoodcate();
            $res = $goodcate_model->update_one_by_session_userid_on_user($load_arr, ['goodcateid' => $goodcateid]);
            $goodcate_row['linkid'] = $load_arr['linkid'];
        }
        $base_url = cache('guestconfig')['listurl'];
        $long_url = "http://" . $base_url . "/lists/" . $goodcate_row['linkid'];
        $short_url = dwz($long_url);
        $goodcate_row['long_url'] = $long_url;
        $goodcate_row['short_url'] = $short_url;
        $assign_arr = array('title' => "分类链接", 'list' => $goodcate_row,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch('cate/link');
    }

    public function resetlink()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $goodcate_model = new Usergoodcate();
            $condition = array('goodcateid' => $post['goodcateid']);
            $load_arr = array('linkid' => getRandomString(16),);
            $res1 = $goodcate_model->update_one_by_session_userid_on_user($load_arr, $condition);
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
            $goodcate_model = new Usergoodcate();
            $condition = array('goodcateid' => $post['goodcateid']);
            $load_arr = array('is_trush' => '1',);
            $res1 = $goodcate_model->update_one_by_session_userid_on_user($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 