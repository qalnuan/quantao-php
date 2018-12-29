<?php

namespace app\admin\controller\store;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreProduct as ProductModel;
use app\admin\model\store\StoreCombinationAttr;
use app\admin\model\store\StoreCombinationAttrResult;
use app\admin\model\store\StoreCombination as StoreCombinationModel;
use think\Url;
use app\admin\model\system\SystemAttachment;

/**
 * 拼团管理
 * Class StoreCombination
 * @package app\admin\controller\store
 */
class StoreCombination extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = StoreCombinationModel::class;

    /**
     * @return mixed
     */
    public function index()
    {
        $where = Util::getMore([
            ['is_show',''],
            ['is_host',''],
            ['store_name',''],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(StoreCombinationModel::systemPage($where));
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->assign(['title'=>'添加拼团','action'=>Url::build('save'),'rules'=>$this->rules()->getContent()]);
        return $this->fetch('public/common_form');
    }

    /**
     * @return \think\response\Json
     */
    public function rules()
    {
        FormBuilder::select('product_id','产品名称',function(){
            $list = ProductModel::getTierList();
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['store_name'].'/'.$menu['id']];
            }
            return $menus;
        });
        FormBuilder::text('title','拼团名称');
        FormBuilder::text('info','拼团简介');
        FormBuilder::upload('image','拼团主图片(305*305px)')->maxLength(1);
        FormBuilder::upload('images','拼团轮播图(640*640px)')->maxLength(5)->multiple();
        FormBuilder::number('price','拼团售价')->min(0);
        FormBuilder::number('people','拼团人数')->min(0)->precision(0);
        FormBuilder::datetimerange('time','拼团时间')->format('yyyy-MM-dd HH:mm');
        FormBuilder::number('sales','销量')->min(0)->precision(0);
        FormBuilder::number('stock','库存')->min(0)->precision(0);
        FormBuilder::number('postage','邮费')->min(0);
        FormBuilder::number('sort','排序');
        FormBuilder::radio('is_show','产品状态',[['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]],0);
        FormBuilder::radio('is_host','热卖单品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
//        FormBuilder::radio('mer_use','商户是否可用',[['label'=>'可用','value'=>1],['label'=>'不可用','value'=>0]],0);
        FormBuilder::radio('is_postage','是否包邮',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
        return FormBuilder::builder();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','store/product/'.date('Ymd'));
        $thumbPath = Upload::thumb($res->dir);
        //产品图片上传记录
        $fileInfo = $res->fileInfo->getinfo();
        SystemAttachment::attachmentAdd($res->fileInfo->getSaveName(),$fileInfo['size'],$fileInfo['type'],$res->dir,$thumbPath,2);
        if($res->status == 200)
            return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = Util::postMore([
            'product_id',
            'title',
            'info',
            ['image',[]],
            ['images',[]],
            ['time',[]],
            'postage',
            'price',
            'people',
            'sort',
            'stock',
            'sales',
            ['is_show',0],
            ['is_host',0],
            ['mer_use',0],
            ['is_postage',0],
        ],$request);
        if($data['product_id'] == '') return Json::fail('请选择产品名称');
        if(!$data['title']) return Json::fail('请输入拼团名称');
        if(!$data['info']) return Json::fail('请输入拼团简介');
        if(count($data['image'])<1) return Json::fail('请上传产品图片');
        if(count($data['images'])<1) return Json::fail('请上传产品轮播图');
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品售价');
        if($data['people'] == '' || $data['people'] < 1) return Json::fail('请输入拼团人数');
        if($data['time'][0] == '' || $data['time'][1] == '') return Json::fail('请选择拼团时间');
//        if($data['postage'] == '' || $data['postage'] < 0) return Json::fail('请输入邮费');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
//        if($data['sales'] == '' || $data['sales'] < 0) return Json::fail('请输入销量');
//        if($data['give_integral'] == '' || $data['give_integral'] < 0) return Json::fail('请输入赠送积分');
        $data['image'] = $data['image'][0];
        $data['images'] = json_encode($data['images']);
        $data['add_time'] = time();
        $data['start_time'] = $data['time'][0];
        $data['stop_time'] = $data['time'][1];
        $data['description'] = '';
        unset($data['time']);
        StoreCombinationModel::set($data);
        return Json::successful('添加拼团成功!');
    }


    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $product = StoreCombinationModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'content'=>StoreCombinationModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $product = StoreCombinationModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'title'=>'编辑产品','rules'=>$this->read($id)->getContent(),
            'action'=>Url::build('update',array('id'=>$id))
        ]);
        return $this->fetch('public/common_form');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $product = StoreCombinationModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        FormBuilder::text('product_id','产品名称',ProductModel::where('id',$product->getData('product_id'))->value('store_name').'/'.$product->getData('product_id'))->disabled();
        FormBuilder::text('title','拼团名称',$product->getData('title'));
        FormBuilder::text('info','拼团简介',$product->getData('info'));
        FormBuilder::upload('image','拼团主图片(305*305px)')->defaultFileList($product->getData('image'))->maxLength(1);
        FormBuilder::upload('images','拼团轮播图(305*305px)')->defaultFileList(json_decode($product->getData('images')))->maxLength(5)->multiple();
        FormBuilder::number('price','拼团售价',$product->getData('price'))->min(0);
        FormBuilder::number('people','拼团人数',$product->getData('people'))->min(0)->precision(0);
        FormBuilder::datetimerange('time','拼团时间')->format('yyyy-MM-dd HH:mm')->value([$product->getData('start_time'),$product->getData('stop_time')]);
        FormBuilder::number('sales','销量',$product->getData('sales'))->min(0)->precision(0);
        FormBuilder::number('stock','库存',$product->getData('stock'))->min(0)->precision(0);
        FormBuilder::number('postage','邮费',$product->getData('postage'))->min(0);
        FormBuilder::number('sort','排序',$product->getData('sort'));
        FormBuilder::radio('is_show','产品状态',[['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]],$product->getData('is_show'));
        FormBuilder::radio('is_host','热卖单品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_host'));
//        FormBuilder::radio('mer_use','商户是否可用',[['label'=>'可用','value'=>1],['label'=>'不可用','value'=>0]],$product->getData('mer_use'));
        FormBuilder::radio('is_postage','是否包邮',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_postage'));
        return FormBuilder::builder();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'title',
            'info',
            ['image',[]],
            ['images',[]],
            ['time',[]],
            'postage',
            'price',
            'people',
            'sort',
            'stock',
            'sales',
            ['is_show',0],
            ['is_host',0],
            ['mer_use',0],
            ['is_postage',0],
        ],$request);
//        if($data['product_id'] == '') return Json::fail('请选择产品名称');
        if(!$data['title']) return Json::fail('请输入拼团名称');
        if(!$data['info']) return Json::fail('请输入拼团简介');
        if(count($data['image'])<1) return Json::fail('请上传产品图片');
        if(count($data['images'])<1) return Json::fail('请上传产品轮播图');
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品售价');
        if($data['people'] == '' || $data['people'] < 1) return Json::fail('请输入拼团人数');
        if($data['time'][0] == '' || $data['time'][1] == '') return Json::fail('请选择拼团时间');
//        if($data['postage'] == '' || $data['postage'] < 0) return Json::fail('请输入邮费');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
//        if($data['sales'] == '' || $data['sales'] < 0) return Json::fail('请输入销量');
//        if($data['give_integral'] == '' || $data['give_integral'] < 0) return Json::fail('请输入赠送积分');
        $data['image'] = $data['image'][0];
        $data['images'] = json_encode($data['images']);
        $data['start_time'] = $data['time'][0];
        $data['stop_time'] = $data['time'][1];
        unset($data['time']);
        StoreCombinationModel::edit($data,$id);
        return Json::successful('修改成功!');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $data['is_del'] = 1;
        if(!StoreCombinationModel::edit($data,$id))
            return Json::fail(StoreCombinationModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**
     * 属性页面
     * @param $id
     * @return mixed|void
     */
    public function attr($id)
    {
        if(!$id) return $this->failed('数据不存在!');
        $result = StoreCombinationAttrResult::getResult($id);
        $image = StoreCombinationModel::where('id',$id)->value('image');
        $this->assign(compact('id','result','product','image'));
        return $this->fetch();
    }

    /**
     * 生成属性
     * @param int $id
     */
    public function is_format_attr($id = 0){
        if(!$id) return Json::fail('产品不存在');
        list($attr,$detail) = Util::postMore([
            ['items',[]],
            ['attrs',[]]
        ],$this->request,true);
        $product = StoreCombinationModel::get($id);
        if(!$product) return Json::fail('产品不存在');
        $attrFormat = attrFormat($attr)[1];
        if(count($detail)){
            foreach ($attrFormat as $k=>$v){
                foreach ($detail as $kk=>$vv){
                    if($v['detail'] == $vv['detail']){
                        $attrFormat[$k]['price'] = $vv['price'];
                        $attrFormat[$k]['sales'] = $vv['sales'];
                        $attrFormat[$k]['pic'] = $vv['pic'];
                        $attrFormat[$k]['check'] = false;
                        break;
                    }else{
                        $attrFormat[$k]['price'] = '';
                        $attrFormat[$k]['sales'] = '';
                        $attrFormat[$k]['pic'] = $product['image'];
                        $attrFormat[$k]['check'] = true;
                    }
                }
            }
        }else{
            foreach ($attrFormat as $k=>$v){
                $attrFormat[$k]['price'] = $product['price'];
                $attrFormat[$k]['sales'] = $product['stock'];
                $attrFormat[$k]['pic'] = $product['image'];
                $attrFormat[$k]['check'] = false;
            }
        }
        return Json::successful($attrFormat);
    }

    /**
     * 添加 修改属性
     * @param $id
     */
    public function set_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        list($attr,$detail) = Util::postMore([
            ['items',[]],
            ['attrs',[]]
        ],$this->request,true);
        $res = StoreCombinationAttr::createProductAttr($attr,$detail,$id);
        if($res)
            return $this->successful('编辑属性成功!');
        else
            return $this->failed(StoreCombinationAttr::getErrorInfo());
    }

    /**
     * 清除属性
     * @param $id
     */
    public function clear_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        if(false !== StoreCombinationAttr::clearProductAttr($id) && false !== StoreCombinationAttrResult::clearResult($id))
            return $this->successful('清空产品属性成功!');
        else
            return $this->failed(StoreCombinationAttr::getErrorInfo('清空产品属性失败!'));
    }
}
