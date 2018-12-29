<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/14
 */

namespace app\admin\library\formBuilderDriver;


class DatePicker extends Driver
{
    const TYPE_DATE = 'date';

    const TYPE_DATERANGE = 'daterange';

    const TYPE_DATETIME = 'datetime';

    const TYPE_DATETIMERANGE = 'datetimerange';

    const TYPE_YEAR = 'year';

    const TYPE_MONTH = 'month';

    protected $type = 'datePicker';

    protected $props = [
        'type'=>'date',
        'format'=>'yyyy/MM/dd',
        'placement'=>'bottom-start',
        'confirm'=>false,
        'disabled'=>false,
        'clearable'=>true,
        'readonly'=>false,
        'editable'=>false,
    ];

    public function __construct($field, $title, $type, $value = '')
    {
        parent::__construct($field, $title);
        $this->type($type);
        $this->value($value);
    }


    /**
     * 显示类型，可选值为 date、daterange、datetime、datetimerange、year、month
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->setProps('type', $type);
        return $this;
    }

    /**
     * 展示的日期格式
     * @param $format
     * @return $this
     */
    public function format($format)
    {
        $this->setProps('format', $format);
        return $this;
    }

    /**
     * 日期选择器出现的位置，可选值为
     * top top-start top-end bottom bottom-start bottom-end
     * left left-start left-end right right-start right-end
     * @param $placement
     * @return $this
     */
    public function placement($placement)
    {
        $this->setProps('placement', $placement);
        return $this;
    }

    /**
     * 占位文本
     * @param $placeholder
     * @return $this
     */
    public function placeholder($placeholder)
    {
        $this->setProps('placeholder', $placeholder);
        return $this;
    }


    /**
     * 是否显示底部控制栏，开启后，选择完日期，选择器不会主动关闭，需用户确认后才可关闭
     * @param $confirm
     * @return $this
     */
    public function confirm($confirm = true)
    {
        $this->setProps('confirm', $confirm);
        return $this;
    }

    /**
     * 手动控制日期选择器的显示状态，true 为显示，false 为收起。使用该属性后，选择器不会主动关闭。建议配合 slot 及 confirm 和相关事件一起使用
     * @param $open
     * @return $this
     */
    public function open($open)
    {
        $this->setProps('open', $open);
        return $this;
    }

    /**
     * 	是否禁用选择器
     * @param $size
     * @return $this
     */
    public function size($size)
    {
        $this->setProps('size', $size);
        return $this;
    }

    /**
     * 是否显示清除按钮
     * @param bool $disabled
     * @return $this
     */
    public function disabled($disabled = true)
    {
        $this->setProps('disabled', $disabled);
        return $this;
    }

    public function clearable($clearable = false)
    {
        $this->setProps('clearable', $clearable);
        return $this;
    }

    /**
     * 完全只读，开启后不会弹出选择器，只在没有设置 open 属性下生效
     * @param bool $readonly
     * @return $this
     */
    public function readonly($readonly = true)
    {
        $this->setProps('readonly', $readonly);
        return $this;
    }

    /**
     * 文本框是否可以输入，只在没有使用 slot 时有效
     * @param bool $editable
     * @return $this
     */
    public function editable($editable = true)
    {
        $this->setProps('editable', $editable);
        return $this;
    }

    /**
     * 给表单元素设置 id
     * @param $elementId
     * @return $this
     */
    public function elementId($elementId)
    {
        $this->setProps('element-id', $elementId);
        return $this;
    }


    public function builder()
    {
        return $this->result();
    }
}