<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\model;

use app\Model;

/**
 * 部门
 * Author: cfn <cfn@leapy.cn>
 */
class Dept extends Model
{
    /**
     * Author: cfn <cfn@leapy.cn>
     * @param $where
     * @return array
     */
    static function list($where): array
    {
        $model = self::where('del_flag', 0);

        if ($where['deptName']) {
            $model = $model->whereLike("dept_name", "%$where[deptName]%");
        }

        return $model
            ->field("parent_id, dept_name, rank, status, remark, create_time, update_time, dept_id")
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
        return (bool)(new self())->where('dept_id', $param['dept_id'])->where("del_flag", 0)->update($param);
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
            ->whereIn("dept_id", $ids)
            ->update([
                'del_flag' => 1,
                'update_time' => date("Y-m-d H:i:s")
            ]);
    }
}