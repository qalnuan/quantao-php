<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/18
 */

namespace app\models\store;


use crmeb\basic\BaseModel;
use crmeb\services\GroupDataService;
use crmeb\services\WechatTemplateService;
use app\models\user\WechatUser;
use app\models\routine\RoutineTemplate;
use think\facade\Route;
use think\facade\Log;
use crmeb\services\SystemConfigService;



/**
 * TODO 霸王餐产品Model
 * Class StoreDine
 * @package app\models\store
 */
class StoreDine extends BaseModel
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
    protected $name = 'store_dine';

    protected function getImagesAttr($value)
    {
        return json_decode($value,true)?:[];
    }

    public static function getDineCount()
    {
        $dineTime = GroupDataService::getData('routine_dine_time')?:[];//霸王餐时间段
        $timeInfo=['time'=>0,'continued'=>0];
        foreach($dineTime as $key=>$value){
            $currentHour = date('H');
            $activityEndHour = bcadd((int)$value['time'],(int)$value['continued'],0);
            if($currentHour >= (int)$value['time'] && $currentHour < $activityEndHour && $activityEndHour < 24){
                $timeInfo=$value;
                break;
            }
        }
        if($timeInfo['time']==0) return 0;
        $activityEndHour = bcadd((int)$timeInfo['time'],(int)$timeInfo['continued'],0);
        $startTime = bcadd(strtotime(date('Y-m-d')),bcmul($timeInfo['time'],3600,0));
        $stopTime = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
        return self::where('is_del',0)->where('status',1)->where('start_time','<=',$startTime)->where('stop_time','>=',$stopTime)->count();
    }

    /**
     * 正在开启的霸王餐活动
     * @param int $status
     * @return StoreDine
     */
    public static function validWhere($status = 1){
        return  self::where('is_del',0)->where('status',$status)->where('start_time','<',time())->where('stop_time','>',time());
    }

    /**
     * 判断霸王餐产品是否开启
     * @param int $dineId
     * @return int|string
     */
    public static function validDine($dineId = 0){
        $model = self::validWhere();
        return $dineId ? $model->where('id',$dineId)->count() : $model->count();
    }

    /**
     * 正在开启的霸王餐活动
     * @param int $status
     * @return StoreDine
     */
    public static function validRunWhere($status = 1){
        return  self::where('is_del',0)->where('status',$status)->where('stop_time','<',time())->where('isrun',0);
    }
    
    /**
     * 判断霸王餐产品是否开奖
     * @param int $dineId
     * @return int|string
     */
    public static function validRunDine($dineId = 0){
        $model = self::validRunWhere();
        return $dineId ? $model->where('id',$dineId)->count() : $model->count();
    }

    /**
     * TODO 获取正在开启的砍价产品编号
     * @return array
     */
    public static function validRunDineNumber(){
        return self::validRunWhere()->column('id');
    }
    
    /*
     * 获取霸王餐列表
     *
     * */
    public static function dineList($startTime,$stopTime,$page = 0,$limit = 20)
    {
       if($page) $list = StoreDine::where('is_del',0)->where('status',1)->where('start_time','<=',$startTime)->where('stop_time','>=',$stopTime)->order('sort desc')->page($page,$limit)->select();
       else $list = StoreDine::where('is_del',0)->where('status',1)->where('start_time','<=',$startTime)->where('stop_time','>=',$stopTime)->order('sort desc')->select();
       if($list) return $list->hidden(['cost','add_time','is_del'])->toArray();
       return [];
    }
    /**
     * 获取所有霸王餐产品
     * @param string $field
     * @return array
     */
    public static function getListAll($offset = 0,$limit = 10,$field = 'id,product_id,image,title,price,ot_price,start_time,stop_time,stock,sales'){
        $time = time();
        $model = self::where('is_del',0)->where('status',1)->where('stock','>',0)->field($field)
            ->where('start_time','<',$time)->where('stop_time','>',$time)->order('sort DESC,add_time DESC');
        $model = $model->limit($offset,$limit);
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }
    /**
     * 获取热门推荐的霸王餐产品
     * @param int $limit
     * @param string $field
     * @return array
     */
    public static function getHotList($limit = 0,$field = 'id,product_id,image,title,price,ot_price,start_time,stop_time,stock')
    {
        $time = time();
        $model = self::where('is_hot',1)->where('is_del',0)->where('status',1)->where('stock','>',0)->field($field)
            ->where('start_time','<',$time)->where('stop_time','>',$time)->order('sort DESC,add_time DESC');
        if($limit) $model->limit($limit);
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }

    /**
     * 获取一条霸王餐产品
     * @param $id
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getValidProduct($id,$field = '*')
    {
        $time = time();
        return self::where('id',$id)->where('is_del',0)->where('status',1)->where('start_time','<',$time)->where('stop_time','>',$time)
            ->field($field)->find();
    }

    public static function initFailDine()
    {
        self::where('is_hot',1)->where('is_del',0)->where('status','<>',1)->where('stop_time','<',time())->update(['status'=>'-1']);
    }

    public static function idBySimilarityDine($id,$limit = 4,$field='*')
    {
        $time = time();
        $list = [];
        $productId = self::where('id',$id)->value('product_id');
        if($productId){
            $list = array_merge($list, self::where('product_id',$productId)->where('id','<>',$id)
                ->where('is_del',0)->where('status',1)->where('stock','>',0)
                ->field($field)->where('start_time','<',$time)->where('stop_time','>',$time)
                ->order('sort DESC,add_time DESC')->limit($limit)->select()->toArray());
        }
        $limit = $limit - count($list);
        if($limit){
            $list = array_merge($list,self::getHotList($limit,$field));
        }

        return $list;
    }

    /** 获取霸王餐产品库存
     * @param $id
     * @return mixed
     */
    public static function getProductStock($id){
        return self::where('id',$id)->value('stock');
    }

    /**
     * 获取字段值
     * @param $id
     * @param string $field
     * @return mixed
     */
    public static function getProductField($id, $field = 'title')
    {
        return self::where('id',$id)->value($field);
    }

    /**
     * 修改霸王餐库存
     * @param int $num
     * @param int $dineId
     * @return bool
     */
    public static function decDineStock($num = 0,$dineId = 0){
        $res = false !== self::where('id',$dineId)->dec('stock',$num)->inc('sales',$num)->update();
        return $res;
    }

    /**
     * 修改霸王餐库存
     * @param int $num
     * @param int $dineId
     * @return bool
     */
    public static function setDineRun($dineId = 0){
        $res = false !== self::where('id',$dineId)->update(['isrun'=>1]);
        return $res;
    }

    /**
     * 增加库存较少销量
     * @param int $num
     * @param int $dineId
     * @return bool
     */
    public static function incDineStock($num = 0,$dineId = 0){
        $dine = self::where('id',$dineId)->field(['stock','sales'])->find();
        if(!$dine) return true;
        if($dine->sales > 0) $dine->sales = bcsub($dine->sales,$num,0);
        if($dine->sales < 0) $dine->sales = 0;
        $dine->stock = bcadd($dine->stock,$num,0);
        return $dine->save();
    }

    /**
     * 创建霸王餐订单
     */
    public static function createDineOrder($uid, $dineId) {
      $productId = self::where('id',$dineId)->value('product_id');
      $res = StoreCart::setCart($uid, $productId, 1, '', 'product', 0, 0, 0, 0, $dineId);
      if (!$res) return Log::info(StoreCart::getErrorInfo());
      
      Log::info("res:".json_encode($res));
      $cartGroup = StoreCart::getUserProductCartList($uid, array($res->id), 1);
      Log::info("cartGroup:".json_encode($cartGroup));
      if (count($cartGroup['invalid'])) return Log::info($cartGroup['invalid'][0]['productInfo']['store_name'] . '已失效!');
      if (!$cartGroup['valid']) return Log::info('请提交购买的商品');
      $cartInfo = $cartGroup['valid'];
      $order = StoreOrder::cacheKeyCreateDineOrder($uid, $cartInfo, 0, "weixin", 0, 0, 2);
      if (!$order) return Log::info(StoreOrder::getErrorInfo());
      Log::info("order:".json_encode($order));
      self::sendDineTemplateMessageSuccess($uid, $dineId, $order);
    }

    /**
     * 发送模板消息  成功
     * @param array $dineUidList  拼团用户编号
     * @param $dine  团长编号
     * @throws \Exception
     */
    public static function sendDineTemplateMessageSuccess($uid, $dine, $order)
    {
        Log::info("sendDineTemplateMessageSuccess bengin.");
        $openid = WechatUser::uidToOpenid($uid, 'openid');
        $routineOpenid = WechatUser::uidToOpenid($uid, 'routine_openid');
        $nickname = WechatUser::uidToOpenid($uid, 'nickname');
        if($openid){
            //公众号模板消息
            $firstWeChat = '亲，恭喜抽中霸王餐';
            $keyword1WeChat = $order->order_id;
            $keyword2WeChat = self::where('id',$dine)->value('title');
            $remarkWeChat = '点击查看订单详情';
            $siteUrl = SystemConfigService::get('site_url');
            $urlWeChat = Route::buildUrl('order/detail/'.$keyword1WeChat)->suffix('')->domain('www.taoyizuan.com')->build();
            Log::info("urlWeChat 1.".$urlWeChat);
            WechatTemplateService::sendTemplate($openid,WechatTemplateService::DINE_SUCCESS,[
                'first'=> $firstWeChat,
                'keyword1'=> $keyword1WeChat,
                'keyword2'=> $keyword2WeChat,
                'remark'=> $remarkWeChat
            ],$urlWeChat);
        }else if($routineOpenid){
            //小程序模板消息
            $keyword4Routine = self::where('id|k_id',$dine)->value('price');
            RoutineTemplate::sendOut('PINK_TRUE',$uid,[
                'keyword1'=>'亲，恭喜抽中霸王餐',
                'keyword2'=>$nickname,
                'keyword3'=>date('Y-m-d H:i:s',time()),
                'keyword4'=>$keyword4Routine
            ]);
        }
        
        Log::info("sendDineTemplateMessageSuccess end.");
    }
}