<?php
namespace app\models\system;
use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;

/**
 * TODO 商户用户
 * Class SystemAdminUser
 * @package app\models\system
 */
class SystemAdmin extends BaseModel
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
    protected $name = 'system_admin';

    use ModelTrait;

    /*
     * 获取商户信息
     * @param string $id 商户ID
     * @return array
     * */
    public static function getSystemAdminInfo($id)
    {
        $model=new self();
        $adminInfo=self::where('id',$id)->field(['real_name'])->find();
        return $adminInfo;
    }

    /*
     * 设置核销微信openid
     * @return array
     * */
    public static function updateCheckId($id, $uid)
    {
        $model=new self();
        $adminInfo=self::where('id',$id)->update(['check_id' => $uid]);
        return $adminInfo;
    }
}