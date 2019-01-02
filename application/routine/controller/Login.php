<?php

namespace app\routine\controller;

use app\admin\model\system\SystemConfig;
use app\routine\model\routine\RoutineServer;
use app\routine\model\user\RoutineUser;
use service\JsonService;
use service\UtilService;
use think\Controller;
use think\Request;
use app\routine\model\store\StoreBargainUser;

use service\GroupDataService;
use service\SystemConfigService;
use app\routine\model\store\StoreProduct;
use app\routine\model\store\StoreSeckill;
use app\routine\model\store\StoreCombination;

class Login extends Controller{


    public function test(){
        $bargain = StoreBargain::getList();
        $bargain = StoreBargainUser::getUserList($bargain);
        return JsonService::successful($bargain);
    }

    public function get_home_info(){
        $banner = GroupDataService::getData('store_home_banner')?:[];//banner图
        $pinkbanner = SystemConfigService::get('collage_banner')?:'';//banner图
        $secbanner = SystemConfigService::get('seckill_banner')?:'';//banner图
        $newbanner = SystemConfigService::get('index_new_banner')?:'';//banner图
        $lovely = GroupDataService::getData('routine_lovely')?:[];//猜你喜欢图
        $best = StoreProduct::getBestProduct('id,image,store_name,cate_id,price,unit_name,sort',8);//精品推荐
        $new = StoreProduct::getNewProduct('id,image,store_name,cate_id,price,unit_name,sort',10);//今日上新
        $hot = StoreProduct::getHotProduct('id,image,store_name,cate_id,price,unit_name,sort',6);//猜你喜欢
        $pink = StoreCombination::getCombinationBest(6);//拼团商品
        $seckill = StoreSeckill::getHotList(6);//秒杀商品
        $data['banner'] = $banner;
        $data['lovely'] = $lovely;
        $data['pinkbanner'] = $pinkbanner;
        $data['secbanner'] = $secbanner;
        $data['newbanner'] = $newbanner;
        $data['best'] = $best;
        $data['new'] = $new;
        $data['hot'] = $hot;
        $data['pink'] = $pink;
        $data['seckill'] = $seckill;
        return JsonService::successful($data);
    }

    /**
     * 获取用户信息
     * @param Request $request
     * @return \think\response\Json
     */

    public function index(Request $request){
        $data = UtilService::postMore([['info',[]]],$request);//获取前台传的code
        $data = $data['info'];
        unset($data['info']);
        $res = $this->setCode($data['code']);
        if(!isset($res['openid'])) return JsonService::fail('openid获取失败');
        if(isset($res['unionid'])) $data['unionid'] = $res['unionid'];
        else $data['unionid'] = '';
        $data['routine_openid'] = $res['openid'];
        $data['session_key'] = $res['session_key'];
        $data['uid'] = RoutineUser::routineOauth($data);
        return JsonService::successful($data);
    }

    /**
     * 根据前台传code  获取 openid 和  session_key //会话密匙
     * @param string $code
     * @return array|mixed
     */
    public function setCode($code = ''){
        if($code == '') return [];
        $routineAppId = SystemConfig::getValue('routine_appId');//小程序appID
        $routineAppSecret = SystemConfig::getValue('routine_appsecret');//小程序AppSecret
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$routineAppId.'&secret='.$routineAppSecret.'&js_code='.$code.'&grant_type=authorization_code';
        return json_decode(RoutineServer::curlGet($url),true);
    }

    /**
     * 获取网站logo
     */
    public function get_enter_logo(){
        $siteLogo = SystemConfig::getValue('routine_authorize');
        $siteName = SystemConfig::getValue('site_name');
        $data['site_logo'] = $siteLogo;
        $data['site_name'] = $siteName;
        return JsonService::successful($data);
    }
}