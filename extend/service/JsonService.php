<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/24
 */

namespace service;


class JsonService
{
    private static $SUCCESSFUL_DEFAULT_MSG = 'ok';

    private static $FAIL_DEFAULT_MSG = 'no';

    public static function result($code,$msg='',$data=[])
    {
        exit(json_encode(compact('code','msg','data')));
    }

    public static function successful($msg = 'ok',$data=[])
    {
        if(false == is_string($msg)){
            $data = $msg;
            $msg = self::$SUCCESSFUL_DEFAULT_MSG;
        }
        return self::result(200,$msg,$data);
    }

    public static function status($status,$msg,$result = [])
    {
        $status = strtoupper($status);
        if(true == is_array($msg)){
            $result = $msg;
            $msg = self::$SUCCESSFUL_DEFAULT_MSG;
        }
        return self::result(200,$msg,compact('status','result'));
    }

    public static function fail($msg,$data=[])
    {
        if(true == is_array($msg)){
            $data = $msg;
            $msg = self::$FAIL_DEFAULT_MSG;
        }
        return self::result(400,$msg,$data);
    }

}