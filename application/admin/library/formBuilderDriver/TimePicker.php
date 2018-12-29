<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/14
 */

namespace app\admin\library\formBuilderDriver;


class TimePicker extends Driver
{
    const TYPE_TIME = 'time';

    const TYPE_TIMERANGE = 'timerange';

    protected $type = 'TimePicker';

    protected $props = [
        'type'=>'time',
        'format'=>'HH:mm:ss',
        'placement'=>'bottom-start',
        'confirm'=>false,
        'disabled'=>false,
        'clearable'=>true,
        'readonly'=>false,
        'editable'=>false
    ];

    public function __construct($field, $title, $type, $value)
    {
        parent::__construct($field, $title);
        $this->type($type);
        $this->value($value);
    }

    /**
     * 显示类型，可选值为 time、timerange
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->setProps('type', $type);
        return $this;
    }

    /**
     * 展示的时间格式
     * @param $format
     * @return $this
     */
    public function format($format)
    {
        $this->setProps('format', $format);
        return $this;
    }

    /**
     * 下拉列表的时间间隔，数组的三项分别对应小时、分钟、秒。
     * 例如设置为 [1, 15] 时，分钟会显示：00、15、30、45。
     * @param $start
     * @param $end
     * @return $this
     */
    public function steps($start, $end)
    {
        $this->setProps('steps', [$start,$end]);
        return $this;
    }

    /**
     * 时间选择器出现的位置，可选值为
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
     * 	占位文本
     * @param $placeholder
     * @return $this
     */
    public function placeholder($placeholder)
    {
        $this->setProps('placeholder', $placeholder);
        return $this;
    }

    /**
     * 是否显示底部控制栏
     * @param bool $confirm
     * @return $this
     */
    public function confirm($confirm = true)
    {
        $this->setProps('confirm', $confirm);
        return $this;
    }

    /**
     * 手动控制时间选择器的显示状态，true 为显示，false 为收起。
     * 使用该属性后，选择器不会主动关闭。建议配合 slot 及 confirm 和相关事件一起使用
     * @param $open
     * @return $this
     */
    public function open($open)
    {
        $this->setProps('open', $open);
        return $this;
    }

    /**
     * 尺寸，可选值为large、small、default或者不设置
     * @param $size
     * @return $this
     */
    public function size($size)
    {
        $this->setProps('size', $size);
        return $this;
    }

    /**
     * 是否禁用选择器
     * @param bool $disabled
     * @return $this
     */
    public function disabled($disabled = true)
    {
        $this->setProps('disabled', $disabled);
        return $this;
    }

    /**
     * 是否显示清除按钮
     * @param bool $clearable
     * @return $this
     */
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

    private function verify()
    {
        is_array($this->value) && (count(array_filter($this->value)) == 0) && ($this->value=['0:0:0','0:0:0']);
    }
    
    public function builder()
    {
        $this->verify();
        return $this->result();
    }
}