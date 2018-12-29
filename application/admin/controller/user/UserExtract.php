<?php
/**
 * Created by PhpStorm.
 * User: lianghuan
 * Date: 2018-03-03
 * Time: 16:37
 */

namespace app\admin\controller\user;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use service\JsonService;
use think\Request;
use service\UtilService as Util;
use think\Url;
class UserExtract extends AuthController
{
   public function index(){
       $where = Util::getMore([
           ['status',''],
           ['nickname',''],
           ['extract_type',''],
           ['nireid',''],
       ],$this->request);
       $this->assign('where',$where);
       $this->assign(\app\admin\model\user\UserExtract::systemPage($where));
      return $this->fetch();
   }

    public function edit($id){

        if(!$id) return $this->failed('数据不存在');

        $UserExtract = \app\admin\model\user\UserExtract::get($id);

        if(!$UserExtract) return JsonService::fail('数据不存在!');
        FormBuilder::text('real_name','姓名',$UserExtract['real_name']);
        FormBuilder::number('extract_price','提现金额',$UserExtract['extract_price']);

       if($UserExtract['extract_type']=='alipay'){
           FormBuilder::text('alipay_code','支付宝账号',$UserExtract['alipay_code']);
       }else{
           FormBuilder::text('bank_code','银行卡号',$UserExtract['bank_code']);
           FormBuilder::text('bank_address','开户行',$UserExtract['bank_address']);
       }
        FormBuilder::textarea('mark','备注',$UserExtract['mark']);
        $this->assign([

            'title'=>'编辑','rules'=>FormBuilder::builder()->getContent(),

            'action'=>Url::build('update',array('id'=>$id))

        ]);


        return $this->fetch('public/common_form');

    }

    public function update(Request $request,$id)
    {
        $UserExtract = \app\admin\model\user\UserExtract::get($id);
        if(!$UserExtract) return JsonService::fail('数据不存在!');
        if($UserExtract['extract_type']=='alipay'){
            $data = Util::postMore([
                'real_name',
                'mark',
                'extract_price',
                'alipay_code',
            ],$request);
            if(!$data['real_name']) return JsonService::fail('请输入姓名');
            if($data['extract_price']<=-1) return JsonService::fail('请输入提现金额');
            if(!$data['alipay_code']) return JsonService::fail('请输入支付宝账号');
        }else{
            $data = Util::postMore([
                'real_name',
                'extract_price',
                'mark',
                'bank_code',
                'bank_address',
            ],$request);
            if(!$data['real_name']) return JsonService::fail('请输入姓名');
            if($data['extract_price']<=-1) return JsonService::fail('请输入提现金额');
            if(!$data['bank_code']) return JsonService::fail('请输入银行卡号');
            if(!$data['bank_address']) return JsonService::fail('请输入开户行');
        }

        if(!\app\admin\model\user\UserExtract::edit($data,$id))
            return JsonService::fail(\app\admin\model\user\UserExtract::getErrorInfo('修改失败'));
        else
            return JsonService::successful('修改成功!');
    }
    public function fail(Request $request,$id)
    {
        if(!\app\admin\model\user\UserExtract::be(['id'=>$id,'status'=>0])) return JsonService::fail('操作记录不存在或状态错误!');
        $fail_msg =$request->post();
        $res = \app\admin\model\user\UserExtract::changeFail($id,$fail_msg['message']);
        if($res){
            return JsonService::successful('操作成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
    }
    public function succ($id)
    {
        if(!\app\admin\model\user\UserExtract::be(['id'=>$id,'status'=>0]))
            return JsonService::fail('操作记录不存在或状态错误!');
        \app\admin\model\user\UserExtract::beginTrans();
        $res = \app\admin\model\user\UserExtract::changeSuccess($id);
        if($res){
            return JsonService::successful('操作成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
    }

}