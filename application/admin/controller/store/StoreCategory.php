<?php

namespace app\admin\controller\store;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use app\admin\model\store\StoreCategory as CategoryModel;
use app\admin\model\wechat\WechatQrcode;
use think\Url;

/**
 * 产品分类控制器
 * Class StoreCategory
 * @package app\admin\controller\system
 */
class StoreCategory extends AuthController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = Util::getMore([
            ['is_show',''],
            ['pid',''],
            ['cate_name',''],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign('cate',CategoryModel::getTierList());
        $this->assign(CategoryModel::systemPage($where));
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->assign(['title'=>'添加分类','action'=>Url::build('save'),'rules'=>$this->rules()->getContent()]);
        return $this->fetch('public/common_form');
    }

    /**
     * @return \think\response\Json
     */
    public function rules()
    {
        FormBuilder::select('pid','父级',function(){
            $list = CategoryModel::getTierList();
            $menus = [['value'=>0,'label'=>'顶级菜单']];
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['html'].$menu['cate_name']];
            }
            return $menus;
        },0);
        FormBuilder::text('cate_name','分类名称');
        FormBuilder::upload('pic','分类图标')->maxLength(1);
        FormBuilder::number('sort','排序');
        FormBuilder::radio('is_show','状态',[['label'=>'显示','value'=>1],['label'=>'隐藏','value'=>0]],1);
        return FormBuilder::builder();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','store/category');
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
            'pid',
            'cate_name',
            ['pic',[]],
            'sort',
            ['is_show',0]
        ],$request);
        if($data['pid'] == '') return Json::fail('请选择父类');
        if(!$data['cate_name']) return Json::fail('请输入分类名称');
        if(count($data['pic'])<1) return Json::fail('请上传分类图标');
        if($data['sort'] <0 ) $data['sort'] = 0;
        $data['pic'] = $data['pic'][0];
        $data['add_time'] = time();
        CategoryModel::set($data);
//        WechatQrcode::createForeverQrcode('shopcrmebcs.kycms.net/wap/my/index.html','ory');
        return Json::successful('添加分类成功!');
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
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $category = CategoryModel::get($id);
        if(!$category) return Json::fail('数据不存在!');
        FormBuilder::select('pid','父级',function() use($id){
            $list = CategoryModel::getTierList(CategoryModel::where('id','<>',$id));
            $menus = [['value'=>0,'label'=>'顶级菜单']];
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['html'].$menu['cate_name']];
            }
            return $menus;
        },$category->getData('pid'));
        FormBuilder::text('cate_name','分类名称',$category->getData('cate_name'));
        FormBuilder::upload('pic','分类图标')->defaultFileList($category->getData('pic'))->maxLength(1);
        FormBuilder::number('sort','排序',$category->getData('sort'));
        FormBuilder::radio('is_show','状态',[['label'=>'显示','value'=>1],['label'=>'隐藏','value'=>0]],$category->getData('is_show'));
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
            'pid',
            'cate_name',
            ['pic',[]],
            'sort',
            ['is_show',0]
        ],$request);
        if($data['pid'] == '') return Json::fail('请选择父类');
        if(!$data['cate_name']) return Json::fail('请输入分类名称');
        if(count($data['pic'])<1) return Json::fail('请上传分类图标');
        if($data['sort'] <0 ) $data['sort'] = 0;
        $data['pic'] = $data['pic'][0];
        CategoryModel::edit($data,$id);
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
        if(!CategoryModel::delCategory($id))
            return Json::fail(CategoryModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function category_two($pid = 0){
        if(!$pid) return $this->failed('参数错误');
        $where = Util::getMore([
            ['is_show',''],
            ['pid',$pid],
            ['cate_name',''],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign('cate',CategoryModel::getTierList());
        $this->assign(CategoryModel::systemPage($where));
        return $this->fetch();
    }
}
