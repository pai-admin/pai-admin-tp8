<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\validate;

use think\Validate;

class DictTypeReq extends Validate
{
    protected $rule = [
        'parentId|上级字典' => 'require',
        'typeName|字典名称' => 'require',
        'flag|字典标识' => 'require',
        'typeId|字典ID' => 'require',
    ];

    protected $scene = [
        'add' => ['parentId','typeName','flag']
    ];
}