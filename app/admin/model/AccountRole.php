<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\model;

use app\Model;

class AccountRole extends Model
{
    public static function getRoles(int $account_id)
    {
        return self::alias("ar")
            ->leftJoin("role r", "r.role_id = ar.role_id")
            ->where("ar.account_id", $account_id)
            ->where("r.del_flag", 0)
            ->field("ar.account_id, r.role_id, r.role_name")
            ->select()
            ->toArray();
    }
}