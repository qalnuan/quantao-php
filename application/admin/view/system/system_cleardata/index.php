{include file="public/frame_head"}
<div class="row" style="width: 100%;margin-top: 50px; text-align: center;">

    <div >
        <h2>1用户(注：请先清除用户相关数据和订单数据)</h2>
        <button type="button" class="btn btn-w-m btn-primary" data-url="{:Url('system.system_cleardata/UserRelevant')}">清除用户相关数据</button>
        <button type="button" class="btn btn-w-m btn-primary" data-url="{:Url('system.system_cleardata/orderdata')}">清除订单数据</button>
        <button type="button" class="btn btn-w-m btn-primary" data-url="{:Url('system.system_cleardata/kefudata')}">清除客服数据</button>
        <br>
        <div style="padding-top: 18px;">
            <button type="button" class="btn btn-w-m btn-danger"  data-url="{:Url('system.system_cleardata/userdate')}">清除用户数据</button>
        </div>

    </div>
    <hr style="border-bottom: 1px solid #FF5722;padding-bottom: 14px; width:86% ;">
    <br>
   <div>
       <h2>2微信</h2>
       <button type="button" class="btn btn-w-m btn-primary" data-url="{:Url('system.system_cleardata/wechatdata')}">清除微信相关数据</button>
       <button type="button" class="btn btn-w-m btn-primary" data-url="{:Url('system.system_cleardata/articledata')}">清除文字分类数据</button>
       <br>
       <div style="padding-top: 18px;">
           <button type="button" class="btn btn-w-m btn-danger" data-url="{:Url('system.system_cleardata/wechatuserdata')}">清除微信用户数据</button>
       </div>

   </div>
    <hr style="border-bottom: 1px solid #FF5722;padding-bottom: 14px;  width:86% ;">
    <br>
    <div >
        <h2>3产品</h2>
        <button type="button" class="btn btn-w-m  btn-primary" data-url="{:Url('system.system_cleardata/storedata')}">清除产品所有数据</button>

        <div style="padding-top: 18px;">
            <button type="button" class="btn btn-w-m btn-danger" data-url="{:Url('system.system_cleardata/categorydata')}">清除产品分类数据</button>

            <button type="button" class="btn btn-w-m btn-danger" data-url="{:Url('system.system_cleardata/uploaddata')}">清除文件上传数据</button>
        </div>
    </div>
    <hr style="border-bottom: 1px solid #FF5722;padding-bottom: 14px;  width:86% ;">
</div>
<script>
    $('button').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                }else
                    return Promise.reject(res.data.msg || '操作失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        },{'title':'您确定要进行此操作吗？','text':'操作后数据相应的数据将会被删除,请谨慎操作！','confirm':'是的，我要操作'})
    })
</script>
{include file="public/inner_footer"}