<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\model;

use app\Model;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Author: cfn <cfn@leapy.cn>
 */
class AccountLog extends Model
{
    /**
     * 写入日志
     * Author: cfn <cfn@leapy.cn>
     * @param int $accountId
     * @param string $username
     * @param string $title
     * @param string $method
     * @param string $flag
     * @param int $code
     * @param string $response
     * @return bool
     */
    static function writeLog(int $accountId, string $username, string $title, string $method, string $flag, int $code, string $response=""): bool
    {
        return (bool)self::insert([
            'account_id' => $accountId,
            'username' => $username,
            'title' => $title,
            'method' => $method,
            'flag' => $flag,
            'code' => $code,
            'request' => json_encode(request()->param(), JSON_UNESCAPED_UNICODE),
            'response' => $response,
            'ip' => request()->ip(),
            'ua' => request()->header("user-agent"),
            'create_time' => date("Y-m-d H:i:s")
        ]);
    }

    /**
     * 获取个人日志
     * Author: cfn <cfn@leapy.cn>
     * @param array $param
     * @return array
     */
    public static function getLog(array $param)
    {
        $model = (new self())->where("account_id", $param['accountId']);
        $sModel = clone $model;
        $count = $model->count();
        $data = $sModel->page($param['page'], $param['limit'])
            ->field("log_id,title,code,ip,ua,create_time")
            ->order("log_id desc")
            ->select()
            ->toArray();
        return compact("data", "count");
    }

    /**
     * Author: cfn <cfn@leapy.cn>
     * @param array $param
     * @return array
     */
    static function list(array $param): array
    {
        $model = (new self());
        if ($param['startTime']) {
            $model = $model->where("create_time",">=", $param['startTime']);
        }
        if ($param['endTime']) {
            $model = $model->where("create_time", "<=", $param['endTime']);
        }
        if ($param['title']) {
            $model = $model->whereLike("title", "%{$param['title']}%");
        }
        $sModel = clone $model;
        $count = $model->count();
        $data = $sModel->page($param['page'], $param['limit'])
            ->field("log_id,title,code,ip,ua,create_time,username,flag,method")
            ->order("log_id desc")
            ->select()->toArray();
        return compact("data", "count");
    }
}