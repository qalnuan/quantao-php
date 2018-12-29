<?php

namespace app\admin\controller\user;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use behavior\wap\UserBehavior;
use service\HookService;
use service\JsonService;
use service\UtilService;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\user\UserEnter as UserEnterModel;

/**
 * 产品管理
 * Class StoreProduct
 * @package app\admin\controller\store
 */
class UserEnter extends AuthController
{

    use CurdControllerTrait;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = Util::getMore([
        ['status',''],
        ['is_lock',''],
        ['merchant_name',''],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(UserEnterModel::systemPage($where));
        return $this->fetch();
    }

    public function fail(Request $request,$id)
    {
        if(!UserEnterModel::be(['id'=>$id,'status'=>0]))
            return JsonService::fail('操作记录不存在或状态错误!');
        $failMessage = UtilService::postMore(['message'],$request);
        UserEnterModel::beginTrans();
        $res = UserEnterModel::changeFail($id,$failMessage);
        if($res){
            try{
                HookService::listen('user_enter_fail',$id,$failMessage,false,UserBehavior::class);
            }catch (\Exception $e){
                return JsonService::fail($e->getMessage());
            }
            UserEnterModel::commitTrans();
            return JsonService::successful('操作成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
    }

    public function succ(Request $request,$id)
    {
        if(!UserEnterModel::be(['id'=>$id,'status'=>0]))
            return JsonService::fail('操作记录不存在或状态错误!');
        UserEnterModel::beginTrans();
        $res = UserEnterModel::changeSuccess($id);
        if($res){
            try{
                HookService::listen('user_enter_success',$id,false,UserBehavior::class);
            }catch (\Exception $e){
                return JsonService::fail($e->getMessage());
            }
            UserEnterModel::commitTrans();
            return JsonService::successful('操作成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
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
        if(!UserEnterModel::edit($data,$id))
            return Json::fail(UserEnterModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }
}
