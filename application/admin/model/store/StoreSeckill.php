<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */

namespace app\admin\model\store;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * Class StoreSeckill
 * @package app\admin\model\store
 */
class StoreSeckill extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where){
        $model = new self;
        $model = $model->alias('s');
//        $model = $model->join('StoreProduct p','p.id=s.product_id');
        if($where['status'] != '')  $model = $model->where('s.status',$where['status']);
        if($where['store_name'] != '')  {
            dump($where);
            $model = $model->where('s.title','LIKE',"%$where[store_name]%");
        }
        $model = $model->order('s.id desc');
        $model = $model->where('s.is_del',0);
        return self::page($model,function($item){
            $item['store_name'] = StoreProduct::where('id',$item['product_id'])->value('store_name');
            if($item['status']){
                if($item['start_time'] > time())
                    $item['start_name'] = '活动未开始';
                else if($item['stop_time'] < time())
                    $item['start_name'] = '活动已结束';
                else if($item['stop_time'] > time() && $item['start_time'] < time())
                    $item['start_name'] = '正在进行中';
            }

        },$where);
    }
}