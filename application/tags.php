<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用行为扩展定义文件
return [
    // 应用初始化
    'app_init'     => [],
    // 应用开始
    'app_begin'    => [],
    // 模块初始化
    'module_init'  => [],
    // 操作开始执行
    'action_begin' => [],
    // 视图内容过滤
    'view_filter'  => [],
    // 日志写入
    'log_write'    => [],
    // 应用结束
    'app_end'      => [],
    //添加素材
    'wechat_material_after' =>[
        \behavior\wechat\MaterialBehavior::class
    ],
    //添加临时素材
    'wechat_material_temporary_after'=>[
        \behavior\wechat\MaterialBehavior::class
    ],
//    //微信菜单点击事件
//    'wecaht_event_click'=>\behavior\wechat\MessageBehavior::class,
//    //微信菜单点击前置操作
//    'wechat_event_click_before'=>[
//        \behavior\wechat\MessageBehavior::class,
//    ],
//    //微信收到用户文字信息事件
//    'wecaht_message_text'=>\behavior\wechat\MessageBehavior::class,
//    //微信关注事件
//    'wecaht_event_subscribe'=>\behavior\wechat\MessageBehavior::class
];
