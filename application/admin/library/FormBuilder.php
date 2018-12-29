<?php
/**
 * 表单创建快捷方式注册
 * @author: xaboy<365615158@qq.com>
 * @github: https://github.com/xaboy
 * @day: 2017/10/13
 */

namespace app\admin\library;

use app\admin\library\formBuilderDriver\ColorPicker;
use app\admin\library\formBuilderDriver\DatePicker;
use app\admin\library\formBuilderDriver\Input;
use app\admin\library\formBuilderDriver\Checkbox;
use app\admin\library\formBuilderDriver\InputNumber;
use app\admin\library\formBuilderDriver\Radio;
use app\admin\library\formBuilderDriver\Select;
use app\admin\library\formBuilderDriver\TimePicker;
use app\admin\library\formBuilderDriver\Upload;
use app\admin\library\formBuilderDriver\Driver as FormBuilderDriver;

class FormBuilder
{
    protected static $modelInstances = [];

    protected static function setInstance(FormBuilderDriver $instance)
    {
        self::$modelInstances[] = $instance;
    }

    public static function text($field,$title,$value = '',$default = '')
    {
        $instance = new Input($field,$title,Input::TEXT,$value,$default);
        self::setInstance($instance);
        return $instance;
    }

    public static function password($field,$title,$value = '',$default = '')
    {
        $instance = new Input($field,$title,Input::PASSWORD,$value,$default);
        self::setInstance($instance);
        return $instance;
    }

    public static function textarea($field,$title,$value = '',$default = '')
    {
        $instance = new Input($field,$title,Input::TEXTAREA,$value,$default);
        self::setInstance($instance);
        return $instance;
    }

    public static function radio($field,$title,$options = [],$value = '',$default = '')
    {
        if(is_callable($options)) $options = $options();
        $instance = new Radio($field,$title,$options,$value,$default);
        self::setInstance($instance);
        return $instance;
    }

    public static function checkbox($field,$title,$options = [],$value = '',$default = '')
    {
        if(is_callable($options)) $options = $options();
        $instance = new Checkbox($field,$title,$options,$value,$default);
        self::setInstance($instance);
        return $instance;
    }

    public static function select($field,$title,$options = [],$value = '',$default = '')
    {
        if(is_callable($options)) $options = $options();
        $instance = new Select($field,$title,$options,$value,$default);
        self::setInstance($instance);
        return $instance;
    }

    public static function number($field,$title,$value='',$default=1)
    {
        $instance = new InputNumber($field,$title,$value,$default);
        self::setInstance($instance);
        return $instance;
    }

    public static function date($field,$title,$value = '')
    {
        $instance = new DatePicker($field,$title,DatePicker::TYPE_DATE,$value);
        self::setInstance($instance);
        return $instance;
    }

    public static function dateMonth($field,$title,$value = '')
    {
        $instance = new DatePicker($field,$title,DatePicker::TYPE_MONTH,$value);
        self::setInstance($instance);
        return $instance;
    }

    public static function dateYear($field,$title,$value = '')
    {
        $instance = new DatePicker($field,$title,DatePicker::TYPE_YEAR,$value);
        self::setInstance($instance);
        return $instance;
    }

    public static function dateTimeRange($field,$title,$startDate = '',$endDate = '')
    {
        $instance = new DatePicker($field,$title,DatePicker::TYPE_DATETIMERANGE,[$startDate,$endDate]);
        self::setInstance($instance);
        return $instance;
    }

    public static function datetime($field,$title,$value = '')
    {
        $instance = new DatePicker($field,$title,DatePicker::TYPE_DATETIME,$value);
        self::setInstance($instance);
        return $instance;
    }

    public static function dateRange($field,$title,$startDate = '',$endDate = '')
    {
        $instance = new DatePicker($field,$title,DatePicker::TYPE_DATERANGE,[$startDate,$endDate]);
        self::setInstance($instance);
        return $instance;
    }

    public static function time($field,$title,$time = '')
    {
        $instance = new TimePicker($field,$title,TimePicker::TYPE_TIME,$time);
        self::setInstance($instance);
        return $instance;
    }

    public static function timeRange($field,$title,$startTime = '',$endTime = '')
    {
        $instance = new TimePicker($field,$title,TimePicker::TYPE_TIMERANGE,[$startTime,$endTime]);
        self::setInstance($instance);
        return $instance;
    }

    public static function color($field,$title,$color = '#ffffff')
    {
        $instance = new ColorPicker($field,$title,$color);
        self::setInstance($instance);
        return $instance;
    }

    public static function upload($field,$title,$type = Upload::TYPE_DRAG)
    {
        $instance = new Upload($field,$title,$type);
        self::setInstance($instance);
        return $instance;
    }


    public static function builder()
    {
        $rules = [];
        foreach (self::$modelInstances as $model){
            $rules[] = $model->builder();
        }
        self::$modelInstances = [];
        return json($rules);
    }

}