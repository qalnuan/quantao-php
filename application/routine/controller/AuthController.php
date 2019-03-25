<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/11
 */

namespace app\routine\controller;

use app\routine\model\user\RoutineUser;
use app\routine\model\user\User;
use service\JsonService;
use think\Controller;
use think\Request;

class AuthController extends Controller
{
    public $userInfo = [];

    protected function _initialize()
    {
        parent::_initialize();
        $uid = Request::instance()->get('uid', 0);
        // $uid = 14;
        if (RoutineUser::isRoutineUser($uid)) {
            $userInfo = RoutineUser::getRoutineUser($uid);
            if ($userInfo) {
              $userInfo->toArray();
              $this->userInfo = $userInfo; //根据uid获取用户信息
            } else {
                return JsonService::fail('没有获取用户UID' . $uid);
            }
        } else {
            $userInfo = User::get($uid);
            if ($userInfo) {
                $userInfo->toArray();
                $this->userInfo = $userInfo; //根据uid获取用户信息
            } else {
                return JsonService::fail('没有获取用户UID' . $uid);
            }

        }
    }
}
