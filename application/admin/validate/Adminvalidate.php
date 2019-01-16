<?php

namespace app\admin\validate;

use think\Validate;

class Adminvalidate extends Validate
{
    protected $rule = ['adminname' => 'require|min:5|max:20', 'adminpass' => 'require|min:5|max:20', 'safepwd' => 'max:20|safepwd',];
    protected $message = ['adminname.require' => '用户名必须填写', 'adminname.min' => '用户名最小长度5位', 'adminname.max' => '用户名最大长度20位', 'adminpass.require' => '密码必须填写', 'adminpass.password' => '密码格式5-20个字母数字组合', 'adminpass.min' => '密码最小长度5位', 'adminpass.max' => '密码最大长度20位', 'safepwd.max' => '安全码最大长度20位',];
    protected $regex = ['password' => '/^[\w]{5,20}$/', 'safepwd' => '/^[\w]$/'];
    protected $scene = ['login' => ['adminname', 'adminpass', 'safepwd'],];
} ?>