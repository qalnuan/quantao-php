<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */

namespace app\admin\model\store;


use app\admin\model\wechat\WechatUser;
use app\routine\model\routine\RoutineFormId;
use app\routine\model\routine\RoutineTemplate;
use app\wap\model\store\StorePink;
use app\wap\model\user\WechatUser as WechatUserWap;
use service\PHPExcelService;
use traits\ModelTrait;
use basic\ModelBasic;
use service\WechatTemplateService;
use think\Url;

/**
 * 订单管理Model
 * Class StoreOrder
 * @package app\admin\model\store
 */
class StoreOrder extends ModelBasic
{
    use ModelTrait;

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where,$userid){
        $model = new self;
        $model = self::getOrderWhere($where,$model);
        if($where['order']){
            $model = $model->order($where['order']);
        }else{
            $model = $model->order('id desc');
        }


        if($where['export'] == 1){
            $list = $model->select()->toArray();
            $export = [];
            foreach ($list as $index=>$item){

                if ($item['pay_type'] == 'weixin'){
                    $payType = '微信支付';
                }elseif($item['pay_type'] == 'yue'){
                    $payType = '余额支付';
                }elseif($item['pay_type'] == 'offline'){
                    $payType = '线下支付';
                }else{
                    $payType = '其他支付';
                }

                $_info = db('store_order_cart_info')->where('oid',$item['id'])->column('cart_info');
                $goodsName = [];
                foreach ($_info as $k=>$v){
                    $v = json_decode($v,true);
                    $goodsName[] = implode(
                        [$v['productInfo']['store_name'],
                            isset($v['productInfo']['attrInfo']) ? '('.$v['productInfo']['attrInfo']['suk'].')' : '',
                            "[{$v['cart_num']} * {$v['truePrice']}]"
                        ],' ');
                }
                $item['cartInfo'] = $_info;
                $export[] = [
                    $item['order_id'],$payType,
                    $item['total_num'],$item['total_price'],$item['total_postage'],$item['pay_price'],$item['refund_price'],
                    $item['mark'],$item['remark'],
                    [$item['real_name'],$item['user_phone'],$item['user_address']],
                    $goodsName,
                    [$item['paid'] == 1? '已支付':'未支付','支付时间: '.($item['pay_time'] > 0 ? date('Y/md H:i',$item['pay_time']) : '暂无')]

                ];
                $list[$index] = $item;
            }
            $user=WechatUser::where(['uid'=>$userid])->value('nickname');

            PHPExcelService::setExcelHeader(['订单号','支付方式','商品总数','商品总价','邮费','支付金额','退款金额','用户备注','管理员备注','收货人信息','商品信息','支付状态'])
                ->setExcelTile('订单导出','订单信息'.time(),'操作人昵称：'.$user.' 生成时间：'.date('Y-m-d H:i:s',time()))
                ->setExcelContent($export)
                ->ExcelSave();
        }

        return self::page($model,function ($item){
            $item['nickname'] = WechatUser::where('uid',$item['uid'])->value('nickname');
            $_info = db('store_order_cart_info')->where('oid',$item['id'])->field('cart_info')->select();
            foreach ($_info as $k=>$v){
                $_info[$k]['cart_info'] = json_decode($v['cart_info'],true);
            }
            $item['_info'] = $_info;
            if($item['pink_id'] && $item['combination_id']){
                $pinkStatus = StorePink::where('order_id_key',$item['id'])->value('status');
                if($pinkStatus == 1){
                    $item['pink_name'] = '[拼团订单]正在进行中';
                    $item['color'] = '#f00';
                }else if($pinkStatus == 2){
                    $item['pink_name'] = '[拼团订单]已完成';
                    $item['color'] = '#00f';
                }else if($pinkStatus == 3){
                    $item['pink_name'] = '[拼团订单]未完成';
                    $item['color'] = '#f0f';
                }else{
                    $item['pink_name'] = '[普通订单]';
                    $item['color'] = '#895612';
                }
            }else{
                   if($item['seckill_id']){
                       $item['pink_name'] = '[秒杀订单]';
                       $item['color'] = '#32c5e9';
                   }else if($item['bargain_id']){
                       $item['pink_name'] = '[砍价订单]';
                       $item['color'] = '#32c0e9';
                   }else{
                       $item['pink_name'] = '[普通订单]';
                       $item['color'] = '#895612';
                   }


            }
        },$where);
    }

    public static function statusByWhere($status,$model = null)
    {
        if($model == null) $model = new self;
        if('' === $status)
            return $model;
        else if($status == 0)//未支付
            return $model->where('paid',0)->where('status',0)->where('refund_status',0);
        else if($status == 1)//已支付 未发货
            return $model->where('paid',1)->where('status',0)->where('refund_status',0);
        else if($status == 2)//已支付  待收货
            return $model->where('paid',1)->where('status',1)->where('refund_status',0);
        else if($status == 3)// 已支付  已收货  待评价
            return $model->where('paid',1)->where('status',2)->where('refund_status',0);
        else if($status == 4)// 交易完成
            return $model->where('paid',1)->where('status',3)->where('refund_status',0);
        else if($status == -1)//退款中
            return $model->where('paid',1)->where('refund_status',1);
        else if($status == -2)//已退款
            return $model->where('paid',1)->where('refund_status',2);
        else
            return $model;
    }

    public static function timeQuantumWhere($startTime = null,$endTime = null,$model = null)
    {
        if($model === null) $model = new self;
        if($startTime != null && $endTime != null)
            $model = $model->where('add_time','>',strtotime($startTime))->where('add_time','<',strtotime($endTime));
        return $model;
    }

    public static function changeOrderId($orderId)
    {
        $ymd = substr($orderId,2,8);
        $key = substr($orderId,16);
        return 'wx'.$ymd.date('His').$key;
    }

    /**
     * 线下付款
     * @param $id
     * @return $this
     */
    public static function updateOffline($id){
        $orderId = self::where('id',$id)->value('order_id');
        $res = self::where('order_id',$orderId)->update(['paid'=>1,'pay_time'=>time()]);
        return $res;
    }

    /**
     * 退款发送模板消息
     * @param $oid
     * $oid 订单id  key
     */
    public static function refundTemplate($data,$oid)
    {
        $order = self::where('id',$oid)->find();
//        WechatTemplateService::sendTemplate(WechatUserWap::uidToOpenid($order['uid']),WechatTemplateService::ORDER_REFUND_STATUS, [
//            'first'=>'亲，您购买的商品已退款,本次退款'.$data['refund_price'].'金额',
//            'keyword1'=>$order['order_id'],
//            'keyword2'=>$order['pay_price'],
//            'keyword3'=>date('Y-m-d H:i:s',$order['add_time']),
//            'remark'=>'点击查看订单详情'
//        ],Url::build('wap/My/order',['uni'=>$order['order_id']],true,true));

        $data['keyword1']['value'] =  $order['order_id'];
        $data['keyword2']['value'] =  date('Y-m-d H:i:s',time());
        $data['keyword3']['value'] =  $order['pay_price'];
        $data['keyword4']['value'] =  '原路返回';
        $data['keyword5']['value'] =  '您的订单已退款，请查看';
        $formId = RoutineFormId::getFormIdOne($order['uid']);
        if($formId){
            RoutineFormId::delFormIdOne($formId);
            RoutineTemplate::sendTemplate(\app\routine\model\user\WechatUser::getOpenId($order['uid']),RoutineTemplate::ORDER_REFUND_SUCCESS,'',$data,$formId);
        }
    }

    /**
     * 处理where条件
     * @param $where
     * @param $model
     * @return mixed
     */
    public static function getOrderWhere($where,$model){
//        $model = $model->where('combination_id',0);
        if($where['status'] != '') $model =  self::statusByWhere($where['status'],$model);
        if($where['is_del'] != '' && $where['is_del'] != -1) $model = $model->where('is_del',$where['is_del']);
        if($where['combination_id'] =='普通订单'){
            $model = $model->where('combination_id',0)->where('seckill_id',0);
        }
        if($where['combination_id'] =='拼团订单'){
            $model = $model->where('combination_id',">",0)->where('pink_id',">",0);
        }
        if($where['combination_id'] =='砍价订单'){
            $model = $model->where('bargain_id',">",0);
        }
        if($where['combination_id'] =='秒杀订单'){
            $model = $model->where('seckill_id',">",0);
        }
        
        if($where['real_name'] != ''){
            $model = $model->where('order_id|real_name|user_phone','LIKE',"%$where[real_name]%");
        }
        if($where['data'] !== ''){
            list($startTime,$endTime) = explode(' - ',$where['data']);
            $model = $model->where('add_time','>',strtotime($startTime));
            $model = $model->where('add_time','<',strtotime($endTime));
        }
        return $model;
    }

    /**
     * 处理订单金额
     * @param $where
     * @return array
     */
    public static function getOrderPrice($where){
        $model = new self;
        $price = array();
        $price['pay_price'] = 0;//支付金额
        $price['refund_price'] = 0;//退款金额
        $price['pay_price_wx'] = 0;//微信支付金额
        $price['pay_price_yue'] = 0;//余额支付金额
        $price['pay_price_offline'] = 0;//线下支付金额
        $price['pay_price_other'] = 0;//其他支付金额
        $price['use_integral'] = 0;//用户使用积分
        $price['back_integral'] = 0;//退积分总数
        $price['deduction_price'] = 0;//抵扣金额
        $price['total_num'] = 0; //商品总数
        $model = self::getOrderWhere($where,$model);
        $list = $model->select()->toArray();
        foreach ($list as $v){
            $price['total_num'] = bcadd($price['total_num'],$v['total_num'],0);
            $price['pay_price'] = bcadd($price['pay_price'],$v['pay_price'],2);
            $price['refund_price'] = bcadd($price['refund_price'],$v['refund_price'],2);
            $price['use_integral'] = bcadd($price['use_integral'],$v['use_integral'],2);
            $price['back_integral'] = bcadd($price['back_integral'],$v['back_integral'],2);
            $price['deduction_price'] = bcadd($price['deduction_price'],$v['deduction_price'],2);
            if ($v['pay_type'] == 'weixin'){
                $price['pay_price_wx'] = bcadd($price['pay_price_wx'],$v['pay_price'],2);
            }elseif($v['pay_type'] == 'yue'){
                $price['pay_price_yue'] = bcadd($price['pay_price_yue'],$v['pay_price'],2);
            }elseif($v['pay_type'] == 'offline'){
                $price['pay_price_offline'] = bcadd($price['pay_price_offline'],$v['pay_price'],2);
            }else{
                $price['pay_price_other'] = bcadd($price['pay_price_other'],$v['pay_price'],2);
            }
        }
        return $price;
    }

    public static function systemPagePink($where){
        $model = new self;
        $model = self::getOrderWherePink($where,$model);
        $model = $model->order('id desc');

        if($where['export'] == 1){
            $list = $model->select()->toArray();
            $export = [];
            foreach ($list as $index=>$item){

                if ($item['pay_type'] == 'weixin'){
                    $payType = '微信支付';
                }elseif($item['pay_type'] == 'yue'){
                    $payType = '余额支付';
                }elseif($item['pay_type'] == 'offline'){
                    $payType = '线下支付';
                }else{
                    $payType = '其他支付';
                }

                $_info = db('store_order_cart_info')->where('oid',$item['id'])->column('cart_info');
                $goodsName = [];
                foreach ($_info as $k=>$v){
                    $v = json_decode($v,true);
                    $goodsName[] = implode(
                        [$v['productInfo']['store_name'],
                            isset($v['productInfo']['attrInfo']) ? '('.$v['productInfo']['attrInfo']['suk'].')' : '',
                            "[{$v['cart_num']} * {$v['truePrice']}]"
                        ],' ');
                }
                $item['cartInfo'] = $_info;
                $export[] = [
                    $item['order_id'],$payType,
                    $item['total_num'],$item['total_price'],$item['total_postage'],$item['pay_price'],$item['refund_price'],
                    $item['mark'],$item['remark'],
                    [$item['real_name'],$item['user_phone'],$item['user_address']],
                    $goodsName,
                    [$item['paid'] == 1? '已支付':'未支付','支付时间: '.($item['pay_time'] > 0 ? date('Y/md H:i',$item['pay_time']) : '暂无')]

                ];
                $list[$index] = $item;
            }
            ExportService::exportCsv($export,'订单导出'.time(),['订单号','支付方式','商品总数','商品总价','邮费','支付金额','退款金额','用户备注','管理员备注','收货人信息','商品信息','支付状态']);
        }

        return self::page($model,function ($item){
            $item['nickname'] = WechatUser::where('uid',$item['uid'])->value('nickname');
            $_info = db('store_order_cart_info')->where('oid',$item['id'])->field('cart_info')->select();
            foreach ($_info as $k=>$v){
                $_info[$k]['cart_info'] = json_decode($v['cart_info'],true);
            }
            $item['_info'] = $_info;
        },$where);
    }

    /**
     * 处理where条件
     * @param $where
     * @param $model
     * @return mixed
     */
    public static function getOrderWherePink($where,$model){
        $model = $model->where('combination_id','GT',0);
        if($where['status'] != '') $model =  $model::statusByWhere($where['status']);
//        if($where['is_del'] != '' && $where['is_del'] != -1) $model = $model->where('is_del',$where['is_del']);
        if($where['real_name'] != ''){
            $model = $model->where('order_id','LIKE',"%$where[real_name]%");
            $model = $model->whereOr('real_name','LIKE',"%$where[real_name]%");
            $model = $model->whereOr('user_phone','LIKE',"%$where[real_name]%");
        }
        if($where['data'] !== ''){
            list($startTime,$endTime) = explode(' - ',$where['data']);
            $model = $model->where('add_time','>',strtotime($startTime));
            $model = $model->where('add_time','<',strtotime($endTime));
        }
        return $model;
    }

    /**
     * 处理订单金额
     * @param $where
     * @return array
     */
    public static function getOrderPricePink($where){
        $model = new self;
        $price = array();
        $price['pay_price'] = 0;//支付金额
        $price['refund_price'] = 0;//退款金额
        $price['pay_price_wx'] = 0;//微信支付金额
        $price['pay_price_yue'] = 0;//余额支付金额
        $price['pay_price_offline'] = 0;//线下支付金额
        $price['pay_price_other'] = 0;//其他支付金额
        $price['use_integral'] = 0;//用户使用积分
        $price['back_integral'] = 0;//退积分总数
        $price['deduction_price'] = 0;//抵扣金额
        $price['total_num'] = 0; //商品总数
        $model = self::getOrderWherePink($where,$model);
        $list = $model->select()->toArray();
        foreach ($list as $v){
            $price['total_num'] = bcadd($price['total_num'],$v['total_num'],0);
            $price['pay_price'] = bcadd($price['pay_price'],$v['pay_price'],2);
            $price['refund_price'] = bcadd($price['refund_price'],$v['refund_price'],2);
            $price['use_integral'] = bcadd($price['use_integral'],$v['use_integral'],2);
            $price['back_integral'] = bcadd($price['back_integral'],$v['back_integral'],2);
            $price['deduction_price'] = bcadd($price['deduction_price'],$v['deduction_price'],2);
            if ($v['pay_type'] == 'weixin'){
                $price['pay_price_wx'] = bcadd($price['pay_price_wx'],$v['pay_price'],2);
            }elseif($v['pay_type'] == 'yue'){
                $price['pay_price_yue'] = bcadd($price['pay_price_yue'],$v['pay_price'],2);
            }elseif($v['pay_type'] == 'offline'){
                $price['pay_price_offline'] = bcadd($price['pay_price_offline'],$v['pay_price'],2);
            }else{
                $price['pay_price_other'] = bcadd($price['pay_price_other'],$v['pay_price'],2);
            }
        }
        return $price;
    }
}