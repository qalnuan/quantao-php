<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/21
 */

namespace app\wap\model\user;

use basic\ModelBasic;
use traits\ModelTrait;
use service\CacheService as Cache;
/**
 * 微信用户model
 * Class WechatUser
 * @package app\routine\model\user
 */
class WechatUser extends ModelBasic
{
    use ModelTrait;

    public static function getOpenId($uid = ''){
        if($uid == '') return false;
        $userType = self::where('uid',$uid)->value('user_type');
        if($userType == 'routine') return self::where('uid',$uid)->value('routine_openid');
        else return self::where('uid',$uid)->value('openid');
    }
    /**
     * 用uid获得openid
     * @param $uid
     * @return mixed
     */
    public static function uidToOpenid($uid,$update = false)
    {
        $cacheName = 'openid_'.$uid;
        $openid = Cache::get($cacheName);
        if($openid && !$update) return $openid;
        $openid = self::where('uid',$uid)->value('openid');
        if(!$openid) exception('对应的openid不存在!');
        Cache::set($cacheName,$openid,0);
        return $openid;
    }
}