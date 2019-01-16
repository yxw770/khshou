<?php

namespace app\admin\controller;

use app\admin\model\Admincheck;
use app\admin\model\Adminconfig;
use app\admin\model\Adminpaychannel;
use app\admin\model\Adminpayprovider;
use app\admin\model\Guestconfig;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpay;
use app\admin\model\Userpaychannel;
use app\admin\model\Userpayment;
use app\admin\model\Usersettlement;
use think\Db;

class Set extends AdminBase
{
    public function index()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "基本设置", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function linkurl()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "域名设置", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function contact()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "联系方式", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function theme()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "模版风格", 'list' => $adminconfig_arr, 'paytheme_arr' => unserialize(paytheme_arr), 'usertheme_arr' => unserialize(usertheme_arr), 'qttheme' => unserialize(qttheme),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function withdraw()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "提现功能", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function email()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "邮件服务", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function fliterkey()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "字词过滤", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function reg()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "注册设置", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function st()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "网站开关", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function zf()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "支付轮询开关", 'list' => $adminconfig_arr,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function dwz()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "短网址API选择", 'list' => $adminconfig_arr, 'dwz_arr' => unserialize(dwz_arr),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function wxgzh()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "微信公众号", 'list' => $adminconfig_arr, 'dwz_arr' => unserialize(dwz_arr),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function sms()
    {
        $adminconfig_model = new Adminconfig();
        $condition = [];
        $adminconfig_arr = $adminconfig_model->get_config_by_condition_on_admin($condition);
        $assign_arr = array('title' => "短信开关", 'list' => $adminconfig_arr, 'dwz_arr' => unserialize(dwz_arr),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $adminconfig_model = new Adminconfig();
            $guestconfig_model = new Guestconfig();
            $condition = ['id' => '1',];
            $res1 = $adminconfig_model->update_one_by_condition_on_admin($post, $condition);
            $res2 = $guestconfig_model->update_one_by_condition_on_admin($post, $condition);
            if (!$res1 && !$res2) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }
} 