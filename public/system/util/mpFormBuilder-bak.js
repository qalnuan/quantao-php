(function(global,factory){
    define && define.amd && define(factory());
})(this,function(){

    var FormBuilderName = 'form-builder';
    var props = {
        'label-width':80
    };

    var formBuilderInstall = function(Vue,rules){
        rules = formBuilder.handleRules(rules);
        Vue.component(FormBuilderName,{
            data: function(){
                return {
                    formValidate:formBuilder.metaData(rules)
                }
            },
            render:function(h){
                window.__vm = this;
                var fb = new formBuilder(this,h,rules);
                window._fb = fb;
                return fb.makeForm()
            },
            watch:{

            },
            created:function(){
                // formBuilder.createWatch(this,rules);
            }
        })
    };

    var formBuilder = function(vm,h,rules){
        this.vm = vm;
        this.h = h;
        this.rules = rules;
    };

    formBuilder.filterFailRule = rules=>{
        return rules.filter((rule)=>!!rule.field);
    };

    formBuilder.fields = rules=>{
        let field = [];
        rules.map((rule)=>{
            field.push(rule.field);
        });
        return field;
    };
    formBuilder.metaData =rules=>{
        let metaData = {};
        rules.map((rule)=>{
            metaData[rule.field] = rule.value;
        });
        return metaData;
    };
    formBuilder.metaRef = field=>{
        return `mp_${field}`;
    };
    formBuilder.metaWatch = function(vm,field){

        return vm.$watch(`formValidate.${field}`,(n)=>{
            vm.$refs[this.metaRef(field)].currentValue = n;
        });
    };
    formBuilder.createWatch = function(vm,rules){
        this.fields(rules).map((field)=>{
            this.metaWatch(vm,field);
        });
    };

    formBuilder.handleRules = function(rules){
        return this.filterFailRule(rules).map(rule=>{
            rule.props || (rule.props = {});
            return rule;
        });
    };

    formBuilder.prototype = {

        onInput(field,value){
            console.log(value);
            this.vm.formValidate[field] = value;
            this.vm.$emit('input', value);
        },

        getFieldValue(field){
            return this.vm.formValidate[field];
        },

        makeForm(){
            return this.h('i-form',{
                props:props
            },this.parse());
        },
        makeFormItem(field,label,VNodeFn){
            return this.h('form-Item',{
                props:{
                    'props':field,
                    'label':label || ''
                }
            },VNodeFn())
        },
        makeInput(rule){
            _vm = this.vm;
            rule.props.value = this.getFieldValue(rule.field);
            return this.h('i-input',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            })
        },
        makeInputNumber(rule){
            rule.props.value = parseFloat(this.getFieldValue(rule.field)) || 1;
            return this.h('Input-Number',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            })
        },
        makeRadioGroup(rule,VNodeFn = function(){}){
            rule.props.value = this.getFieldValue(rule.field);
            return this.h('Radio-Group',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            },VNodeFn());
        },
        makeRadio(rule){
            return this.makeRadioGroup(rule,()=>{
                return rule.options.map((option)=>{
                    return this.h('Radio',{
                        props:option.props
                    })
                });
            });
        },
        makeCheckBoxGroup(rule,VNodeFn = function(){}){
            rule.props.value = this.getFieldValue(rule.field);
            return this.h('Checkbox-Group',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            },VNodeFn());
        },
        makeCheckBox(rule){
            return this.makeCheckBoxGroup(rule,()=>{
                return rule.options.map((checkbox)=>{
                    return this.h('Checkbox',{
                        props:checkbox.props
                    })
                });
            })
        },
        markSelectOptions(options){
            return options.map((option)=>{
                return this.h('i-option',{
                    props:option.props
                })
            });
        },
        markSelect(rule){
            rule.props.value = this.getFieldValue(rule.field);
            return this.h('i-select',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            },this.markSelectOptions(rule.options));
        },

        makeDatePicker(rule){
            rule.props.value = this.getFieldValue(rule.field);
            rule.props.type || (rule.props.type = 'date');
            return this.h('Date-Picker',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            });
        },
        makeTimePicker(rule){
            rule.props.value = this.getFieldValue(rule.field);
            rule.props.type || (rule.props.type = 'time');
            return this.h('Time-Picker',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            })
        },
        makeColorPicker(rule){
            rule.props.value = this.getFieldValue(rule.field);
            return this.h('Color-Picker',{
                props:rule.props,
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            })
        },
        makeUpload(rule){
            rule.props.value = this.getFieldValue(rule.field);
            return this.h('Upload',{
                props:rule.props,
                attrs:{
                    style:'display: inline-block;width:58px'
                },
                on:{
                    input:(value)=>this.onInput(rule.field,value)
                },
                ref:formBuilder.metaRef(rule.field)
            },[
                this.h('div',{style:{width:'58px',height:'58px',lineHeight:'58px'}},[this.h('Icon',{
                    props:{
                        type:"camera",
                        size:20
                    }
                })])
            ])
        },
        parse(){
            return this.rules.filter((rule)=>!!rule.field).map((rule)=>this.makeFormItem(rule.field,rule.title,()=>this[rule.type.toLowerCase()].call(this,rule)));
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
            return [this.makeUpload(rule)];
        }
    };
    return {
        install:formBuilderInstall
    }

});