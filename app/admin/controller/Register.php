<?php

namespace app\admin\controller;

use app\admin\model\system\SystemAdmin;
use app\http\validates\admin\RegisterAdminValidates;
use crmeb\services\UtilService;
use think\facade\Route as Url;

/**
 * 登录验证控制器
 * Class Login
 * @package app\admin\controller
 */
class Register extends SystemBasic
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 登录验证 + 验证码验证
     */
    public function register()
    {
        if (!request()->isPost()) {
            return $this->failed('请登陆!');
        }

        list($account, $real_name, $pwd, $repwd, $verify) = UtilService::postMore([
            'account', 'real_name', 'pwd', 'repwd', 'verify',
        ], null, true);
        //检验验证码
        if (!captcha_check($verify)) {
            return $this->failed('验证码错误，请重新输入');
        }

        if ($pwd !== $repwd) {
            return $this->failed('确认密码与密码不一致，请重新输入');
        }
        try {
            validate(RegisterAdminValidates::class)->scene('register')->check(['account' => $account, 'captcha' => $verify, 'password' => $pwd]);
        } catch (ValidateException $e) {
            return $this->failed($e->getError());
        }
        //检验帐号密码
        $res = SystemAdmin::register($account, $real_name, $pwd);
        if ($res) {
            return $this->successful("商户创建成功，请耐心等待管理员审核！", Url::buildUrl('Index/index'));
        } else {
            return $this->failed(SystemAdmin::getErrorInfo('用户名错误，请重新输入'));
        }
    }

    public function captcha()
    {
        ob_clean();
        return captcha();
    }
}
