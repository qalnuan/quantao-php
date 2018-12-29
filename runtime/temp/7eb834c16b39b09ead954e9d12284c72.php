<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:56:"F:\Mine\crmeb/application/admin\view\user\user\index.php";i:1527816030;s:57:"F:\Mine\crmeb/application/admin\view\public\container.php";i:1527060420;s:58:"F:\Mine\crmeb/application/admin\view\public\frame_head.php";i:1527060420;s:53:"F:\Mine\crmeb/application/admin\view\public\style.php";i:1527060420;s:58:"F:\Mine\crmeb/application/admin\view\public\inner_page.php";i:1527060420;s:60:"F:\Mine\crmeb/application/admin\view\public\frame_footer.php";i:1527060420;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{__FRAME_PATH}css/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="{__FRAME_PATH}css/font-awesome.min.css?v=4.3.0" rel="stylesheet">
    <link href="{__FRAME_PATH}css/animate.min.css" rel="stylesheet">
    <link href="{__FRAME_PATH}css/style.min.css?v=3.0.0" rel="stylesheet">
    <script src="{__FRAME_PATH}js/jquery.min.js"></script>
    <script src="{__FRAME_PATH}js/bootstrap.min.js"></script>
    <script>
        $eb = parent._mpApi;
        if(!$eb) top.location.reload();
    </script>


    <title></title>
    
    <!--<script type="text/javascript" src="/static/plug/basket.js"></script>-->
<script type="text/javascript" src="{__ADMIN_PATH}/plug/requirejs/require.js"></script>
<?php /*  <script type="text/javascript" src="/static/plug/requirejs/require-basket-load.js"></script>  */ ?>
<script>
    requirejs.config({
        map: {
            '*': {
                'css': '/public/static/plug/requirejs/require-css.js'
            }
        },
        shim:{
            'iview':{
                deps:['css!iviewcss']
            },
            'layer':{
                deps:['css!layercss']
            }
        },
        baseUrl:'//'+location.hostname+'/public',
        paths: {
            'static':'static',
            'system':'system',
            'vue':'static/plug/vue/dist/vue.min',
            'axios':'static/plug/axios.min',
            'iview':'static/plug/iview/dist/iview.min',
            'iviewcss':'static/plug/iview/dist/styles/iview',
            'lodash':'static/plug/lodash',
            'layer':'static/plug/layer/layer',
            'layercss':'static/plug/layer/theme/default/layer',
            'jquery':'static/plug/jquery-1.10.2.min',
            'moment':'static/plug/moment',
            'mpBuilder':'system/util/mpBuilder',
            'sweetalert':'static/plug/sweetalert2/sweetalert2.all.min'

        },
        basket: {
            excludes:['system/util/mpFormBuilder','system/js/index','system/util/mpVueComponent','system/util/mpVuePackage']
        }
    });
</script>
<script type="text/javascript" src="{__ADMIN_PATH}util/mpFrame.js"></script>
    
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline">
                            <select name="status" aria-controls="editable" class="form-control input-sm">
                                <option value="" <?php if($where['status'] == ''): ?>selected="selected"<?php endif; ?>>状态</option>
                                <option value="1" <?php if($where['status'] == '1'): ?>selected="selected"<?php endif; ?>>正常</option>
                                <option value="0" <?php if($where['status'] == '0'): ?>selected="selected"<?php endif; ?>>锁定</option>
                            </select>
                            <select name="is_promoter" aria-controls="editable" class="form-control input-sm">
                                <option value="" <?php if($where['is_promoter'] == ''): ?>selected="selected"<?php endif; ?>>身份</option>
                                <option value="0" <?php if($where['is_promoter'] == '0'): ?>selected="selected"<?php endif; ?>>普通用户</option>
                                <option value="1" <?php if($where['is_promoter'] == '1'): ?>selected="selected"<?php endif; ?>>推广员</option>
                            </select>
                            <div class="input-group">
                                <input size="26" type="text" name="nickname" value="<?php echo $where['nickname']; ?>" placeholder="请输入姓名、编号" class="input-sm form-control"> <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> <i class="fa fa-search" ></i>搜索</button> </span>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">编号</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">头像</th>
<!--                            <th class="text-center">余额</th>-->
<!--                            <th class="text-center">积分</th>-->
                            <th class="text-center">推荐人</th>
                            <th class="text-center">推广员</th>
                            <th class="text-center">状态</th>
                            <th class="text-center">用户类型</th>
                            <th class="text-center">添加时间</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-center"><?php echo $vo['uid']; ?></td>
                            <td class="text-center"><?php echo $vo['nickname']; ?></td>
                            <td class="text-center">
                                <img src="<?php echo $vo['avatar']; ?>" alt="<?php echo $vo['nickname']; ?>" class="open_image" data-image="<?php echo $vo['avatar']; ?>" style="width: 50px;height: 50px;cursor: pointer;">
                            </td>
<!--                            <td class="text-center"><?php echo $vo['now_money']; ?></td>-->
<!--                            <td class="text-center"><?php echo $vo['integral']; ?></td>-->
                            <td class="text-center"><?php echo $vo['spread_uid_nickname']; ?>/<?php echo $vo['spread_uid']; ?></td>
                            <td class="text-center">
                                <i class="fa <?php if($vo['is_promoter'] == '1'): ?>fa-check text-navy<?php else: ?>fa-close text-danger<?php endif; ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="fa <?php if($vo['status'] == '1'): ?>fa-check text-navy<?php else: ?>fa-close text-danger<?php endif; ?>"></i>
                            </td>
                            <td class="text-center">
                                <?php if($vo['user_type'] == 'routine'): ?>
                                小程序类型
                                <?php else: ?>
                                其他类型
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?php echo date('Y-m-d H:i:s',$vo['add_time']); ?></td>
                            <td class="text-center">
                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('编辑','<?php echo Url('edit',array('uid'=>$vo['uid'])); ?>')"><i class="fa fa-paste"></i> 编辑</button>
                            </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                </div>
                <link href="{__FRAME_PATH}css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
<div class="row">
    <div class="col-sm-6">
        <div class="dataTables_info" id="DataTables_Table_0_info" role="alert" aria-live="polite" aria-relevant="all">共 <?php echo $total; ?> 项</div>
    </div>
    <div class="col-sm-6">
        <div class="dataTables_paginate paging_simple_numbers" id="editable_paginate">
            <?php echo $page; ?>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>



<script>
    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>


</div>
</body>
</html>
