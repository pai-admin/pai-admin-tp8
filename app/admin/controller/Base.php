<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

namespace app\admin\controller;

use app\admin\model\AccountLog;
use app\admin\model\Menu;
use app\BaseController;
use think\exception\HttpResponseException;
use think\facade\Cache;
use utils\Tools;

class Base extends BaseController
{
    /**
     * 需要登录的接口
     * @var array
     * Author: cfn <cfn@leapy.cn>
     */
    protected $noNeedLogin = [];

    /**
     * 需要权限的接口
     * @var array
     * Author: cfn <cfn@leapy.cn>
     */
    protected $noNeedAuth = [];

    protected $account = [
        'accountId' => 0,
        'username' => ''
    ];

    protected function initialize()
    {
        parent::initialize();
        // 检查权限
        $this->checkAuth();
    }

    /**
     * 鉴权
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    private function checkAuth()
    {
        // 先检查登录状况
        if ($this->noNeedLogin == "*" || $this->noNeedLogin == ['*'] || in_array($this->action, $this->noNeedLogin)) {
            return;
        }
        $token = $this->request->header("Authorization", "");
        if (!$token) self::result(Tools::CODE_INVALID, "登录已过期");
        $account = Cache::store("redis")->get(config("app.token_key") . $token);
        if (empty($account)) self::result(Tools::CODE_INVALID, "登录已过期");
        // 续签
        Cache::store("redis")->set(config("app.token_key") . $token, $account, config("app.token_ttl"));
        $this->account = $account;
        // 鉴权操作
        if ($this->noNeedAuth == "*" || $this->noNeedAuth == ['*'] || in_array($this->method, $this->noNeedAuth)) {
            return;
        }
        // 验证权限
        $flag = strtolower(uncamelize($this->method . ":" . $this->module . ":" . $this->controller . ":" . $this->action));
        $auth = Cache::store("redis")->get(config("app.token_key") . "AUTH:" . $account['accountId']);
        if (!$auth || !in_array($flag, $auth)) {
            self::result(Tools::CODE_NOAUTH, "权限不足");
        }
    }

    /**
     * @param string $msg
     * @param array|null $data
     * @param int|null $count
     */
    public function success(string $msg = '操作成功', array $data = null, int $count = null)
    {
        self::result(Tools::CODE_SUCCESS, $msg, $data, $count);
    }

    /**
     * Author: cfn <cfn@leapy.cn>
     * @param string $msg
     * @return void
     */
    public function fail(string $msg = '操作失败')
    {
        self::result(Tools::CODE_FAIL, $msg);
    }

    /**
     * @param string $msg
     */
    public function error(string $msg = '服务异常')
    {
        self::result(Tools::CODE_ERROR, $msg);
    }

    /**
     * @param int $code
     * @param string $msg
     * @param array|null $data
     * @param int|null $count
     * 接口返回
     */
    public function result(int $code, string $msg, array $data = null, int $count = null)
    {
        $ret = compact("code", "msg");
        $data !== null && $ret['data'] = $data;
        $count !== null && $ret['count'] = $count;
        // 日志记录
        self::writeLog($ret);
        // 返回数据
        throw new HttpResponseException(\think\Response::create($ret, 'json'));
    }

    // 写日志
    private function writeLog(array $ret)
    {
        // 不登录不记录日志
        if ($this->noNeedLogin == "*" || $this->noNeedLogin == ['*'] || in_array($this->action, $this->noNeedLogin)) {
            return;
        }
        // 不鉴权不记录日志
        if ($this->noNeedAuth == "*" || $this->noNeedAuth == ['*'] || in_array($this->method, $this->noNeedAuth)) {
            return;
        }
        // 获取操作信息
        $auth = strtolower(uncamelize($this->module . ":" . $this->controller . ":" . $this->action));
        $title = Menu::getAuthTitle(strtolower($this->method . ":" . $auth));
        AccountLog::writeLog($this->account['accountId'], $this->account['username'], $title, strtoupper($this->method),
            $auth, $ret['code'], json_encode($ret, JSON_UNESCAPED_UNICODE));
    }
}