<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2018/01/17
 */

namespace app\admin\controller\store;


use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use app\admin\model\store\StoreCouponIssue as CouponIssueModel;
use app\admin\model\store\StoreCouponIssueUser;
use service\JsonService;
use think\Url;
use traits\CurdControllerTrait;

class StoreCouponIssue extends AuthController
{
    use CurdControllerTrait;

    protected $bindModel = CouponIssueModel::class;

    public function index()
    {
        $model = CouponIssueModel::alias('A')->field('A.*,B.title')
            ->join('__STORE_COUPON__ B','A.cid = B.id')->where('A.is_del',0)->order('A.add_time DESC');
        $this->assign(CouponIssueModel::page($model));
        $this->assign([
            'where'=>['status'=>'','coupon_title'=>'']
        ]);
        return $this->fetch();
    }

    public function delete($id = '')
    {
        if(!$id) return JsonService::fail('参数有误!');
        if(CouponIssueModel::edit(['is_del'=>1],$id,'id'))
            return JsonService::successful('删除成功!');
        else
            return JsonService::fail('删除失败!');
    }

    public function edit($id = '')
    {
        if(!$id) return JsonService::fail('参数有误!');
        $issueInfo = CouponIssueModel::get($id);
        if(-1 == $issueInfo['status'] || 1 == $issueInfo['is_del']) return $this->failed('状态错误,无法修改');
        FormBuilder::radio('status','是否开启',[
            ['value'=>1,'label'=>'开启'],
            ['value'=>0,'label'=>'关闭']
        ],$issueInfo['status']);
        $this->assign(['title'=>'状态修改','rules'=>FormBuilder::builder()->getContent(),'action'=>Url::build('change_field',array('id'=>$id,'field'=>'status'))]);
        return $this->fetch('public/common_form');
    }

    public function issue_log($id = '')
    {
        if(!$id) return JsonService::fail('参数有误!');
        $this->assign(StoreCouponIssueUser::systemCouponIssuePage($id));
        return $this->fetch();
    }
}