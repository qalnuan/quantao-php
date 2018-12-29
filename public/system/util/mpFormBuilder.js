(function(global,factory){
define && define.amd && define(['mpBuilder','axios'],factory);
})(this,function(r,axios){
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
    var FormBuilderName = 'form-builder';
    var api={};
    var opt={

    };
    var formBuilderInstall = function(Vue,_api,rules,_opt){
        api = _api;
        opt = _opt;
        var builder = new formBuilder(rules),render;
        Vue.component(FormBuilderName,{
            data: function(){
                return {
                    formValidate:builder.metaData(),
                    formStatus:{
                        loading:false,
                        form:'formValidate'
                    }
                }
            },
            render:function(h){
                window.__vm = this;
                render = builder.createRender(this,h);
                window._fb = render;
                return render.parse();
            },
            watch:{

            },
            methods:{
                getApi(){
                    var vm =this;
                    return {
                        set(field,value){
                            vm.$set(vm.formValidate[field],'value',value);
                        }
                    };
                }
            },
            mounted(){
                // render._bindWatch(this);
                render._mountedCall(this);
                Vue.prototype.$formBuilder = this.getApi();
            }
        });
    };

    var formBuilder = function(rules){
        this.original = rules;
        this.rules = this._handleRules();
        this.fields = this._getFields();
    };

    formBuilder.prototype = {
        //创建表单生成器
        createRender(vm,h){
            return new formRender(this.rules,vm,h);
        },
        //获得表单字段
        _getFields(){
            let field = [];
            this.rules.map((rule)=>{
                field.push(rule.field);
            });
            return field;
        },
        field(){
            return this.fields;
        },
        //获得表单键值对
        metaData(){
            let metaData = {};
            this.rules.map((rule)=>{
                metaData[rule.field] = {
                    value:rule.value,
                    type:rule.type,
                    select:rule.select
                };
            });
            return metaData;
        },
        //初始化参数
        _handleRules(){
            return this.original.filter((rule)=>!!rule.field).map(rule=>{
                rule.props || (rule.props = {});
                rule.type = rule.type.toLowerCase();
                return rule;
            });
        }

    };

    var formRender = function (rules,vm,h) {
        this.vm = vm;
        this.h = h;
        this.rules = rules;
        this.r = new r(h);
        this.t = this.r.$t();
        this._mountedCallList = [];
    };

    formRender.prototype = {
        _mounted(call){
            this._mountedCallList.push(call);
        },
        _mountedCall(vm){
            this._mountedCallList.map((call)=>{
                call(vm);
            })
        },
        //绑定表单监听事件
        _bindWatch(vm){
            this.rules.map((rule)=> {
                this._bindMetaWatch(vm,rule.field);
            });
        },
        //绑定字段监听事件
        _bindMetaWatch(vm,field){
            return this.vm.$watch(`formValidate.${field}`, (n)=> {
                this.vm.$refs[this.metaRef(field)].currentValue && (this.vm.$refs[this.metaRef(field)].currentValue = n);
            });
        },
        _bindInput(field,value){
            this.setFieldValue(field,value);
            this.vm.$emit('input', value);
        },
        getFieldValue(field){
            return this.vm.formValidate[field].value;
        },
        setFieldValue(field,value){
            this.vm.formValidate[field].value = value;
        },
        //获得表单的ref名称
        metaRef(field){
            return `mp_${field}`;
        },
        getRef(field){
            return this.vm.$refs[this.metaRef(field)];
        },
        getFormRef(){
            return this.vm.$refs[this.vm.formStatus.form];
        },
        dateToString(date){
            return date == '' || date == null ? '' : (isNaN(Date.parse(date)) ? Date.parse(new Date) : Date.parse(date))/1000;
        },
        getParseFormData(){
            var parseData = {},formData = this.vm.formValidate;
            for(let it of Object.keys(formData)){
                let item = formData[it],c;
                if(['datepicker','timepicker'].indexOf(item.type) != -1){
                    if(Object.prototype.toString.call(item.value) == '[object Array]'){
                        c = item.value.map((value)=>{return this.dateToString(value)});
                    }else{
                        c = parseData[it] = this.dateToString(item.value);
                    }
                    parseData[it] = c;

                } 
                else if(['checkbox','radio'].indexOf(item.type) != -1){
                    if(Object.prototype.toString.call(item.value) == '[object Array]'){
                        c = [];
                        item.value.map((value)=>{
                            item.select.map((v,k)=>{
                                v.label == value && (c.push(k));
                            });
                        });
                    }else{
                        item.select.map((v,k)=>{
                            v.label == item.value && (c = v.value);
                        });
                        // c = (item.select[item.value])
                    }
                    parseData[it] = c;
                }
                else
                    parseData[it] = item.value;
            }
            return parseData;
        },
        makeForm(VNodeFn){
            var t = new this.t();
            t.props({'label-width':125}).ref(this.vm.formStatus.form).attrs({method:'POST',action:opt.action}).nativeOn('submit',(e)=>{
                e.preventDefault();
                this.vm.formStatus.loading=true;
                var _this = this.getFormRef();
                var parseData = this.getParseFormData();
                axios.post(_this.$attrs['action'],parseData).then((res)=>{
                    if(res.status && res.data.code == 200)
                        return Promise.resolve(res.data);
                    else
                        return Promise.reject(res.data.msg || '添加失败,请稍候再试!');
                }).then((res)=>{
                    api.message('success',res.msg || '操作成功!');
                    api.closeModalFrame(window.name);
                }).catch((err)=>{
                    this.vm.formStatus.loading=false;
                    api.message('error',err);
                });
            });
            return this.r.form(t.get(),VNodeFn);
        },
        makeFormItem(field,label,VNodeFn){
            return this.r.formItem({
                props:{
                    'props':field,
                    'label':label || ''
                },
                attrs:{
                    id:'eb-field-'+field
                }
            },VNodeFn);
        },
        makeInput(rule){
            _vm = this.vm;
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            t.props(rule.props).props('value',this.getFieldValue(field))
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.input(t.get());
        },
        makeInputNumber(rule){
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            t.props(rule.props).props('value',this.getFieldValue(field))
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.inputNumber(t.get());
        },
        makeRadio(rule){
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            t.props(rule.props).props('value',this.getFieldValue(field))
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.radioGroup(t.get(),()=>rule.options.map((option)=>this.r.radio({props:option.props})));
        },
        makeCheckBox(rule){
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            t.props(rule.props).props('value',this.getFieldValue(field))
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.checkboxGroup(t.get(),()=>rule.options.map((option)=>this.r.checkbox({props:option.props})));
        },
        markSelect(rule){
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            t.props(rule.props)
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.select(t.get(),this.markSelectOptions(rule.options));
        },
        markSelectOptions(options){
            return options.map((option)=>this.r.option({props:option.props}));
        },
        stringToDate(field){
            var val = this.getFieldValue(field);
            if(Object.prototype.toString.call(val) == '[object Array]'){
                val.map((v,k)=>{
                    Object.prototype.toString.call(v) == '[object Date]' ||
                    (!v  ? (val[k] = '') :(val[k] = new Date(v*1000)));
                })
            }else{
                Object.prototype.toString.call(val) == '[object Date]' ||
                (!v  ? (val = '') : (val = new Date(v*1000)));
            }
        },
        stringToTime(field){
            var val = this.getFieldValue(field);
            if(Object.prototype.toString.call(val) == '[object Array]'){
                val.map((v,k)=>{
                    Object.prototype.toString.call(v) == '[object Date]' ||
                    (!v  ? (val[k] = '') :(val[k] = new Date(v*1000)));
                })
            }else{
                Object.prototype.toString.call(val) == '[object Date]' ||
                (!v  ? (val = '') : (val = new Date(v*1000)));
            }
        },
        today(){
            var date = new Date();
                var y = date.getFullYear();
                var m = date.getMonth() + 1;
                m = m < 10 ? '0' + m : m;
                var d = date.getDate();
                d = d < 10 ? ('0' + d) : d;
                return y + '-' + m + '-' + d;
        },
        makeDatePicker(rule){
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            rule.props.type || (rule.props.type = 'date');
            this.stringToDate(field);
            t.props(rule.props).props('value',this.getFieldValue(field))
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.datePicker(t.get());
        },
        makeTimePicker(rule){
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            rule.props.type || (rule.props.type = 'time');
            this.stringToTime(field);
            t.props(rule.props).props('value',this.getFieldValue(field))
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.timePicker(t.get());
        },
        makeColorPicker(rule){
            var t = new this.t,field=rule.field,ref = this.metaRef(field);
            t.props(rule.props).props('value',this.getFieldValue(field))
                .ref(ref).on('input',(value)=>this._bindInput(field,value));
            return this.r.colorPicker(t.get());
        },
        makeUpload(rule){
            var t = new this.t,field=rule.field,ref=this.metaRef(field);
            t.props(rule.props).props('value',this.getFieldValue(field));
            //上传文件之前的钩子，参数为上传的文件，若返回 false 或者 Promise 则停止上传
            t.props('before-upload',()=>{
                if(rule.props['max-length'] && rule.props['max-length'] <= this.getFieldValue(field).length){
                    api.message('最多可上传'+rule.props['max-length']+'张图片');
                    return false;
                }
            });
            //文件上传时的钩子，返回字段为 event, file, fileList
            t.props('on-progress',(event, file, fileList)=>{});
            //文件上传成功时的钩子，返回字段为 response, file, fileList
            t.props('on-success',(response, file, fileList)=>{
                if(response.code == 200){
                    api.message('success',file.name+'图片上传成功');
                    this.getFieldValue(field).push(response.data.url);
                }else{
                    api.message('error',file.name+'图片上传失败,'+response.msg);
                }
            });
            //点击已上传的文件链接时的钩子，返回字段为 file， 可以通过 file.response 拿到服务端返回数据
            t.props('on-preview',(file)=>{});
            //文件列表移除文件时的钩子，返回字段为 file, fileList
            t.props('on-remove',(file)=>{});
            //文件格式验证失败时的钩子，返回字段为 file, fileList
            t.props('on-format-error',(file, fileList)=>{
                api.message('error',file.name+'格式不正确，请上传 jpg 或 png 格式的图片。');
            });
            //文件超出指定大小限制时的钩子，返回字段为 file, fileList
            t.props('on-exceeded-size',(file, fileList)=>{
                api.message('error',file.name+'太大，不能超过 '+rule.props['max-size']+'kb');
            });
            //文件上传失败时的钩子，返回字段为 error, file, fileList
            t.props('on-error',(error, file, fileList)=>{
                api.message('error',file.name+'上传失败，'+error);
            });
            t.class('mp-upload',true);
            t.ref(ref);
            var data = t.get();
            return (()=>{
                var render = [];
                if(data.props['mp-show-upload-list'] == true)
                    render.push((()=>{
                        return data.props.value.map((img)=>{
                            return this.r.make('div',{class:{'demo-upload-list':true}},[
                                this.r.make('img',{attrs:{src:img}}),
                                this.r.make('div',{class:{'demo-upload-list-cover':true}},[
                                    this.r.icon({props:{type:'ios-eye-outline'},nativeOn:{'click':()=>{
                                        api.layer.open({
                                            type: 1,
                                            title: false,
                                            closeBtn: 0,
                                            shadeClose: true,
                                            content: `<img src="${img}" style="display: block;width: 100%;" />`
                                        });
                                    }}}),
                                    this.r.icon({props:{type:'ios-trash-outline'},nativeOn:{'click':()=>{
                                        data.props.value.splice(data.props.value.indexOf(img),1);
                                    }}}),

                                ])
                            ]);
                        });
                    })());
                if(!rule.props['max-length'] || rule.props['max-length'] > this.getFieldValue(rule.field).length)
                    render.push((()=>{
                        return this.r.upload(data,()=>{
                            return [this.r.make('div',{class:{'mp-upload-btn':true}},[this.r.icon({props:{type:"camera", size:20}})])]
                        })
                    })());
                return render;
            })();
        },
        makeSubmit(){
            var t = new this.t;
            t.props({type:'primary','html-type':'submit',long:true,size:"large",loading:this.vm.formStatus.loading}).on('click',()=>{
                this.getFormRef().validate((valid)=>{
                    console.log(valid);
                })
            });
            return this.r.formItem({class:{'add-submit-item':true}},[this.r.button(t.get(),()=>{
                return [this.r.span('提交')];
            })]);
        },
        parse(){
            return this.makeForm(()=>{
                var form = this.rules.filter((rule)=>!!rule.field).map((rule)=>this.makeFormItem(rule.field,rule.title,()=>this[rule.type].call(this,rule)));
                form.push(this.makeSubmit());
                return form;
            });
        },
        text(rule){
            return [this.makeInput(rule)];
        },
        radio(rule){
            return [this.makeRadio(rule)];
        },
        checkbox(rule){
            return [this.makeCheckBox(rule)];
        },
        select(rule){
            return [this.markSelect(rule)];
        },
        inputnumber(rule){
            return [this.makeInputNumber(rule)];
        },
        datepicker(rule){
            return [this.makeDatePicker(rule)];
        },
        timepicker(rule){
            return [this.makeTimePicker(rule)];
        },
        colorpicker(rule){
            return [this.makeColorPicker(rule)];
        },
        upload(rule){
            return this.makeUpload(rule);
        }
    };
    return {
        install:formBuilderInstall
    }

});