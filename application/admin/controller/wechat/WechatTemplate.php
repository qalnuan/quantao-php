<?php

namespace app\admin\controller\wechat;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use service\UtilService as Util;
use service\JsonService as Json;
use service\WechatTemplateService;
use think\Cache;
use think\Request;
use think\Url;
use app\admin\model\wechat\WechatTemplate as WechatTemplateModel;
use app\admin\model\system\SystemConfig;

/**
 * 微信模板消息控制器
 * Class WechatTemplate
 * @package app\admin\controller\wechat
 */
class WechatTemplate extends AuthController
{

    protected $cacheTag = '_system_wechat';

    public function index()
    {
        $where = Util::getMore([
            ['name',''],
            ['status','']
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(WechatTemplateModel::SystemPage($where));
        $industry = Cache::tag($this->cacheTag)->remember('_wechat_industry',function(){
            $cache = WechatTemplateService::getIndustry();
            if(!$cache) return [];
            Cache::tag($this->cacheTag,['_wechat_industry']);
            return $cache->toArray();
        },0)?:[];
        !is_array($industry) && $industry = [];
        $this->assign('industry',$industry);
        return $this->fetch();
    }

    /**
     * 添加模板消息
     * @return mixed
     */
    public function create()
    {
        $this->assign(['title'=>'添加模板消息','action'=>Url::build('save'),'rules'=>$this->rules()->getContent()]);
        return $this->fetch('public/common_form');
    }
    public function rules()
    {
        FormBuilder::text('tempkey','模板编号');
        FormBuilder::text('tempid','模板ID');
        FormBuilder::text('name','模板名');
        FormBuilder::textarea('content','回复内容');
        FormBuilder::radio('status','状态',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],0);
        return FormBuilder::builder();
    }
    public function save(Request $request)
    {
        $data = Util::postMore([
            'tempkey',
            'tempid',
            'name',
            'content',
            ['status',0]
        ],$request);
        if($data['tempkey'] == '') return Json::fail('请输入模板编号');
        if($data['tempkey'] != '' && WechatTemplateModel::be($data['tempkey'],'tempkey'))
            return Json::fail('请输入模板编号已存在,请重新输入');
        if($data['tempid'] == '') return Json::fail('请输入模板ID');
        if($data['name'] == '') return Json::fail('请输入模板名');
        if($data['content'] == '') return Json::fail('请输入回复内容');
        $data['add_time'] = time();
        WechatTemplateModel::set($data);
        return Json::successful('添加模板消息成功!');
    }

    /**
     * 编辑模板消息
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $product = WechatTemplateModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'title'=>'编辑模板消息','rules'=>$this->read($id)->getContent(),
            'action'=>Url::build('update',array('id'=>$id))
        ]);
        return $this->fetch('public/common_form');
    }
    public function read($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $product = WechatTemplateModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        FormBuilder::text('tempkey','模板编号',$product->getData('tempkey'))->readonly();
        FormBuilder::text('name','模板名',$product->getData('name'))->readonly();
        FormBuilder::text('tempid','模板ID',$product->getData('tempid'));
        FormBuilder::radio('status','状态',[['value'=>1,'label'=>'开启'],['value'=>0,'label'=>'关闭']],$product->getData('status'));
        return FormBuilder::builder();
    }
    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'tempid',
            ['status',0]
        ],$request);
        if($data['tempid'] == '') return Json::fail('请输入模板ID');
        if(!$id) return $this->failed('数据不存在');
        $product = WechatTemplateModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        WechatTemplateModel::edit($data,$id);
        return Json::successful('修改成功!');
    }

    /**
     * 删除模板消息
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        if(!$id) return Json::fail('数据不存在!');
        if(!WechatTemplateModel::del($id))
            return Json::fail(WechatTemplateModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }


}