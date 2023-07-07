<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;

use app\admin\model\Dept as dModel;
use app\admin\validate\DeptReq;

/**
 * 字典项目
 * Author: cfn <cfn@leapy.cn>
 */
class Dept extends Base
{
    /**
     * 字典列表
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function list()
    {
        $where = $this->request->only([
           'deptName' => ''
        ]);
        $this->success('部门列表', dModel::list($where));
    }

    /**
     * 添加字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function add()
    {
        $param = $this->request->only([
            'parentId'=>0,'deptName','remark','rank'=>1, 'status' => 1
        ]);
        // 数据校验
        validate(DeptReq::class)->scene("add")->check($param);
        $param = arrayUncamelize($param);
        $param['create_time'] = date("Y-m-d H:i:s");
        $res = dModel::insert($param);
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
            'parentId'=>0,'deptName','remark','rank'=>1, 'status' => 1, 'deptId'
        ]);
        validate(DeptReq::class)->check($param);
        // 验证
        $res = dModel::edit($param);
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
        $res = dModel::delByIds($ids);
        $res ? self::success('删除成功') : self::fail('删除失败');
    }
}