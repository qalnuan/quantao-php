<?php
namespace app\admin\model\wechat;

use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use app\admin\model\system\SystemAdmin;

/**
 * 客服管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class StoreVerifyService extends BaseModel
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'store_verify_service';

    use ModelTrait;

    /**
     * @param $mer_id
     * @return array
     */
    public static function getList($mer_id){
        return self::page(self::where('mer_id',$mer_id)->order('id desc'),function($item){
            $item['wx_name']=WechatUser::where(['uid'=>$item['uid']])->value('nickname');
        });
    }

    /**
     * @param $mer_id
     * @return array
     */
    public static function getALLList(){
        return self::page(self::order('id desc'),function($item){
            $item['wx_name']=WechatUser::where(['uid'=>$item['uid']])->value('nickname');
            $item['mer_name']=SystemAdmin::where(['id'=>$item['mer_id']])->value('real_name');
        });
    }
}