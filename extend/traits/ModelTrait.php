<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */
namespace traits;

use think\db\Query;
use think\Model;

trait ModelTrait
{

    /**
     * 添加一条数据
     * @param $data
     * @return object $model 数据对象
     */
    public static function set($data)
    {
        return self::create($data);
    }


    /**
     * 添加多条数据
     * @param $group
     * @param bool $replace
     * @return mixed
     */
    public static function setAll($group, $replace = false)
    {
        return self::insertAll($group,$replace);
    }

    /**
     * 修改一条数据
     * @param $data
     * @param $id
     * @param $field
     * @return bool $type 返回成功失败
     */
    public static function edit($data,$id,$field = null)
    {
        $model = new self;
        if(!$field) $field = $model->getPk();
        return false !== $model->update($data,[$field=>$id]);
    }

    /**
     * 查询一条数据是否存在
     * @param $map
     * @param string $field
     * @return bool 是否存在
     */
    public static function be($map, $field = '')
    {
        $model = (new self);
        if(!is_array($map) && empty($field)) $field = $model->getPk();
        $map = !is_array($map) ? [$field=>$map] : $map;
        return 0 < $model->where($map)->count();
    }

    /**
     * 删除一条数据
     * @param $id
     * @return bool $type 返回成功失败
     */
    public static function del($id)
    {
        return false !== self::destroy($id);
    }


    /**
     * 分页
     * @param null $model 模型
     * @param null $eachFn 处理结果函数
     * @param array $params 分页参数
     * @param int $limit 分页数
     * @return array
     */
    public static function page($model = null, $eachFn = null, $params = [], $limit = 20)
    {
        if(is_numeric($eachFn)){
            $limit = $eachFn;
            $eachFn = null;
        }else if(is_array($eachFn)){
            $params = $eachFn;
            $eachFn = null;
        }

        if(is_callable($model)){
            $eachFn = $model;
            $model = null;
        }elseif(is_numeric($model)){
            $limit = $model;
            $model = null;
        }elseif(is_array($model)){
            $params = $model;
            $model = null;
        }

        if(is_numeric($params)){
            $limit = $params;
            $params = [];
        }

        $paginate = $model === null ? self::paginate($limit,false,['query'=>$params]) : $model->paginate($limit,false,['query'=>$params]);
        $list = is_callable($eachFn) ? $paginate->each($eachFn) : $paginate;
        $page = $list->render();
        $total = $list->total();
        return compact('list','page','total');
    }
    /**
     * 高精度 加法
     * @param int|string $uid id
     * @param string $decField 相加的字段
     * @param float|int $dec 加的值
     * @param string $keyField id的字段
     * @param int $acc 精度
     * @return bool
     */
    public static function bcInc($key, $incField, $inc, $keyField = null, $acc=2)
    {
        if(!is_numeric($inc)) return false;
        $model = new self();
        if($keyField === null) $keyField = $model->getPk();
        $result = self::where($keyField,$key)->find();
        if(!$result) return false;
        $new = bcadd($result[$incField],$inc,$acc);
        return false !== $model->where($keyField,$key)->update([$incField=>$new]);
    }


    /**
     * 高精度 减法
     * @param int|string $uid id
     * @param string $decField 相减的字段
     * @param float|int $dec 减的值
     * @param string $keyField id的字段
     * @param bool $minus 是否可以为负数
     * @param int $acc 精度
     * @return bool
     */
    public static function bcDec($key, $decField, $dec, $keyField = null, $minus = false, $acc=2)
    {
        if(!is_numeric($dec)) return false;
        $model = new self();
        if($keyField === null) $keyField = $model->getPk();
        $result = self::where($keyField,$key)->find();
        if(!$result) return false;
        if(!$minus && $result[$decField] < $dec) return false;
        $new = bcsub($result[$decField],$dec,$acc);
        return false !== $model->where($keyField,$key)->update([$decField=>$new]);
    }

    /**
     * @param null $model
     * @return Model
     */
    protected static function getSelfModel($model = null)
    {
        return $model == null ? (new self()) : $model;
    }

}