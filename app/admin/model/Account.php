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
            ->leftJoin("dept d","d.dept_id = a.dept_id")
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
}