<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/14
 */

namespace app\admin\library\formBuilderDriver;


use think\Url;

class Upload extends Driver
{
    const TYPE_SELECT = 'select';

    const TYPE_DRAG = 'drag';

    protected $type = 'Upload';

    protected $props = [
        'multiple'=>false,
        'name'=>'file',
        'with-credentials'=>false,
        'show-upload-list'=>false,
        'mp-show-upload-list'=>true,
        'type'=>'select',
        'action'=>'',
        'format'=>['jpg','jpeg','png','gif'],
        'accept'=>'image/*',
        'default-file-list'=>[],
        'max-length'=>3,
        'max-size'=>2048
    ];

    public function __construct($field, $title, $type)
    {
        parent::__construct($field, $title);
        $this->defaultSetup();
        $this->type($type);
    }

    protected function defaultSetup()
    {
        $this->action(Url::build('upload'));
    }

    /**
     * 上传的地址，必填
     * @param $action
     * @return $this
     */
    public function action($action)
    {
        $this->setProps('action', $action);
        return $this;
    }

    /**
     * 设置上传的请求头部
     * @param array $headers
     * @return $this
     */
    public function headers($headers = [])
    {
        if(is_array($headers))
            $this->setProps('headers', $headers);
        return $this;
    }

    /**
     * 是否支持多选文件
     * @param $multiple
     * @return $this
     */
    public function multiple($multiple = true)
    {
        $this->setProps('multiple', $multiple);
        return $this;
    }

    /**
     * 上传时附带的额外参数
     * @param array $data
     * @return $this
     */
    public function data($data = [])
    {
        if(is_array($data))
            $this->setProps('data', $data);
        return $this;
    }

    /**
     * 上传的文件字段名
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        $this->setProps('name', $name);
        return $this;
    }

    /**
     * 支持发送 cookie 凭证信息
     * @param $withCredentials
     * @return $this
     */
    public function withCredentials($withCredentials = true)
    {
        $this->setProps('with-credentials', $withCredentials);
        return $this;
    }

    /**
     * 是否显示已上传文件列表
     * @param $showUploadList
     * @return $this
     */
    public function showUploadList($showUploadList = false)
    {
        $this->setProps('show-upload-list', $showUploadList);
        return $this;
    }

    /**
     * 上传控件的类型，可选值为 select（点击选择），drag（支持拖拽）
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->setProps('type', $type);
        return $this;
    }

    /**
     * 接受上传的文件类型
     * @param $accept
     * @return $this
     */
    public function accept($accept)
    {
        $this->setProps('accept', $accept);
        return $this;
    }

    /**
     * 支持的文件类型，与 accept 不同的是，format 是识别文件的后缀名，accept 为 input 标签原生的 accept 属性，
     * 会在选择文件时过滤，可以两者结合使用
     * @param array $format
     * @return $this
     */
    public function format($format = [])
    {
        $format = $this->toArray($format);
        $this->setProps('format', $format);
        return $this;
    }

    /**
     * 文件大小限制，单位 kb
     * @param $maxSize
     * @return $this
     */
    public function maxSize($maxSize)
    {
        $this->setProps('max-size', $maxSize);
        return $this;
    }

    /**
     * 最多上传几张图片 0为无限
     * @param $length
     * @return $this
     */
    public function maxLength($length = 1)
    {
        $this->setProps('max-length',$length);
        return $this;
    }

    /**
     * 默认已上传的文件列表，例如：
     * [
     *      {
     *          name: 'img1.jpg',
     *          url: 'http://www.xxx.com/img1.jpg'
     *      },
     *      {
     *          name: 'img2.jpg',
     *          url: 'http://www.xxx.com/img2.jpg'
     *      }
     * ]
     * @param $defaultFileList
     * @return $this
     */
    public function defaultFileList($defaultFileList)
    {
        $defaultFileList = $this->toArray($defaultFileList);
        $this->value = $defaultFileList;
        return $this;
    }

    public function builder()
    {
        is_array($this->value) || ($this->value = []);
        return $this->result();
    }
}