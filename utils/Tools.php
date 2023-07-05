<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */
namespace utils;

class Tools
{
    // 操作成功
    const CODE_SUCCESS = 200;
    // 操作失败
    const CODE_FAIL = 400;
    // 登录失效
    const CODE_INVALID = 401;
    // 权限不足
    const CODE_NOAUTH = 403;
    // 服务异常
    const CODE_ERROR = 500;

    /**
     * 获取uuid
     * Author: cfn <cfn@leapy.cn>
     * @return string
     */
    static function uuid(): string
    {
        $chars = md5(uniqid(mt_rand(), true));
        return substr($chars, 0, 8)
            . substr($chars, 8, 4)
            . substr($chars, 12, 4)
            . substr($chars, 16, 4)
            . substr($chars, 20, 12);
    }

    /**
     * 获取随机字符串
     * Author: cfn <cfn@leapy.cn>
     * @param $len
     * @return string
     */
    static function randomString($len): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = strlen($characters);
        $nonce_str = '';
        for ($i = 0; $i < $len; $i++) {
            $nonce_str .= $characters[rand(0, $length - 1)];
        }
        return $nonce_str;
    }

    /**
     * 创建token
     * Author: cfn <cfn@leapy.cn>
     * @return string
     */
    static function makeToken(): string
    {
        return md5(self::uuid() . time() . rand(10000000, 99999999) . config("app.secret") . self::randomString(8));
    }
}