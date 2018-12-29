{extend name="public/container"}
{block name="content"}
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline">
                            <select name="status" aria-controls="editable" class="form-control input-sm">
                                <option value="" {eq name="where.status" value=""}selected="selected"{/eq}>状态</option>
                                <option value="1" {eq name="where.status" value="1"}selected="selected"{/eq}>正常</option>
                                <option value="0" {eq name="where.status" value="0"}selected="selected"{/eq}>锁定</option>
                            </select>
                            <select name="is_promoter" aria-controls="editable" class="form-control input-sm">
                                <option value="" {eq name="where.is_promoter" value=""}selected="selected"{/eq}>身份</option>
                                <option value="0" {eq name="where.is_promoter" value="0"}selected="selected"{/eq}>普通用户</option>
                                <option value="1" {eq name="where.is_promoter" value="1"}selected="selected"{/eq}>推广员</option>
                            </select>
                            <div class="input-group">
                                <input size="26" type="text" name="nickname" value="{$where.nickname}" placeholder="请输入姓名、编号" class="input-sm form-control"> <span class="input-group-btn">
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
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">{$vo.uid}</td>
                            <td class="text-center">{$vo.nickname}</td>
                            <td class="text-center">
                                <img src="{$vo.avatar}" alt="{$vo.nickname}" class="open_image" data-image="{$vo.avatar}" style="width: 50px;height: 50px;cursor: pointer;">
                            </td>
<!--                            <td class="text-center">{$vo.now_money}</td>-->
<!--                            <td class="text-center">{$vo.integral}</td>-->
                            <td class="text-center">{$vo.spread_uid_nickname}/{$vo.spread_uid}</td>
                            <td class="text-center">
                                <i class="fa {eq name='vo.is_promoter' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                            </td>
                            <td class="text-center">
                                <i class="fa {eq name='vo.status' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                            </td>
                            <td class="text-center">
                                {if condition="$vo['user_type'] eq 'routine'"}
                                小程序类型
                                {else/}
                                其他类型
                                {/if}
                            </td>
                            <td class="text-center">{$vo.add_time|date='Y-m-d H:i:s',###}</td>
                            <td class="text-center">
                                <button class="btn btn-info btn-xs" type="button"  onclick="$eb.createModalFrame('编辑','{:Url('edit',array('uid'=>$vo['uid']))}')"><i class="fa fa-paste"></i> 编辑</button>
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    $(".open_image").on('click',function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>
{/block}
