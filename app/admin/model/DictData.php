<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\model;

use app\Model;

class DictData extends Model
{
    /**
     * Author: cfn <cfn@leapy.cn>
     * @param $where
     * @return array
     */
    static function list($where): array
    {
        $model = self::where('del_flag', 0)->where("type_id", $where['typeId']);
        $sModel = clone $model;
        $count = $model->count();
        $data = $sModel
            ->page($where['page'], $where['limit'])
            ->field("type_id, data_id, name, content, rank, status")
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
        return (bool)(new self())->where('data_id', $param['data_id'])->where("del_flag", 0)->update($param);
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
            ->whereIn("data_id", $ids)
            ->update([
                'del_flag' => 1,
                'update_time' => date("Y-m-d H:i:s")
            ]);
    }
}