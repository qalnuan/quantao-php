<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:75:"C:\Code\Mine\crmeb/application/admin\view\store\store_coupon_user\index.php";i:1527060420;s:62:"C:\Code\Mine\crmeb/application/admin\view\public\container.php";i:1527060420;s:63:"C:\Code\Mine\crmeb/application/admin\view\public\frame_head.php";i:1527060420;s:58:"C:\Code\Mine\crmeb/application/admin\view\public\style.php";i:1527060420;s:63:"C:\Code\Mine\crmeb/application/admin\view\public\inner_page.php";i:1527060420;s:65:"C:\Code\Mine\crmeb/application/admin\view\public\frame_footer.php";i:1527060420;}*/ ?>
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

                            <select name="is_fail" aria-controls="editable" class="form-control input-sm">
                                <option value="">是否有效</option>
                                <option value="1" <?php if($where['is_fail'] == '1'): ?>selected="selected"<?php endif; ?>>是</option>
                                <option value="0" <?php if($where['is_fail'] == '0'): ?>selected="selected"<?php endif; ?>>否</option>
                            </select>
                            <select name="status" aria-controls="editable" class="form-control input-sm">
                                <option value="">状态</option>
                                <option value="1" <?php if($where['status'] == '1'): ?>selected="selected"<?php endif; ?>>已使用</option>
                                <option value="0" <?php if($where['status'] == '0'): ?>selected="selected"<?php endif; ?>>未使用</option>
                                <option value="2" <?php if($where['status'] == '2'): ?>selected="selected"<?php endif; ?>>已过期</option>
                            </select>
                            <div class="input-group">
                                <input type="text" name="nickname" value="<?php echo $where['nickname']; ?>" placeholder="请输入发放人姓名" class="input-sm form-control"> <span class="input-group-btn">
                            </div>
                            <div class="input-group">
                                <input type="text" name="coupon_title" value="<?php echo $where['coupon_title']; ?>" placeholder="请输入优惠券名称" class="input-sm form-control"> <span class="input-group-btn">
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
                            <th class="text-center">优惠券名称</th>
                            <th class="text-center">发放人</th>
                            <th class="text-center">优惠券面值</th>
                            <th class="text-center">优惠券最低消费</th>
                            <th class="text-center">优惠券开始使用时间</th>
                            <th class="text-center">优惠券结束使用时间</th>
                            <th class="text-center">获取放方式</th>
                            <th class="text-center">是否可用</th>
                            <th class="text-center">状态</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-center">
                                <?php echo $vo['id']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['coupon_title']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['nickname']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['coupon_price']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['use_min_price']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo date('Y-m-d H:i:s',$vo['add_time']); ?>
                            </td>
                            <td class="text-center">
                                <?php echo date('Y-m-d H:i:s',$vo['end_time']); ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['type']=='send'?'后台发放' : '手动领取'; ?>
                            </td>
                            <td class="text-center">
                                <?php if($vo['status'] == 0): ?>
                                    <i class="fa fa-check text-navy"></i>
                                <?php else: ?>
                                <i class="fa fa-close text-danger"></i>
                                <?php endif; ?>
                             </td>
                            <td class="text-center">
                                <?php if($vo['status'] == 2): ?>
                                已过期
                                <?php elseif($vo['status'] == 1): ?>
                                已使用
                                <?php else: ?>
                                未使用
                                <?php endif; ?>
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




</div>
</body>
</html>
