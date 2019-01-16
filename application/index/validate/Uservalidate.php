<?php

namespace app\index\validate;

use think\Validate;

class Uservalidate extends Validate
{
    protected $rule = ['username' => 'require|unique:user|min:6|max:20', 'password' => 'require|confirm|min:6|max:20|password', 'tel' => 'require|tel|unique:userdefine', 'email' => 'require|email',];
    protected $message = ['username.require' => '用户名必须填写', 'username.min' => '用户名最小长度6位', 'username.max' => '用户名最大长度20位', 'username.unique' => '用户名存在', 'password.require' => '密码必须填写', 'password.password' => '密码格式6-20个字母数字组合', 'password.confirm' => '确认密码和密码输入不一致', 'password.min' => '密码最小长度6位', 'password.max' => '密码最大长度20位', 'tel.require' => '手机号必须填写', 'tel.tel' => '手机号必须填写正确', 'tel.unique' => '手机号已经存在', 'email.require' => '邮箱必须填写', 'email.email' => '邮箱格式错误',];
    protected $regex = ['tel' => "/^1([358][0-9]|4[579]|5[01356789]|7[0135678]|66|88|9[89])\d{8}$/", 'password' => '/^[\w]{6,20}$/'];
    protected $scene = ['register' => ['username', 'password', 'tel', 'email'], 'login' => ['username' => 'require|min:6|max:20', 'password' => 'require|min:6|max:20|password'], 'sms' => ['tel'],];
} ?>