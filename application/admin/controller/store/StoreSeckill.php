<?php

namespace app\admin\controller\store;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use app\admin\model\store\StoreProduct;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreProduct as ProductModel;
use think\Url;
use app\admin\model\store\StoreSeckill as StoreSeckillModel;

/**
 * 限时秒杀  控制器
 * Class StoreSeckill
 * @package app\admin\controller\store
 */
class StoreSeckill extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = StoreSeckillModel::class;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = Util::getMore([
            ['status',''],
            ['store_name',''],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(StoreSeckillModel::systemPage($where));
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->assign(['title'=>'添加产品','action'=>Url::build('save'),'rules'=>$this->rules()->getContent()]);
        return $this->fetch('public/common_form');
    }

    /**
     * @return \think\response\Json
     */
    public function rules()
    {
//        FormBuilder::number('product_id','产品ID')->min(0);
        FormBuilder::text('title','产品标题');
        FormBuilder::text('info','产品简介');
        FormBuilder::text('unit_name','单位')->placeholder('个、位');
        FormBuilder::dateTimeRange('section_time','活动时间')->format("yyyy-MM-dd HH:mm:ss");
        FormBuilder::upload('img','推荐图(305*305px)')->maxLength(1);
        FormBuilder::upload('images','轮播图图(640*640px)')->maxLength(5);
        FormBuilder::number('price','秒杀价')->min(0);
        FormBuilder::number('ot_price','原价')->min(0);
        FormBuilder::number('stock','库存')->min(0)->precision(0);
        FormBuilder::number('sales','销量')->min(0)->precision(0);
        FormBuilder::number('sort','排序');
        FormBuilder::number('num','单次购买产品个数',1)->precision(0);
        FormBuilder::number('give_integral','赠送积分')->min(0)->precision(0);
        FormBuilder::number('postage','邮费')->min(0);
        FormBuilder::radio('is_postage','是否包邮',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
        FormBuilder::radio('is_hot','热门推荐',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],0);
        FormBuilder::radio('status','活动状态',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],0);
        return FormBuilder::builder();
    }
    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','store/seckill');
        $thumbPath = Upload::thumb($res->dir);
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
            'title',
            'info',
            'unit_name',
            ['img',[]],
            ['images',[]],
            'price',
            'ot_price',
            'sales',
            'stock',
            'sort',
            'num',
            'give_integral',
            'postage',
            ['section_time',[]],
            ['is_postage',0],
            ['is_hot',0],
            ['status',0],
        ],$request);
//        if($data['product_id'] == '') return Json::fail('请选择产品ID');
//        $image = StoreProduct::where('id',$data['product_id'])->where('is_del',0)->where('is_show',1)->column('id,image');
//        if(!$image) return Json::fail('产品不存,请重新选择');
        if(!$data['title']) return Json::fail('请输入产品标题');
        if(!$data['unit_name']) return Json::fail('请输入产品单位');
        if(count($data['section_time'])<1) return Json::fail('请选择活动时间');
        $data['start_time'] = $data['section_time'][0];
        $data['stop_time'] = $data['section_time'][1];
        unset($data['section_time']);
        if(count($data['img'])<1) return Json::fail('请选择推荐图');
        $data['image'] = $data['img'][0];
        if(count($data['images'])<1) return Json::fail('请选择轮播图');
        $data['images'] = json_encode($data['images']);
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品秒杀售价');
        if($data['ot_price'] == '' || $data['ot_price'] < 0) return Json::fail('请输入产品原售价');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
        unset($data['img']);
        $data['add_time'] = time();
        StoreSeckillModel::set($data);
        return Json::successful('添加产品成功!');
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
        $product = StoreSeckillModel::get($id);
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
        $product = StoreSeckillModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        FormBuilder::text('title','产品标题',$product->getData('title'));
        FormBuilder::text('info','产品简介',$product->getData('info'));
        FormBuilder::text('unit_name','单位',$product->getData('unit_name'))->placeholder('个、位');
        FormBuilder::dateTimeRange('section_time','活动时间',$product->getData('start_time'),$product->getData('stop_time'))->format("yyyy-MM-dd HH:mm:ss");
        FormBuilder::upload('img','推荐图(305*305px)')->maxLength(1)->defaultFileList($product->getData('image'));
        FormBuilder::upload('images','轮播图图(640*640px)')->maxLength(5)->defaultFileList(json_decode($product->getData('images'),true));
        FormBuilder::number('price','*秒杀价',$product->getData('price'))->min(0);
        FormBuilder::number('ot_price','*原价',$product->getData('ot_price'))->min(0);
        FormBuilder::number('stock','*库存',$product->getData('stock'))->min(0)->precision(0);
        FormBuilder::number('sales','销量',$product->getData('sales'))->min(0)->precision(0);
        FormBuilder::number('sort','排序',$product->getData('sort'));
        FormBuilder::number('num','*单次秒杀个数',$product->getData('num'))->precision(0);
        FormBuilder::number('give_integral','赠送积分',$product->getData('give_integral'))->min(0)->precision(0);
        FormBuilder::number('postage','邮费',$product->getData('postage'))->min(0);
        FormBuilder::radio('is_postage','是否包邮',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_postage'));
        FormBuilder::radio('is_hot','热门推荐',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],$product->getData('is_hot'));
        FormBuilder::radio('status','活动状态',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],$product->getData('status'));
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
        if(!$id) return $this->failed('数据不存在!');
        $data = Util::postMore([
            'title',
            'info',
            'unit_name',
            ['img',[]],
            ['images',[]],
            'price',
            'ot_price',
            'sales',
            'stock',
            'sort',
            'num',
            'give_integral',
            'postage',
            ['section_time',[]],
            ['is_postage',0],
            ['is_hot',0],
            ['status',0],
        ],$request);
        if(!$data['title']) return Json::fail('请输入产品标题');
        if(!$data['unit_name']) return Json::fail('请输入产品单位');
        if(count($data['section_time'])<1) return Json::fail('请选择活动时间');
        $data['start_time'] = $data['section_time'][0];
        $data['stop_time'] = $data['section_time'][1];
        unset($data['section_time']);
        if(count($data['img'])<1) return Json::fail('请选择推荐图');
        $data['image'] = $data['img'][0];
        if(count($data['images'])<1) return Json::fail('请选择轮播图');
        $data['images'] = json_encode($data['images']);
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品秒杀售价');
        if($data['ot_price'] == '' || $data['ot_price'] < 0) return Json::fail('请输入产品原售价');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
        if($data['num'] == '' || $data['num'] <= 0) return Json::fail('请输入单次秒杀个数');
        unset($data['img']);
        StoreSeckillModel::edit($data,$id);
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
        $product = StoreSeckillModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        StoreProduct::edit(['is_seckill'=>0],$product['product_id']);
        if(!StoreSeckillModel::edit($data,$id))
            return Json::fail(StoreSeckillModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $seckill = StoreSeckillModel::get($id);
        if(!$seckill) return Json::fail('数据不存在!');
        $this->assign([
            'content'=>StoreSeckillModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }

    public function edit_rule($id){
        if(!$id) return $this->failed('数据不存在');
        $seckill = StoreSeckillModel::get($id);
        if(!$seckill) return Json::fail('数据不存在!');
        $this->assign([
            'content'=>StoreSeckillModel::where('id',$id)->value('rule'),
            'field'=>'rule',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'rule'])
        ]);
        return $this->fetch('public/edit_content');
    }
}
