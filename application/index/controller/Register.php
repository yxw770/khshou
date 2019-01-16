<?php

namespace app\index\controller;

use app\admin\model\Adminpaychannel;
use app\index\model\User;
use think\Db;

class Register extends IndexBase
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
        $is_sms = cache('adminconfig')['is_sms'] ? cache('adminconfig')['is_sms'] : 0;
        $assign_arr = array('title' => '用户注册', 'is_sms' => $is_sms,);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function register_save()
    {
        if (request()->isPost()) {
            $post_arr = trim_arr(input('post.'));
            $username = $post_arr['username'];
            $password = $post_arr['password'];
            $password_confirm = $post_arr['password_confirm'];
            $tel = $post_arr['tel'];
            $email = $post_arr['email'];
            $validate_arr = array('username' => $username, 'password' => $password, 'password_confirm' => $password_confirm, 'tel' => $tel, 'email' => $email,);
            $validate = \think\Loader::validate('Uservalidate');
            if (!$validate->scene('register')->check($validate_arr)) {
                return json_error($validate->getError());
            }
            $is_sms = cache('adminconfig')['is_sms'];
            if ($is_sms) {
                $chkcode = $post_arr['chkcode'];
                if (!$chkcode) {
                    return json_error('请输入手机验证码');
                }
                $sms_row = db('sms')->where(['is_send' => ['<>', '2'], 'tel' => $tel])->order('smsid desc')->find();
                $code = $sms_row['code'];
                if ($code != $chkcode) {
                    return json_error('手机验证码输入错误 请重新输入');
                } else {
                    db('sms')->where('smsid', $sms_row['smsid'])->update(['is_send' => '2', 'updatetime' => date('Y-m-d H:i:s')]);
                }
            }
            Db::startTrans();
            $password_encrypt = encrypt_userpass($password);
            $load_arr_user = array('username' => $username, 'password' => $password_encrypt, 'addtime' => date('Y-m-d H:i:s'),);
            $user_model = new User();
            $userid = $user_model->insertGetId($load_arr_user);
            if (!$userid) {
                Db::rollback();
                return json_success("注册失败，请重新填写！");
            }
            $load_arr_usercheck = array('userid' => $userid, 'username' => $username, 'sessionid' => session_id(), 'login_time' => date('Y-m-d H:i:s'), 'login_ip' => request()->ip(), 'userleave' => date('Y-m-d H:i:s'), 'useruniqid' => uniqid(),);
            $usercheck_id = db('usercheck')->insertGetId($load_arr_usercheck);
            if (!$usercheck_id) {
                Db::rollback();
                return json_success("注册失败，请重新填写！");
            }
            $adminconfig_cache = cache('adminconfig');
            $usertheme = $adminconfig_cache['usertheme'];
            $paytheme = $adminconfig_cache['paytheme'];
            $load_arr_userdefine = array('userid' => $userid, 'usertheme' => $usertheme, 'paytheme' => $paytheme, 'linkid' => getRandomString(16), 'tel' => $tel, 'email' => $email,);
            $userdefine_id = db('userdefine')->insertGetId($load_arr_userdefine);
            if (!$userdefine_id) {
                Db::rollback();
                return json_success("注册失败，请重新填写！");
            }
            $load_arr_userpay = array('userid' => $userid,);
            $userpay_id = db('userpay')->insertGetId($load_arr_userpay);
            if (!$userpay_id) {
                Db::rollback();
                return json_success("注册失败，请重新填写！");
            }
            $load_arr_usersettlement = array('userid' => $userid,);
            $usersettlement_id = db('usersettlement')->insertGetId($load_arr_usersettlement);
            if (!$usersettlement_id) {
                Db::rollback();
                return json_success("注册失败，请重新填写！");
            }
            $adminpaychennel_model = new Adminpaychannel();
            $adminpaychennel_arr = $adminpaychennel_model->get_list_by_condition_on_admin([], [], [], []);
            $load_arr_userpaychannel = [];
            foreach ($adminpaychennel_arr as $k => $v) {
                $load_arr_userpaychannel['userid'] = $userid;
                $load_arr_userpaychannel['channelid'] = $v['channelid'];
                $load_arr_userpaychannel['channelname'] = $v['channelname'];
                $load_arr_userpaychannel['userrate'] = $v['userproportion'];
                $load_arr_userpaychannel['is_state'] = $v['is_state'];
                $load_arr_userpaychannel['payway'] = $v['payway'];
                $userpaychannel_id = db('userpaychannel')->insertGetId($load_arr_userpaychannel);
                if (!$userpaychannel_id) {
                    Db::rollback();
                    return json_success("注册失败，请重新填写！");
                }
            }
            Db::commit();
            return json_success("注册成功，请登录！");
        }
        return false;
    }

    public function help()
    {
        return $this->fetch();
    }

    public function sms()
    {
        $phone = ['tel' => input('phone'),];
        $validate = \think\Loader::validate('Uservalidate');
        if (!$validate->scene('sms')->check($phone)) {
            return json_error($validate->getError());
        }
        $sms_row = db('sms')->where(['tel' => input('phone')])->order('smsid desc')->find();
        if (!empty($sms_row)) {
            $addtime = $sms_row['addtime'];
            if ((time() - strtotime($addtime)) < 80) {
                return json_error('您的注册短信发送太频繁 请稍后再试');
            }
        }
        $code = rand(000000, 999999);
        $data = ['tel' => input('phone'), 'code' => $code, 'content' => "您的注册验证码是：" . $code, 'addtime' => date('Y-m-d H:i:s', time()),];
        $sms_id = db('sms')->insertGetId($data);
        require_once EXTEND_PATH . 'dysms/SmsDemo.php';
        $result = \dySms\SmsDemo::sendSms('', '', input('phone'), ['code' => $code]);
        $result_arr = json_decode(json_encode($result), true);
        if ($result_arr['Code'] == 'OK' || $result_arr['Message'] == 'OK') {
            db('sms')->where('smsid', $sms_id)->update(['is_send' => '1', 'updatetime' => date('Y-m-d H:i:s')]);
            return json_success();
        } else {
            return json_error($result_arr['Message']);
        }
    }
} 