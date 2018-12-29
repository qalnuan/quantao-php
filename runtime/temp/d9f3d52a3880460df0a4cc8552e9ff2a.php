<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:71:"C:\Code\Mine\crmeb/application/admin\view\store\store_product\index.php";i:1527730200;s:62:"C:\Code\Mine\crmeb/application/admin\view\public\container.php";i:1527060420;s:63:"C:\Code\Mine\crmeb/application/admin\view\public\frame_head.php";i:1527060420;s:58:"C:\Code\Mine\crmeb/application/admin\view\public\style.php";i:1527060420;s:63:"C:\Code\Mine\crmeb/application/admin\view\public\inner_page.php";i:1527060420;s:65:"C:\Code\Mine\crmeb/application/admin\view\public\frame_footer.php";i:1527060420;}*/ ?>
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
    
<link rel="stylesheet" href="{__PLUG_PATH}daterangepicker/daterangepicker.css">
<link href="{__FRAME_PATH}css/plugins/footable/footable.core.css" rel="stylesheet">
<script src="{__PLUG_PATH}sweetalert2/sweetalert2.all.min.js"></script>
<script src="{__PLUG_PATH}moment.js"></script>
<script src="{__PLUG_PATH}daterangepicker/daterangepicker.js"></script>
<script src="{__PLUG_PATH}echarts.common.min.js"></script>
<script src="{__FRAME_PATH}js/plugins/footable/footable.all.min.js"></script>

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
            <div class="ibox-title">
                <button type="button" class="btn btn-w-m btn-primary" onclick="$eb.createModalFrame(this.innerText,'<?php echo Url('create'); ?>')">添加产品</button>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline" id="form">
                            <div class="search-item" data-name="data">
                                <span>创建时间：</span>
                                <button style="height: 26px;line-height: 12px;font-size: 12px;" type="button" class="btn btn-outline btn-link" data-value="<?php echo $limitTimeList['today']; ?>">今天</button>
                                <button style="height: 26px;line-height: 12px;font-size: 12px;" type="button" class="btn btn-outline btn-link" data-value="<?php echo $limitTimeList['week']; ?>">本周</button>
                                <button style="height: 26px;line-height: 12px;font-size: 12px;" type="button" class="btn btn-outline btn-link" data-value="<?php echo $limitTimeList['month']; ?>">本月</button>
                                <button style="height: 26px;line-height: 12px;font-size: 12px;" type="button" class="btn btn-outline btn-link" data-value="<?php echo $limitTimeList['quarter']; ?>">本季度</button>
                                <button style="height: 26px;line-height: 12px;font-size: 12px;" type="button" class="btn btn-outline btn-link" data-value="<?php echo $limitTimeList['year']; ?>">本年</button>
                                <div class="datepicker" style="display: inline-block;">
                                    <button style="height: 26px;line-height: 12px;font-size: 12px;" type="button" class="btn btn-outline btn-link" data-value="<?php echo !empty($where['data'])?$where['data']:'no'; ?>">自定义</button>
                                </div>
                                <input class="search-item-value" type="hidden" name="data" value="<?php echo $where['data']; ?>" />
                            </div>

                            <div style="padding-top:6px;">
                                <div  style="float: left; width: 70px;height: 30px;line-height: 30px;">商品标签：</div>
                                <div class="search-is" data-name="is_hot" style="float: left;">
                                    <input class="search-is-value" type="hidden" name="is_hot" value="<?php echo $where['is_hot']; ?>" />
                                    <button type="button" class="btn btn-outline btn-link" data-value="1" style="font-size: 12px;height: 26px;line-height: 12px;" >热卖</button>
                                </div>
                                <div class="search-is" data-name="is_benefit" style="float: left;margin-left: 5px;">
                                    <input class="search-is-value" type="hidden" name="is_benefit" value="<?php echo $where['is_benefit']; ?>" />
                                    <button type="button" class="btn btn-outline btn-link" data-value="1" style="font-size: 12px;height: 26px;line-height: 12px;" >促销</button>
                                </div>
                                <div class="search-is" data-name="is_best" style="float: left;margin-left: 5px;">
                                    <input class="search-is-value" type="hidden" name="is_best" value="<?php echo $where['is_best']; ?>" />
                                    <button type="button" class="btn btn-outline btn-link" data-value="1" style="font-size: 12px;height: 26px;line-height: 12px;" >精品</button>
                                </div>
                                <div class="search-is" data-name="is_new" style="float: left;margin-left: 5px;">
                                    <input class="search-is-value" type="hidden" name="is_new" value="<?php echo $where['is_new']; ?>" />
                                    <button type="button" class="btn btn-outline btn-link" data-value="1" style="font-size: 12px;height: 26px;line-height: 12px;" >首发</button>
                                </div>
                            </div>

                            <div class="input-group" style="float: right;">
                                <span class="input-group-btn">
                                <input type="hidden" name="export" value="0">
                                <input type="hidden" name="is_show" value="<?php echo $where['is_show']; ?>" />
                                <input type="text" name="store_name" value="<?php echo $where['store_name']; ?>" placeholder="请输入产品名称,关键字,编号" class="input-sm form-control" size="38">
                                 <button type="submit" id="no_export" class="btn btn-sm btn-primary"> <i class="fa fa-search" ></i> 搜索</button>
                                <button type="submit" id="export" class="btn btn-sm btn-info btn-outline"> <i class="fa fa-exchange" ></i> Excel导出</button></span>
                                <script>
                                    $('#export').on('click',function(){
                                        $('input[name=export]').val(1);
                                    });
                                    $('#no_export').on('click',function(){
                                        $('input[name=export]').val(0);
                                    });
                                </script>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="table-responsive" style="margin-top: 20px;overflow:visible;">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">编号</th>
                            <th class="text-center">产品图片</th>
                            <th class="text-center">产品名称</th>
                            <th class="text-center">产品分类</th>
                            <th class="text-center">价格</th>
                            <th class="text-center">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="is_show btn btn-white btn-xs dropdown-toggle" style="font-weight: bold;background-color: #f5f5f6;border: solid 0;font-size: 16px;"
                                            aria-expanded="false">产品状态
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu search-item" data-name="is_show">
                                        <li data-value="">
                                            <a class="save_mark" href="javascript:void(0);"  >
                                                全部
                                            </a>
                                        </li>
                                        <li data-value="1">
                                            <a class="save_mark" href="javascript:void(0);"  >
                                                上架
                                            </a>
                                        </li>
                                        <li data-value="0">
                                            <a class="save_mark" href="javascript:void(0);">
                                                下架
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th class="text-center">热卖单品</th>
                            <th class="text-center">促销单品</th>
                            <th class="text-center">精品推荐</th>
                            <th class="text-center">首发新品</th>
                            <th class="text-center">库存</th>
                            <th class="text-center">排序</th>
<!--                            <th class="text-center">点赞</th>-->
                            <th class="text-center">收藏</th>
                            <th class="text-center">内容</th>
                            <th class="text-center">秒杀</th>
                            <th class="text-center">砍价</th>
                            <th class="text-center" width="5%">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td class="text-center">
                                <?php echo $vo['id']; ?>
                            </td>
                            <td class="text-center">
                                <img src="<?php echo $vo['image']; ?>" alt="<?php echo $vo['store_name']; ?>" class="open_image" data-image="<?php echo $vo['image']; ?>" style="width: 50px;height: 50px;cursor: pointer;">
                            </td>
                            <td class="text-center">
                                <?php echo $vo['store_name']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['cate_name']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['price']; ?>
                            </td>
                            <td class="text-center">
                                <i class="fa <?php if($vo['is_show'] == '1'): ?>fa-check text-navy<?php else: ?>fa-close text-danger<?php endif; ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="fa <?php if($vo['is_hot'] == '1'): ?>fa-check text-navy<?php else: ?>fa-close text-danger<?php endif; ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="fa <?php if($vo['is_benefit'] == '1'): ?>fa-check text-navy<?php else: ?>fa-close text-danger<?php endif; ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="fa <?php if($vo['is_best'] == '1'): ?>fa-check text-navy<?php else: ?>fa-close text-danger<?php endif; ?>"></i>
                            </td>
                            <td class="text-center">
                                <i class="fa <?php if($vo['is_new'] == '1'): ?>fa-check text-navy<?php else: ?>fa-close text-danger<?php endif; ?>"></i>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['stock']; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $vo['sort']; ?>
                            </td>
<!--                            <td class="text-center">-->
<!--                                <span class="btn btn-xs btn-white" <?php if($vo['collect'] > 0): ?>onclick="$eb.createModalFrame('点赞','<?php echo Url('collect',array('id'=>$vo['id'])); ?>')"<?php endif; ?> style="cursor: pointer">-->
<!--                                    <i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo $vo['collect']; ?>-->
<!--                                </span>-->
<!--                            </td>-->
                            <td class="text-center">
                                <span class="btn btn-xs btn-white" <?php if($vo['like'] > 0): ?>onclick="$eb.createModalFrame('收藏','<?php echo Url('like',array('id'=>$vo['id'])); ?>')"<?php endif; ?>  style="cursor: pointer">
                                    <i class="fa fa-heart"></i>&nbsp;&nbsp;<?php echo $vo['like']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-primary" onclick="$eb.createModalFrame(this.innerText,'<?php echo Url('edit_content',array('id'=>$vo['id'])); ?>')"><i class="fa fa-pencil"></i> 编辑内容</button>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-success btn-outline" onclick="$eb.createModalFrame(this.innerText,'<?php echo Url('seckill',array('id'=>$vo['id'])); ?>')"><i class="fa fa-forumbee"></i> 开启秒杀</button>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-success btn-outline" onclick="$eb.createModalFrame(this.innerText,'<?php echo Url('bargain',array('id'=>$vo['id'])); ?>')"><i class="fa fa-forumbee"></i> 开启砍价</button>
                            </td>
                            <td class="text-center">
                                <div class="input-group-btn js-group-btn" style="min-width: 106px;">
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-warning btn-xs dropdown-toggle"
                                                aria-expanded="false">操作
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('<?php echo $vo['store_name']; ?>-属性','<?php echo Url('attr',array('id'=>$vo['id'])); ?>')">
                                                    <i class="fa fa-shekel"></i> 属性
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('<?php echo $vo['store_name']; ?>-编辑','<?php echo Url('edit',array('id'=>$vo['id'])); ?>')">
                                                    <i class="fa fa-paste"></i> 编辑
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="delstor" data-url="<?php echo Url('delete',array('id'=>$vo['id'])); ?>">
                                                    <i class="fa fa-trash"></i> 删除
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo Url('store.storeProductReply/index',array('product_id'=>$vo['id'])); ?>">
                                                    <i class="fa fa-warning"></i> 评论查看
                                                </a>
                                            </li>
                                        </ul>
                                </div>
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
    $('.search-item>li').on('click', function () {
        var that = $(this), value = that.data('value'), p = that.parent(), name = p.data('name'), form = $('#form');
        form.find('input[name="' + name + '"]').val(value);
        $('input[name=export]').val(0);
        form.submit();
    });
    $('.search-item>.btn').on('click',function(){
        var that = $(this),value = that.data('value'),p = that.parent(),name = p.data('name'),form = p.parents();
        form.find('input[name="'+name+'"]').val(value);
        $('input[name=export]').val(0);
        form.submit();
    });
    $('.search-item-value').each(function(){
        var that = $(this),name = that.attr('name'), value = that.val(),dom = $('.search-item[data-name="'+name+'"] .btn[data-value="'+value+'"]');
        dom.eq(0).removeClass('btn-outline btn-link').addClass('btn-primary btn-sm')
            .siblings().addClass('btn-outline btn-link').removeClass('btn-primary btn-sm')
    });
    $('.search-is>.btn').on('click',function(){
        var that = $(this),value = that.data('value'),p = that.parent(),name = p.data('name'),form = p.parents();
        var valueAdmin = form.find('input[name="'+name+'"]').val();
        if(valueAdmin) value = '';
        else value = 1;
        form.find('input[name="'+name+'"]').val(value);
        $('input[name=export]').val(0);
        form.submit();
    });
    $('.js-group-btn').on('click',function(){
        $('.js-group-btn').css({zIndex:1});
        $(this).css({zIndex:2});
    });
    $('.search-is-value').each(function(){
        var that = $(this),name = that.attr('name'), value = that.val(),dom = $('.search-is[data-name="'+name+'"] .btn[data-value="'+value+'"]');
        dom.eq(0).removeClass('btn-outline btn-link').addClass('btn-primary btn-sm')
            .siblings().addClass('btn-outline btn-link').removeClass('btn-primary btn-sm')
    });
    $('.delstor').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                console.log(res);
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    });
    var dateInput =$('.datepicker');
    dateInput.daterangepicker({
        autoUpdateInput: false,
        "opens": "center",
        "drops": "down",
        "ranges": {
            '今天': [moment(), moment().add(1, 'days')],
            '昨天': [moment().subtract(1, 'days'), moment()],
            '上周': [moment().subtract(6, 'days'), moment()],
            '前30天': [moment().subtract(29, 'days'), moment()],
            '本月': [moment().startOf('month'), moment().endOf('month')],
            '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "locale" : {
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            format : 'YYYY/MM/DD',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                '七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
    });

    dateInput.on('cancel.daterangepicker', function(ev, picker) {
        //$("input[name=limit_time]").val('');
    });
    dateInput.on('apply.daterangepicker', function(ev, picker) {
        $("input[name=data]").val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        $('input[name=export]').val(0);
        $('form').submit();
    });
    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>


</div>
</body>
</html>
