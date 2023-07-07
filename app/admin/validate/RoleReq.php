<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\validate;

use think\Validate;

/**
 * Author: cfn <cfn@leapy.cn>
 */
class RoleReq extends Validate
{
    protected $rule = [
        'roleName|部门名称' => 'require',
        'checkedMenus|选中的节点(包含半选)' => 'require',
        'flag|角色标识' => 'require',
        'roleId|角色ID' => 'require',
        'menus|权限' => 'require',
    ];

    protected $scene = [
        'add' => ['roleName','checkedMenus','flag', 'menus']
    ];
}