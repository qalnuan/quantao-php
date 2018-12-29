<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */

namespace app\admin\model\store;

use app\admin\model\wechat\WechatUser;
use app\admin\model\system\Merchant;
use service\PHPExcelService;
use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\store\StoreCategory as CategoryModel;

/**
 * 产品管理 model
 * Class StoreProduct
 * @package app\admin\model\store
 */
class StoreProduct extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where,$userId){
        $model = new self;
        if($where['is_hot'] != '')  $model = $model->where('is_hot',$where['is_hot']);
        if($where['is_show'] != '')  $model = $model->where('is_show',$where['is_show']);
        if($where['is_benefit'] != '')  $model = $model->where('is_benefit',$where['is_benefit']);
        if($where['is_best'] != '')  $model = $model->where('is_best',$where['is_best']);
        if($where['is_new'] != '')  $model = $model->where('is_new',$where['is_new']);
        if($where['store_name'] != '')  {
            $model = $model->where('store_name','LIKE',"%$where[store_name]%")->whereOr('keyword','LIKE',"%$where[store_name]%");
            if((int)$where['store_name']) $model = $model->whereOr('id',$where['store_name']);
        }
        if($where['data'] != '') $model = $model->whereTime('add_time', 'between', explode('-',$where['data']));
        $model = $model->order('id desc');
        $model = $model->where('is_del',0);
        $model = $model->where('mer_id',0);
        if($where['export'] == 1){
            $list = $model->select()->toArray();
            $export = [];
            foreach ($list as $index=>$item){
                $export[] = [
                    $item['store_name'],
                    $item['store_info'],
                    CategoryModel::where('id',$item['cate_id'])->value('cate_name'),
                    '￥'.$item['price'],
                    $item['stock'],
                    $item['sales'],
                    StoreProductRelation::where('product_id',$item['id'])->where('type','like')->count(),
                    StoreProductRelation::where('product_id',$item['id'])->where('type','collect')->count()
                ];
                $list[$index] = $item;
            }
            $user=WechatUser::where(['uid'=>$userId])->value('nickname');

            PHPExcelService::setExcelHeader(['产品名称','产品简介','产品分类','价格','库存','销量','点赞人数','收藏人数'])
                ->setExcelTile('产品导出','产品信息'.time(),'操作人昵称：'.$user.' 生成时间：'.date('Y-m-d H:i:s',time()))
                ->setExcelContent($export)
                ->ExcelSave();
        }
        return self::page($model,function($item){
            $item['cate_name'] = CategoryModel::where('id',$item['cate_id'])->value('cate_name');
            $item['collect'] = StoreProductRelation::where('product_id',$item['id'])->where('type','collect')->count();//收藏
            $item['like'] = StoreProductRelation::where('product_id',$item['id'])->where('type','like')->count();//点赞
        },$where);
    }

    /**
     * @param $where
     * @return array
     */
    public static function systemPageMerchant($where){
        $model = new self;
        if($where['is_hot'] != '')  $model = $model->where('is_hot',$where['is_hot']);
        if($where['is_del'] != -1 && $where['is_del'] != '')  $model = $model->where('is_del',$where['is_del']);
        else  $model = $model->where('is_del','IN','0,2');
        if($where['is_show'] != '')  $model = $model->where('is_show',$where['is_show']);
        if($where['is_benefit'] != '')  $model = $model->where('is_benefit',$where['is_benefit']);
        if($where['is_best'] != '')  $model = $model->where('is_best',$where['is_best']);
        if($where['is_new'] != '')  $model = $model->where('is_new',$where['is_new']);
        if($where['store_name'] != '')  {
            $model = $model->where('store_name','LIKE',"%$where[store_name]%")->whereOr('keyword','LIKE',"%$where[store_name]%");
        }
        $model = $model->order('id desc');
        $model = $model->where('mer_id','NEQ',0);
        return self::page($model,function($item){
            $item['cate_name'] = CategoryModel::where('id',$item['cate_id'])->value('cate_name');
            $item['mer_name'] = Merchant::where('id',$item['mer_id'])->value('mer_name');
        },$where);
    }

    public static function changeStock($stock,$productId)
    {
        return self::edit(compact('stock'),$productId);
    }


    /**
     * @param $where
     * @return array
     */
    public static function systemPageMer($where){
        $model = new self;
        if($where['is_hot'] != '')  $model = $model->where('is_hot',$where['is_hot']);
        if($where['is_show'] != '')  $model = $model->where('is_show',$where['is_show']);
        if($where['is_benefit'] != '')  $model = $model->where('is_benefit',$where['is_benefit']);
        if($where['is_best'] != '')  $model = $model->where('is_best',$where['is_best']);
        if($where['is_new'] != '')  $model = $model->where('is_new',$where['is_new']);
        if($where['store_name'] != '')  $model = $model->where('store_name','LIKE',"%$where[store_name]%")->whereOr('keyword','LIKE',"%$where[store_name]%");
        $model = $model->order('id desc');
        $model = $model->where('mer_id',$where['mer_id']);
        return self::page($model,function($item){
            $item['cate_name'] = CategoryModel::where('id',$item['cate_id'])->value('cate_name');
        },$where);
    }


    public static function getTierList($model = null)
    {
        if($model === null) $model = new self();
        return $model->field('id,store_name')->where('is_del',0)->select()->toArray();
    }

    /**
     * 销量折线图
     * @param $orderPrice
     * @return array
     */
    public static function brokenLine($orderPrice){
        $orderCategory=[];$orderDays1 = [];$sum=[];
        foreach ($orderPrice as $price){
            $orderDays1[] = date('Y-m-d', $price['add_time']);
            $orderCategory[]=$price['total_num'];
        }
        $orderDays =array_unique($orderDays1);
        sort($orderDays);
        for($i=0;$i<count($orderDays);$i++){
            $t=$orderDays[$i];$t2=strtotime($t);
            $t1=date('Y-m-d',strtotime("+1day",$t2));
            $order = StoreOrder::whereTime('add_time', 'between', [$t,$t1])->sum('total_num');
            $sum[]=$order;
        }
        if(isset($orderDays)&&isset($sum)){return ['c1'=>$orderDays,'c2'=>$sum];}
    }


    /**
     * 销量前十图表
     * @param $where
     * @param $color
     * @param string $dat
     * @return array
     */
    public static function salesVolume($where,$color,$dat=''){
        if($dat){
            if($where['sex']!='') {
                if($where['sex']==1) {
                    $user = StoreOrder::alias('s')->join('eb_wechat_user w', 's.uid=w.uid')->join('eb_store_order_cart_info c', 's.id=c.oid')->where('w.sex', 1)->whereTime('s.add_time', 'between', $dat)->select();
                }else if($where['sex']==0){
                    $user = StoreOrder::alias('s')->join('eb_wechat_user w', 's.uid=w.uid')->join('eb_store_order_cart_info c', 's.id=c.oid')->where('w.sex', 2)->whereTime('s.add_time', 'between', $dat)->select();
                }else if($where['sex']==2){
                    $user=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->whereTime('s.add_time', 'between', $dat)->select();
                }
            }else{
                $user=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->whereTime('s.add_time', 'between', $dat)->select();
            }
        }else{
            if($where['sex']!='') {
                if($where['sex']==1) {
                    $user = StoreOrder::alias('s')->join('eb_wechat_user w', 's.uid=w.uid')->join('eb_store_order_cart_info c', 's.id=c.oid')->where('w.sex', 1)->select();
                }else if($where['sex']==0){
                    $user = StoreOrder::alias('s')->join('eb_wechat_user w', 's.uid=w.uid')->join('eb_store_order_cart_info c', 's.id=c.oid')->where('w.sex', 2)->select();
                }else if($where['sex']==2){
                    $user=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->select();
                }
            }else{
                $user=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->select();
            }
        }
        $stoer=[];$storeSum=[];
        foreach($user as $v){
            $stoer[]=$v['product_id'];
            $storeSum[]=$v['total_num'];
        }
        $storeSum1=array_sum($storeSum);$c=array_count_values($stoer);arsort($c);//对数组单元进行由高到低排序并保持索引关系
        $str=array_slice($c,0,10,true );
        $puid=[];$pname=[];$price=[];
        foreach($str as $k=>$v){
            $pname[]=self::where('id',$k)->value('store_name');
            $price[]=self::where('id',$k)->value('price');
            $puid[]=$v;
        }
        $total=array_sum($puid);
        $cunt=count($str);
        $sump=[];
        if($cunt>0){
            for($i=0;$i<$cunt;$i++){
                $sump[]=$price[$i]*$puid[$i];
            }
            $pric=array_sum($sump);
        }else{
            $pric=0;
        }
        foreach($pname as $key=>$val){
            $stores[$key]['name']=$pname[$key];
            $stores[$key]['sum']=$puid[$key];
            $stores[$key]['price']=$price[$key];
            $stores[$key]['color']=$color[$key];
        }
        if(isset($stores)&&isset($storeSum1)){return ['c1'=>$stores,'c2'=>$storeSum1,'c3'=>$total,'c4'=>$pric];}

    }
    //利润前十图表
    public static function profit($where,$color,$dat=''){
        if($dat){
            if($where['sex1']!=''){
                if($where['sex1']==1) {
                    $user1=StoreOrder::alias('s')->join('eb_wechat_user w','s.uid=w.uid')->join('eb_store_order_cart_info c','s.id=c.oid')->where('w.sex',1)->whereTime('s.add_time', 'between', $dat)->select();
                }else if($where['sex1']==0){
                    $user1 = StoreOrder::alias('s')->join('eb_wechat_user w', 's.uid=w.uid')->join('eb_store_order_cart_info c', 's.id=c.oid')->where('w.sex', 2)->whereTime('s.add_time', 'between', $dat)->select();
                }else if($where['sex1']==2){
                    $user1=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->whereTime('s.add_time', 'between', $dat)->select();
                }
            }else{
                $user1=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->whereTime('s.add_time', 'between', $dat)->select();
            }
        }else{
            if($where['sex1']!='') {
                if($where['sex1']==1) {
                    $user1=StoreOrder::alias('s')->join('eb_wechat_user w','s.uid=w.uid')->join('eb_store_order_cart_info c','s.id=c.oid')->where('w.sex',1)->select();
                }else if($where['sex1']==0){
                    $user1 = StoreOrder::alias('s')->join('eb_wechat_user w', 's.uid=w.uid')->join('eb_store_order_cart_info c', 's.id=c.oid')->where('w.sex', 2)->select();
                }else if($where['sex1']==2){
                    $user1=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->select();
                }
            }else{
                $user1=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->select();
            }
        }
        $stoer=[];//商品ID
        $storeSum=[];//总价
        $price=[];//商品单价
        $piece=[];//商总件数
        foreach($user1 as $v){
            $stoer[]=$v['product_id'];
            $storeSum[]=$v['total_price'];
        }
        $priceSum=array_sum($storeSum);
        $c=array_count_values($stoer);
        arsort($c);//对数组单元进行由高到低排序并保持索引关系
        $str=array_slice($c,0,10,true );
        $pname=[];
        foreach($str as $k=>$v){
            $pname[]=self::where('id',$k)->value('store_name');
            $price[]=self::where('id',$k)->value('price');
            $piece[]=$v;
        }
        $cunt=count($str);
        $total1=array_sum($piece);
        $suml=[];
        if($cunt>0) {
            for ($i = 0; $i < $cunt; $i++) {
                $suml[] = $piece[$i] * $price[$i];
            }
            $pric = array_sum($suml);
        }else{
            $pric=0;
        }
        foreach($pname as $key=>$val){
            $stor[$key]['name']=$pname[$key];
            $stor[$key]['price']=$price[$key];
            $stor[$key]['piece']=$piece[$key];
            $stor[$key]['color']=$color[$key];
        }
        if(isset($stor)&&isset($priceSum)){return ['c1'=>$stor,'c2'=>$priceSum,'c3'=>$total1,'c4'=>$pric];}

    }
//差评图表
    public static function ncomment($dat=''){
        if($dat){
            $store0=self::alias('s')->join('eb_store_product_reply r','s.id=r.product_id')->field('product_id,store_name,price')->whereTime('r.add_time', 'between', $dat)->where('product_score',1)->select();
        }else{
            $store0=self::alias('s')->join('eb_store_product_reply r','s.id=r.product_id')->field('product_id,store_name,price')->where('product_score',1)->select();
        }
        $produ=array();$frequency=[];
        foreach($store0 as $v){
            $produ[]=$v['product_id'];
        }
        $uid=array_unique($produ);
        foreach($uid as $v){
            $n=0;
            foreach($store0 as $t){
                if($v==$t['product_id'])
                    $n++;
            }
            $frequency[]=$n;
        }
        $comment= array_combine($uid,$frequency);
        arsort($comment);//对数组单元进行由高到低排序并保持索引关系
        $str=array_slice($comment,0,10,true );
        $name=[];$pric=[];$sun=[];$uid=[];$stor1=[];
        foreach($str as $k=>$v){
            $uid[]=$k;
            $name[]= self::where('id',$k)->value('store_name');
            $pric[]=self::where('id',$k)->value('price');
            $sun[]=$v;
        }
        foreach($name as $key=>$val){
            $stor1[$key]['name']=$name[$key];
            $stor1[$key]['price']=$pric[$key];
            $stor1[$key]['sun']=$sun[$key];
            $stor1[$key]['uid']=$uid[$key];
        }
        return $stor1;
    }

    /**
     * 退款图表
     * @param string $dat
     * @return array
     */
    public static function refund($dat=''){
        if($dat){
            $perd=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->where('status',-1)->whereTime('s.add_time', 'between', $dat)->column('product_id');
        }else{
            $perd=StoreOrder::alias('s')->join('eb_store_order_cart_info c','s.id=c.oid')->where('status',-1)->column('product_id');
        }
        $c=array_count_values($perd);
        arsort($c);//对数组单元进行由高到低排序并保持索引关系
        $str=array_slice($c,0,10,true );
        $sname=[];$price=[];$sun=[];$sid=[];$refund=[];
        foreach($str as $k=>$v){
            $sid[]=$k;
            $sname[]=self::where('id',$k)->value('store_name');
            $price[]=self::where('id',$k)->value('price');
            $sun[]=$v;
        }
        foreach($sname as $key=>$val){
            $refund[$key]['name']=$sname[$key];
            $refund[$key]['price']=$price[$key];
            $refund[$key]['sid']=$sid[$key];
            $refund[$key]['sun']=$sun[$key];
        }
        return $refund;
    }
}