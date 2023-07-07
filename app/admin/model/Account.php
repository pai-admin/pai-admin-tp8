<?php

namespace app\admin\model;

use app\Model;
use utils\Tools;

/**
 * Author: cfn <cfn@leapy.cn>
 */
class Account extends Model
{
    /**
     * 通过用户名查询账号
     * Author: cfn <cfn@leapy.cn>
     * @param $username
     * @param $field
     * @return array
     */
    static function getByUsername($username, $field): array
    {
        return self::where("del_flag", 0)->where("username", $username)->field($field)->find()->toArray();
    }

    /**
     * Author: cfn <cfn@leapy.cn>
     * @param int $account_id
     * @return array
     */
    static function getInfo(int $account_id)
    {
        return self::alias("a")
            ->leftJoin("dept d", "d.dept_id = a.dept_id")
            ->where('a.del_flag', 0)
            ->where("a.account_id", $account_id)
            ->field("a.account_id, a.username, a.avatar, d.dept_name")
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * Author: cfn <cfn@leapy.cn>
     * @param int $account_id
     * @return array
     */
    static function getInfoById(int $account_id, string $field)
    {
        return self::where('del_flag', 0)
            ->where("account_id", $account_id)
            ->field($field)
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * 按钮权限
     * Author: cfn <cfn@leapy.cn>
     * @param int $accountId
     * @return array
     */
    static function getButtons(int $accountId)
    {
        return (new self())->alias("a")
            ->leftJoin("account_role ar", "ar.account_id = a.account_id")
            ->leftJoin("role_menu rm", "rm.role_id = ar.role_id")
            ->leftJoin("menu m", "m.menu_id = rm.menu_id")
            ->where("a.account_id", $accountId)
            ->where("m.type", 1)
            ->where("m.del_flag", 0)
            ->group("m.menu_id")
            ->column("m.flag");
    }

    /**
     * 获取菜单
     * Author: cfn <cfn@leapy.cn>
     * @param int $accountId
     * @return array
     */
    static function getMenus(int $accountId)
    {
        return (new self())->alias("a")
            ->leftJoin("account_role ar", "ar.account_id = a.account_id")
            ->leftJoin("role_menu rm", "rm.role_id = ar.role_id")
            ->leftJoin("menu m", "m.menu_id = rm.menu_id")
            ->where("a.account_id", $accountId)
            ->where("m.type", 0)
            ->where("m.del_flag", 0)
            ->field("m.menu_id, m.parent_id, m.title, m.name, m.path, m.icon, m.hidden")
            ->group("m.menu_id")
            ->order("m.rank desc")
            ->select()
            ->toArray();
    }

    /**
     * 获取角色
     * Author: cfn <cfn@leapy.cn>
     * @param int $accountId
     * @return array
     */
    static function getRoles(int $accountId)
    {
        return (new self())->alias("a")
            ->leftJoin("account_role ar", "ar.account_id = a.account_id")
            ->leftJoin("role r", "r.role_id = ar.role_id")
            ->where("a.account_id", $accountId)
            ->column("r.flag");
    }

    /**
     * 更新密码
     * Author: cfn <cfn@leapy.cn>
     * @param int $accountId
     * @param string $newPassword
     * @return Account
     */
    static function changePwd(int $accountId, string $newPassword)
    {
        $salt = Tools::randomString(6);
        return (new self())->where("account_id", $accountId)
            ->update([
                'salt' => $salt,
                'password' => md5($newPassword . $salt),
                'update_time' => date("Y-m-d H:i:s")
            ]);
    }

    /**
     * 获取接口权限
     * Author: cfn <cfn@leapy.cn>
     * @param int $accountId
     * @return
     */
    static function getApis(int $accountId)
    {
        return array_column((new self())->alias("a")
            ->leftJoin("account_role ar", "ar.account_id = a.account_id")
            ->leftJoin("role_menu rm", "rm.role_id = ar.role_id")
            ->leftJoin("menu m", "m.menu_id = rm.menu_id")
            ->where("a.account_id", $accountId)
            ->where("m.type", 2)
            ->where("m.del_flag", 0)
            ->group("m.menu_id")
            ->column("CONCAT(m.method,':',m.flag) as auth"), "auth");
    }

    /**
     * Author: cfn <cfn@leapy.cn>
     * @param $where
     * @return array
     */
    static function list($where): array
    {
        $model = self::alias("a")->where('a.del_flag', 0);

        if ($where['deptId']) {
            $model = $model->where("a.dept_id", $where['deptId']);
        }

        if ($where['username']) {
            $model = $model->whereLike("a.username", "%$where[username]%");
        }

        $sModel = clone $model;
        $count = $model->count();
        $data = $sModel
            ->page($where['page'], $where['limit'])
            ->leftJoin("dept d", "d.dept_id = a.dept_id")
            ->field("a.username, a.dept_id, a.status, a.account_id, a.avatar, a.update_time, a.create_time, d.dept_name")
            ->order("a.account_id desc")
            ->select()->each(function ($item){
                $item['roles'] = AccountRole::getRoles($item['account_id']);
                return $item;
            })->toArray();
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
        $roles = $param['roles'];
        unset($param['roles']);

        // 填写就是修改密码
        if ($param['password']) {
            $salt = Tools::randomString(6);
            $param['password'] = md5($param['password'] . $salt);
            $param['salt'] = $salt;
        }

        $res = (bool)(new self())->where('account_id', $param['account_id'])->where("del_flag", 0)->update($param);
        if (!$res) {
            return false;
        }

        // 删除原来的
        AccountRole::where("account_id", $param['account_id'])->delete();
        $accountRole = array();
        foreach ($roles as $roleId) {
            $accountRole[] = ['account_id' => $param['account_id'], 'role_id' => $roleId];
        }
        return AccountRole::insertAll($accountRole);
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
            ->whereIn("account_id", $ids)
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
        $roles = $param['roles'];
        unset($param['roles']);
        // 密码处理
        if ($param['password']) {
            $salt = Tools::randomString(6);
            $param['password'] = md5($param['password'] . $salt);
            $param['salt'] = $salt;
        }
        $accountId = self::insertGetId($param);
        if (!$accountId) return false;
        $accountRole = array();
        foreach ($roles as $roleId) {
            $accountRole[] = ['account_id' => $accountId, 'role_id' => $roleId];
        }
        return AccountRole::insertAll($accountRole);
    }
}