<?php

namespace app\user\controller;

use app\common\oss\Oss;
use app\user\model\Userdefine;
use app\user\model\Userpay;
use app\wx\controller\Respone;
use think\Db;

class Userinfo extends UserBase
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
        $userdefine_model = new Userdefine();
        $userdefine_row = $userdefine_model->get_one_by_session_userid_on_user();
        $userpay_model = new Userpay();
        $userpay_row = $userpay_model->get_one_by_session_userid_on_user();
        $assign_arr = array('title' => "用户信息", 'userdefine_row' => $userdefine_row, 'userpay_row' => $userpay_row, 'contact_limit_arr' => unserialize(contact_limit_arr), 'paytheme_arr' => unserialize(paytheme_arr),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function edituserpay()
    {
        $userdefine_model = new Userdefine();
        $userdefine_row = $userdefine_model->get_one_by_session_userid_on_user();
        $userpay_model = new Userpay();
        $userpay_row = $userpay_model->get_one_by_session_userid_on_user();
        $assign_arr = array('title' => "用户信息", 'userdefine_row' => $userdefine_row, 'userpay_row' => $userpay_row, 'contact_limit_arr' => unserialize(contact_limit_arr), 'paytheme_arr' => unserialize(paytheme_arr),);
        $this->assign($assign_arr);
        return $this->fetch();
    }

    public function editsave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            $oss_model = new Oss();
            if (!empty($_FILES)) {
                if ($post['ptype'] == '1') {
                    $filename = 'alipayimg';
                } elseif ($post['ptype'] == '3') {
                    $filename = 'weixinimg';
                } else {
                    $filename = '';
                }
                if (!empty($filename)) {
                    $json_arr = $oss_model->upload_oss($filename, 'payimgs');
                    $arr_arr = json_decode($json_arr, true);
                    if ($arr_arr['status'] == '1') {
                        $img_url = $arr_arr['orther'];
                    } else {
                        return $json_arr;
                    }
                } else {
                    $img_url = '';
                }
            }
            unset($post['email']);
            unset($post['tel']);
            Db::startTrans();
            $userdefine_model = new Userdefine();
            $res1 = $userdefine_model->update_one_by_session_userid_on_user($post);
            $userpay_model = new Userpay();
            $ptype = $userpay_model->get_value_by_session_userid_on_user('ptype');
            if ($ptype == 0) {
                if (!empty($filename)) {
                    $post[$filename] = $img_url;
                }
                $res2 = $userpay_model->update_one_by_session_userid_on_user($post);
                if (!$res1 && !$res2) {
                    Db::rollback();
                    return json_error();
                }
            } else {
                if (!$res1) {
                    Db::rollback();
                    return json_error();
                }
            }
            Db::commit();
            return json_success();
        }
    }

    public function edituserpaysave()
    {
        if (request()->isPost()) {
            $post = input('post.');
            if (cache('adminconfig')['is_sms_userpay'] == 1) {
                $chkcode = $post['chkcode'];
                if (empty($chkcode)) {
                    return json_error("请输入短信验证码");
                }
                $sms_row = db('sms')->where(['tel' => input('post.tel'), 'type' => 1])->order('smsid desc')->find();
                if (empty($sms_row)) {
                    return json_error("短信验证码错误");
                }
                if ($sms_row['code'] != $chkcode) {
                    return json_error("短信验证码错误");
                }
                if ((time() - strtotime($sms_row['addtime'])) > 5 * 60) {
                    return json_error("验证码过期");
                }
            }
            $oss_model = new Oss();
            if (!empty($_FILES)) {
                if ($post['ptype'] == '1') {
                    $filename = 'alipayimg';
                } elseif ($post['ptype'] == '3') {
                    $filename = 'weixinimg';
                } else {
                    $filename = '';
                }
                if (!empty($filename)) {
                    $json_arr = $oss_model->upload_oss($filename, 'payimgs');
                    $arr_arr = json_decode($json_arr, true);
                    if ($arr_arr['status'] == '1') {
                        $img_url = $arr_arr['orther'];
                        $post[$filename] = $img_url;
                    } else {
                        return $json_arr;
                    }
                } else {
                    $img_url = '';
                }
            }
            if ($post['ptype'] == '1') {
                if (isset($post['alipayimg']) && empty($post['alipayimg'])) {
                    unset($post['alipayimg']);
                }
            } elseif ($post['ptype'] == '3') {
                if (isset($post['weixinimg']) && empty($post['weixinimg'])) {
                    unset($post['weixinimg']);
                }
            }
            Db::startTrans();
            $userpay_model = new Userpay();
            $res2 = $userpay_model->update_one_by_session_userid_on_user($post);
            if (!$res2) {
                Db::rollback();
                return json_error();
            }
            db('sms')->where('smsid', $sms_row['smsid'])->update(['type' => '0', 'updatetime' => date('Y-m-d H:i:s')]);
            Db::commit();
            return json_success();
        }
    }

    public function upload()
    {
        $file = request()->file('image');
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                echo $info->getExtension();
                echo $info->getSaveName();
                echo $info->getFilename();
            } else {
                echo $file->getError();
            }
        }
    }

    public function sms_userinfo()
    {
        $phone = ['tel' => input('phone'),];
        $validate = \think\Loader::validate('Uservalidate');
        if (!$validate->scene('sms_userinfo')->check($phone)) {
            return json_error($validate->getError());
        }
        $sms_row = db('sms')->where(['tel' => input('phone')])->order('smsid desc')->find();
        if (!empty($sms_row)) {
            $addtime = $sms_row['addtime'];
            if ((time() - strtotime($addtime)) < 80) {
                return json_error('您的短信发送太频繁 请稍后再试');
            }
        }
        $code = rand(100000, 999999);
        $data = ['userid' => session('user.userid'), 'tel' => input('phone'), 'code' => $code, 'content' => "您的修改收款信息验证码是：" . $code, 'addtime' => date('Y-m-d H:i:s', time()), 'type' => 1,];
        $sms_id = db('sms')->insertGetId($data);
        require_once EXTEND_PATH . 'dysms/SmsDemo.php';
        $result = \dySms\SmsDemo::sendSms('', '', input('phone'), ['code' => $code]);
        $result_arr = json_decode(json_encode($result), true);
        if ($result_arr['Code'] == 'OK' || $result_arr['Message'] == 'OK') {
            db('sms')->where('smsid', $sms_id)->update(['is_send' => '1', 'updatetime' => date('Y-m-d H:i:s')]);
            return json_success("短信发送成功");
        } else {
            return json_error($result_arr['Message']);
        }
    }
}