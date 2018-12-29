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
 * 退款理由Model
 * Class StoreRefundReason
 * @package app\admin\model\store
 */
class StoreRefundReason extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where){
        $model = new self;
        if($where['status'] != '')  $model = $model->where('status',$where['status']);
        if($where['reason'] != '')  $model = $model->where('reason','LIKE',"%$where[reason]%");
        return self::page($model,$where);
    }

    /**
     * 获取显示的理由
     * @return array
     */
    public static function getCategory($field = 'id,reason')
    {
        return self::where('status',1)->column($field);
    }

    /**
     * 删除理由
     * @param $id
     * @return bool
     */
    public static function delCategory($id){
        return self::del($id);
    }
}