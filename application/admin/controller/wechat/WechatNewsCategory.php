<?php

namespace app\admin\controller\wechat;

use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use app\admin\model\wechat\WechatReply;
use app\admin\model\wechat\WechatUser;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UtilService;
use service\WechatService;
use think\Db;
use think\Request;
use think\Url;
use \app\admin\model\wechat\WechatNewsCategory as WechatNewsCategoryModel;
use app\admin\model\article\Article as ArticleModel;
/**
 * 图文信息
 * Class WechatNewsCategory
 * @package app\admin\controller\wechat
 *
 */
class WechatNewsCategory extends AuthController
{
    public function select($callback = '_selectNews$eb'){
        $where = Util::getMore([
            ['cate_name','']
        ],$this->request);
        $this->assign('where',$where);
        $this->assign('callback',$callback);
        $this->assign(WechatNewsCategoryModel::getAll($where));
        return $this->fetch();
    }
    public function index()
    {
        $where = Util::getMore([
            ['cate_name','']
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(WechatNewsCategoryModel::getAll($where));
        return $this->fetch();
    }
    public function create(){
        FormBuilder::text('cate_name','图文名称')->autofocus();
        FormBuilder::select('new_id','图文列表',function(){
            $list = ArticleModel::getNews();
            $options = [];
            foreach ($list as $id=>$roleName){
                $options[] = ['label'=>$roleName,'value'=>$id];
            }
            return $options;
        })->multiple()->filterable();
        $rules =  FormBuilder::builder()->getContent();
        $this->assign(['title'=>'编辑菜单','rules'=>$rules,'save'=>Url::build('save')]);
        return $this->fetch();
    }
    public function save(Request $request){
        $data = Util::postMore([
            'cate_name',
            ['new_id',[]],
            ['sort',0],
            ['add_time',time()],
            ['status',1],],$request);
        if(!$data['cate_name']) return Json::fail('请输入图文名称');
        if(empty($data['new_id'])) return Json::fail('请选择图文列表');
        $data['new_id'] = array_unique($data['new_id']);
        if(count($data['new_id']) > 8){
            $data['new_id'] = array_slice($data['new_id'], 0, 8);
        };
        $data['new_id'] = implode(',',$data['new_id']);
        WechatNewsCategoryModel::set($data);
        return Json::successful('添加菜单成功!');
    }
    public function edit($id){
        $this->assign(['title'=>'编辑菜单','read'=>Url::build('read',array('id'=>$id)),'update'=>Url::build('update',array('id'=>$id))]);
        return $this->fetch();
    }
    public function read($id)
    {
        $menu = WechatNewsCategoryModel::get($id);
//        dump($menu);
        if(!$menu) return Json::fail('数据不存在!');
        $arr_new_id = array_unique(explode(',',$menu->new_id));
        foreach ($arr_new_id as $k=>$v){
            $arr_new_id[$k] = intval($v);
        }
//        dump($arr_new_id);
        FormBuilder::text('cate_name','分类名称',$menu['cate_name']);
        FormBuilder::select('new_id','图文列表',function(){
            $list = ArticleModel::getNews();
            $options = [];
            foreach ($list as $id=>$roleName){
                $options[] = ['label'=>$roleName,'value'=>$id];
            }
            return $options;
        },$arr_new_id)->multiple();
        return FormBuilder::builder();
    }
    public function update(Request $request, $id)
    {
        $data = Util::postMore([
            'cate_name',
            ['new_id',[]],
            ['sort',0],
            ['status',1],],$request);
        if(!$data['cate_name']) return Json::fail('请输入图文名称');
        if(empty($data['new_id'])) return Json::fail('请选择图文列表');
        if(count($data['new_id']) > 8){
            $data['new_id'] = array_slice($data['new_id'], 0, 8);
        };
        $data['new_id'] = implode(',',$data['new_id']);;
        if(!WechatNewsCategoryModel::get($id)) return Json::fail('编辑的记录不存在!');
        WechatNewsCategoryModel::edit($data,$id);
        return Json::successful('修改成功!');
    }
    public function delete($id){
        if(!WechatNewsCategoryModel::del($id))
            return Json::fail(WechatNewsCategoryModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**
     * 发送消息
     * @param int $id
     * @param string $wechat
     * $wechat  不为空  发消息  /  空 群发消息
     */
    public function push($id = 0,$wechat = ''){
        if(!$id) return Json::fail('参数错误');
        $list = WechatNewsCategoryModel::getWechatNewsItem($id);
        $wechatNews = [];
        if($list){
            if($list['new'] && is_array($list['new'])){
                foreach ($list['new'] as $kk=>$vv){
                    $wechatNews[$kk]['title'] = $vv['title'];
                    $wechatNews[$kk]['image'] = $vv['image_input'];
                    $wechatNews[$kk]['date'] = date('m月d日',time());
                    $wechatNews[$kk]['description'] = $vv['synopsis'];
                    $wechatNews[$kk]['id'] = $vv['id'];
                }
            }
        }
        if($wechat != ''){//客服消息
            $wechatNews = WechatReply::tidyNews($wechatNews);
            $message = WechatService::newsMessage($wechatNews);
            $errorLog = [];//发送失败的用户
            $user = WechatUser::where('uid','IN',$wechat)->column('nickname,subscribe,openid','uid');
            if($user){
                foreach ($user as $v){
                    if($v['subscribe'] && $v['openid']){
                        try {
                            WechatService::staffService()->message($message)->to($v['openid'])->send();
                        } catch (\Exception $e) {
                            $errorLog[] = $v['nickname'].'发送失败';
                        }
                    }else{
                        $errorLog[] = $v['nickname'].'没有关注发送失败';
                    }
                }
            }else return Json::fail('发送失败，参数不正确');
            if(!count($errorLog)) return Json::successful('全部发送成功');
            else return Json::successful(implode(',',$errorLog).'，剩余的发送成功');
        }else{//群发消息
//        if($list){
//               if($list['new'] && is_array($list['new'])){
//                   foreach ($list['new'] as $kk=>$vv){
//                       $wechatNews[$kk]['title'] = $vv['title'];
//                       $wechatNews[$kk]['thumb_media_id'] = $vv['image_input'];
//                       $wechatNews[$kk]['author'] = $vv['author'];
//                       $wechatNews[$kk]['digest'] = $vv['synopsis'];
//                       $wechatNews[$kk]['show_cover_pic'] = 1;
//                       $wechatNews[$kk]['content'] = Db::name('articleContent')->where('nid',$vv["id"])->value('content');
//                       $wechatNews[$kk]['content_source_url'] = $vv['url'];
//                   }
//            }
//        }
            //6sFx6PzPF2v_Lv4FGOMzz-oQunU2Z3wrOWb-7zS508E
            //6sFx6PzPF2v_Lv4FGOMzz7SUUuamgWwlqdVfhQ5ALT4
//        foreach ($wechatNews as $k=>$v){
//            $material = WechatService::materialService()->uploadImage(UtilService::urlToPath($v['thumb_media_id']));
//            dump($material);
//            $wechatNews[$k]['thumb_media_id'] = $material->media_id;
//        }
//        $mediaIdNews = WechatService::uploadNews($wechatNews);
//        $res = WechatService::sendNewsMessage($mediaIdNews->media_id);
//        if($res->errcode) return Json::fail($res->errmsg);
//        else return Json::successful('推送成功');
//        dump($mediaIdNews);
//        dump($res);
        }
    }

    public function send_news($id = ''){
        if($id == '') return $this->failed('参数错误');
        $where = Util::getMore([
            ['cate_name','']
        ],$this->request);
        $this->assign('where',$where);
        $this->assign('wechat',$id);
        $this->assign(WechatNewsCategoryModel::getAll($where));
        return $this->fetch();
    }

}