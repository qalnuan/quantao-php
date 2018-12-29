<?php

namespace app\admin\controller\system;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use service\JsonService;
use service\UtilService as Util;
use service\JsonService as Json;
use think\Request;
use app\admin\model\system\SystemRole;
use think\Url;
use app\admin\model\system\SystemAdmin as AdminModel;

/**
 * 管理员列表控制器
 * Class SystemAdmin
 * @package app\admin\controller\system
 */
class SystemAdmin extends AuthController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $admin = $this->adminInfo;
        $where = Util::getMore([
            ['name',''],
            ['roles',''],
            ['level',bcadd($admin->level,1,0)]
        ],$this->request);
        $this->assign('where',$where);
        $this->assign('role',SystemRole::getRole(bcadd($admin->level,1,0)));
        $this->assign(AdminModel::systemPage($where));
        return $this->fetch();
    }
    //创建获取管理员身份
    public function rules()
    {
        $admin = $this->adminInfo;
        FormBuilder::text('account','管理员账号');
        FormBuilder::password('pwd','管理员密码');
        FormBuilder::password('conf_pwd','确认密码');
        FormBuilder::text('real_name','管理员姓名');
        FormBuilder::select('roles','管理员身份',function()use($admin){
            $list = SystemRole::getRole(bcadd($admin->level,1,0));
            $options = [];
            foreach ($list as $id=>$roleName){
                $options[] = ['label'=>$roleName,'value'=>$id];
            }
            return $options;
        })->multiple();
        FormBuilder::radio('status','状态',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],1);
        return FormBuilder::builder();
    }



    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->assign(['title'=>'添加管理员','action'=>Url::build('save'),'rules'=>$this->rules()->getContent()]);
        return $this->fetch('public/common_form');
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
            'account',
            'conf_pwd',
            'pwd',
            'real_name',
            ['roles',[]],
            ['status',0]
        ],$request);
        if(!$data['account']) return Json::fail('请输入管理员账号');
        if(!$data['roles']) return Json::fail('请选择至少一个管理员身份');
        if(!$data['pwd']) return Json::fail('请输入管理员登陆密码');
        if($data['pwd'] != $data['conf_pwd']) return Json::fail('两次输入密码不想同');
        if(AdminModel::be($data['account'],'account')) return Json::fail('管理员账号已存在');
        $data['pwd'] = md5($data['pwd']);
        unset($data['conf_pwd']);
        $data['level'] = $this->adminInfo['level'] + 1;
        AdminModel::set($data);
        return Json::successful('添加管理员成功!');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $admin = AdminModel::get($id);
        FormBuilder::text('account','管理员账号',$admin->account);
        FormBuilder::password('pwd','管理员密码');
        FormBuilder::password('conf_pwd','确认密码');
        FormBuilder::text('real_name','管理员姓名',$admin->real_name);
        FormBuilder::select('roles','管理员身份',function() use($admin){
            $list = SystemRole::getRole($admin->level);
            $options = [];
            foreach ($list as $id=>$roleName){
                $options[] = ['label'=>$roleName,'value'=>$id];
            }
            return $options;
        },explode(',',$admin->roles))->multiple();
        FormBuilder::radio('status','状态',[['label'=>'开启','value'=>1],['label'=>'关闭','value'=>0]],$admin->status);
        return FormBuilder::builder();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        $this->assign(['title'=>'编辑管理员','rules'=>$this->read($id)->getContent(),'action'=>Url::build('update',array('id'=>$id))]);
        return $this->fetch('public/common_form');
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
            'account',
            'conf_pwd',
            'pwd',
            'real_name',
            ['roles',[]],
            ['status',0]
        ],$request);
        if(!$data['account']) return Json::fail('请输入管理员账号');
        if(!$data['roles']) return Json::fail('请选择至少一个管理员身份');
        if(!$data['pwd'])
            unset($data['pwd']);
        else{
            if(isset($data['pwd']) && $data['pwd'] != $data['conf_pwd']) return Json::fail('两次输入密码不想同');
            $data['pwd'] = md5($data['pwd']);
        }
        if(AdminModel::where('account',$data['account'])->where('id','<>',$id)->count()) return Json::fail('管理员账号已存在');
        unset($data['conf_pwd']);
        AdminModel::edit($data,$id);
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
        if(!$id)
            return JsonService::fail('删除失败!');
        if(AdminModel::edit(['is_del'=>1,'status'=>0],$id,'id'))
            return JsonService::successful('删除成功!');
        else
            return JsonService::fail('删除失败!');
    }

    /**
     * 个人资料 展示
     * */
    public function adminInfo(){
        $adminInfo = $this->adminInfo;//获取当前登录的管理员
        $this->assign('adminInfo',$adminInfo);
        return $this->fetch();
    }

    public function setAdminInfo(Request $request){
        $adminInfo = $this->adminInfo;//获取当前登录的管理员
        if($request->isPost()){
            $data = Util::postMore([
                ['new_pwd',''],
                ['new_pwd_ok',''],
                ['pwd',''],
                'real_name',
            ],$request);
//            if ($data['pwd'] == '') unset($data['pwd']);
            if($data['pwd'] != ''){
                $pwd = md5($data['pwd']);
                if($adminInfo['pwd'] != $pwd) return Json::fail('原始密码错误');
            }
            if($data['new_pwd'] != ''){
                if(!$data['new_pwd_ok']) return Json::fail('请输入确认新密码');
                if($data['new_pwd'] != $data['new_pwd_ok']) return Json::fail('俩次密码不一样');
            }
            if($data['pwd'] != '' && $data['new_pwd'] != ''){
                $data['pwd'] = md5($data['new_pwd']);
            }else{
                unset($data['pwd']);
            }
            unset($data['new_pwd']);
            unset($data['new_pwd_ok']);
            AdminModel::edit($data,$adminInfo['id']);
            return Json::successful('修改成功!,请重新登录');
        }
    }
}
