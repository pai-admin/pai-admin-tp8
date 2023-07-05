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
}