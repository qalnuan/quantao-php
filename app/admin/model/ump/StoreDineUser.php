<?php
namespace app\admin\model\ump;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * 参与砍价Model
 * Class StoreDineUser
 * @package app\admin\model\ump
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
     * 删除活动
     * @param int $dineId $dineId 砍价产品ID
     * @return int|string
     */
    public static function delDine($dineId = 0){
        if(!$dineId) return 0;
        $model = new self();
        return $model->where('dine_id',$dineId)->update(['is_del'=>'1']);
    }

    /**
     * 获取中奖名单
     * @param int $dineId $dineId 砍价产品ID
     * @return int|string
     */
    public static function getDineWinUsers($dineId = 0){
        if(!$dineId) return 0;
        $model = new self();
        $model = $model->alias('a')->join('user r','r.uid=a.uid','LEFT')->field('a.*,r.nickname,r.phone,r.spread_uid');
        $model = $model->where('a.dine_id',$dineId);
        $model = $model->where('a.status', 3);
        $list = $model->select();
        if($list) return $list->toArray();
        else return [];
    }

}