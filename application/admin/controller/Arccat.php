<?php

namespace app\admin\controller;

use app\admin\model\Adminarticlecate;
use app\admin\model\Admincheck;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpaychannel;
use think\Db;

class Arccat extends AdminBase
{
    public function index()
    {
        $articlecate_model = new Adminarticlecate();
        $condition = ['is_trush' => '0',];
        $articlecate_arr = $articlecate_model->get_list_by_condition_on_admin($condition);
        $list = $articlecate_arr[0];
        $page = $articlecate_arr[1];
        $assign_arr = array('title' => "文章分类", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function edit()
    {
        $articlecate_model = new Adminarticlecate();
        $condition = ['articlecateid' => input('param.articlecateid'),];
        $articlecate_row = $articlecate_model->get_one_by_condition_on_admin($condition);
        $assign_arr = array('title' => "编辑分类", 'list' => $articlecate_row,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $articlecate_model = new Adminarticlecate();
            $load_arr = ['catename' => $post['catename'],];
            if (empty(trim($post['catename']))) {
                return json_error();
            }
            $condition = ['articlecateid' => $post['articlecateid'],];
            $res1 = $articlecate_model->update_one_by_condition_on_admin($load_arr, $condition);
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
        $assign_arr = array('title' => "新建分类",);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function addsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $articlecate_model = new Adminarticlecate();
            $load_arr = ['catename' => $post['catename'], 'addtime' => date('Y-m-d H:i:s'),];
            $res1 = $articlecate_model->insert_one_by_arr_on_admin($load_arr);
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
            if ($post['articlecateid'] == "1") {
                return json_error("网站公告分类不能删除");
            }
            if ($post['articlecateid'] == "2") {
                return json_error("首页公告分类不能删除");
            }
            Db::startTrans();
            $articlecate_model = new Adminarticlecate();
            $load_arr = array('is_trush' => '1',);
            $condition = array('articlecateid' => $post['articlecateid']);
            $res1 = $articlecate_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 