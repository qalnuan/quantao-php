<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/14
 */

namespace app\admin\library\formBuilderDriver;


class InputNumber extends Driver
{
    protected $type = 'inputNumber';

    protected $props = [
        'step'=>1,
        'precision'=>2
    ];

    public function __construct($field, $title,$value='', $default = 1)
    {
        parent::__construct($field, $title);
        $this->value($value,$default);
    }

    /**
     * 	最大值
     * @param $max
     * @return $this
     */
    public function max($max)
    {
        $this->setProps('max', $max);
        return $this;
    }

    /**
     * 	最小值
     * @param $min
     * @return $this
     */
    public function min($min)
    {
        $this->setProps('min', $min);
        return $this;
    }

    /**
     * 每次改变的步伐，可以是小数
     * @param $step
     * @return $this
     */
    public function step($step)
    {
        $this->setProps('step', $step);
        return $this;
    }

    /**
     * 	输入框尺寸，可选值为large、small、default或者不填
     * @param $size
     * @return $this
     */
    public function size($size)
    {
        $this->setProps('size', $size);
        return $this;
    }

    /**
     * 设置禁用状态
     * @param bool $disabled
     * @return $this
     */
    public function disabled($disabled = true)
    {
        $this->setProps('disabled', $disabled);
        return $this;
    }

    public function value($value,$default = 1)
    {
        ($value === false || $value === null) && ($value = $default);
        $this->value = floatval(bcmul($value,1,$this->props['precision']));
        return $this;
    }

    /**
     * 	数值精度
     * @param int $precision
     * @return $this
     */
    public function precision($precision = 2)
    {
        $this->setProps('precision', $precision);
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

    public function defaultSetup()
    {

    }

    public function builder()
    {
        return $this->result();
    }
}