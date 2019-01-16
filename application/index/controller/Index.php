<?php

namespace app\index\controller;
class Index extends IndexBase
{
    function _initialize()
    {
        parent::_initialize();
        $gusetconfig_arr = cache('guestconfig');
        $assign_arr = array('qtarr' => $gusetconfig_arr,);
        $this->assign($assign_arr);
    }

    public function index()
    {
        $payment_all = db('adminarticlelist')->where(['articlecateid' => 2, 'is_trush' => 0])->limit(4)->order('articlelistid desc')->select();
        $assign_arr = array('list' => $payment_all, 'title' => '网站首页',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function help()
    {
        $assign_arr = array('title' => '使用帮助',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function baike()
    {
        $assign_arr = array('title' => '商家百科',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function notice()
    {
        $assign_arr = array('title' => '最新公告',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function about()
    {
        $assign_arr = array('title' => '关于',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function app()
    {
        $assign_arr = array('title' => 'APP',);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function contact()
    {
        $assign_arr = array('title' => '联系',);
        $this->assign($assign_arr);
        return $this->fetch();
    }
}