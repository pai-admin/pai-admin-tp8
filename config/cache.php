<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => 'file',

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // redis缓存
        'redis' => [
            // 驱动方式
            'type' => 'redis',
            // 服务器地址
            'host' => env('RD_HOST', '127.0.0.1'),
            // 端口号
            'port' => env('RD_PORT', 6379),
            // 密码
            'password' => env('RD_AUTH', ''),
            // 位置
            'select' => env('RD_DB', 2),
            // 持久化
            'persistent' => false
        ],
        // 更多的缓存连接
    ],
];
