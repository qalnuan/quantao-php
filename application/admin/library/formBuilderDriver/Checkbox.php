<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/13
 */

namespace app\admin\library\formBuilderDriver;


class Checkbox extends Driver
{

    protected $type = 'checkbox';

    protected $disabledOptions = [];

    protected $indeterminateOptions = [];



    public function __construct($field, $title,$options = [],$value = '',$default = '')
    {
        parent::__construct($field, $title);
        $this->options($options);
        $this->value($value,$default);
    }

    /**
     * 是否禁用当前项
     * @param array|string|number $options
     * @return $this
     */
    public function disabled($options)
    {
        $options = $this->toArray($options);
        $this->disabledOptions = $options;
        return $this;
    }

    /**
     * 指定当前选项的 value 值
     * @param String | Number | array $value
     * @return $this
     */
    public function value($value,$default='')
    {
        empty($value) && ($value = $default);
        $value = $this->toArray($value);
        $this->value = $value;
        return $this;
    }


    /**
     * 设置 indeterminate 状态
     * @param array|string|number $options
     * @return $this
     */
    public function indeterminate($options)
    {
        $options = $this->toArray($options);
        $this->indeterminateOptions = $options;
        return $this;
    }

    /**
     * 单选框的尺寸，可选值为 large、small、default 或者不设置
     * @param String $size
     * @return $this
     */
    public function size($size)
    {
        $this->size = $size;
        return $this;
    }


    /**
     * 设置选项列表 [[value=>value,label=>label[,disabled=>true,indeterminate=>true]]]
     * @param array $options
     * @return $this
     */
    public function options($options)
    {
        if(!is_array($options)) exception('options参数类型必须为Array');
        $_options = [];
        $_select = [];
        foreach ($options as $value => $option){
            if(is_array($option))
                $_options[$option['value']] = $option;
            else
                $_options[$value] = ['value'=>$value,'label'=>$option,'disabled'=>false,'indeterminate'=>false];

        }
        $this->options = $_options;
        $this->select = $_select;
        return $this;
    }

    /**
     * 设置选项
     * @param Number|String $value
     * @param Number|String $label
     * @return $this
     */
    public function option($value, $label,$disabled = false,$indeterminate = false)
    {
        $this->options[$value] = compact('value','label','disabled','indeterminate');
        return $this;
    }

    /**
     * 尺寸，可选值为large、small、default或者不设置
     * @param String $size
     * @return $this
     */
    public function groupSize($size)
    {
        $this->setProps('size',$size);
        return $this;
    }

    protected function verify()
    {
        is_array($this->value) || (empty($this->value) ? ($this->value = []) : ($this->value=$this->toArray($this->value)));
        if(count($this->value)>1) $this->value = array_unique($this->value);
        $checked = [];
        $options = [];
        foreach ($this->options as $option){
            $option['disabled'] = isset($option['disabled']) ? $option['disabled']:in_array($option['value'],$this->disabledOptions);
            $option['indeterminate'] = isset($option['indeterminate']) ? $option['indeterminate']:in_array($option['value'],$this->indeterminateOptions);
            in_array($option['value'],$this->value) && ($checked[] = $option['label']);
            empty($this->size) || ($option['size'] = $this->size);
            $value = $option['value'];
            $option['value'] = $option['label'];
            $options [] = ['value'=>$value,'props' => $option];
        }
        $this->value = $checked;
        $this->options = $options;
    }

    public function builder()
    {
        $this->verify();
        return $this->result();
    }
}