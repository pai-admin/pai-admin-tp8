<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;
use app\admin\model\DictType as dtModel;
use app\admin\validate\DictTypeReq;

/**
 * 字典
 * Author: cfn <cfn@leapy.cn>
 */
class DictType extends Base
{
    /**
     * 字典列表
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function list()
    {
        $this->success('字典列表', dtModel::list());
    }

    /**
     * 添加字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function add()
    {
        $param = $this->request->only([
            'parentId'=>0,'typeName','flag','rank'=>1
        ]);
        // 数据校验
        validate(DictTypeReq::class)->scene("add")->check($param);
        $param = arrayUncamelize($param);
        $param['create_time'] = date("Y-m-d H:i:s");
        $typeId = dtModel::insertGetId($param);
        $typeId ? self::success('添加成功', compact("typeId")) : self::fail('添加失败');
    }

    /**
     * 修改字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function edit()
    {
        $param = $this->request->only([
            'parentId'=>0,'typeName','flag','rank'=>1, 'typeId'
        ]);
        validate(DictTypeReq::class)->check($param);
        // 验证
        $res = dtModel::edit($param);
        $res ? self::success('添加成功') : self::fail('添加失败');
    }

    /**
     * 删除字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function del()
    {
        $ids = $this->request->param("ids");
        if (!$ids) self::fail("字典ID必传");
        $res = dtModel::delByIds($ids);
        $res ? self::success('删除成功') : self::fail('删除失败');
    }
}