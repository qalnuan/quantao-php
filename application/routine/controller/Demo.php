<?php
namespace app\routine\controller;

use app\routine\model\store\StoreBargain;
use app\routine\model\store\StoreBargainUser;
use app\routine\model\store\StoreBargainUserHelp;
use service\JsonService;

class Demo{

    /**
     * 获取砍价列表
     * @return \think\response\Json
     */
    public function get_bargain_list(){
        $bargain = StoreBargain::getList();
        $bargain = StoreBargainUser::getUserList($bargain);
        return JsonService::successful($bargain);
    }

    /**
     * 砍价详情
     * @param int $bargainId
     * @return \think\response\Json
     */
    public function get_bargain($bargainId = 1){
        if(!$bargainId) return JsonService::fail('参数错误');
        $bargain = StoreBargain::getBargainTerm($bargainId);
        dump($bargain);
    }

    /**
     * 获取砍价帮
     * @param int $bargainId
     */
    public function get_bargain_user($bargainId = 1,$uid = 100){
        if(!$bargainId || !$uid) return JsonService::fail('参数错误');
        $bargainUserId = StoreBargainUser::setUserBargain($bargainId,$uid);
        $storeBargainUserHelp = StoreBargainUserHelp::getList($bargainUserId);
        dump($bargainUserId);
        dump($storeBargainUserHelp);
    }

    public function set_bargain($bargainId = 1){
        if(!$bargainId) return JsonService::fail('参数错误');
        $uid = 100;
//        $uid = $this->userInfo['uid'];
        $res = StoreBargainUser::setBargain($bargainId,$uid);
        dump($res);
    }

}