<?php
namespace app\admin\validate;

use think\Validate;

/**
 * Author: cfn <cfn@leapy.cn>
 */
class LoginReq extends Validate
{
    protected $rule = [
        'username' => 'require|min:2|max:20',
        'password' => 'require|min:6|max:32',
        'verifyId' => 'require',
        'verifyCode' => 'require',
    ];

    protected $message = [
        'username.require' => '账号必填',
        'password.require' => '密码必填',
        'verifyId.require' => '验证码ID必传',
        'verifyCode.require' => '验证码必填'
    ];

    protected $scene = [
        'login' => ['username','password','verifyId', 'verifyCode']
    ];
}