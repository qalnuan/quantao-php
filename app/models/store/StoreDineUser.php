<?php
namespace app\models\store;

use app\models\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\facade\Log;

/**
 * TODO 参与霸王餐Model
 * Class StoreDineUser
 * @package app\models\store
 */
class StoreDineUser extends BaseModel
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
    protected $name = 'store_dine_user';

    use ModelTrait;

    /**
     * TODO 根据霸王餐产品获取正在参与的用户头像
     * @param array $dine
     * @param int $limit
     * @return array
     */
    public static function getUserList($dine = array(),$limit = 10){
         if(count($dine) < 1) return [];
         foreach ($dine as $k=>$v){
             if(is_array($v)){
                 $uid = self::getUserIdList($v['id']);
                 if(count($uid) > 0) {
                     $userInfo = User::where('uid','IN',implode(',',$uid))->limit($limit)->column('avatar','uid');
                     $dine[$k]['userInfo'] = $userInfo;
                     $dine[$k]['userInfoCount'] = count($userInfo);
                 }
                 else {
                     $dine[$k]['userInfo'] = [];
                     $dine[$k]['userInfoCount'] = 0;
                 }
             }else{
                 $uid = self::getUserIdList($dine['id']);
                 if(count($uid) > 0) $dine['userInfo'] = User::where('uid','IN',implode(',',$uid))->column('avatar','uid');
                 else $dine['userInfo'] = [];
             }
         }
         return $dine;
    }

    /**
     * TODO 根据霸王餐产品编号获取正在参与人的编号
     * @param int $dineId $dineId  霸王餐产品ID
     * @param int $status   $status  状态  1 进行中  2 结束失败  3结束成功
     * @return array
     */
    public static function getUserIdList($dineId = 0,$status = 1){
        if(!$dineId) return [];
        return self::where('dine_id',$dineId)->where('status',$status)->column('uid','id');
    }

    /**
     * TODO 添加一条霸王餐记录
     * @param int $dineId  $dineId 霸王餐产品编号
     * @param int $dineUserUid  $dineUserUid 开启霸王餐用户编号
     * @return bool|object
     */
    public static function setDine($dineId = 0,$dineUserUid = 0){
        if(!$dineId || !$dineUserUid || !StoreDine::validDine($dineId) || self::be(['dine_id'=>$dineId,'uid'=>$dineUserUid,'status'=>1,'is_del'=>0])) return false;
        $data['dine_id'] = $dineId;
        $data['uid'] = $dineUserUid;
        $data['status'] = 1;
        $data['is_del'] = 0;
        $data['add_time'] = time();
        return self::create($data);
    }

    /**
     * TODO 判断当前人是否已经参与
     * @param int $dineId  $dineId 霸王餐产品编号
     * @param int $dineUserUid  $dineUserUid 用户编号
     * @return bool|int|string
     * @throws \think\Exception
     */
    public static function isDineUser($dineId = 0,$dineUserUid = 0){
        if(!$dineId || !$dineUserUid || !StoreDine::validDine($dineId)) return false;
        return self::where('dine_id',$dineId)->where('uid',$dineUserUid)->where('is_del',0)->count();
    }

    /**
     * 获取砍掉用户当前状态
     * @param int $id  $id 用户参与霸王餐表编号
     * @return int
     */
    public static function getDineUserStatusEnd($id = 0){
        return (int)self::where('id',$id)->value('status');
    }

    /**
     * TODO 获取霸王餐表ID
     * @param int $dineId $dineId 霸王餐产品
     * @param int $dineUserUid  $dineUserUid  开启霸王餐用户编号
     * @param int $status $status  霸王餐状态 1参与中 2 活动结束参与失败 3活动结束参与成功
     * @return mixed
     */
    public static function getDineUserTableId($dineId = 0,$dineUserUid = 0){
        return self::where('dine_id',$dineId)->where('uid',$dineUserUid)->where('is_del',0)->value('id');
    }

    /**
     * TODO 获取用户的霸王餐产品
     * @param int $dineUserUid  $dineUserUid  开启霸王餐用户编号
     * @param int $page
     * @param int $limit
     * @return array
     */
    public static function getDineUserAll($dineUserUid = 0,$page = 0,$limit = 20){
       if(!$dineUserUid) return [];
       $model = new self;
       $model = $model->alias('u');
       $model = $model->field('u.uid,u.is_del,u.id,u.dine_id,u.status,b.title,b.image,b.stop_time as datatime');
       $model = $model->join('StoreDine b','b.id=u.dine_id');
       $model = $model->where('u.uid',$dineUserUid);
       $model = $model->where('u.is_del',0);
       $model = $model->order('u.id desc');
       if($page) $model = $model->page($page,$limit);
       $list = $model->select();
       if($list) return $list->toArray();
       else return [];
    }

    /**
     * TODO 修改用户霸王餐状态  支付订单
     * @param int $dineId $dineId 霸王餐产品
     * @param int $dineUserUid  $dineUserUid  开启霸王餐用户编号
     * @return StoreDineUser|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function setDineUserStatus($dineId = 0, $dineUserUid = 0){
        if(!$dineId || !$dineUserUid) return false;
        $dineUserTableId = self::getDineUserTableId($dineId,$dineUserUid);//TODO 获取用户参与霸王餐表编号  下订单
        if(!$dineUserTableId) return false;
        $count = self::where('id',$dineUserTableId)->where('status',1)->count();
        if(!$count) return false;
        return self::where('id',$dineUserTableId)->where('status',1)->update(['status'=>3]);
    }

    /**
     * 批量修改霸王餐状态为 霸王餐失败
     * @return StoreDineUser|bool
     */
    public static function startDineUserStatus()
    {
        $currentDine = self::getDineUserCurrent(0); //TODO 获取当前用户正在霸王餐的产品
        Log::info("currentDine:".json_encode($currentDine));
        $dineProduct = StoreDine::validRunDineNumber(); //TODO 获取正在开奖的霸王餐产品编号
        Log::info("dineProduct:".json_encode($dineProduct));
        $closeDine = [];
        foreach ($currentDine as $key=>$item) {
          if(in_array($item,$dineProduct)) { 
            $closeDine[] = $item;
            $userList = self::getUserIdList($item);
            $mun = StoreDine::getProductField($item, 'num');
            $countUser = count($userList);
            $mun = $mun > $countUser ? $countUser : $mun;
            $rand_keys = array_rand($userList, $mun);
            Log::info("rand_keys:".json_encode($rand_keys).gettype($userList));
            if ($mun == 1) {
              self::where('status', 1)->where('id', $rand_keys)->update(['status' => 3]);
              self::where('status', 1)->where('dine_id', $item)->where('id', '<>', $rand_keys)->update(['status' => 2]);
              $uid = self::where('id', $rand_keys)->value('uid');
              Log::info("uid:".json_encode($uid));
              StoreDine::createDineOrder($uid, $item);
            } else {
              self::where('status', 1)->where('id', 'IN', implode(',', $rand_keys))->update(['status' => 3]);
              self::where('status', 1)->where('dine_id', $item)->where('id', 'not IN', implode(',', $rand_keys))->update(['status' => 2]);
              foreach ($rand_keys as $id) {
                $uid = self::where('id', $id)->value('uid');
                Log::info("uid:".json_encode($uid));
                StoreDine::createDineOrder($uid, $item);
              }
            }

            StoreDine::setDineRun($item);
          }
        }// TODO 获取已经结束的霸王餐产品

        
        Log::info("closeDine:".json_encode($closeDine));
        
        // self::where('status',1)->where('dine_id',14)->update(['status'=>4]);
        // if(count($closeDine)) return self::where('status',1)->where('dine_id','IN',implode(',',$closeDine))->update(['status'=>2]);
        return true;
    }

    /**
     * TODO 修改霸王餐状态为 霸王餐失败
     * @param $uid $uid 当前用户编号
     * @return StoreDineUser|bool
     */
    public static function editDineUserStatus($uid){
        $currentDine = self::getDineUserCurrent($uid); //TODO 获取当前用户正在霸王餐的产品
        $dineProduct = StoreDine::validRunDineNumber(); //TODO 获取正在开启的霸王餐产品编号
        $closeDine = [];
        foreach ($currentDine as $key=>&$item) { if(!in_array($item,$dineProduct)) { $closeDine[] = $item; } }// TODO 获取已经结束的霸王餐产品
        return true;
    }

    /**
     * TODO 获取当前用户正在霸王餐的产品
     * @param $uid  $uid 当前用户编号
     * @return array
     */
    public static function getDineUserCurrent($uid){
        if($uid) return self::where('uid',$uid)->where('is_del',0)->where('status',1)->column('dine_id');
        else return self::where('is_del',0)->where('status',1)->column('dine_id');
    }

    /**
     * TODO 获取霸王餐成功的用户信息
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getDineUserStatusSuccess(){
        $dineUser = self::where('status',3)->order('id desc')->field('uid,dine_price_min,dine_id')->select();
        if($dineUser) {
            $dineUser = $dineUser->toArray();
            foreach ($dineUser as $k=>$v){
                $dineUser[$k]['info'] = User::where('uid',$v['uid'])->value('nickname').'霸王餐成功了'.$v['dine_price_min'].'砍到了'.StoreDine::where('id',$v['dine_id'])->value('title');
            }
        }
        else{
            $dineUser[]['info'] = '霸王餐上线了，快邀请您的好友来霸王餐';
        }
        return $dineUser;
    }

    /**
     * TODO  获取用户霸王餐产品状态
     * @param int $dineId $dineId 霸王餐产品
     * @param int $dineUserUid  $dineUserUid  开启霸王餐用户编号
     * @return bool|mixed
     */
    public static function getDineUserStatus($dineId,$dineUserUid){
        if(!$dineId || !$dineUserUid) return false;
        //TODO status  霸王餐状态 1参与中 2 活动结束参与失败 3活动结束参与成功
        return self::where('dine_id',$dineId)->where('uid',$dineUserUid)->order('add_time DESC')->value('status');
    }

    /**
     * 获取参与的ID
     * @param int $dineId
     * @param int $uid
     * @param int $status
     * @return array|mixed
     */
    public static function setUserDine($dineId = 0,$uid = 0,$status = 1){
        if(!$dineId || !$uid) return [];
        $dineIdUserTableId = self::where('dine_id',$dineId)->where('uid',$uid)->where('status',$status)->value('id');
        return $dineIdUserTableId;
    }
}