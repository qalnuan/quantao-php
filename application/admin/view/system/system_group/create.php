<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/head"}
    <title>{$title}</title>
</head>
<body>
<div id="form-add" class="mp-form" v-cloak="">
    <i-Form :model="formData" :label-width="80" >
        <Form-Item label="数据组名称">
            <i-input v-model="formData.name" placeholder="请输入数据组名称"></i-input>
        </Form-Item>
        <Form-Item label="数据字段">
            <i-input v-model="formData.config_name" placeholder="请输入数据字段例如：site_url"></i-input>
        </Form-Item>
        <Form-Item label="数据组简介">
            <i-input v-model="formData.info" placeholder="请输入数据组简介"></i-input>
        </Form-Item>
        <Form-Item v-for="(item, index) in formData.typelist" :label="'字段' + (index+1)">
            <row ref="typelist">
                <i-col span="5">
                    <Form-Item>
                        <i-input :placeholder="item.name.placeholder" v-model="item.name.value"></i-input>
                    </Form-Item>
                </i-col>
                <i-col span="5">
                    <Form-Item>
                        <i-input :placeholder="item.title.placeholder" v-model="item.title.value"></i-input>
                    </Form-Item>
                </i-col>
                <i-col span="5">
                    <Form-Item>
                        <i-select :placeholder="item.type.placeholder" v-model="item.type.value">
                            <i-option value="input">文本框</i-option>
                            <i-option value="textarea">多行文本框</i-option>
                            <i-option value="radio">单选框</i-option>
                            <i-option value="checkbox">多选框</i-option>
                            <i-option value="upload">单文件上传</i-option>
                            <i-option value="uploads">多文件上传</i-option>
                        </i-select>
                    </Form-Item>
                </i-col>
                <i-col span="7">
                    <Form-Item>
                        <i-input :placeholder="item.param.placeholder" v-model="item.param.value"></i-input>
                    </Form-Item>
                </i-col>
                <i-col span="2" style="display:inline-block; text-align:right;">
                    <i-button type="primary" icon="close-round" @click="removeType(index)"></i-button>
                </i-col>
            </row>
        </Form-Item>
        <Form-Item><i-button type="primary" @click="addType">添加字段</i-button></Form-Item>
        <Form-Item :class="'add-submit-item'">
            <i-Button :type="'primary'" :html-type="'submit'" :size="'large'" :long="true" @click.prevent="submit">提交</i-Button>
        </Form-Item>
    </i-Form>
</div>
<script>
    $eb = parent._mpApi;
    mpFrame.start(function(Vue){
        new Vue({
            el:"#form-add",
            data:{
                formData:{
                    name: '',
                    config_name: '',
                    typelist: [],
                    info:''
                }
            },
            methods:{
                addType: function(){
                    this.formData.typelist.push({
                        name: {
                            placeholder: "字段名称",
                            value: ''
                        },
                        title: {
                            placeholder: "字段配置名",
                            value: ''
                        },
                        type: {
                            placeholder: "字段类型",
                            value: ''
                        },
                        param: {
                            placeholder: "字段参数",
                            value: ''
                        }
                    })
                },
                removeType: function(index){
                    this.formData.typelist.splice(index,1);
                },
                submit: function(){
                    $eb.axios.post("{$save}",this.formData).then((res)=>{
                        if(res.status && res.data.code == 200)
                            return Promise.resolve(res.data);
                        else
                            return Promise.reject(res.data.msg || '添加失败,请稍候再试!');
                    }).then((res)=>{
                        $eb.message('success',res.msg || '操作成功!');
                        $eb.closeModalFrame(window.name);
                    }).catch((err)=>{
                        this.loading=false;
                        $eb.message('error',err);
                    });
                }
            }
        });
    });
</script>
</body>
