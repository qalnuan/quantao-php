<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/13
 */

namespace app\admin\library\formBuilderDriver;


class Select extends Driver
{
    protected $type = 'select';

    protected $props = [
        'multiple'=>false,
        'clearable'=>false,
        'filterable'=>false,
        'remote'=>false,
        'loading'=>false,
        'loading-text'=>'加载中',
        'placeholder'=>'请选择',
        'not-found-text'=>'无匹配数据',
        'label-in-value'=>false,
        'placement'=>'bottom',
        'disabled'=>false
    ];

    protected $disabledOptions = [];

    public function __construct($field, $title,$options = [],$value = '',$default = '')
    {
        parent::__construct($field, $title);
        $this->options($options);
        $this->value($value,$default);
    }

    /**
     * 是否支持多选
     * @param bool $multiple
     * @return $this
     */
    public function multiple($multiple = true)
    {
        $this->setProps('multiple', $multiple);
        if($multiple == true)
            empty($this->value) ? ($this->value = []) : $this->toArray($this->value);
        return $this;
    }
    
    /**
     * 是否禁用当前项
     * @param Boolean $value
     * @return $this
     */
    public function disabled($options)
    {
        $options = $this->toArray($options);
        $this->disabledOptions = $options;
        return $this;
    }

    /**
     * 是否可以清空选项，只在单选时有效
     * @param bool $clearable
     * @return $this
     */
    public function clearable($clearable = true)
    {
        $this->setProps('clearable', $clearable);
        return $this;
    }

    /**
     * 是否支持搜索
     * @param $filterable
     * @return $this
     */
    public function filterable($filterable = true)
    {
        $this->setProps('filterable', $filterable);
        return $this;
    }

    /**
     * 是否使用远程搜索
     * @param bool $remote
     * @return $this
     */
    public function remote($remote = true)
    {
        $this->setProps('remote', $remote);
        return $this;
    }

    public function loading($loading)
    {
        $this->setProps('loading', $loading);
        return $this;
    }

    public function loadingText($loadingText)
    {
        $this->setProps('loading-text', $loadingText);
        return $this;
    }
    

    /**
     * 单选框的尺寸，可选值为 large、small、default 或者不设置
     * @param String $size
     * @return $this
     */
    public function size($size)
    {
        $this->setProps('size', $size);
        return $this;
    }

    /**
     * 选择框默认文字
     * @param $placeholder
     * @return $this
     */
    public function placeholder($placeholder)
    {
        $this->setProps('placeholder', $placeholder);
        return $this;
    }

    /**
     * 当下拉列表为空时显示的内容
     * @param $notFoundText
     * @return $this
     */
    public function notFoundText($notFoundText)
    {
        $this->setProps('not-found-text', $notFoundText);
        return $this;
    }

    /**
     * 在返回选项时，是否将 label 和 value 一并返回，默认只返回 value
     * @param $labelInValue
     * @return $this
     */
    public function labelInValue($labelInValue=true)
    {
        $this->setProps('label-in-value', $labelInValue);
        return $this;
    }

    /**
     * 弹窗的展开方向，可选值为 bottom 和 top
     * @param string $placement
     * @return $this
     */
    public function placement($placement = 'top')
    {
        $this->setProps('placement', $placement);
        return $this;
    }

    public function elementId($elementId)
    {
        $this->setProps('element-id', $elementId);
        return $this;
    }

    /**
     * 设置选项列表 [[value=>value,label=>label[,disabled=>true]]]
     * @param array $options
     * @return $this
     */
    public function options($options)
    {
        if(!is_array($options)) exception('options参数类型必须为Array');
        $_options = [];
        foreach ($options as $option){
            $_options[$option['value']] = $option;
        }
        $this->options = $_options;
        return $this;
    }

    /**
     * 设置选项
     * @param Number|String $value
     * @param Number|String $label
     * @return $this
     */
    public function option($value, $label,$disabled = false)
    {
        $this->options[$value] = compact('value','label','disabled');
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

    private function verify()
    {
        $options = [];
        foreach ($this->options as $option){
            $option['disabled'] = isset($option['disabled']) ? $option['disabled']:in_array($option['value'],$this->disabledOptions);
            $option['value'] = (string)$option['value'];
            empty($this->size) || ($option['size'] = $this->size);
            $options [] = ['value'=>$option['value'],'props' => $option];
        }
        if($this->props['multiple'] == true && !is_array($this->value)) $this->value = [$this->value];
        $this->value = is_array($this->value) ? array_map(function($v){ return (string)$v; },$this->value) : (string) $this->value;
        $this->options = $options;
    }

    public function builder()
    {
        $this->verify();
        return $this->result();
    }
}