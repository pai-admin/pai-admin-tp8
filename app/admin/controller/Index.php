<?php

namespace app\admin\controller;

use app\admin\model\Account;
use app\admin\model\AccountLog;
use app\admin\validate\EditPwdReq;
use MathCaptcha\Captcha;
use think\facade\Cache;
use think\facade\Filesystem;
use utils\Tools;
use app\admin\validate\LoginReq;

/**
 * Author: cfn <cfn@leapy.cn>
 */
class Index extends Base
{
    protected $noNeedLogin = ["getCode", "login"];
    protected $noNeedAuth = ['*'];

    /**
     * 获取验证码
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function getCode()
    {
        // 生成验证码
        $ca = new Captcha();
        $code = $ca->setDigits(1)->setPoint(100)->setLine(2)->setFontSize(24)->result();
        $image = $ca->base64();
        $uuid = Tools::uuid();
        // 验证码缓存
        Cache::store("redis")->set(config("app.token_key") . $uuid, md5($code), config("app.verify_ttl"));
        self::success("admin", ["verifyId" => $uuid, "base64Content" => $image]);
    }

    /**
     * 登录接口
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function login()
    {
        $param = $this->request->only([
            'verifyId' => '',
            'verifyCode' => '',
            'username' => '',
            'password' => ''
        ]);
        // 验证登录
        validate(LoginReq::class)->scene("login")->check($param);
        // 验证码验证
        $code = Cache::store("redis")->get(config("app.token_key") . $param['verifyId']);
        if (empty($code)) self::fail("验证码已过期");
        if (md5($param['verifyCode']) != $code) self::fail("验证码不正确");
        // 验证一次
        Cache::store("redis")->delete(config("app.token_key") . $param['verifyId']);
        // 查询账号
        $account = Account::getByUsername($param['username'], "account_id,username,password,salt,status");
        if (empty($account)) self::fail("用户名或者密码错误");
        if (md5($param['password'] . $account['salt']) != $account['password']) self::fail("用户名或者密码错误");
        if ($account['status'] != 1) self::fail("账号已停用");
        // 生成token
        $token = Tools::makeToken();
        Cache::store("redis")->set(config("app.token_key") . $token, $account, config("app.token_ttl"));
        // 登录记录日志
        AccountLog::writeLog($account['accountId'], $account['username'], "管理员登录", $this->method, "login", 200);
        // 缓存权限
        $apis = Account::getApis($account['accountId']);
        Cache::store("redis")->set(config("app.token_key") . "AUTH:" . $account['accountId'], $apis);
        self::success('登录成功', ['token' => $token]);
    }

    /**
     * 账号信息
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function info()
    {
        $info = Account::getInfo($this->account['accountId']);
        self::success('账号信息', $info);
    }

    /**
     * 查询菜单权限
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function auth()
    {
        $buttons = Account::getButtons($this->account['accountId']);
        $menus = Account::getMenus($this->account['accountId']);
        $roles = Account::getRoles($this->account['accountId']);
        self::success('权限信息', compact("buttons", "menus", "roles"));
    }

    /**
     * 退出成功
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function logout()
    {
        $token = $this->request->header("Authorization");
        Cache::store("redis")->delete(config("app.token_key") . $token);
        self::success("退出成功");
    }

    /**
     * 修改密码
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function editPwd()
    {
        $param = $this->request->only([
            'newPassword' => '',
            'oldPassword' => ''
        ]);
        validate(EditPwdReq::class)->check($param);
        if ($param['newPassword'] == $param['oldPassword']) self::fail("新旧密码不能相同");
        // 验证原密码
        $account = Account::getInfoById($this->account['accountId'], "password,salt,account_id");
        if ($account['password'] != md5($param['oldPassword'] . $account['salt'])) {
            self::fail("原密码不正确");
        }
        $res = Account::changePwd($this->account['accountId'], $param['newPassword']);
        $res ? self::success("修改成") : self::fail("修改失败");
    }

    /**
     * 修改个人信息
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function editInfo()
    {
        $param = $this->request->only([
            'avatar' => ''
        ]);
        $param['update_time'] = date("Y-m-d H:i:s");
        $res = Account::where("account_id", $this->account['accountId'])
            ->where("del_flag", 0)
            ->update($param);
        $res ? self::success("修改成功") : self::fail("修改失败");
    }

    /**
     * 个人日志
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function myLog()
    {
        $param = $this->request->only([
            'page' => 1,
            'limit' => 10
        ]);
        $param['accountId'] = $this->account['accountId'];
        $result = AccountLog::getLog($param);
        self::success("个人日志", $result['data'], $result['count']);
    }

    /**
     * 文件上传
     * Author: cfn <cfn@leapy.cn>
     * @return void
     */
    public function upload()
    {
        // 获取表单上传文件
        $file = request()->file("file");
        $size = $file->getSize();
        $path = "upload" . DIRECTORY_SEPARATOR . date("Y") . DIRECTORY_SEPARATOR . date("m") . DIRECTORY_SEPARATOR . date("d");
        $name = md5(md5_file($file) . time()) . "." . $file->getOriginalExtension();
        $path = Filesystem::disk('public')->putFileAs($path, $file, $name);
        // 上传到本地服务器
        self::success('文件上传', compact("size", "name", "path"));
    }
}
