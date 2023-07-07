<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\model;

use app\Model;

class Role extends Model
{
    /**
     * Author: cfn <cfn@leapy.cn>
     * @param $where
     * @return array
     */
    static function list($where): array
    {
        $model = self::where('del_flag', 0);

        if ($where['roleName']) {
            $model = $model->whereLike("role_name", "%$where[roleName]%");
        }

        $sModel = clone $model;
        $count = $model->count();
        $data = $sModel->page($where['page'], $where['limit'])
            ->field("role_name, rank, status, remark, create_time, update_time, role_id, flag, checked_menus")
            ->order("rank desc")
            ->select()
            ->toArray();
        return compact("data", "count");
    }

    /**
     * 修改
     * Author: cfn <cfn@leapy.cn>
     * @param array $param
     * @return bool
     */
    static function edit(array $param): bool
    {
        $param = arrayUncamelize($param);
        $param['update_time'] = date("Y-m-d H:i:s");
        $menus = $param['menus'];
        unset($param['menus']);
        $res = (bool)(new self())->where('role_id', $param['role_id'])->where("del_flag", 0)->update($param);
        if (!$res) {
            return false;
        }
        // 删除原来的
        RoleMenu::where("role_id", $param['role_id'])->delete();
        $roleMenus = array();
        foreach ($menus as $menuId) {
            $roleMenus[] = ['role_id' => $param['role_id'], 'menu_id' => $menuId];
        }
        // 重新添加
        return RoleMenu::insertAll($roleMenus);
    }

    /**
     * 删除
     * Author: cfn <cfn@leapy.cn>
     * @param mixed $ids
     * @return bool
     */
    static function delByIds(mixed $ids)
    {
        return (bool)(new self())->where("del_flag", 0)
            ->whereIn("role_id", $ids)
            ->update([
                'del_flag' => 1,
                'update_time' => date("Y-m-d H:i:s")
            ]);
    }

    /**
     * 添加菜单
     * Author: cfn <cfn@leapy.cn>
     * @param array $param
     * @return false|void
     */
    static function add(array $param)
    {
        $param = arrayUncamelize($param);
        $param['create_time'] = date("Y-m-d H:i:s");
        $menus = $param['menus'];
        unset($param['menus']);
        $roleId = self::insertGetId($param);
        if (!$roleId) return false;
        $roleMenus = array();
        foreach ($menus as $menuId) {
            $roleMenus[] = ['role_id' => $roleId, 'menu_id' => $menuId];
        }
        return RoleMenu::insertAll($roleMenus);
    }
}