<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\model;

use app\Model;

class DictType extends Model
{
    /**
     * Author: cfn <cfn@leapy.cn>
     * @return array
     */
    static function list(): array
    {
        return self::where('del_flag', 0)
            ->field("type_id, parent_id, type_name, flag, rank")
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
        return (bool)(new self())->where('type_id', $param['type_id'])->where("del_flag", 0)->update($param);
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
            ->whereIn("type_id", $ids)
            ->update([
                'del_flag' => 1,
                'update_time' => date("Y-m-d H:i:s")
            ]);
    }
}