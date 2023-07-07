<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;

use app\admin\model\Account as aModel;
use app\admin\validate\AccountReq;

/**
 * 账号管理
 * Author: cfn <cfn@leapy.cn>
 */
class Account extends Base
{
    /**
     * 列表
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function list()
    {
        $where = $this->request->only([
            'username' => '',
            'deptId' => '',
            'page' => 1,
            'limit' => 10,
        ]);
        $result = aModel::list($where);
        $this->success('账号列表', $result['data'], $result['count']);
    }

    /**
     * 添加
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function add()
    {
        $param = $this->request->only([
            'username','password','status'=>1, 'avatar', 'deptId', 'roles'
        ]);
        // 数据校验
        validate(AccountReq::class)->scene('add')->check($param);
        $res = aModel::add($param);
        $res ? self::success('添加成功') : self::fail('添加失败');
    }

    /**
     * 修改
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function edit()
    {
        $param = $this->request->only([
            'username','password','status'=>1, 'avatar', 'deptId', 'accountId', 'roles'
        ]);
        validate(AccountReq::class)->scene('edit')->check($param);
        // 验证
        $res = aModel::edit($param);
        $res ? self::success('添加成功') : self::fail('添加失败');
    }

    /**
     * 删除
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function del()
    {
        $ids = $this->request->param("ids");
        if (!$ids) self::fail("ID必传");
        $res = aModel::delByIds($ids);
        $res ? self::success('删除成功') : self::fail('删除失败');
    }
}