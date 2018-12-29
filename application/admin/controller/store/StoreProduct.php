<?php

namespace app\admin\controller\store;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use app\admin\model\store\StoreProductAttr;
use app\admin\model\store\StoreProductAttrResult;
use app\admin\model\store\StoreProductRelation;
use app\admin\model\store\StoreSeckill;
use app\admin\model\system\SystemConfig;
use app\admin\model\store\StoreBargain;
use service\JsonService;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreCategory as CategoryModel;
use app\admin\model\store\StoreProduct as ProductModel;
use think\Url;
use app\admin\model\store\StoreSeckill as StoreSeckillModel;
use app\admin\model\store\StoreOrder as StoreOrderModel;
use app\admin\model\store\StoreBargain as StoreBargainModel;

/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
 */
class StoreProduct extends AuthController
{

    use CurdControllerTrait;

    protected $bindModel = ProductModel::class;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = Util::getMore([
            ['is_show',''],
            ['is_hot',''],
            ['is_benefit',''],
            ['is_best',''],
            ['is_new',''],
            ['data',''],
            ['sex',''],
            ['sex1',''],
            ['store_name',''],
            ['export',0]
        ],$this->request);
        $limitTimeList = [
            'today'=>implode(' - ',[date('Y/m/d'),date('Y/m/d',strtotime('+1 day'))]),
            'week'=>implode(' - ',[
                date('Y/m/d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
                date('Y/m/d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600))
            ]),
            'month'=>implode(' - ',[date('Y/m').'/01',date('Y/m').'/'.date('t')]),
            'quarter'=>implode(' - ',[
                date('Y').'/'.(ceil((date('n'))/3)*3-3+1).'/01',
                date('Y').'/'.(ceil((date('n'))/3)*3).'/'.date('t',mktime(0,0,0,(ceil((date('n'))/3)*3),1,date('Y')))
            ]),
            'year'=>implode(' - ',[
                date('Y').'/01/01',date('Y/m/d',strtotime(date('Y').'/01/01 + 1year -1 day'))
            ])
        ];
        $this->assign('where',$where);
        $this->assign('limitTimeList',$limitTimeList);
        $this->assign(ProductModel::systemPage($where,$this->adminId));
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
        $list = CategoryModel::getTierList();
        if(!$list) return $this->failed('请先添加分类');
        FormBuilder::select('cate_id','产品分类',function(){
            $list = CategoryModel::getTierList();
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['html'].$menu['cate_name']];//,'disabled'=>$menu['pid']== 0];
            }
            return $menus;
        });
        FormBuilder::text('store_name','产品名称');
//        FormBuilder::text('store_info','产品简介');
        FormBuilder::text('keyword','产品关键字')->placeholder('多个用英文状态下的逗号隔开');
        FormBuilder::text('unit_name','产品单位','件');
        FormBuilder::upload('image','产品主图片(305*305px)')->maxLength(1);
        FormBuilder::upload('slider_image','产品轮播图(640*640px)')->maxLength(5)->multiple();
        FormBuilder::number('price','产品售价')->min(0);
        FormBuilder::number('ot_price','产品市场价')->min(0);
//        FormBuilder::number('give_integral','赠送积分')->min(0)->precision(0);
        FormBuilder::number('postage','邮费')->min(0);
        FormBuilder::number('sales','销量')->min(0)->precision(0);
        FormBuilder::number('stock','库存')->min(0)->precision(0);
//        FormBuilder::number('cost','产品成本价')->min(0);
        FormBuilder::number('sort','排序');
        FormBuilder::radio('is_show','产品状态',[['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]],0);
        FormBuilder::radio('is_hot','热卖单品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
        FormBuilder::radio('is_benefit','促销单品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
        FormBuilder::radio('is_best','精品推荐',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
        FormBuilder::radio('is_new','首发新品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
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
        $res = Upload::image('file','store/product');
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
            'cate_id',
            'store_name',
            'store_info',
            'keyword',
            ['unit_name','件'],
            ['image',[]],
            ['slider_image',[]],
            'postage',
            'ot_price',
            'price',
            'sort',
            'stock',
            'sales',
            ['give_integral',0],
            ['is_show',0],
            ['cost',0],
            ['is_hot',0],
            ['is_benefit',0],
            ['is_best',0],
            ['is_new',0],
            ['mer_use',0],
            ['is_postage',0],
        ],$request);
        if($data['cate_id'] == '') return Json::fail('请选择产品分类');
        if(!$data['store_name']) return Json::fail('请输入产品名称');
//        if(!$data['store_info']) return Json::fail('请输入产品简介');
//        if(!$data['keyword']) return Json::fail('请输入产品关键字');
        if(count($data['image'])<1) return Json::fail('请上传产品图片');
        if(count($data['slider_image'])<1) return Json::fail('请上传产品轮播图');
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品售价');
        if($data['ot_price'] == '' || $data['ot_price'] < 0) return Json::fail('请输入产品市场价');
        if($data['postage'] == '' || $data['postage'] < 0) return Json::fail('请输入邮费');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
//        if($data['cost'] == '' || $data['cost'] < 0) return Json::fail('请输入产品成本价');
        if($data['sales'] == '' || $data['sales'] < 0) return Json::fail('请输入销量');
//        if($data['give_integral'] < 0) return Json::fail('请输入赠送积分');
        $data['image'] = $data['image'][0];
        $data['slider_image'] = json_encode($data['slider_image']);
        $data['add_time'] = time();
        $data['description'] = '';
        ProductModel::set($data);
        return Json::successful('添加产品成功!');
    }


    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'content'=>ProductModel::where('id',$id)->value('description'),
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
        $product = ProductModel::get($id);
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
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        FormBuilder::select('cate_id','产品分类',function(){
            $list = CategoryModel::getTierList();
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['html'].$menu['cate_name']];//,'disabled'=>$menu['pid']== 0];
            }
            return $menus;
        },$product->getData('cate_id'));
        FormBuilder::text('store_name','产品名称',$product->getData('store_name'));
//        FormBuilder::text('store_info','产品简介',$product->getData('store_info'));
        FormBuilder::text('keyword','产品关键字',$product->getData('keyword'));
        FormBuilder::text('unit_name','产品单位',$product->getData('unit_name'),'件');
        FormBuilder::upload('image','产品主图片(305*305px)')->defaultFileList($product->getData('image'))->maxLength(1);
        FormBuilder::upload('slider_image','产品轮播图(640*640px)')->defaultFileList(json_decode($product->getData('slider_image'),true))->maxLength(5)->multiple();
        FormBuilder::number('price','产品售价',$product->getData('price'))->min(0);
        FormBuilder::number('ot_price','产品市场价',$product->getData('ot_price'))->min(0);
//        FormBuilder::number('give_integral','赠送积分',$product->getData('give_integral'))->min(0)->precision(0);
        FormBuilder::number('postage','邮费',$product->getData('postage'))->min(0);
        FormBuilder::number('sales','销量',$product->getData('sales'))->min(0)->precision(0);
        FormBuilder::number('stock','库存',$product->getData('stock'))->min(0)->precision(0);
//        FormBuilder::number('cost','产品成本价',$product->getData('cost'))->min(0);
        FormBuilder::number('sort','排序',$product->getData('sort'));
        FormBuilder::radio('is_show','产品状态',[['label'=>'上架','value'=>1],['label'=>'下架','value'=>0]],$product->getData('is_show'));
        FormBuilder::radio('is_hot','热卖单品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_hot'));
        FormBuilder::radio('is_benefit','促销单品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_benefit'));
        FormBuilder::radio('is_best','精品推荐',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_best'));
        FormBuilder::radio('is_new','首发新品',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_new'));
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
            'cate_id',
            'store_name',
            'store_info',
            'keyword',
            ['unit_name','件'],
            ['image',[]],
            ['slider_image',[]],
            'postage',
            'ot_price',
            'price',
            'sort',
            'stock',
            'sales',
            ['give_integral',0],
            ['is_show',0],
            ['cost',0],
            ['is_hot',0],
            ['is_benefit',0],
            ['is_best',0],
            ['is_new',0],
            ['mer_use',0],
            ['is_postage',0],
        ],$request);
        if($data['cate_id'] == '') return Json::fail('请选择产品分类');
        if(!$data['store_name']) return Json::fail('请输入产品名称');
//        if(!$data['store_info']) return Json::fail('请输入产品简介');
//        if(!$data['keyword']) return Json::fail('请输入产品关键字');
        if(count($data['image'])<1) return Json::fail('请上传产品图片');
        if(count($data['slider_image'])<1) return Json::fail('请上传产品轮播图');
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入产品售价');
        if($data['ot_price'] == '' || $data['ot_price'] < 0) return Json::fail('请输入产品市场价');
        if($data['postage'] == '' || $data['postage'] < 0) return Json::fail('请输入邮费');
//        if($data['cost'] == '' || $data['cost'] < 0) return Json::fail('请输入产品成本价');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
        if($data['sales'] == '' || $data['sales'] < 0) return Json::fail('请输入销量');
//        if($data['give_integral'] < 0) return Json::fail('请输入赠送积分');
        $data['image'] = $data['image'][0];
        $data['slider_image'] = json_encode($data['slider_image']);
        ProductModel::edit($data,$id);
        return Json::successful('修改成功!');
    }

    public function attr($id)
    {
        if(!$id) return $this->failed('数据不存在!');
        $result = StoreProductAttrResult::getResult($id);
        $image = ProductModel::where('id',$id)->value('image');
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
        $product = ProductModel::get($id);
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
                        $attrFormat[$k]['price'] = $product['price'];
                        $attrFormat[$k]['sales'] = $product['stock'];
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

    public function set_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        list($attr,$detail) = Util::postMore([
            ['items',[]],
            ['attrs',[]]
        ],$this->request,true);
        $res = StoreProductAttr::createProductAttr($attr,$detail,$id);
        if($res)
            return $this->successful('编辑属性成功!');
        else
            return $this->failed(StoreProductAttr::getErrorInfo());
    }

    public function clear_attr($id)
    {
        if(!$id) return $this->failed('产品不存在!');
        if(false !== StoreProductAttr::clearProductAttr($id) && false !== StoreProductAttrResult::clearResult($id))
            return $this->successful('清空产品属性成功!');
        else
            return $this->failed(StoreProductAttr::getErrorInfo('清空产品属性失败!'));
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
        if(!ProductModel::edit($data,$id))
            return Json::fail(ProductModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function seckill($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'title'=>'添加产品','rules'=>$this->readSeckill($id)->getContent(),
            'action'=>Url::build('updateSeckill',array('id'=>$id))
        ]);
        return $this->fetch('public/common_form');
    }


    public function readSeckill($id)
    {
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $storeseckill=StoreSeckill::where('product_id',$id)->find();
        if(!empty($storeseckill)){ProductModel::edit(['is_seckill'=>1],$id,'id');return $this->failed('秒杀已开启！');}
        FormBuilder::text('title','产品标题',$product->getData('store_name'));
        FormBuilder::text('info','产品简介',$product->getData('store_info'));
        FormBuilder::text('unit_name','单位')->placeholder('个、位');
        FormBuilder::dateTimeRange('section_time','活动时间')->format("yyyy-MM-dd HH:mm:ss");
        FormBuilder::upload('img','推荐图(305*305px)')->maxLength(1)->defaultFileList($product->getData('image'));
        FormBuilder::upload('images','轮播图(640*640px)')->maxLength(5)->defaultFileList(json_decode($product->getData('slider_image'),true));
        FormBuilder::number('price','秒杀价')->min(0);
        FormBuilder::number('ot_price','原价',$product->getData('price'))->min(0);
        FormBuilder::number('stock','库存',$product->getData('stock'))->min(0)->precision(0);
        FormBuilder::number('sales','销量',$product->getData('sales'))->min(0)->precision(0);
//        FormBuilder::number('cost','产品成本价',$product->getData('cost'))->min(0);
        FormBuilder::number('sort','排序',$product->getData('sort'))->precision(0);
//        FormBuilder::number('give_integral','赠送积分',$product->getData('give_integral'))->min(0)->precision(0);
        FormBuilder::number('postage','邮费',$product->getData('postage'))->min(0);
        FormBuilder::radio('is_postage','是否包邮',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],$product->getData('is_postage'));
        FormBuilder::radio('is_hot','热门推荐',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],0);
        FormBuilder::radio('status','活动状态',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],0);
        return FormBuilder::builder();
    }

    public function updateSeckill(Request $request,$id)
    {
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $data = Util::postMore([
            'title',
            'info',
            'unit_name',
            ['img',[]],
            ['images',[]],
            'price',
            'ot_price',
            'cost',
            'sales',
            'stock',
            'sort',
            'give_integral',
            'postage',
            ['section_time',[]],
            ['is_postage',0],
            ['cost',0],
            ['is_hot',0],
            ['status',0],
        ],$request);
        $data['product_id'] = $product['id'];
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
//        if($data['cost'] == '' || $data['cost'] < 0) return Json::fail('请输入产品成本价');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
        unset($data['img']);
        $data['add_time'] = time();
        StoreSeckillModel::set($data);
        ProductModel::edit(['is_seckill'=>1],$id,'id');
        return Json::successful('开启成功!');
    }


    /**
     * 点赞
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function collect($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign(StoreProductRelation::getCollect($id));
        return $this->fetch();
    }

    /**
     * 收藏
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function like($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign(StoreProductRelation::getLike($id));
        return $this->fetch();
    }
    /**
     * 产品统计
     * @return mixed
     */
    public function statistics(){
        $where = Util::getMore([
            ['is_show',''],
            ['is_hot',''],
            ['is_benefit',''],
            ['is_best',''],
            ['is_new',''],
            ['data',''],
            ['sex',''],
            ['sex1',''],
            ['store_name',''],
            ['export',0]
        ],$this->request);
        $limitTimeList = [
            'today'=>implode(' - ',[date('Y/m/d'),date('Y/m/d',strtotime('+1 day'))]),
            'week'=>implode(' - ',[
                date('Y/m/d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
                date('Y/m/d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600))
            ]),
            'month'=>implode(' - ',[date('Y/m').'/01',date('Y/m').'/'.date('t')]),
            'quarter'=>implode(' - ',[
                date('Y').'/'.(ceil((date('n'))/3)*3-3+1).'/01',
                date('Y').'/'.(ceil((date('n'))/3)*3).'/'.date('t',mktime(0,0,0,(ceil((date('n'))/3)*3),1,date('Y')))
            ]),
            'year'=>implode(' - ',[
                date('Y').'/01/01',date('Y/m/d',strtotime(date('Y').'/01/01 + 1year -1 day'))
            ])
        ];
        $replenishment_num = SystemConfig::getValue('replenishment_num');
        $replenishment_num = $replenishment_num > 0 ? $replenishment_num : 20;
        $stock=ProductModel::column('stock');
        $sums=array_sum($stock);
        $is_new=ProductModel::where('is_new',1)->column('stock');
        $new=array_sum($is_new);
        $stores=StoreOrderModel::column('total_num');
        $strsum=array_sum($stores);
        $stock1=ProductModel::where('stock','<',$replenishment_num)->column('stock');
        $cunt=count($stock1);
        $stk=[];
        for($i=0;$i<$cunt;$i++){
            $stk[]=$replenishment_num-$stock1[$i];
        }
        $lack=array_sum($stk);
        $header=[
            ['name'=>'商品总数', 'class'=>'fa fa-ioxhost', 'value'=>$sums, 'color'=>'red'],
            ['name'=>'新增商品', 'class'=>'fa-line-chart', 'value'=>$new, 'color'=>'lazur'],
            ['name'=>'活动商品', 'class'=>'fa-bar-chart', 'value'=>$strsum, 'color'=>'navy'],
            ['name'=>'缺货商品', 'class'=>'fa-cube', 'value'=>$lack, 'color'=>'yellow']
        ];
//        var_dump($where);
        //进度条的颜色
        $color=['progress-bar-success','progress-bar-info','progress-bar-warning','progress-bar-success','progress-bar-danger','progress-bar-warning','progress-bar-info','progress-bar-warning','progress-bar-danger','progress-bar-success'];
        if($where['data']!=''){
            $dat=explode('-',$where['data']);
            $orderPrice = StoreOrderModel::whereTime('add_time', 'between', $dat)->select();
            $sto=ProductModel::salesVolume($where,$color,$dat);//销量前十
            $stores=$sto['c1'];
            $storeSum1=$sto['c2'];
            $total=$sto['c3'];
            $price=$sto['c4'];
            $sto1=ProductModel::profit($where,$color,$dat);//利润前十
            $stor=$sto1['c1'];
            $priceSum=$sto1['c2'];
            $total1=$sto1['c3'];
            $price1=$sto1['c4'];
            $stock=ProductModel::where('stock','<',$replenishment_num)->order('stock asc')->select();//库存
            $stor1=ProductModel::ncomment($dat);//差评
            $refund=ProductModel::refund($dat);//退款
        }else {
            $orderPrice = StoreOrderModel::select();
            $sto=ProductModel::salesVolume($where,$color);//销量前十
            $stores=$sto['c1'];
            $storeSum1=$sto['c2'];
            $total=$sto['c3'];
            $price=$sto['c4'];
            $sto1=ProductModel::profit($where,$color);//利润前十
            $stor=$sto1['c1'];
            $priceSum=$sto1['c2'];
            $total1=$sto1['c3'];
            $price1=$sto1['c4'];
            $stock=ProductModel::where('stock','<',$replenishment_num)->order('stock asc')->select();//库存
            $stor1=ProductModel::ncomment();//差评
            $refund=ProductModel::refund();//退款
        }
        $orde=ProductModel::brokenLine($orderPrice);//销量折线图
        $orderDays=$orde['c1'];
        $sum=$orde['c2'];
        $this->assign(compact('limitTimeList','where','orderDays','sum','stores','storeSum1','total','price','stor','priceSum','total1','price1','stock','stor1','refund','header'));
        return $this->fetch();

    }


    /**
     * 开启砍价产品
     * @param int $id
     * @return mixed|\think\response\Json|void
     */
    public function bargain($id = 0){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $this->assign([
            'title'=>'添加砍价产品','rules'=>$this->readBargain($id)->getContent(),
            'action'=>Url::build('updateBargain',array('id'=>$id))
        ]);
        return $this->fetch('public/common_form');
    }

    /**
     * 砍价产品页面
     * @param $id
     * @return \think\response\Json
     */
    public function readBargain($id){
        if(!$id) return $this->failed('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $storeBargain = StoreBargainModel::where('product_id',$id)->find();
        if(!empty($storeBargain)){
            ProductModel::edit(['is_bargain'=>1],$id,'id');return $this->failed('砍价已开启！');
        }
        FormBuilder::text('title','砍价活动名称');
        FormBuilder::text('info','砍价活动简介');
        FormBuilder::text('store_name','砍价产品名称',$product->getData('store_name'));
        FormBuilder::text('unit_name','单位',$product->getData('unit_name'))->placeholder('个、位');
        FormBuilder::dateTimeRange('section_time','活动时间')->format("yyyy-MM-dd HH:mm:ss");
        FormBuilder::upload('img','推荐图(305*305px)')->maxLength(1)->defaultFileList($product->getData('image'));
        FormBuilder::upload('images','轮播图(640*640px)')->maxLength(5)->defaultFileList(json_decode($product->getData('slider_image'),true));
        FormBuilder::number('price','砍价金额',$product->getData('price'))->min(0);
        FormBuilder::number('min_price','砍价最低金额')->min(0);
        FormBuilder::number('bargain_max_price','用户单次砍价的最大金额')->min(0);
        FormBuilder::number('bargain_min_price','用户单次砍价的最小金额')->min(0);
//        FormBuilder::number('cost','成本价',$product->getData('cost'))->min(0);
        FormBuilder::number('bargain_num','用户单次砍价的次数')->min(1);
        FormBuilder::number('stock','库存',$product->getData('stock'))->min(0)->precision(0);
        FormBuilder::number('sales','销量',$product->getData('sales'))->min(0)->precision(0);
        FormBuilder::number('sort','排序');
        FormBuilder::number('num','单次购买的砍价产品数量')->min(1)->precision(0);
//        FormBuilder::number('give_integral','赠送积分',$product->getData('give_integral'))->min(0)->precision(0);
        FormBuilder::number('postage','邮费',$product->getData('postage'))->min(0);
        FormBuilder::radio('is_postage','是否包邮',[['label'=>'是','value'=>1],['label'=>'否','value'=>0]],0);
        FormBuilder::radio('is_hot','热门推荐',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],0);
        FormBuilder::radio('status','活动状态',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],0);
        return FormBuilder::builder();
    }

    /**
     * 砍价产品保存
     * @param Request $request
     * @param $id
     * @return \think\response\Json|void
     */
    public function updateBargain(Request $request,$id){
        if(!$id) return Json::fail('数据不存在');
        $product = ProductModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $data = Util::postMore([
            ['title',''],
            ['info',''],
            ['store_name',''],
            ['unit_name',''],
            ['section_time',[]],
            ['img',[]],
            ['images',[]],
            ['price',0],
            ['min_price',0],
            ['bargain_max_price',0],
            ['bargain_min_price',0],
            ['cost',0],
            ['bargain_num',0],
            ['stock',0],
            ['sales',0],
            ['sort',0],
            ['num',0],
            ['give_integral',0],
            ['postage',0],
            ['is_postage',0],
            ['is_hot',0],
            ['status',0],
        ],$request);
        if($data['title'] == '') return Json::fail('请输入砍价活动名称');
        if($data['info'] == '') return Json::fail('请输入砍价活动简介');
        if($data['store_name'] == '') return Json::fail('请输入砍价产品名称');
        if($data['unit_name'] == '') return Json::fail('请输入产品单位');
        if(count($data['section_time'])<1) return Json::fail('请选择活动时间');
        if(!$data['section_time'][0]) return Json::fail('请选择活动时间');
        if(!$data['section_time'][1]) return Json::fail('请选择活动时间');
        $data['start_time'] = $data['section_time'][0];
        $data['stop_time'] = $data['section_time'][1];
        unset($data['section_time']);
        if(count($data['img'])<1) return Json::fail('请选择推荐图');
        $data['image'] = $data['img'][0];
        if(count($data['images'])<1) return Json::fail('请选择轮播图');
        $data['images'] = json_encode($data['images']);
        if($data['price'] == '' || $data['price'] < 0) return Json::fail('请输入砍价金额');
        if($data['min_price'] == '' || $data['min_price'] < 0) return Json::fail('请输入砍价最低金额');
        if($data['bargain_max_price'] == '' || $data['bargain_max_price'] < 0) return Json::fail('请输入用户单次砍价的最大金额');
        if($data['bargain_min_price'] == '' || $data['bargain_min_price'] < 0) return Json::fail('请输入用户单次砍价的最小金额');
//        if($data['cost'] == '' || $data['cost'] < 0) return Json::fail('请输入成本价');
        if($data['bargain_num'] == '' || $data['bargain_num'] < 0) return Json::fail('请输入用户单次砍价的次数');
        if($data['stock'] == '' || $data['stock'] < 0) return Json::fail('请输入库存');
        if($data['num'] == '' || $data['num'] < 0) return Json::fail('请输入单次购买的砍价产品数量');
        unset($data['img']);
        $data['product_id'] = $product['id'];
        $data['add_time'] = time();
        $data['is_del'] = 0;
        $res = StoreBargain::set($data);
        ProductModel::edit(['is_bargain'=>1],$id);
        if($res) return Json::successful('添加成功');
        else return Json::fail('添加失败');
    }
}
