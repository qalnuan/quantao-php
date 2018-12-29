<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/23
 */

namespace app\routine\model\user;


use basic\ModelBasic;
use traits\ModelTrait;

class UserEnter extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function getCharterAttr($value)
    {
        return json_decode($value,true)?:[];
    }

    protected function setCharterAttr($value)
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function setEnter($data,$uid)
    {
        $data['uid'] = $uid;
        $data['apply_time'] = time();
        return self::set($data);
    }

    public static function editEvent($data,$uid)
    {
        unset($data['uid']);
        unset($data['id']);
        $data['apply_time'] = time();
        $data['status'] = 0;
        return self::edit($data,$uid,'uid');
    }

}