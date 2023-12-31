<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;

use app\admin\model\DictData as ddModel;
use app\admin\validate\DictDataReq;

/**
 * 字典项目
 * Author: cfn <cfn@leapy.cn>
 */
class DictData extends Base
{
    /**
     * 字典列表
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function list()
    {
        $where = $this->request->only([
           'page' => 1,
           'limit' => 10,
           'typeId' => ''
        ]);
        $result = ddModel::list($where);
        $this->success('字典项目列表', $result['data'], $result['count']);
    }

    /**
     * 添加字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function add()
    {
        $param = $this->request->only([
            'typeId'=>0,'name','content','rank'=>1, 'status' => 1
        ]);
        // 数据校验
        validate(DictDataReq::class)->scene("add")->check($param);
        $param = arrayUncamelize($param);
        $param['create_time'] = date("Y-m-d H:i:s");
        $res = ddModel::insertGetId($param);
        $res ? self::success('添加成功') : self::fail('添加失败');
    }

    /**
     * 修改字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function edit()
    {
        $param = $this->request->only([
            'typeId'=>0,'name','content','rank'=>1, 'status' => 1, 'dataId'
        ]);
        validate(DictDataReq::class)->check($param);
        // 验证
        $res = ddModel::edit($param);
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
        if (!$ids) self::fail("ID必传");
        $res = ddModel::delByIds($ids);
        $res ? self::success('删除成功') : self::fail('删除失败');
    }
}