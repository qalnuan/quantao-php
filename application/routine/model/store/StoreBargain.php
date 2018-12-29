<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/18
 */

namespace app\routine\model\store;

use basic\ModelBasic;
use traits\ModelTrait;

class StoreBargain extends ModelBasic
{
    use ModelTrait;

    /**
     * 正在开启的砍价活动
     * @return $this
     */
    public static function validWhere($status = 1){
        return  self::where('is_del',0)->where('status',$status)->where('start_time','LT',time())->where('stop_time','GT',time());
    }

    /**
     * 判断砍价产品是否开启
     * @param int $bargainId
     * @return int|string
     */
    public static function validBargain($bargainId = 0){
        $model = self::validWhere();
        return $model->where('id',$bargainId)->count();
    }
        /**
     * 获取正在进行中的砍价产品
     * @param string $field
     */
    public static function getList($field = 'id,product_id,title,price,min_price,image'){
        $model = self::validWhere();
        $list = $model->field($field)->select();
        if($list) return $list->toArray();
        else return [];
    }

    /**
     * 获取一条正在进行中的砍价产品
     * @param int $bargainId
     * @param string $field
     * @return array
     */
    public static function getBargainTerm($bargainId = 0,$field = 'id,product_id,bargain_num,num,rule,unit_name,image,title,price,min_price,image,description,start_time,stop_time'){
        if(!$bargainId) return [];
        $model = self::validWhere();
        $bargain = $model->field($field)->where('id',$bargainId)->find();
        if($bargain) return $bargain->toArray();
        else return [];
    }

    /**
     * 获取一条砍价产品
     * @param int $bargainId
     * @param string $field
     * @return array
     */
    public static function getBargain($bargainId = 0,$field = 'id,product_id,title,price,min_price,image'){
        if(!$bargainId) return [];
        $model = new self();
        $bargain = $model->field($field)->where('id',$bargainId)->find();
        if($bargain) return $bargain->toArray();
        else return [];
    }

    /**
     * 获取最高价和最低价
     * @param int $bargainId
     * @return array
     */
    public static function getBargainMaxMinPrice($bargainId = 0){
        if(!$bargainId) return [];
        return self::where('id',$bargainId)->field('bargain_min_price,bargain_max_price')->find()->toArray();
    }

    /**
     * 获取砍价次数
     * @param int $bargainId
     * @return mixed
     */
    public static function getBargainNum($bargainId = 0){
       return self::where('id',$bargainId)->value('bargain_num');
    }

    /**
     * 判断当前砍价是否活动进行中
     * @param int $bargainId
     * @return bool
     */
    public static function setBargainStatus($bargainId = 0){
        $model = self::validWhere();
        $count = $model->where('id',$bargainId)->count();
        if($count) return true;
        else return false;
    }

    /**
     * 获取库存
     * @param int $bargainId
     * @return mixed
     */
    public static function getBargainStock($bargainId = 0){
        return self::where('id',$bargainId)->value('stock');
    }

    /**
     * 修改库存和销量
     * @param int $bargainId
     * @param int $num
     * @return bool
     */
    public static function decBargainStock($num = 0,$bargainId = 0){
        $res = false !== self::where('id',$bargainId)->dec('stock',$num)->inc('sales',$num)->update();
        return $res;
    }
}