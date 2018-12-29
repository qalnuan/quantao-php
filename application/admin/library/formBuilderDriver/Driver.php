<?php
/**
 *表单生成组件接口
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/13
 */

namespace app\admin\library\formBuilderDriver;


abstract class Driver
{
    //表单字段名
    protected $field;
    //表单字段昵称
    protected $title;
    //表单的类型
    protected $type;
    //表单的属性
    protected $props = [];
    //表单的值
    protected $value = '';
    //表单的选项
    protected $options = [];

    public function __construct($field,$title)
    {
        $this->field = $field;
        $this->title = $title;
    }

    /**
     * 参数生成
     * @return array
     */
    abstract protected function builder();


    /**
     * 绑定的值
     * @param String | Number $value
     * @return $this
     */
    public function value($value,$default = '')
    {
        $value === null && ($value=$default);
        $this->value = $value;
        return $this;
    }


    /**
     * 设置表单属性
     * @param $propsKey
     * @param $propsVal
     */
    protected function setProps($propsKey, $propsVal)
    {
        $this->props[$propsKey] = $propsVal;
    }

    protected function result()
    {
        $type = $this->type;
        $field = $this->field;
        $title = $this->title;
        $value = $this->value;
        $props = $this->props;
        $options = $this->options;
        $select = $this->getSelect();

        $props['value'] = $value;
        $result = compact('type','field','title','props','value','select');
        is_array($options) && ($result['options'] = $options);
        return $result;
    }

    protected function getSelect()
    {
        $select = [];
        $options = $this->options;
        if(!$options || !count($options)) return $select;
        foreach ($options as $option){
            $select[] = [
                'value'=>$option['value'],
                'label'=>$option['props']['label']
            ];
//            $select[$option['props']['value']] = $option['props']['label'];
        }
        return $select;
    }

    protected function toArray($res)
    {
        is_array($res) || $res = [$res];
        return $res;
    }

}