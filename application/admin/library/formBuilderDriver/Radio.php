<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/13
 */

namespace app\admin\library\formBuilderDriver;


class Radio extends Driver
{
    protected $type = 'radio';

    protected $props = [
        'vertical'=>false
    ];

    protected $disabledOptions = [];

    public function __construct($field, $title,$options = [],$value = '',$default = '')
    {
        parent::__construct($field, $title);
        $this->options($options);
        $this->value($value,$default);
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
     * 设置选项列表 [[value=>value,label=>label[,disabled=>true]]]
     * @param array $options
     * @return $this
     */
    public function options($options)
    {
        if(!is_array($options)) exception('options参数类型必须为Array');
        $_options = [];
        foreach ($options as $value => $option){
            if(is_array($option))
                $_options[$option['value']] = $option;
            else
                $_options[$value] = ['value'=>$value,'label'=>$option,'disabled'=>false];

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
    public function option($value, $label, $disabled=false)
    {
        $this->options[$value] = compact('value','label','disabled');
        return $this;
    }

    /**
     * 可选值为 button 或不填
     * @return $this
     */
    public function groupButtonType()
    {
        $this->props['type'] = 'button';
        return $this;
    }

    /**
     * 尺寸，可选值为large、small、default或者不设置
     * @param String $size
     * @return $this
     */
    public function groupSize($size)
    {
        $this->props['size'] = $size;
        return $this;
    }

    /**
     * 是否垂直排列，按钮样式下无效
     * @param Boolean $vertical
     * @return $this
     */
    public function groupVertical($vertical = true)
    {
        $this->props['vertical'] = $vertical;
        return $this;
    }

    protected function verify()
    {
        $checked = '';
        $options = [];
        foreach ($this->options as $option){
            $option['disabled'] = isset($option['disabled']) ? $option['disabled'] == true :in_array($option['value'],$this->disabledOptions);
            ($option['value'] == $this->value) && ($checked = $option['label']);
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