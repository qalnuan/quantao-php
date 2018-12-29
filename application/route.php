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
use \think\Route;

//function resource($name,$controller){
//    Route::rule('/'.$name.'/attr','admin/'.$controller.'/attribute','post');
//    Route::rule('/'.$name.'/save','admin/'.$controller.'/save','post');
//    Route::rule('/'.$name.'/rules','admin/'.$controller.'/rules','post');
//    Route::rule('/'.$name.'/create','admin/'.$controller.'/create','get');
//    Route::rule('/'.$name.'/page','admin/'.$controller.'/page','get');
//    Route::rule('/'.$name.'/:id/edit','admin/'.$controller.'/edit','get');
//    Route::rule('/'.$name.'/:id','admin/'.$controller.'/delete','delete');
//    Route::rule('/'.$name.'/:id','admin/'.$controller.'/update','put');
//    Route::rule('/'.$name.'/:id','admin/'.$controller.'/read','get');
//    Route::rule('/'.$name,'admin/'.$controller.'/index','get');
//    Route::alias($name,'\app\admin\controller\User'.$controller);
//}

Route::group('admin',function(){
    Route::rule('/index2','admin/Index/index2','get');
//    Route::controller('index','admin/Index');
//    resource('system_menus','SystemMenus');
//    Route::rule('/menus','SystemMenus','get');
//    Route::resource('menus','admin/SystemMenus',['var'=>['menus'=>'menu_id']]);
//    Route::miss(function(){
//        return '页面不存在!';
//    });
});

