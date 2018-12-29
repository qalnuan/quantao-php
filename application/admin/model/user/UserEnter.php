<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */

namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class UserEnter extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where){
        $model = new self;
        if($where['status'] != '')  $model = $model->where('e.status',$where['status']);
        if($where['is_lock'] != '')  $model = $model->where('e.is_lock',$where['is_lock']);
        if($where['merchant_name'] != '')  $model = $model->where('e.merchant_name',$where['merchant_name']);
        $model = $model->alias('e');
        $model = $model->field('e.*,u.nickname');
        $model = $model->join('__WECHAT_USER__ u','u.uid=e.uid');
        $model = $model->order('e.id desc');
        $model = $model->where('e.is_del',0);
        return self::page($model,function($item){
            $item['charterarr'] = json_decode($item['charter'],true);
        },$where);
    }

    public static function changeFail($id,$fail_message)
    {
        $fail_time = time();
        $status = -1;
        dump(compact('fail_time','fail_message','status'));exit;
        return self::edit(compact('fail_time','fail_message','status'),$id);
    }

    public static function changeSuccess($id)
    {
        $success_time = time();
        $status = 1;
        return self::edit(compact('success_time','status'),$id);
    }

}