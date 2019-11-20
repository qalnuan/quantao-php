<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/23
 */

namespace app\models\store;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * TODO 核销员Model
 * Class StoreService
 * @package app\models\store
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
     * 获取核销员列表
     * @param $page
     * @param $limit
     * @return array
     */
    public static function lst($page, $limit)
    {
        $model = new self;
        $model = $model->where('status', 1);
        return $model->select();
    }

    /**
     * 获取核销员信息
     * @param $uid
     * @param string $field
     * @return array|null|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getVerifyServiceInfo($uid, $field = '*')
    {
        return self::where('uid', $uid)->where('status', 1)->field($field)->find();
    }

    /**
     * 判断是否核销员
     * @param $uid
     * @return int
     */
    public static function orderVerifyServiceStatus($uid)
    {
        return self::where('uid', $uid)->where('status', 1)->count();
    }
}