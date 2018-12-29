<?php

namespace app\admin\controller\system;
use app\admin\common\Error;
use app\admin\library\FormBuilder;
use service\JsonService as Json;
use service\UploadService as Upload;
use service\UtilService as Util;
use think\Request;
use think\Url;
use app\admin\model\system\SystemGroup as GroupModel;
use app\admin\model\system\SystemGroupData as GroupDataModel;
use app\admin\controller\AuthController;

/**
 * 数据列表控制器  在组合数据中
 * Class SystemGroupData
 * @package app\admin\controller\system
 */
class SystemGroupData extends AuthController
{

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index($gid)
    {
        $where = Util::getMore([
            ['status','']
        ],$this->request);
        $this->assign('where',$where);
        $this->assign(compact("gid"));
        $this->assign(GroupModel::getField($gid));
        $where['gid'] = $gid;
        $this->assign(GroupDataModel::getList($where));
//        dump(GroupModel::getField($gid));
//        dump(GroupDataModel::getList($where)['list']->toArray());
//        exit();
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create($gid)
    {
        $Fields = GroupModel::getField($gid);
        foreach ($Fields["fields"] as $key => $value) {
            if($value["type"] == "input")
                FormBuilder::text($value["title"],$value["name"]);
            else if($value["type"] == "textarea")
                FormBuilder::textarea($value["title"],$value["name"])->placeholder($value['param']);
            else if($value["type"] == "radio") {
                $params = explode("-", $value["param"]);
                foreach ($params as $index => $param) {
                    $info[$index]["value"] = $param;
                    $info[$index]["label"] = $param;
                }
                FormBuilder::radio($value["title"], $value["name"], $info, $info[0]["value"]);
            }else if($value["type"] == "checkbox"){
                $params = explode("-",$value["param"]);
                foreach ($params as $index => $param) {
                    $info[$index]["value"] = $param;
                    $info[$index]["label"] = $param;
                }
                FormBuilder::checkbox($value["title"],$value["name"],$info);
            }else if($value["type"] == "upload")
                FormBuilder::upload($value["title"],$value["name"])->maxLength();
            else if($value['type'] == 'uploads')
                FormBuilder::upload($value["title"],$value["name"]);

        }
        FormBuilder::text('sort','排序',1);
        FormBuilder::radio('status','状态',[['value'=>1,'label'=>'显示'],['value'=>2,'label'=>'隐藏']],1);
        $this->assign(['title'=>'添加数据','groups'=>FormBuilder::builder()->getContent(),'save'=>Url::build('save',["gid"=>$gid])]);
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request,$gid)
    {
        $Fields = GroupModel::getField($gid);
        $params = $request->post();
        foreach ($params as $key => $param) {
            foreach ($Fields['fields'] as $index => $field) {
                if($key == $field["title"]){
                    if($param == "" || count($param) == 0)
                        return Json::fail($field["name"]."不能为空！");
                    else{
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }

        $data = array("gid"=>$gid,"add_time"=>time(),"value"=>json_encode($value),"sort"=>$params["sort"],"status"=>$params["status"]);
        GroupDataModel::set($data);
        return Json::successful('添加数据成功!');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($gid,$id)
    {
        $GroupData = GroupDataModel::get($id);
        $GroupDataValue = json_decode($GroupData["value"],true);
        $Fields = GroupModel::getField($gid);
        foreach ($Fields["fields"] as $key => $value) {
            if($value["type"] == "input")FormBuilder::text($value["title"],$value["name"],$GroupDataValue[$value["title"]]["value"]);
            if($value["type"] == "textarea")FormBuilder::textarea($value["title"],$value["name"],$GroupDataValue[$value["title"]]["value"]);
            if($value["type"] == "radio"){
                $params = explode("-",$value["param"]);
                foreach ($params as $index => $param) {
                    $info[$index]["value"] = $param;
                    $info[$index]["label"] = $param;
                }
                FormBuilder::radio($value["title"],$value["name"],$info,$GroupDataValue[$value["title"]]["value"]);
            }
            if($value["type"] == "checkbox"){
                $params = explode("-",$value["param"]);
                foreach ($params as $index => $param) {
                    $info[$index]["value"] = $param;
                    $info[$index]["label"] = $param;
                }
                FormBuilder::checkbox($value["title"],$value["name"],$info,$GroupDataValue[$value["title"]]["value"]);
            }
            if($value["type"] == "upload")
                FormBuilder::upload($value["title"],$value["name"])->maxLength()->defaultFileList($GroupDataValue[$value["title"]]["value"]);
            else if($value['type'] == 'uploads')
                FormBuilder::upload($value["title"],$value["name"])->defaultFileList($GroupDataValue[$value["title"]]["value"]);

        }
        FormBuilder::text('sort','排序',$GroupData["sort"]);
        FormBuilder::radio('status','状态',[['value'=>1,'label'=>'显示'],['value'=>2,'label'=>'隐藏']],$GroupData["status"]);
        $this->assign(['title'=>'添加数据','groups'=>FormBuilder::builder()->getContent(),'save'=>Url::build('update',["id"=>$id])]);
        return $this->fetch();
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
        $GroupData = GroupDataModel::get($id);
        $Fields = GroupModel::getField($GroupData["gid"]);
        $params = $request->post();
        foreach ($params as $key => $param) {
            foreach ($Fields['fields'] as $index => $field) {
                if($key == $field["title"]){
                    if($param == "" || count($param) == 0)
                        return Json::fail($field["name"]."不能为空！");
                    else{
                        $value[$key]["type"] = $field["type"];
                        $value[$key]["value"] = $param;
                    }
                }
            }
        }
        $data = array("value"=>json_encode($value),"sort"=>$params["sort"],"status"=>$params["status"]);
        GroupDataModel::edit($data,$id);
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
        if(!GroupDataModel::del($id))
            return Json::fail(GroupDataModel::getErrorInfo('删除失败,请稍候再试!'));
        else
            return Json::successful('删除成功!');
    }

    public function upload()
    {
        $res = Upload::image('file','common');
        $thumbPath = Upload::thumb($res->dir);
        if($res->status == 200)
            return Json::successful('图片上传成功!',['name'=>$res->fileInfo->getSaveName(),'url'=>Upload::pathToUrl($thumbPath)]);
        else
            return Json::fail($res->error);
    }
}
