<?php
/**
 * Created by PhpStorm.
 * User: liying
 * Date: 2018/5/24
 * Time: 10:58
 */

namespace app\admin\controller\system;


use app\admin\controller\AuthController;
use app\admin\model\user\User;
use app\admin\model\wechat\WechatUser;
use service\JsonService as Json;
use think\db;
use app\admin\controller\system\Clear;
class SystemCleardata  extends AuthController
{
  public function index(){

      return $this->fetch();
  }
  
    //  清除用户相关信息	 截断表
    public function UserRelevant(){
        SystemCleardata::ClearData('eb_user_recharge',1);
        SystemCleardata::ClearData('eb_user_address',1);
        SystemCleardata::ClearData('eb_user_bill',1);
        SystemCleardata::ClearData('eb_user_enter',1);
        SystemCleardata::ClearData('eb_user_extract',1);
        SystemCleardata::ClearData('eb_user_notice',1);
        SystemCleardata::ClearData('eb_user_notice_see',1);
        SystemCleardata::ClearData('eb_wechat_qrcode',1);
        SystemCleardata::ClearData('eb_wechat_message',1);
        SystemCleardata::ClearData('eb_store_coupon_user',1);
        SystemCleardata::ClearData('eb_store_coupon_issue_user',1);
        SystemCleardata::ClearData('eb_store_bargain_user',1);
        SystemCleardata::ClearData('eb_store_bargain_user_help',1);
        SystemCleardata::ClearData('eb_store_product_reply',1);
        $this->delDirAndFile('./public/uploads/store/comment');
        SystemCleardata::ClearData('eb_store_product_relation',1);
        return Json::successful('清除数据成功!');
    }
	//清除商品信息  截断表
    public function  storedata(){
        SystemCleardata::ClearData('eb_store_coupon',1);
        SystemCleardata::ClearData('eb_routine_form_id',1);
        SystemCleardata::ClearData('eb_routine_access_token',1);
        SystemCleardata::ClearData('eb_store_coupon_issue',1);
        SystemCleardata::ClearData('eb_store_bargain',1);
        SystemCleardata::ClearData('eb_store_combination',1);
        SystemCleardata::ClearData('eb_store_product_attr',1);
        SystemCleardata::ClearData('eb_store_product_attr_result',1);
        SystemCleardata::ClearData('eb_store_product_attr_value',1);
        SystemCleardata::ClearData('eb_store_seckill',1);
        SystemCleardata::ClearData('eb_store_product',1);
        SystemCleardata::ClearData('eb_store_visit',1);
        $this->delDirAndFile('./public/uploads/store/product');

        return Json::successful('清除数据成功!');
    }
	
	//清除商品分类  截断表
    public function categorydata(){
        SystemCleardata::ClearData('eb_store_category',1);
        $this->delDirAndFile('./public/uploads/store/product');
        return Json::successful('清除数据成功!');
    }
	
	//  清除订单  截断表
    public function orderdata(){
        SystemCleardata::ClearData('eb_store_order',1);
        SystemCleardata::ClearData('eb_store_order_cart_info',1);
        SystemCleardata::ClearData('eb_store_order_copy',1);
        SystemCleardata::ClearData('eb_store_order_status',1);
        SystemCleardata::ClearData('eb_store_pink',1);
        SystemCleardata::ClearData('eb_store_cart',1);
        return Json::successful('清除数据成功!');
    }
	
	//清除客服表 截断表
    public function kefudata(){
        SystemCleardata::ClearData('eb_store_service',1);
        $this->delDirAndFile('./public/uploads/store/service');
        SystemCleardata::ClearData('eb_store_service_log',1);
        return Json::successful('清除数据成功!');
    }

	//清除用户  截断表
    public function userdate(){
       SystemCleardata::ClearData('eb_user',1);
        $headimgurl= WechatUser::Where('uid',1)->value('headimgurl');
        $data['account']='crmeb';
        $data['pwd']=md5(123456);
        $data['avatar']=$headimgurl;
        $data['add_time']=time();
        $data['status']=1;
        $data['level']=0;
        $data['user_type']="wechat";
        $data['is_promoter']=1;
//        User::create($data);
        return Json::successful('清除数据成功!');
    }
	
	//清除微信相关信息  截断表
    public function wechatdata(){
        SystemCleardata::ClearData('eb_wechat_media',1);
        SystemCleardata::ClearData('eb_wechat_reply',1);
        SystemCleardata::ClearData('eb_wechat_news_content',1);
        SystemCleardata::ClearData('eb_wechat_news',1);
        SystemCleardata::ClearData('eb_wechat_news_category',1);
       $this->delDirAndFile('./public/uploads/wechat');
        return Json::successful('清除数据成功!');
    }
	
	//清除所有上传文件 
    public function uploaddata(){
        $this->delDirAndFile('./public/uploads');
        return Json::successful('清除上传文件成功!');
    }
	
	//清除微信用户  截断表
    public function  wechatuserdata(){
        $data= WechatUser::get(1)->toArray();
        SystemCleardata::ClearData('eb_wechat_user',1);
        unset($data['uid']);
//        WechatUser::set($data);
        return Json::successful('清除数据成功!');
    }
	
	//清除文章分类和上传文件分类图片   截断表
    public function articledata(){
        SystemCleardata::ClearData('eb_article_category',1);
        $this->delDirAndFile('./public/uploads/article/');
        return Json::successful('清除数据成功!');
    }
	
	//表截断和删除   table_name 表名 status  状态  1 截断 0 删除
    public  function  ClearData($table_name = '',$status = 0){
        try{
            if($status){
                db::query('TRUNCATE TABLE '.$table_name);
            }else{
                db::query('DELETE FROM'.$table_name);
            }
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
	
	//删除文件
    function delDirAndFile($dirName = '',$subdir=true){
//        if ($handle = opendir("$dirName")){
//            while(false !== ($item = readdir($handle))){
//                if($item != "." && $item != ".."){
//                    if(is_dir("$dirName/$item"))
//                        $this->delDirAndFile("$dirName/$item",false);
//                    else
//                        @unlink("$dirName/$item");
//                }
//            }
//            closedir($handle);
//            if(!$subdir) @rmdir($dirName);
//        }
    }
}