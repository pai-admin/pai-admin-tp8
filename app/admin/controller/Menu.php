<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;
use app\admin\model\Menu as mModel;
use app\admin\validate\MenuReq;

class Menu extends Base
{
    /**
     * 字典列表
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function list()
    {
        $this->success('菜单列表', mModel::list());
    }

    /**
     * 添加字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function add()
    {
        $param = $this->request->only([
            'parentId'=>0,'title','remark','rank'=>1, 'type' => 1, 'method', 'flag', 'name', 'path', 'icon', 'hidden'
        ]);
        // 数据校验
        validate(MenuReq::class)->scene("add")->check($param);
        $param = arrayUncamelize($param);
        $param['create_time'] = date("Y-m-d H:i:s");
        $menuId = mModel::insertGetId($param);
        $menuId ? self::success('添加成功', compact("menuId")) : self::fail('添加失败');
    }

    /**
     * 修改字典
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function edit()
    {
        $param = $this->request->only([
            'parentId'=>0,'title','remark','rank'=>1, 'type' => 1, 'method', 'flag', 'name', 'path', 'icon', 'hidden', 'menuId'
        ]);
        validate(MenuReq::class)->check($param);
        // 验证
        $res = mModel::edit($param);
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
        $res = mModel::delByIds($ids);
        $res ? self::success('删除成功') : self::fail('删除失败');
    }
}