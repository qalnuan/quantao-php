<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2018/01/17
 */

namespace app\admin\model\store;


use basic\ModelBasic;
use traits\ModelTrait;

class StoreCouponIssue extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function setIssue($cid,$total_count = 0,$start_time = 0,$end_time = 0,$remain_count = 0,$status = 0)
    {
        return self::set(compact('cid','start_time','end_time','total_count','remain_count','status'));
    }
}