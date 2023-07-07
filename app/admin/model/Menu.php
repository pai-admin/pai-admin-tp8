<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\model;

use app\Model;
use think\facade\Cache;

class Menu extends Model
{
    /**
     * Author: cfn <cfn@leapy.cn>
     * @param string $auth
     * @return string
     */
    static function getAuthTitle(string $auth): string
    {
        // 先查询权限名称和标识
        $auths = Cache::store("redis")->get(config("app.token_key")."AUTHS");
        if (empty($auths) || !key_exists($auth, $auths)) {
            $_auth = (new self())->where("del_flag", 0)->where("type",2)->whereFieldRaw("lower(CONCAT(method,':',flag))",$auth)->value("title");
            if ($_auth) {
                $auths = self::getAllAuth();
                Cache::store("redis")->set(config("app.token_key")."AUTHS", $auths);
            }
        }
        return $auths[$auth] ?? "未知操作";
    }

    /**
     * 查询所有权限信息
     * Author: cfn <cfn@leapy.cn>
     * @return array
     */
    static function getAllAuth()
    {
        return (new self())->where("del_flag", 0)->where("type",2)->column("title", "lower(CONCAT(method,':',flag))");
    }

    /**
     * Author: cfn <cfn@leapy.cn>
     * @return array
     */
    static function list(): array
    {
        return self::where('del_flag', 0)
            ->field("parent_id, title, rank, type, remark, create_time, update_time, menu_id, method, flag, name, path, icon, hidden")
            ->order("rank desc")
            ->select()
            ->toArray();
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
        return (bool)(new self())->where('menu_id', $param['menu_id'])->where("del_flag", 0)->update($param);
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
            ->whereIn("menu_id", $ids)
            ->update([
                'del_flag' => 1,
                'update_time' => date("Y-m-d H:i:s")
            ]);
    }
}