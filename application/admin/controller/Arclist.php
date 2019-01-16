<?php

namespace app\admin\controller;

use app\admin\model\Adminarticlecate;
use app\admin\model\Adminarticlelist;
use app\admin\model\Admincheck;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpaychannel;
use app\wx\controller\Respone;
use think\Db;

class Arclist extends AdminBase
{
    public function index()
    {
        $articlelist_model = new Adminarticlelist();
        $condition = ['is_trush' => '0',];
        $articlecate_model = new Adminarticlecate();
        $articlecate_arr = $articlecate_model->get_column_by_condition_on_admin('articlecateid,catename', $condition);
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (isset($get['articlecateid'])) {
                    $condition['articlecateid'] = $get['articlecateid'];
                }
                $articlelist_arr = $articlelist_model->get_list_by_condition_on_admin($condition);
                $list = $articlelist_arr[0];
                $page = $articlelist_arr[1];
            } else {
                $articlelist_arr = $articlelist_model->get_list_by_condition_on_admin($condition);
                $list = $articlelist_arr[0];
                $page = $articlelist_arr[1];
            }
        }
        $assign_arr = array('title' => "文章列表", 'list' => $list, 'page' => $page, 'arr' => $articlecate_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function edit()
    {
        $articlecate_model = new Adminarticlecate();
        $condition = ['is_trush' => '0',];
        $articlecate_arr = $articlecate_model->get_column_by_condition_on_admin('articlecateid,catename', $condition);
        $articlelist_model = new Adminarticlelist();
        $condition = ['articlelistid' => input('param.articlelistid'),];
        $articlelist_row = $articlelist_model->get_one_by_condition_on_admin($condition);
        $assign_arr = array('title' => "编辑文章", 'list' => $articlelist_row, 'arr' => $articlecate_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $articlecate_model = new Adminarticlecate();
            $condition = ['articlecateid' => $post['articlecateid'],];
            $catename = $articlecate_model->get_value_by_condition_on_admin('catename', $condition);
            Db::startTrans();
            $articlelist_model = new Adminarticlelist();
            $load_arr = ['title' => $post['title'], 'catename' => $catename, 'content' => $post['content'], 'is_noticed' => (isset($post['is_noticed']) && $post['is_noticed'] == 'on') ? '1' : '0', 'addtime' => date('Y-m-d H:i:s'),];
            foreach ($load_arr as $k => $v) {
                if ($k == 'is_noticed') {
                    continue;
                }
                if (empty($v)) {
                    return json_error();
                }
            }
            $condition = ['articlelistid' => $post['articlelistid'],];
            $res1 = $articlelist_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function add()
    {
        $articlecate_model = new Adminarticlecate();
        $condition = ['is_trush' => '0',];
        $articlecate_arr = $articlecate_model->get_column_by_condition_on_admin('articlecateid,catename', $condition);
        $assign_arr = array('title' => "新建文章", 'arr' => $articlecate_arr,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $articlecate_model = new Adminarticlecate();
            $condition = ['articlecateid' => $post['articlecateid'],];
            $catename = $articlecate_model->get_value_by_condition_on_admin('catename', $condition);
            Db::startTrans();
            $articlelist_model = new Adminarticlelist();
            $load_arr = ['articlecateid' => $post['articlecateid'], 'title' => $post['title'], 'catename' => $catename, 'content' => $post['content'], 'is_noticed' => (isset($post['is_noticed']) && $post['is_noticed'] == 'on') ? '1' : '0', 'addtime' => date('Y-m-d H:i:s'),];
            foreach ($load_arr as $k => $v) {
                if ($k == 'is_noticed') {
                    continue;
                }
                if (empty($v)) {
                    return json_error();
                }
            }
            $res1 = $articlelist_model->insert_one_by_arr_on_admin($load_arr);
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
            $articlelist_model = new Adminarticlelist();
            $load_arr = array('is_trush' => '1',);
            $condition = array('articlelistid' => $post['articlelistid']);
            $res1 = $articlelist_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
}