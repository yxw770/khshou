<?php

namespace app\user\validate;

use think\Validate;

class Uservalidate extends Validate
{
    protected $rule = ['tel' => 'require|tel',];
    protected $message = ['tel.require' => '手机号必须填写', 'tel.tel' => '手机号必须填写正确',];
    protected $regex = ['tel' => "/^1([358][0-9]|4[579]|5[01356789]|7[0135678]|66|88|9[89])\d{8}$/", 'password' => '/^[\w]{6,20}$/'];
    protected $scene = ['sms_userinfo' => ['tel'],];
} ?>