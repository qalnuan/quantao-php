(function(global,factory){
    define && define.amd && define(factory);
    // global.$r = factory();
})(this,function(){
    var r = function(h){
        this.h = h;
    };

    var t = function(){
        this.data = this._initData();
    };

    r.prototype = {
        form(data,VNodeFn){
            return this.make('i-form',data,VNodeFn);
        },
        formItem(data,VNodeFn){
            return this.make('form-Item',data,VNodeFn);
        },
        input(data,VNodeFn){
            return this.make('i-input',data,VNodeFn);
        },
        inputNumber(data,VNodeFn){
            return this.make('Input-Number',data,VNodeFn);
        },
        radioGroup(data,VNodeFn){
            return this.make('Radio-Group',data,VNodeFn);
        },
        radio(data,VNodeFn){
            return this.make('Radio',data,VNodeFn);
        },
        checkboxGroup(data,VNodeFn){
            return this.make('Checkbox-Group',data,VNodeFn);
        },
        checkbox(data,VNodeFn){
            return this.make('Checkbox',data,VNodeFn);
        },
        select(data,VNodeFn){
            return this.make('i-select',data,VNodeFn);
        },
        option(data,VNodeFn){
            return this.make('i-option',data,VNodeFn);
        },
        datePicker(data,VNodeFn){
            return this.make('Date-Picker',data,VNodeFn);
        },
        timePicker(data,VNodeFn){
            return this.make('Time-Picker',data,VNodeFn);
        },
        colorPicker(data,VNodeFn){
            return this.make('Color-Picker',data,VNodeFn);
        },
        upload(data,VNodeFn){
            return this.make('Upload',data,VNodeFn);
        },
        span(data,VNodeFn){
            if(typeof data == 'string') data = {domProps:{innerHTML:data}};
            return this.make('span',data,VNodeFn);
        },
        icon(data,VNodeFn){
            return this.make('Icon',data,VNodeFn);
        },
        button(data,VNodeFn){
            return this.make('i-button',data,VNodeFn);
        },
        make(nodeName,data,VNodeFn){
            return this.h(nodeName,data,this.getVNode(VNodeFn));
        },
        more(...args){
            var vNodeList = [];
            args.map((arg)=>{vNodeList.push(arg)});
            return vNodeList;
        },
        getVNode(VNode){
            return typeof VNode == 'function' ? VNode() : VNode;
        },
        $t(){
            return t;
        }

    };


    t.prototype = {
        _initData(){
            return {
                class:{},
                style:{},
                attrs:{},
                props:{},
                domProps:{},
                on:{},
                nativeOn:{},
                directives:[],
                scopedSlots:{},
                slot:undefined,
                key:undefined,
                ref:undefined
            };
        },
        class(opt,status){
            status !== undefined ? (this.data.class[opt] = status) : (this.data.class = opt);
            return this;
        },
        style(opt,status){
            status !== undefined ? (this.data.style[opt] = status) : (this.data.style = opt);
            return this;
        },
        attrs(opt,value){
            value !== undefined ? (this.data.attrs[opt] = value) : (this.data.attrs = opt);
            return this;
        },
        props(opt,value){
            value !== undefined ? (this.data.props[opt] = value) : (this.data.props = opt);
            return this;
        },
        domProps(opt,value){
            value !== undefined ? (this.data.domProps[opt] = value) : (this.data.domProps = opt);
            return this;
        },
        on(opt,call){
            call !== undefined ? (this.data.on[opt] = call) : (this.data.on = opt);
            return this;
        },
        nativeOn(opt,call){
            call !== undefined ? (this.data.nativeOn[opt] = call) : (this.data.nativeOn = opt);
            return this;
        },
        directives(opt){
            this.data.directives.push(opt);
            return this;
        },
        scopedSlots(opt,call){
            call !== undefined ? (this.data.scopedSlots[opt] = call) : (this.data.scopedSlots = opt);
            return this;
        },
        slot(value){
            this.data.slot = value;
            return this;
        },
        key(value){
            this.data.key = value;
            return this;
        },
        ref(value){
            this.data.ref = value;
            return this;
        },
        init(){
            this.data = this._initData();
        },
        get(){
            var data = this.data;
            this.init();
            return data;
        }
    };

    return r;
});
