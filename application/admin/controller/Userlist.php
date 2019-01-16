<?php

namespace app\admin\controller;

use app\admin\model\Admincheck;
use app\admin\model\User;
use app\admin\model\Userdefine;
use app\admin\model\Userpaychannel;
use think\Db;

class Userlist extends AdminBase
{
    public function index()
    {
        $userlist_model = new User();
        $condition = [];
        if (request()->isGet()) {
            $get = input('get.');
            if (isset($get['do']) && $get['do'] == 'search') {
                if (isset($get['is_state'])) {
                    $condition['is_state'] = $get['is_state'];
                }
                if (!empty($get['keyword']) && !empty($get['ktype'])) {
                    if ($get['ktype'] == 'userid') {
                        $condition['userid'] = $get['keyword'];
                    } elseif ($get['ktype'] == 'username') {
                        $condition['username'] = $get['keyword'];
                    }
                }
                if (!empty($get['keyword']) && empty($get['ktype'])) {
                    $condition['keyword'] = $get['keyword'];
                }
                $userlist_arr = $userlist_model->get_userlist_by_condition_on_admin($condition);
                $list = $userlist_arr[0];
                $page = $userlist_arr[1];
            } else {
                $userlist_arr = $userlist_model->get_userlist_by_condition_on_admin($condition);
                $list = $userlist_arr[0];
                $page = $userlist_arr[1];
            }
        }
        $assign_arr = array('title' => "用户列表", 'list' => $list, 'page' => $page,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function flag()
    {
        $userid = input('param.userid');
        $orderlist_model = new \app\admin\model\Orderlist();
        $zero_time = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $condition = ['userid' => $userid, 'is_buylist' => '1', 'is_payment' => '0', 'addtime' => ['<', $zero_time,]];
        $unpayment = $orderlist_model->sum_by_condition_on_admin($condition, 'paymoney');
        $unpayment = number_format($unpayment, '2', '.', '');
        $user_model = new User();
        $condition = ['userid' => $userid,];
        $user_row = $user_model->get_one_by_condition_on_admin($condition);
        $assign_arr = array('title' => "标记用户", 'unpayment' => $unpayment, 'list' => $user_row,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function flagsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $user_model = new User();
            $load_arr = ['flag' => $post['flag'], 'remark' => $post['remark'],];
            $condition = ['userid' => $post['userid'],];
            $res1 = $user_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function userinfo()
    {
        $userid = input('param.userid');
        $user_model = new User();
        $condition = ['userid' => $userid,];
        $user_row = $user_model->get_one_user_by_condition_on_admin($userid);
        $assign_arr = array('title' => "标记用户", 'list' => $user_row,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function edit()
    {
        $userid = input('param.userid');
        $user_model = new User();
        $condition = ['userid' => $userid,];
        $user_row = $user_model->get_one_user_by_condition_on_admin($userid);
        $assign_arr = array('title' => "编辑用户", 'list' => $user_row, 'usertheme_arr' => unserialize(usertheme_arr), 'paytheme_arr' => unserialize(paytheme_arr),);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            Db::startTrans();
            $user_model = new User();
            $load_arr = ['is_state' => $post['is_state'],];
            if (!empty(trim($post['password']))) {
                $load_arr['password'] = encrypt_userpass($post['password']);
            }
            $condition = ['userid' => $post['userid'],];
            $res1 = $user_model->update_one_by_condition_on_admin($load_arr, $condition);
            $userdefine_model = new Userdefine();
            $load_arr = ['usertheme' => $post['usertheme'], 'qq' => $post['qq'], 'tel' => $post['tel'], 'email' => $post['email'], 'sitename' => $post['sitename'], 'siteurl' => $post['siteurl'], 'usernotice' => $post['usernotice'], 'paytheme' => $post['paytheme'],];
            $condition = ['userid' => $post['userid'],];
            $res2 = $userdefine_model->update_one_by_condition_on_admin($load_arr, $condition);
            if (!$res1 && !$res2) {
                Db::rollback();
                return json_error();
            }
            Db::commit();
            return json_success();
        }
    }

    public function login()
    {
        $post = input('post.');
        $userid = input('userid');
        $user_model = new User();
        $condition = ['userid' => $userid,];
        $user_row = $user_model->get_one_by_condition_on_admin($condition);
        $usercheck_row = db('usercheck')->where('userid', $userid)->find();
        $usertheme = db('userdefine')->where('userid', $userid)->value('usertheme');
        $load_arr_session = array('userid' => $userid, 'username' => $user_row['username'], 'sessionid' => $usercheck_row['sessionid'], 'login_time' => $usercheck_row['login_time'], 'login_ip' => $usercheck_row['login_ip'], 'userleave' => $usercheck_row['userleave'], 'usertheme' => $usertheme,);
        session('user', $load_arr_session);
        if (!session('?user')) {
            return false;
        }
        $this->redirect('/center');
    }

    public function userrate()
    {
        $userid = input('param.userid');
        $userpaychennel_model = new Userpaychannel();
        $condition = ['userid' => input('param.userid'),];
        $userpaychennel_arr = $userpaychennel_model->get_list_by_condition_on_admin($condition, [], [], '');
        $user_model = new User();
        $username = $user_model->get_value_by_condition_on_admin('username', $condition);
        $assign_arr = array('title' => "设置分成", 'list' => $userpaychennel_arr, 'username' => $username,);
        $this->layout = false;
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function userratesave()
    {
        $userid = input('param.userid');
        Db::startTrans();
        $userpaychennel_model = new Userpaychannel();
        $load_arr = ['userrate' => input('userrate'), 'is_state' => input('is_state'),];
        $condition = ['userid' => input('param.userid'), 'channelid' => input('param.channelid'),];
        $res1 = $userpaychennel_model->update_one_by_condition_on_admin($load_arr, $condition);
        if (!$res1) {
            Db::rollback();
            return json_error();
        }
        Db::commit();
        return json_success();
    }
} 