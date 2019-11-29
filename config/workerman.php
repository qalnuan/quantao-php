<?php

return [
    'admin' => [
        //协议
        'protocol' => 'websocket',
        //监听地址
        'ip' => '0.0.0.0',
        //监听端口
        'port' => 39002,
        //设置当前Worker实例启动多少个进程
        'serverCount' => 1,
    ],

    'chat' => [
        //协议
        'protocol' => 'websocket',
        //监听地址
        'ip' => '0.0.0.0',
        //监听端口
        'port' => 39003,
        //设置当前Worker实例启动多少个进程
        'serverCount' => 1,
    ],

    'channel' => [
        //内部通讯监听端口
        'port' => 39012,
        //内部通讯地址
        'ip' => '172.19.227.5',
    ],

];