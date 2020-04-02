<?php
namespace app\api\controller\activity;

use app\admin\model\system\SystemAttachment;
use app\models\store\StoreProductRelation;
use app\models\store\StoreProductReply;
use app\models\store\StoreDine;
use app\models\store\StoreDineUser;
use app\Request;
use crmeb\services\GroupDataService;
use crmeb\services\SystemConfigService;
use crmeb\services\UtilService;

/**
 * 秒杀产品类
 * Class StoreDineController
 * @package app\api\controller\activity
 */
class StoreDineController
{
    /**
     * 秒杀产品时间区间
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //秒杀时间段
        $dineTime = GroupDataService::getData('routine_dine_time') ?? [];
        $dineTimeIndex = 0;
        if(count($dineTime)){
            foreach($dineTime as $key=>&$value){
                $currentHour = date('H');
                $activityEndHour = bcadd((int)$value['time'],(int)$value['continued'],0);
                $value['activityEndHour'] = $activityEndHour;
                if($activityEndHour > 24){
                  if($currentHour >= (int)$value['time']){
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '抢购中';
                        $value['stop'] = (int)bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                        $value['status'] = 1;
                        if(!$dineTimeIndex) $dineTimeIndex = $key;
                    } else {
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '即将开始';
                        $value['status'] = 2;
                        $value['stop'] = (int)bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                    }
                }else{
                    if($currentHour >= (int)$value['time'] && $currentHour < $activityEndHour){
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '抢购中';
                        $value['stop'] = (int)bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                        $value['status'] = 1;
                        if(!$dineTimeIndex) $dineTimeIndex = $key;
                    }else if($currentHour < (int)$value['time']){
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '即将开始';
                        $value['status'] = 2;
                        $value['stop'] = (int)bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                    }else if($currentHour >= $activityEndHour){
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'].':00' : '0'.(int)$value['time'].':00';
                        $value['state'] = '已结束';
                        $value['status'] = 0;
                        $value['stop'] = (int)bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
                    }
                }
            }
        }
        $data['lovely'] = SystemConfigService::get('dine_header_banner');
        if(strstr($data['lovely'],'http') === false && strlen(trim($data['lovely']))) $data['lovely'] = SystemConfigService::get('site_url').$data['lovely'];
        $data['lovely'] = str_replace('\\','/',$data['lovely']);
        $data['dineTime'] = $dineTime;
        $data['dineTimeIndex'] = $dineTimeIndex;
        $data['curhour'] = date('H');
        return app('json')->successful($data);
    }

    /**
     * 秒杀产品列表
     * @param Request $request
     * @param $time
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lst(Request $request, $time)
    {
        list($page, $limit) = UtilService::getMore([
            ['page',0],
            ['limit',0],
        ],$request, true);
        if(!$time) return app('json')->fail('参数错误');
        $timeInfo = GroupDataService::getDataNumber($time);
        $activityEndHour = bcadd((int)$timeInfo['time'],(int)$timeInfo['continued'],0);
        $startTime = bcadd(strtotime(date('Y-m-d')),bcmul($timeInfo['time'],3600,0));
        $stopTime = bcadd(strtotime(date('Y-m-d')),bcmul($activityEndHour,3600,0));
        $dineInfo = StoreDine::dineList($startTime, $stopTime, $page, $limit);
        if(count($dineInfo)){
            foreach ($dineInfo as $key=>&$item){
                $percent = (int)bcmul(bcdiv($item['sales'],bcadd($item['stock'],$item['sales'],0),2),100,0);
                $item['percent'] = $percent ? $percent : 10;
            }
        }
        
        // $dineInfo['startTime'] = $startTime;
        // $dineInfo['stopTime'] = $stopTime;
        // $dineInfo['timeInfo'] = $timeInfo;
        return app('json')->successful($dineInfo);
    }

    /**
     * 秒杀产品详情
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function detail(Request $request, $id,$time = 0){
        if(!$id || !($storeInfo = StoreDine::getValidProduct($id))) return app('json')->fail('商品不存在或已下架!');
        $storeInfo = $storeInfo->hidden(['cost','add_time','is_del'])->toArray();

        //公众号
        $name = $id.'_dine_detail_wap.jpg';
        $imageInfo = SystemAttachment::getInfo($name,'name');
        $siteUrl = SystemConfigService::get('site_url');
        if(!$imageInfo){
            $codeUrl = UtilService::setHttpType($siteUrl.'/activity/dine_detail/'.$id.'/'.$time, 1);//二维码链接
            $imageInfo = UtilService::getQRCodePath($codeUrl, $name);
            if(is_array($imageInfo)){
                SystemAttachment::attachmentAdd($imageInfo['name'],$imageInfo['size'],$imageInfo['type'],$imageInfo['dir'],$imageInfo['thumb_path'],1,$imageInfo['image_type'],$imageInfo['time'],2);
                $url = $imageInfo['dir'];
            }else {
                $url = '';
                $imageInfo = ['image_type'=>0];
            }
        }else $url = $imageInfo['att_dir'];
        if($imageInfo['image_type'] == 1) $url = $siteUrl.$url;
        $storeInfo['image'] = UtilService::setSiteUrl($storeInfo['image'], $siteUrl);
        $storeInfo['image_base'] = UtilService::setSiteUrl($storeInfo['image'], $siteUrl);
        $storeInfo['code_base'] = $url;
        $uid = $request->uid();
        $storeInfo['userLike'] = StoreProductRelation::isProductRelation($id, $uid, 'like', 'product_dine');
        $storeInfo['like_num'] = StoreProductRelation::productRelationNum($id,'like','product_dine');
        $storeInfo['userCollect'] = StoreProductRelation::isProductRelation($id, $uid,'collect','product_dine');
        $storeInfo['uid'] = $uid;
        $data['storeInfo'] = $storeInfo;
        setView($uid,$id,$storeInfo['product_id'],'viwe');
        $data['reply'] = StoreProductReply::getRecProductReply($storeInfo['product_id']);
        $data['replyCount'] = StoreProductReply::productValidWhere()->where('product_id',$storeInfo['product_id'])->count();
        $count = StoreDineUser::isDineUser($id,$uid);
        if($count) $data['isIn'] = true;
        else $data['isIn'] = false;
        return app('json')->successful($data);
    }

    /**
     * 霸王餐报名
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function apply(Request $request)
    {
        if(!$request) return app('json')->fail('参数错误');
        $data = UtilService::postMore([
            ['productId',''],
            ['dineId',''],
        ]);
        
        if(!$data || !isset($data['dineId'])) return app('json')->fail('参数错误');
        $count = StoreDineUser::isDineUser($data['dineId'],$request->uid());
        if($count === false) return app('json')->fail('参数错误');
        else if($count) return app('json')->status('SUCCESSFUL', '参与成功');
        else $res = StoreDineUser::setDine($data['dineId'],$request->uid());
        if(!$res) return app('json')->fail('参与失败');
        else return app('json')->status('SUCCESS', '参与成功');
    }

    /**
     * 砍价列表(已参与)
     * @param Request $request
     * @return mixed
     */
    public function user_list(Request $request)
    {
        list($page, $limit) = UtilService::getMore([
            ['page',0],
            ['limit',0],
        ],$request, true);
        $uid = $request->uid();
        StoreDineUser::editDineUserStatus($uid);// TODO 判断过期砍价活动
        $list = StoreDineUser::getDineUserAll($uid, $page, $limit);
        if(count($list)) return app('json')->successful($list);
        else return app('json')->fail('暂无参与霸王餐');
    }
}