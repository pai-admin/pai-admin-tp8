<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;

use app\admin\model\Role as rModel;
use app\admin\validate\RoleReq;

/**
 * 角色
 * Author: cfn <cfn@leapy.cn>
 */
class Role extends Base
{
    /**
     * 字典列表
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function list()
    {
        $where = $this->request->only([
            'roleName' => '',
            'page' => 1,
            'limit' => 10,
        ]);
        $result = rModel::list($where);
        $this->success('角色列表', $result['data'], $result['count']);
    }

    /**
     * 添加字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function add()
    {
        $param = $this->request->only([
            'roleName', 'checkedMenus', 'status' => 1, 'rank' => 1, 'flag', 'remark', 'menus'
        ]);
        // 数据校验
        validate(RoleReq::class)->scene("add")->check($param);
        $res = rModel::add($param);
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
            'roleName', 'checkedMenus', 'status' => 1, 'rank' => 1, 'flag', 'remark', 'roleId', 'menus'
        ]);
        validate(RoleReq::class)->check($param);
        // 验证
        $res = rModel::edit($param);
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
        $res = rModel::delByIds($ids);
        $res ? self::success('删除成功') : self::fail('删除失败');
    }
}