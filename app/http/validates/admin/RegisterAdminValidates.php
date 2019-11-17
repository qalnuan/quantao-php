<?php
namespace app\http\validates\admin;


use think\Validate;

/**
 * 注册管理员验证
 * Class RegisterAdminValidates
 * @package app\http\validates\user
 */
class RegisterAdminValidates extends Validate
{
    protected $regex = [ 'phone' => '/^1[3456789]\d{9}$/'];

    protected $rule = [
        'account'  =>  'require|regex:phone',
        'captcha'  =>  'require|length:4',
        'password'  =>  'require',
    ];

    protected $message  =   [
        'account.require'   =>  '手机号必须填写',
        'account.regex'     =>  '手机号格式错误',
        'captcha.require'   =>  '验证码必须填写',
        'captcha.length'    =>  '验证码不能超过4个字符',
        'password.require'  =>  '密码必须填写',
    ];


    public function sceneRegister()
    {
        return $this->only(['account','captcha','password']);
    }
}