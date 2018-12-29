<?php
/**
 * Created by PhpStorm.
 * User: lianghuan
 * Date: 2018-03-03
 * Time: 16:47
 */

namespace app\admin\model\user;
use app\wap\model\user\UserBill;
use app\wap\model\user;
use think\Url;
use traits\ModelTrait;
use basic\ModelBasic;
use service\WechatTemplateService;
/**
 * 用户提现管理 model
 * Class User
 * @package app\admin\model\user
 */
class UserExtract extends ModelBasic
{
    use ModelTrait;
    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where)
    {
        $model = new self;
        if($where['status'] != '')  $model = $model->where('a.status',$where['status']);
        if($where['extract_type'] != '')  $model = $model->where('a.extract_type',$where['extract_type']);
        if($where['nireid'] != ''){
                $model = $model->whereOr('a.real_name','like',"%$where[nireid]%");
                $model = $model->whereOr('a.id',(int)$where['nireid']);
                $model = $model->whereOr('b.nickname','like',"%$where[nireid]%");
                $model = $model->whereOr('a.bank_code','like',"%$where[nireid]%");
                $model = $model->whereOr('a.alipay_code','like',"%$where[nireid]%");
        }
        $model = $model->alias('a');
        $model = $model->field('a.*,b.nickname');
        $model = $model->join('__USER__ b','b.uid=a.uid','LEFT');
        $model = $model->order('a.id desc');
        return self::page($model);
    }

    public static function changeFail($id,$fail_msg)
    {
        $fail_time = time();
        $data =self::get($id);
        $extract_number=$data['extract_price'];
        $mark='提现失败,退回佣金'.$extract_number.'元';
        $uid=$data['uid'];
        $status = -1;
        $User= user\User::find(['uid'=>$uid])->toArray();
        UserBill::income('提现失败',$uid,'now_money','extract',$extract_number,$id,$User['now_money'],$mark);

        user\User::bcInc($uid,'now_money',$extract_number,'uid');
        WechatTemplateService::sendTemplate(user\WechatUser::uidToOpenid($uid),WechatTemplateService::USER_BALANCE_CHANGE,[
            'first'=> $mark,
            'keyword1'=>'佣金提现',
            'keyword2'=>date('Y-m-d H:i:s',time()),
            'keyword3'=>$extract_number,
            'remark'=>'错误原因:'.$fail_msg
        ],Url::build('wap/my/user_pro',[],true,true));
        return self::edit(compact('fail_time','fail_msg','status'),$id);
    }

    public static function changeSuccess($id)
    {
        $status = 1;
        $data =self::get($id);
        $extract_number=$data['extract_price'];
        $mark='成功提现佣金'.$extract_number.'元';
        $uid=$data['uid'];
        WechatTemplateService::sendTemplate(user\WechatUser::getOpenId($uid),WechatTemplateService::USER_BALANCE_CHANGE,[
            'first'=> $mark,
            'keyword1'=>'佣金提现',
            'keyword2'=>date('Y-m-d H:i:s',time()),
            'keyword3'=>$extract_number,
            'remark'=>'点击查看我的佣金明细'
        ],Url::build('wap/my/user_pro',[],true,true));
        return self::edit(compact('status'),$id);
    }

}