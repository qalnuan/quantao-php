<?php
namespace app\admin\controller\store;
use app\admin\controller\AuthController;
use app\admin\library\FormBuilder;
use service\UtilService as Util;
use service\JsonService as Json;
use service\UploadService as Upload;
use think\Request;
use think\Url;
use app\admin\model\store\StoreService as ServiceModel;
use app\admin\model\store\StoreServiceLog as StoreServiceLog;
use app\admin\model\wechat\WechatUser as UserModel;

/**
 * 客服管理
 * Class StoreService
 * @package app\admin\controller\store
 */
class StoreService extends AuthController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->assign(ServiceModel::getList(0));
        return $this->fetch();
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create(){
        $where = Util::getMore([
            ['nickname',''],
            ['data',''],
            ['tagid_list',''],
            ['groupid','-1'],
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(UserModel::systemPage($where));
        $this->assign(['title'=>'添加客服','save'=>Url::build('save')]);
        return $this->fetch();
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request){
        $params = $request->post();
        if(count($params["checked_menus"]) <= 0)return Json::fail('请选择要添加的用户!');
        if(ServiceModel::where('mer_id',0)->where(array("uid"=>array("in",$params["checked_menus"])))->count())return Json::fail('添加用户中存在已有的客服!');
        foreach ($params["checked_menus"] as $key => $value) {
            $now_user = UserModel::get($value);
            $data[$key]["mer_id"] = 0;
            $data[$key]["uid"] = $now_user["uid"];
            $data[$key]["avatar"] = $now_user["headimgurl"];
            $data[$key]["nickname"] = $now_user["nickname"];
            $data[$key]["add_time"] = time();
        }
        ServiceModel::setAll($data);
        return Json::successful('添加成功!');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $service = ServiceModel::get($id);
        if(!$service) return Json::fail('数据不存在!');
        FormBuilder::upload('avatar','客服头像')->defaultFileList($service['avatar'])->maxLength(1);
        FormBuilder::text('nickname','客服名称',$service["nickname"]);
        FormBuilder::radio('status','状态',[['value'=>1,'label'=>'显示'],['value'=>0,'label'=>'隐藏']],$service['status']);
        $this->assign(['title'=>'修改数据','groups'=>FormBuilder::builder()->getContent(),'save'=>Url::build('update',["id"=>$id])]);
        return $this->fetch();
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function update(Request $request,$id)
    {
        $params = $request->post();
        if(isset($_POST["nickname"]) || !empty($_POST["nickname"]))return Json::fail("客服名称不能为空！");
        $data = array("avatar"=>$params["avatar"][0],"nickname"=>$params["nickname"],'status'=>$params['status']);
        ServiceModel::edit($data,$id);
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
        if(!ServiceModel::del($id))
            return Json::fail(ServiceModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $res = Upload::image('file','store/service');
        $thumbPath = Upload::thumb($res->dir);
        if($res->status == 200)
            return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function chat_user($id)
    {
        $now_service = ServiceModel::get($id);
        if(!$now_service) return Json::fail('数据不存在!');
        $list = ServiceModel::getChatUser($now_service,0);
        $this->assign(compact('list','now_service'));
        return $this->fetch();
    }

     /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function chat_list($uid,$to_uid)
    {
        $this->assign(StoreServiceLog::getChatList($uid,$to_uid,0));
        $this->assign('to_uid',$to_uid);
        return $this->fetch();
    }
}
