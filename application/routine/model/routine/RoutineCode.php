<?php
namespace  app\routine\model\routine;


class RoutineCode{

    /**
     * 获取分销二维码
     * @param int $uid  yonghuID
     * @param array $color 二维码线条颜色
     * @return mixed
     */
    public static function getCode($uid = 0,$color = array()){
        $accessToken = RoutineServer::get_access_token();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$accessToken;
        if($uid) $data['scene'] = $uid;
        else $data['scene'] = 0;
        if(empty($color)){
            $color['r'] = 0;
            $color['g'] = 0;
            $color['b'] = 0;
        }
        $data['page'] = '';
        $data['width'] = 430;
        $data['auto_color'] = false;
        $data['line_color'] = $color;
        $data['is_hyaline'] = false;
        return RoutineServer::curlPost($url,json_encode($data));
    }
}