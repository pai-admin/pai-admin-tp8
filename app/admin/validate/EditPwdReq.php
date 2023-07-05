<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\validate;

use think\Validate;

class EditPwdReq extends Validate
{
    protected $rule = [
        'newPassword' => 'require',
        'oldPassword' => 'require',
    ];

    protected $message = [
        'newPassword.require' => '新密码必填',
        'oldPassword.require' => '旧密码必填'
    ];
}