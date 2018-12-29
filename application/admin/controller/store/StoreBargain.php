<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/16 0016
 * Time: 10:39
 */

namespace app\admin\controller\store;

use app\admin\controller\AuthController;
use app\admin\model\store\StoreProduct;
use service\JsonService;
use service\UtilService as Util;
use app\admin\library\FormBuilder;
use service\UtilService;
use traits\CurdControllerTrait;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\store\StoreBargain as StoreBargainModel;


class StoreBargain extends AuthController
{
    use CurdControllerTrait;

    protected $bindModel = StoreBargainModel::class;

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
            ['export',0],
            ['data',''],
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
        $this->assign(StoreBargainModel::systemPage($where));
        return $this->fetch();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','store/bargain');
        $thumbPath = Upload::thumb($res->dir);
        if($res->status == 200)
            return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
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
        $product = StoreBargainModel::get($id);
        if(!$product) return $this->failed('数据不存在!');
        $this->assign([
            'title'=>'编辑砍价产品','rules'=>$this->read($id)->getContent(),
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
        $product = StoreBargainModel::get($id);
        if(!$product) return $this->failed('数据不存在!');
        FormBuilder::text('title','砍价活动名称',$product->getData('title'));
        FormBuilder::text('info','砍价活动简介',$product->getData('info'));
        FormBuilder::text('store_name','砍价产品名称',$product->getData('store_name'));
        FormBuilder::text('unit_name','单位',$product->getData('unit_name'))->placeholder('个、位');
        FormBuilder::dateTimeRange('section_time','活动时间',$product->getData('start_time'),$product->getData('stop_time'))->format("yyyy-MM-dd HH:mm:ss");
        FormBuilder::upload('img','推荐图')->maxLength(1)->defaultFileList($product->getData('image'));
        FormBuilder::upload('images','轮播图图')->maxLength(5)->defaultFileList(json_decode($product->getData('images'),true));
        FormBuilder::number('price','砍价金额',$product->getData('price'))->min(0);
        FormBuilder::number('min_price','砍价最低金额',$product->getData('min_price'))->min(0);
        FormBuilder::number('bargain_max_price','用户单次砍价的最大金额',$product->getData('bargain_max_price'))->min(0);
        FormBuilder::number('bargain_min_price','用户单次砍价的最小金额',$product->getData('bargain_min_price'))->min(0);
        FormBuilder::number('cost','成本价',$product->getData('cost'))->min(0);
        FormBuilder::number('bargain_num','用户单次砍价的次数',$product->getData('bargain_num'))->min(0);
        FormBuilder::number('stock','库存',$product->getData('stock'))->min(0);
        FormBuilder::number('sales','销量',$product->getData('sales'))->min(0);
        FormBuilder::number('sort','排序',$product->getData('sort'));
        FormBuilder::number('num','单次购买的砍价产品数量',$product->getData('num'));
        FormBuilder::number('give_integral','赠送积分',$product->getData('give_integral'))->min(0);
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
        if(!$id) return Json::fail('数据不存在');
        $product = StoreBargainModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $data = UtilService::postMore([
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
        if($data['title'] == '') return JsonService::fail('请输入砍价活动名称');
        if($data['info'] == '') return JsonService::fail('请输入砍价活动简介');
        if($data['store_name'] == '') return JsonService::fail('请输入砍价产品名称');
        if($data['unit_name'] == '') return JsonService::fail('请输入产品单位');
        if(count($data['section_time'])<1) return JsonService::fail('请选择活动时间');
        if(!$data['section_time'][0]) return JsonService::fail('请选择活动时间');
        if(!$data['section_time'][1]) return JsonService::fail('请选择活动时间');
        $data['start_time'] = $data['section_time'][0];
        $data['stop_time'] = $data['section_time'][1];
        unset($data['section_time']);
        if(count($data['img'])<1) return JsonService::fail('请选择推荐图');
        $data['image'] = $data['img'][0];
        if(count($data['images'])<1) return JsonService::fail('请选择轮播图');
        $data['images'] = json_encode($data['images']);
        if($data['price'] == '' || $data['price'] < 0) return JsonService::fail('请输入砍价金额');
        // if($data['min_price'] == '' || $data['min_price'] < 0) return JsonService::fail('请输入砍价最低金额');
        if($data['bargain_max_price'] == '' || $data['bargain_max_price'] < 0) return JsonService::fail('请输入用户单次砍价的最大金额');
        if($data['bargain_min_price'] == '' || $data['bargain_min_price'] < 0) return JsonService::fail('请输入用户单次砍价的最小金额');
        if($data['cost'] == '' || $data['cost'] < 0) return JsonService::fail('请输入成本价');
        if($data['bargain_num'] == '' || $data['bargain_num'] < 0) return JsonService::fail('请输入用户单次砍价的次数');
        if($data['stock'] == '' || $data['stock'] < 0) return JsonService::fail('请输入库存');
        if($data['num'] == '' || $data['num'] < 0) return JsonService::fail('请输入单次购买的砍价产品数量');
        unset($data['img']);
        $res = StoreBargainModel::edit($data,$id);
        if($res) return JsonService::successful('修改成功');
        else return JsonService::fail('修改失败');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if(!$id) return Json::fail('数据不存在');
        $product = StoreBargainModel::get($id);
        if(!$product) return Json::fail('数据不存在!');
        $data['is_del'] = 1;
        $productEdit['is_bargain'] = 0;
        if(StoreBargainModel::edit($data,$id) && StoreProduct::edit($productEdit,$product['product_id']))
            return Json::successful('删除成功!');
        else
            return Json::fail(StoreBargainModel::getErrorInfo('删除失败,请稍候再试!'));
    }

    /**
     * 显示内容窗口
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit_content($id){
        if(!$id) return $this->failed('数据不存在');
        $seckill = StoreBargainModel::get($id);
        if(!$seckill) return $this->failed('数据不存在');
        $this->assign([
            'content'=>StoreBargainModel::where('id',$id)->value('description'),
            'field'=>'description',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'description'])
        ]);
        return $this->fetch('public/edit_content');
    }

    /**
     * 显示内容窗口
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit_rule($id){
        if(!$id) return $this->failed('数据不存在');
        $seckill = StoreBargainModel::get($id);
        if(!$seckill) return $this->failed('数据不存在');
        $this->assign([
            'content'=>StoreBargainModel::where('id',$id)->value('rule'),
            'field'=>'rule',
            'action'=>Url::build('change_field',['id'=>$id,'field'=>'rule'])
        ]);
        return $this->fetch('public/edit_content');
    }
}