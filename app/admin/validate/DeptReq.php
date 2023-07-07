<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\validate;

use think\Validate;

class DeptReq extends Validate
{
    protected $rule = [
        'deptName|字典项目名称' => 'require',
        'deptId|项目内容' => 'require',
        'parentId|字典ID' => 'require'
    ];

    protected $scene = [
        'add' => ['deptName','parentId']
    ];
}