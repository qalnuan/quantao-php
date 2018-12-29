<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/14
 */

namespace app\admin\library\formBuilderDriver;


class ColorPicker extends Driver
{
    protected $type = 'ColorPicker';

    protected $props = [
        'alpha'=>false,
        'recommend'=>false,
        'colors'=>[]
    ];

    public function __construct($field, $title,$color = '#fffff')
    {
        parent::__construct($field, $title);
        $this->value($color);
    }

    /**
     * 是否支持透明度选择
     * @param bool $alpha
     * @return $this
     */
    public function alpha($alpha = true)
    {
        $this->setProps('alpha', $alpha);
        return $this;
    }

    /**
     * 是否显示推荐的颜色预设
     * @param $recommend
     * @return $this
     */
    public function recommend($recommend = true)
    {
        $this->setProps('recommend', $recommend);
        return $this;
    }

    /**
     * 自定义颜色预设
     * @param $colors
     * @return $this
     */
    public function colors($colors)
    {
        $this->setProps('colors', $colors);
        return $this;
    }

    /**
     * 颜色的格式，可选值为 hsl、hsv、hex、rgb
     * @param $format
     * @return $this
     */
    public function format($format)
    {
        $this->setProps('format', $format);
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


    public function builder()
    {
        return $this->result();
    }
}