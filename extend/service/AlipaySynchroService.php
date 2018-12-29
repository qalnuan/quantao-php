<?php
namespace service;

/**
 * 支付宝扫码同步回调
 * Class AlipaySynchroService
 * @package service
 *
 * 调用实例
 *
 * //支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
    $alipayPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnmhmNDw0GonAjEle3iTNBWYU80z2o/A5+hYTB5P/oXqnu9747+IouyNQeFm8x7ri1wEemk7i/Cma/FSzVzbXFlClSidlVagw1Za7pg11fk2k/tSn9HDFPfcicJ0u2QN4AWGZQYVd/KQBYFKA4i0P//YXlDQEGELcSLznU9yAs7ZAQITaHfCuyMvepW5DDEJkErNDVVlI0Y5m8R00UMa76sQIG0p7s6Byj3eZXYiXclry3q6URfFPwW6N0BP2KsDHNO19v+HMi13zDtM9I/71dE02NQ9mQdYiVSCkNy/nNQh9GL2J02uWnyOa+Zza/aensTSmortvoArLwqOFH+GSbQIDAQAB';
    $aliPay = new AlipaySynchroService($alipayPublicKey);//实例化支付宝扫码同步回调类
    //验证签名
    $arr = $_GET;//获取支付宝返回的数据
    $result = $aliPay->rsaCheck($arr);// true 为成功  false  为失败
 */
class AlipaySynchroService{
    //支付宝公钥
    protected $alipayPublicKey;
    protected $charset;
    public function __construct($alipayPublicKey)
    {
        $this->charset = 'UTF-8';
        $this->alipayPublicKey=$alipayPublicKey;
    }

//    public function rsaCheck($params,$signType='RSA2') {
//        $sign = $params['sign'];
//        $params['sign_type'] = null;
//        $params['sign'] = null;
//        return $this->verify($this->getSignContent($params), $sign,$signType);
//    }
//
//    public function getSignContent($params) {
//        ksort($params);
//
//        $stringToBeSigned = "";
//        $i = 0;
//        foreach ($params as $k => $v) {
//            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
//
//                // 转换成目标字符集
//                $v = $this->characet($v, $this->charset);
//
//                if ($i == 0) {
//                    $stringToBeSigned .= "$k" . "=" . "$v";
//                } else {
//                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
//                }
//                $i++;
//            }
//        }
//
//        unset ($k, $v);
//        return $stringToBeSigned;
//    }
//
//    function verify($data, $sign,  $signType = 'RSA') {
//        $pubKey= $this->alipayPublicKey;
//        $res = "-----BEGIN PUBLIC KEY-----\n" .
//            wordwrap($pubKey, 64, "\n", true) .
//            "\n-----END PUBLIC KEY-----";
//
//        //调用openssl内置方法验签，返回bool值
//        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
//
//        //调用openssl内置方法验签，返回bool值
//
//        if ("RSA2" == $signType) {
//            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
//        } else {
//            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
//        }
////        if ("RSA2" == $signType) {
////            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
////        } else {
////            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
////        }
//
//        return $result;
//    }


    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }





    /**
     *  验证签名
     **/
    public function rsaCheck($params) {
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }
    function verify($data, $sign, $signType = 'RSA') {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($this->alipayPublicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值


        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
//        if(!$this->checkEmpty($this->alipayPublicKey)) {
//            //释放资源
//            openssl_free_key($res);
//        }
        return $result;
    }


    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }
    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }
}
