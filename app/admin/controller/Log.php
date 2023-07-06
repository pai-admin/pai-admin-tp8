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
        /**
         * 日志列表
         */
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

    /**
     * 删除日志
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function del()
    {
        $ids = $this->request->param("ids");
        $res = AccountLog::whereIn('log_id', $ids)->delete();
        $res ? self::success('删除成功') : self::fail("删除失败");
    }
}