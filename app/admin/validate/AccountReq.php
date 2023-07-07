<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\validate;

use think\Validate;

/**
 * Author: cfn <cfn@leapy.cn>
 */
class AccountReq extends Validate
{
    protected $rule = [
        'accountId|管理员ID' => 'require',
        'username|用户名' => 'require',
        'deptId|部门ID' => 'require',
        'password|密码' => 'require',
        'roles|角色' => 'require'
    ];

    protected $scene = [
        'add' => ['username','deptId', 'password', 'roles'],
        'edit' => ['username','deptId', 'accountId', 'roles']
    ];
}