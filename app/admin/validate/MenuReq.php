<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\validate;

use think\Validate;

/**
 * Author: cfn <cfn@leapy.cn>
 */
class MenuReq extends Validate
{
    protected $rule = [
        'title|名称' => 'require',
        'parentId|上级' => 'require',
        'type|类型' => 'require',
        'menuId|菜单ID' => 'require'
    ];

    protected $scene = [
        'add' => ['name','parentId','type']
    ];
}