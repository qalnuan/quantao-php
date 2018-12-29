<?php

namespace app\admin\controller\system;

use think\Request;
use think\Url;
use app\admin\model\system\SystemFile as SystemFileModel;

use app\admin\controller\AuthController;

/**
 * 文件校验控制器
 * Class SystemFile
 * @package app\admin\controller\system
 *
 */
class SystemFile extends AuthController
{
   public function index(){
       $app = $this->getDir('./application');
       $extend = $this->getDir('./extend');
       $public = $this->getDir('./public');
       $arr = array();
       $arr = array_merge($app,$extend);
       $arr = array_merge($arr,$public);
       $fileAll = array();//本地文件
       $cha = array();//不同的文件
       foreach ($arr as $k=>$v) {
           $fp = fopen($v, 'r');
           if (filesize($v))  $ct = fread($fp, filesize($v));
           else $ct = null;
           fclose($fp);
           $cthash = md5($ct);
                   $update_time = stat($v);
           $fileAll[$k]['cthash'] = $cthash;
           $fileAll[$k]['filename'] = $v;
           $fileAll[$k]['atime'] = $update_time['atime'];
           $fileAll[$k]['mtime'] = $update_time['mtime'];
           $fileAll[$k]['ctime'] = $update_time['ctime'];
       }
       $file = SystemFileModel::all(function($query){
           $query->order('atime', 'desc');
       })->toArray();//数据库中的文件
       if(empty($file)){
           $data_num = array_chunk($fileAll,10);
           SystemFileModel::beginTrans();
           $res = true;
           foreach ($data_num as $k=>$v){
               $res = $res && SystemFileModel::insertAll($v);
           }
           SystemFileModel::checkTrans($res);
           if($res){
               $cha = array();//不同的文件
           }else{
               $cha = $fileAll;
           }
       }else{
           $cha = array();//差异文件
           foreach ($file as $k=>$v){
               foreach ($fileAll as $ko=>$vo){
                   if($v['filename'] == $vo['filename']){
                       if($v['cthash'] != $vo['cthash']){
                           $cha[$k]['filename'] = $v['filename'];
                           $cha[$k]['cthash'] = $v['cthash'];
                           $cha[$k]['atime'] = $v['atime'];
                           $cha[$k]['mtime'] = $v['mtime'];
                           $cha[$k]['ctime'] = $v['ctime'];
                           $cha[$k]['type'] =  '已修改';
                       }
                       unset($fileAll[$ko]);
                       unset($file[$k]);
                   }
               }

           }
           foreach ($file as $k=>$v){
               $cha[$k]['filename'] = $v['filename'];
               $cha[$k]['cthash'] = $v['cthash'];
               $cha[$k]['atime'] = $v['atime'];
               $cha[$k]['mtime'] = $v['mtime'];
               $cha[$k]['ctime'] = $v['ctime'];
               $cha[$k]['type'] =  '已删除';
           }
           foreach ($fileAll as $k=>$v){
               $cha[$k]['filename'] = $v['filename'];
               $cha[$k]['cthash'] = $v['cthash'];
               $cha[$k]['atime'] = $v['atime'];
               $cha[$k]['mtime'] = $v['mtime'];
               $cha[$k]['ctime'] = $v['ctime'];
               $cha[$k]['type'] =  '新增的';
           }

       }
//   dump($file);
//   dump($fileAll);
       $this->assign('cha',$cha);
       return $this->fetch();
   }

    /**
     * 获取文件夹中的文件 包括子文件 不能直接用  直接使用  $this->getDir()方法 P156
     * @param $path
     * @param $data
     */
    public function searchDir($path,&$data){
        if(is_dir($path) && !strpos($path,'uploads')){
            $dp=dir($path);
            while($file=$dp->read()){
                if($file!='.'&& $file!='..'){
                    $this->searchDir($path.'/'.$file,$data);
                }
            }
            $dp->close();
        }
        if(is_file($path)){
           $data[]=$path;
        }
    }

    /**
     * 获取文件夹中的文件 包括子文件
     * @param $dir
     * @return array
     */
    public function getDir($dir){
        $data=array();
        $this->searchDir($dir,$data);
        return   $data;
    }
}
