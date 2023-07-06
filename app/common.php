<?php
// 应用公共文件

if (!function_exists("camelize")) {
    /**
     * 下划线转驼峰
     * Author: cfn <cfn@leapy.cn>
     * @param string $uncamelized_words
     * @param string $separator
     * @return string
     */
    function camelize(string $uncamelized_words, string $separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }
}


if (!function_exists("uncamelize")) {
    /**
     * 驼峰命名转下划线命名
     * Author: cfn <cfn@leapy.cn>
     * @param string $camelCaps
     * @param $separator
     * @return
     */
    function uncamelize(string $camelCaps, string $separator='_')
    {
        if (is_array($camelCaps)) {
            $_arr = array();
            foreach ($camelCaps as $k => $v) {

            }
        }
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}


if (!function_exists("arrayUncamelize")) {
    /**
     * 驼峰命名转下划线命名
     * Author: cfn <cfn@leapy.cn>
     * @param array $camelCaps
     * @param string $separator
     * @return array
     */
    function arrayUncamelize(array $camelCaps, string $separator='_')
    {
        $_arr = array();
        foreach ($camelCaps as $k => $v) {
            $_arr[uncamelize($k)] = $v;
        }
        return $_arr;
    }
}