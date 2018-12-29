<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/13
 */

namespace app\admin\library\formBuilderDriver;


class Input extends Driver
{
    protected $type = 'text';

    const TEXT = 'text';

    const PASSWORD = 'password';

    const TEXTAREA = 'textarea';

    protected $props = [
        'type'=>'text',
        'disabled'=>false,
        'readonly'=>false,
        'rows'=>4,
        'autosize'=>false,
        'number'=>false,
        'autofocus'=>false,
        'autocomplete'=>'off'
    ];

    public function __construct($field, $title,$type = self::TEXT,$value = '',$default = '')
    {
        parent::__construct($field, $title);
        $this->placeholder('请输入'.$title);
        $this->type($type);
        $this->value($value,$default);
    }

    /**
     * 输入框类型，可选值为 text、password 或 textarea
     * @param String $type
     * @return $this
     */
    public function  type($type)
    {
        $this->setProps('type',$type);
        return $this;
    }


    /**
     * 输入框尺寸，可选值为large、small、default或者不设置
     * @param String $size
     * @return $this
     */
    public function size($size)
    {
        $this->setProps('size',$size);
        return $this;
    }

    /**
     * 占位文本
     * @param 	String $placeholder
     * @return $this
     */
    public function placeholder($placeholder)
    {
        $this->setProps('placeholder',$placeholder);
        return $this;
    }

    /**
     * 设置输入框为禁用状态
     * @param Boolean $disabled
     * @return $this
     */
    public function disabled($disabled = true)
    {
        $this->setProps('disabled', $disabled);
        return $this;
    }

    /**
     * 设置输入框为只读
     * @param Boolean $readonly
     * @return $this
     */
    public function readonly($readonly = true)
    {
        $this->setProps('readonly', $readonly);
        return $this;
    }

    /**
     * 最大输入长度
     * @param Number $maxlength
     * @return $this
     */
    public function maxlength($maxlength)
    {
        $this->setProps('maxlength', $maxlength);
        return $this;
    }

    /**
     * 输入框尾部图标，仅在 text 类型下有效
     * @param String $icon
     * @return $this
     */
    public function icon($icon)
    {
        $this->setProps('icon', $icon);
        return $this;
    }

    /**
     * 文本域默认行数，仅在 textarea 类型下有效
     * @param Number $rows
     * @return $this
     */
    public function rows($rows = 2)
    {
        $this->setProps('rows', $rows);
        return $this;
    }

    /**
     * 自适应内容高度，仅在 textarea 类型下有效，可传入对象，如 { minRows: 2, maxRows: 6 }
     * @param Boolean | array $autosize
     * @return $this
     */
    public function autosize($minRows,$maxRows = null)
    {
        $group = compact('minRows');
        if($maxRows !== null) $group['maxRows'] = $maxRows;
        $this->setProps('autosize', $group);
        return $this;
    }

    /**
     * 将用户的输入转换为 Number 类型
     * @param Boolean $number
     * @return $this
     */
    public function number($number = true)
    {
        $this->setProps('number', $number);
        return $this;
    }

    /**
     * 自动获取焦点
     * @param Boolean $autofocus
     * @return $this
     */
    public function autofocus($autofocus = true)
    {
        $this->setProps('autofocus', $autofocus);
        return $this;
    }

    /**
     * 原生的自动完成功能，可选值为 off 和 on
     * @param String $autocomplete
     * @return $this
     */
    public function autocomplete($autocomplete = 'off')
    {
        $this->setProps('autocomplete', $autocomplete);
        return $this;
    }

    /**
     * 给表单元素设置 id
     * @param String $elementId
     * @return $this
     */
    public function elementId($elementId)
    {
        $this->setProps('element-id', $elementId);
        return $this;
    }


    /**
     * 生成参数
     * @return array
     */
    public function builder()
    {
        return $this->result();
    }
}