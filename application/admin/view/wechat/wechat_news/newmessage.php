{extend name="public/container"}
{block name="head_top"}
<link href="{__ADMIN_PATH}plug/umeditor/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
<link href="{__ADMIN_PATH}module/wechat/news/css/style.css" type="text/css" rel="stylesheet">
<link href="{__FRAME_PATH}css/plugins/chosen/chosen.css" rel="stylesheet">
<script type="text/javascript" src="{__ADMIN_PATH}plug/umeditor/third-party/jquery.min.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}plug/umeditor/third-party/template.min.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/umeditor/umeditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/umeditor/umeditor.min.js"></script>
<script src="{__ADMIN_PATH}frame/js/ajaxfileupload.js"></script>
<script src="{__ADMIN_PATH}plug/validate/jquery.validate.js"></script>
<script src="{__FRAME_PATH}js/plugins/chosen/chosen.jquery.js"></script>
{/block}
{block name="content"}
<div class="row">
    <div class="col-sm-12">
        <div style="border-bottom: 1px solid #e7eaec;color: inherit;margin-bottom: 0;margin-top: 10px;padding: 0px 15px 0px 15px; min-height: 48px;font-size: 18px;">
            <h5 style="display: inline-block;font-size: 16px;margin: 16px 0px;font-weight: 500;text-indent: 8px;text-overflow: ellipsis;border-left: 2px solid #1ab394;">新建文章</h5>
        </div>
    </div>
    <div class="col-sm-12">
                <div class="panel-body">
                    <div class="col-sm-2 panel panel-default news-left">
                        <div class="panel-heading">文章列表</div>
                        <div class="panel-body news-box type-all" >
                            {if condition="$news['image_input']"}
                            <div class="news-item transition active news-image" style="margin-bottom: 20px;background-image:url({$news['image_input']})"></div>
                            {else/}
                            <div class="news-item transition active news-image" style="margin-bottom: 20px;background-image:url('/public/system/module/wechat/news/images/image.png')"></div>
                            {/if}
                            <input type="hidden" name="new_id" value="{$news.id}" class="new-id">
                        </div>
                    </div>
                    <div class="col-sm-10 panel panel-default news-right" >
                        <div class="panel-heading">文章内容编辑</div>
                    <form class="form-horizontal" id="signupForm">
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="form-control" style="height:auto">
                                    <label style="color:#ccc">图文封面大图片设置</label>
                                    <div class="row nowrap">
                                        <div class="col-xs-3" style="width:160px">
                                            {if condition="$news['image_input']"}
                                            <div class="upload-image-box transition image_img" style="background-image:url({$news['image_input']})">
                                                <input value="" type="hidden" name="local_url">
                                            </div>
                                            {else/}
                                            <div class="upload-image-box transition image_img" style="background-image:url('/public/system/module/wechat/news/images/image.png')">
                                                <input value="" type="hidden" name="local_url">
                                            </div>
                                            {/if}
                                        </div>
                                        <div class="col-xs-6">
                                            <input type="file" class="upload" name="image" style="display: none;" id="image" />
                                            <br>
                                            <a class="btn btn-sm add_image upload_span">上传图片</a>
                                            <br>
                                            <br>
                                        </div>
                                    </div>
                                    <input type="hidden" name="image" id="image_input" value="{$news['image_input']}"/>
                                    <p class="help-block" style="margin-top:10px;color:#ccc">封面大图片建议尺寸：900像素 * 500像素</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label style="color:#aaa">文章内容</label>
                                <script type="text/plain" id="myEditor" style="width:100%;">{$news['content']}</script>
                            </div>
                            <input type="hidden" name="id" value="{$news.id}">
                        </div>
                        <div class="form-actions">
                            <div class="row">
                            <div class="col-md-offset-4 col-md-9">
                                <button type="button" class="btn btn-w-m btn-info save_news">保存</button>
                            </div>
                            </div>
                        </div>
                    </form>
                </div>
                                    </div>
            </div>
        </div>
    </div>
<script>
    var editor = document.getElementById('myEditor');
    editor.style.height = '300px';
    //实例化编辑器
    var um = UM.getEditor('myEditor',{
    });
    /**
     * 获取编辑器内的内容
     * */
    function getContent() {
        return (UM.getEditor('myEditor').getContent());
    }
    function hasContent() {
        return (UM.getEditor('myEditor').hasContents());
    }
    $('.upload_span').on('click',function (e) {
        $('.upload').trigger('click');
    })
    /**
     * 触发图片上传按钮
     * */
    $('.upload').on('change',function (e) {
        var image = $('#image_input').val();
        url = "{:Url('Common/rmPublicResource')}";
        $.ajax({
            url:url,
            data:'url='+image,
            type:'get',
            success:function (re) {
                if(re.code == 200){
                    $eb.message('success',re.msg);
                }else{
                    $eb.message('error',re.msg);
                }
            }
        })


        ajaxFileUpload(this);

    })

    /**

     * 图片上传

     * */

    function ajaxFileUpload(is) {

        $.ajaxFileUpload({

            url: "{:url('upload_image')}",

            data:{file: 'image'},

            type: 'post',

            secureuri: false, //一般设置为false

            fileElementId: 'image', // 上传文件的id、name属性名

            dataType: 'json', //返回值类型，一般设置为json、application/json

            success: function(data, status, e){

                if(data.code == 200){

                    $(".image_img").css('background-image',"url("+data.data.url+")");

                    $(".active").css('background-image',"url("+data.data.url+")");

                    $('#image_input').val(data.data.url);

                    $eb.message('success',data.msg);

                }else{

                    $eb.message('error',data.msg);

                }

                $('.upload').on('change',function(){



                    var image = $('#image_input').val();

                    url = "{:Url('Common/rmPublicResource')}";
                    $.ajax({
                        url:url,
                        data:'url='+image,
                        type:'get',
                        success:function (re) {
                            if(re.code == 200){
                                $eb.message('success',re.msg);
                            }else{
                                $eb.message('error',re.msg);
                            }
                        }
                    })

                    ajaxFileUpload(this);
                })

            },

            error: function(data, status, e){

                $('.upload').on('change',function(){

                    var image = $('#image_input').val();

                    url = "{:Url('Common/rmPublicResource')}";
                    $.ajax({
                        url:url,
                        data:'url='+image,
                        type:'get',
                        success:function (re) {
                            if(re.code == 200){
                                $eb.message('success',re.msg);
                            }else{
                                $eb.message('error',re.msg);
                            }
                        }
                    })

                    ajaxFileUpload(this);
                })

            }

        });

    }
    /**
     * 提交图文
     * */
    $('.save_news').on('click',function(){
        var list = {};
        list.image_input = $('#image_input').val();/* 图片 */
        list.content = getContent();/* 内容 */
        var Expression = /http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
        var objExp=new RegExp(Expression);
        if(list.content == ''){
            $eb.message('error','请输入内容');
            return false;
        }
        var id = $('.type-all>.active>.new-id').val();
        if(id != ''){
            list.id = id;
        }
        var data = {};
        $.ajax({
            url:"{:Url('addmessage')}",
            data:list,
            type:'post',
            dataType:'json',
            success:function(re){
                if(re.code == 200){
                    data[re.data] = list;
                    $('.type-all>.active>.new-id').val(re.data);
                    $eb.message('success',re.msg);
                    setTimeout(function (e) {
                        window.history.go(-1);
                    },600)
                }else{
                    $eb.message('error',re.msg);
                }
            }
        })
    });
    $('.article-add ').on('click',function (e) {
        var num_div = $('.type-all').children('div').length;
        if(num_div > 7){
            $eb.message('error','一组图文消息最多可以添加8个');
            return false;
        }
        var url = "/public/system/module/wechat/news/images/image.png";
        html = '';
        html += '<div class="news-item transition active news-image" style=" margin-bottom: 20px;background-image:url('+url+')">'
        html += '<input type="hidden" name="new_id" value="" class="new-id">';
        html += '<span class="news-title del-news">x</span>';
        html += '</div>';
        $(this).siblings().removeClass("active");
        $(this).before(html);
    })
    $(document).on("click",".del-news",function(){
        $(this).parent().remove();
    })
    $(document).ready(function() {
        var config = {
            ".chosen-select": {},
            ".chosen-select-deselect": {allow_single_deselect: true},
            ".chosen-select-no-single": {disable_search_threshold: 10},
            ".chosen-select-no-results": {no_results_text: "沒有找到你要搜索的分类"},
            ".chosen-select-width": {width: "95%"}
        };
        for (var selector in config) {
            $(selector).chosen(config[selector])
        }
    })
</script>
{/block}