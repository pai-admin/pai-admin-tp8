<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\validate;

use think\Validate;

class DictDataReq extends Validate
{
    protected $rule = [
        'name|字典项目名称' => 'require',
        'content|项目内容' => 'require',
        'typeId|字典ID' => 'require',
        'dataId|字典ID' => 'require',
    ];

    protected $scene = [
        'add' => ['typeId','name','content']
    ];
}