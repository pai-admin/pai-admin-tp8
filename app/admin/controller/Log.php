<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;

use app\admin\model\AccountLog;

/**
 * 日志
 * Author: cfn <cfn@leapy.cn>
 */
class Log extends Base
{
    public function list()
    {
        $param = $this->request->only([
            'page' => 1,
            'limit' => 10,
            'startTime' => '',
            'endTime' => '',
            'title' => ''
        ]);
        $result = AccountLog::list($param);
        self::success("日志记录", $result['data'], $result['count']);
    }

    public function del()
    {

    }
}