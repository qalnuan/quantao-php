<?php

namespace app\admin\controller\system;

use app\admin\library\FormBuilder;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\system\SystemMenus as MenusModel;
use app\admin\controller\AuthController;

/**
 * 菜单管理控制器
 * Class SystemMenus
 * @package app\admin\controller\system
 */
class SystemMenus extends AuthController
{
    use CurdControllerTrait;

    public $bindModel = MenusModel::class;

    public function rules($id=0)
    {
        FormBuilder::text('menu_name','按钮名称');
        FormBuilder::select('pid','父级id',function(){
            $list = (Util::sortListTier(MenusModel::all()->toArray(),'顶级','pid','menu_name'));
            $menus = [['value'=>0,'label'=>'顶级按钮']];
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['html'].$menu['menu_name']];
            }
            return $menus;
        },$id)->filterable();
        FormBuilder::select('module','模块名',[['label'=>'总后台','value'=>'admin']],'admin');
        FormBuilder::text('controller','控制器名');
        FormBuilder::text('action','方法名');
        FormBuilder::text('params','参数')->placeholder('举例:a/123/b/234');
        FormBuilder::text('icon','图标');
        FormBuilder::number('sort','排序',0);
        FormBuilder::radio('is_show','是否显示',[0=>'隐藏',1=>'显示'],1);
        return FormBuilder::builder();
    }

    public function upload()
    {
        $res = Upload::Image('file','config');
        if(!$res->status) return Json::fail($res->error);
        $thumbPath = Upload::thumb($res->dir);
        return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
    }

    public function attribute()
    {
        $limit = 15;
        $total = MenusModel::count();
        $head = ['id'=>'编号','pid'=>'上级菜单','menu_name'=>'按钮名称','module'=>'模块','action'=>'方法','is_show'=>'是否显示','access'=>'管理员可用','_handle'=>['edit','del']];
        return Json::successful(compact('limit','total','head'));
    }

    public function page()
    {
        $limit = (int)$_GET['limit'];
        $first = (int)$_GET['first'];
        $menu = new MenusModel;
        $list = $menu->limit($first,$limit)->select();
        return Json::successful($list);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $params = Util::getMore([
            ['is_show',''],
            ['access',''],
            ['keyword','']
        ],$this->request);
        $this->assign(MenusModel::getAdminPage($params));
        $this->assign(compact('params'));
        return $this->fetch();
    }


    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create($cid)
    {

        $this->assign(['title'=>'编辑菜单','rules'=>$this->rules($cid)->getContent(),'action'=>Url::build('save')]);
        return $this->fetch();
//        return $this->fetch('public/common_form');
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
            'menu_name',
            'controller',
            ['module','admin'],
            'action',
            'icon',
            'params',
            ['pid',0],
            ['sort',0],
            ['is_show',0],
            ['access',1]],$request);
        if(!$data['menu_name']) return Json::fail('请输入按钮名称');
        MenusModel::set($data);
        return Json::successful('添加菜单成功!');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $menu = MenusModel::get($id);
        if(!$menu) return Json::fail('数据不存在!');
        FormBuilder::text('menu_name','按钮名称',$menu['menu_name']);
        FormBuilder::select('pid','父级id',function()use($id){
            $list = (Util::sortListTier(MenusModel::where('id','<>',$id)->select()->toArray(),'顶级','pid','menu_name'));
            $menus = [['value'=>0,'label'=>'顶级按钮']];
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['html'].$menu['menu_name']];
            }
            return $menus;
        },$menu->getData('pid'))->filterable();
        FormBuilder::select('module','模块名',[['label'=>'总后台','value'=>'admin']],$menu->getData('module'));
        FormBuilder::text('controller','控制器名',$menu['controller']);
        FormBuilder::text('action','方法名',$menu['action']);
        FormBuilder::text('params','参数',MenusModel::paramStr($menu['params']))->placeholder('举例:a/123/b/234');
        FormBuilder::text('icon','图标',$menu['icon']);
        FormBuilder::number('sort','排序',$menu['sort']);
        FormBuilder::radio('is_show','是否显示',[0=>'隐藏',1=>'显示'],$menu['is_show']);
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
        $this->assign(['title'=>'编辑菜单','rules'=>$this->read($id)->getContent(),'action'=>Url::build('update',array('id'=>$id))]);
        return $this->fetch();
        //return $this->fetch('public/common_form');
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
            'menu_name',
            'controller',
            ['module','admin'],
            'action',
            'params',
            'icon',
            ['sort',0],
            ['pid',0],
            ['is_show',0],
            ['access',1]],$request);
        if(!$data['menu_name']) return Json::fail('请输入按钮名称');
        if(!MenusModel::get($id)) return Json::fail('编辑的记录不存在!');
        MenusModel::edit($data,$id);
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
        $res = MenusModel::delMenu($id);
        if(!$res)
            return Json::fail(MenusModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function edit_content($id)
    {
        $this->assign(['field'=>'action','action'=>Url::build('change_field',['id'=>$id,'field'=>'action'])]);
        return $this->fetch();
    }

    /**
     * ICON图标展示页面
     *
     */
    public function icon()
    {
        return $this->fetch();
    }
}
