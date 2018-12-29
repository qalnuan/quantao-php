<?php
namespace app\admin\controller;

use think\Controller;
use service\UpgradeApi;
use service\UpgradeService;
use service\JsonService as Json;
use think\Request;
use service\HookService;

class Upgrade extends Controller
{
    private $num=1000;

    public function now_version(){
        $request=Request::instance();
        $ip=$request->ip();
        if($num=UpgradeApi::getNum()){
            if($num>$this->num){
                return Json::fail('今日访问次数超出限制');
            }else{
                UpgradeApi::where('ip',$ip)->setInc('num');
                UpgradeApi::where('ip',$ip)->update(['add_time'=>time()]);
            }
        }else{
            UpgradeApi::create(['ip'=>$ip,'add_time'=>time(),'type'=>'select','num'=>1]);
        }
        HookService::afterListen('update_upgrade_success',['ip'=>$ip,'add_time'=>date('Y-m-d H:i:s',time())],$ip,false,Upgrade::class);
        return Json::successful(['version'=>UpgradeApi::getNowVersion()]);
    }
}