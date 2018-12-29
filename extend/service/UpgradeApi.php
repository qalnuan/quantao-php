<?php
namespace service;

use traits\ModelTrait;
use basic\ModelBasic;
use think\Request;
class UpgradeApi extends ModelBasic{
    use ModelTrait;

    protected $name="upgrade";

    public static function getNowVersion(){
        return self::order('id desc')->value('version');
    }

    public  static function getNum(){
        $request=Request::instance();
        return self::where('ip',$request->ip())->whereTime('add_time','today')->value('num');
    }

}