// pages/unshop/unshop.js
var app = getApp();
var wxh = require('../../utils/wxh.js');
Page({
  data: {
    attrName: '',
    num:1,
    url: app.globalData.url,
    hiddendown:true,
    currentTab:"-1",
    taber:"-1",
    active:0,
    Arraylike:[],
    productAttr: [],
    productSelect: [
      { image: "" },
      { store_name: "" },
      { price: 0 },
      { unique: "" },
      { stock: 0 },
    ],
    productValue:[],
    total:'全部',
    animationData:{},
    cid:'',
    sid:'',
    price:'',
    sales:'',
    news:'',
    t:1,
    sortyi:[],
    offset: 0,
    title: "玩命加载中...",
    hidden: false,
    show: false,
    prostatus: false,
    sorter:[],
    productid:'',
    CartCount:''
  },
  onLoad:function(e){
    app.setUserInfo();
    var that = this;
    that.getCartCount();
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
      method: 'GET',
      success: function (res) {
        if (res.data.code==200){
        that.setData({
          Arraylike: res.data.data
        })
        }else{
          that.setData({
            Arraylike: []
          })
        }
      },
      fail: function (res){
        console.log('submit fail');
      },
      complete: function (res) {
        console.log('submit complete');
      }
    })
  },
  parameterShow: function () {
    var that = this;
  
    
  },
  goCart:function(){
    wx.switchTab({
      url: '/pages/buycar/buycar'
    });
  },
  product:function(e){
    var index = e.target.dataset.num;
    if(index==1){
        this.setData({
          _num: index,
          num:2
        }) 
    }else{
      this.setData({
        _num: index,
        num: 1
      }) 
    } 
    this.setData({
      _num: e.target.dataset.num
    })
  },
  sort:function(e){
    var that = this;
    var all = this.data.hiddendown;
    this.setData({
      active: 0
    });
    if(all){
       this.setData({
         hiddendown:false
       })
    }else{
      this.setData({
        hiddendown: true
      })
    }
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_pid_cate?uid=' + app.globalData.uid,
      method: 'POST',
      success: function (res) {
        if (res.data.code==200){
        that.setData({
          sortyi:res.data.data,
        })
        }else{
          that.setData({
            sortyi: []
          })
        }
      }
      
    })
  },
  itemdown:function(e){
    var that = this;
    var animation = wx.createAnimation({
      duration: 500,
      timingFunction: 'ease',
    });
    this.animation = animation;
    var width = 50+'%';
    animation.width(width).step();    
     var id=e.target.dataset.idx;
     wx.request({
       url: app.globalData.url + '/routine/auth_api/get_id_cate?uid=' + app.globalData.uid,
       data: { id: e.target.dataset.idx},
       method: 'POST',
       success: function (res) {
         if (res.data.code==200){
         that.setData({
           currentTab: e.target.dataset.idx,
           animationData: animation.export()
           });
         that.setData({
           cid: e.target.dataset.idx,
           sorter: res.data.data,
         })
         }else{
           that.setData({
             currentTab: 0,
             animationData: {}
           });

           that.setData({
             cid: 0,
             sorter: []
           })
         }
       },
       
     })
  },
  itemdowner:function(e){
    var that = this;
    var SoerErId = 0;
    
    that.setData({
      total: '全部',
      sid:e.target.dataset.idx,
      currentTab: e.target.dataset.idx,
      taber:1,
      hiddendown: true,
      price: '',
      sales: '',
      news: '',
    })
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
      data: { cid: that.data.cid, sid: that.data.sid},
      method: 'GET',
      success: function (res) {
        if(res.data.code==200){
        that.setData({
          offset:1,
          Arraylike: [],
          Arraylike:res.data.data
        })
        }else{
          that.setData({
            offset: 0,
            Arraylike: []
          })
        }
      },
    })
  },
  getCartCount: function () {
    var that = this;
    var header = {
      'content-type': 'application/x-www-form-urlencoded',
    };
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_cart_num?uid=' + app.globalData.uid,
      method: 'POST',
      header: header,
      success: function (res) {
        that.setData({
          CartCount: res.data.data
        })
      }
    })
  },
  wholeproduct: function (e) {
    var that = this;
    var $taber = e.target.dataset.ider;
    if (that.data.cid != '') {
      var cid = that.data.cid;
    } else {
      var cid = '';
    }
    var arr = that.data.sortyi;
    var len = that.data.sortyi.length;
    for (var i = 0; i < len; i++) {
      if (arr[i].id == that.data.cid) {
        that.setData({
          total: arr[i].cate_name + '/全部',
          taber: $taber
        })
      }
    }
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
      data: { cid: cid, sid: '' },
      method: 'GET',
      success: function (res) {
        if (res.data.code == 200) {
          that.setData({
            sid: 0,
            offset: 0,
            Arraylike: res.data.data,
            hiddendown: true
          })
        } else {
          that.setData({
            sid: 0,
            offset: 0,
            Arraylike: []
          })
        }
      },
    })
  },
  allproduct: function () {
    var that = this;
    that.setData({
      price: '',
      sales: '',
      news: '',
    })
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
      data: { cid: '', sid: '' },
      method: 'GET',
      success: function (res) {
        if (res.data.code == 200) {
          that.setData({
            sid: 0,
            offset: 1,
            total: '全部',
            taber: '-1', 
            currentTab:'-1',
            Arraylike: res.data.data,
            hiddendown: true
          })
        } else {
          that.setData({
            sid: 0,
            offset: 0,
            total: '',  
            taber:'-1',
            Arraylike: []
          })
        }
      },
    })
  },
  maskhide:function(e){
     this.setData({
       hiddendown:true
     })
  },
  navactive:function(e){
    var that = this;
    that.setData({
       active: e.target.dataset.act,
       hiddendown: true
     })
    var act = e.target.dataset.act;
    var priceOrder = '';
    var t = that.data.t;
    var n=t+1;
    if (n%2>0){
      priceOrder ='asc';
    }else{
      priceOrder='desc';
    }
    var sid=that.data.sid;
     wx.request({
       url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
       data: { sid: sid, priceOrder: priceOrder, first:0, limit: 20},
       method: 'GET',
       success: function (res) {
         if (res.data.code==200){
         that.setData({
           t:n,
           price: priceOrder,
           news: '',
           sales: '',
           offset: 1,
           Arraylike: res.data.data
         })
         }else{
           that.setData({
             t: 0,
             price: '',
             news: '',
             sales: '',
             offset: 0,
             Arraylike: []
           })
         }
       }
     })
  },
  navactive1: function (e) {
    var that = this;
    that.setData({
      active: e.target.dataset.act,
      hiddendown: true
    })
    var act = e.target.dataset.act;
    var  salesOrder = '';
    var t = that.data.t;
    var n = t + 1;
    if (n%2>0) {
      salesOrder = 'asc';
    } else{
      salesOrder='desc';
    }
   var sid= that.data.sid;
     wx.request({
       url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
       data: { sid: sid,salesOrder: salesOrder},
       method: 'GET',
       success: function (res) {
         if(res.data.code==200){
         that.setData({
           t: n,
           sales: salesOrder,
           price: '',
           news: '',
           offset: 1,
           Arraylike: res.data.data
         })
         }else{
           that.setData({
             t: 0,
             sales: '',
             price: '',
             news: '',
             offset: 0,
             Arraylike: []
           })
         }
       }
      
     })
  },
  navactive2: function (e) {
    var that = this;
    that.setData({
      active: e.target.dataset.act,
      hiddendown: true
    })
    var act = e.target.dataset.act;
    var news = '';
    if (act == 3) {
      news = 1;
    }else{
      news='';
    }
    that.setData({
      news:news
    })
    var sid = that.data.sid;
    var limit = 20;
    var offset = that.data.offset;
    var startpage = limit * offset;
    var startpage = 0;
    var header = {
      'content-type': 'application/x-www-form-urlencoded',
    };
     wx.request({
       url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
       data: { sid: sid,news: that.data.news, first: startpage, limit: limit},
       header: header,
       method: 'GET',
       success: function (res) {
         if(res.data.code==200){
           that.setData({
             news: news,
             price: '',
             sales: '',
             offset: 1,
             Arraylike: res.data.data
           })
         }else{
         that.setData({
           news: '',
           price: '',
           sales: '',
           offset: 0,
           Arraylike: []
         })
         }
       }
     })
  },
  searchSubmit:function(e){
    var that = this;
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_form_id?uid=' + app.globalData.uid,
      method: 'GET',
      data: {
        formId: e.detail.formId
      },
      success: function (res) { }
    })
    var $search = e.detail.value;
    wx.request({
      url: app.globalData.url + '/routine/auth_api/store?uid=' + app.globalData.uid,
      data: {value: $search},
      method: 'GET',
      success: function (res){
        if (res.data.code==200){
           that.setData({
             Arraylike: res.data.data
           })  
        }else{
          that.setData({
            Arraylike: []
          })  
        }
      }
    
    })
  },
  cart:function(e){
    var that = this;
    var id=e.target.dataset.id;
    wx.request({
      url: app.globalData.url + '/routine/auth_api/details?uid='+app.globalData.uid,
      data: {
        id: id
      },
      method: 'POST',
      success: function (res) {
        var image = "productSelect.image";
        var store_name = "productSelect.store_name";
        var price = "productSelect.price";
        var unique = "productSelect.unique";
        var stock = "productSelect.stock";
        if(res.data.code==200){
        that.setData({
          productAttr: res.data.data.productAttr,
          productValue: res.data.data.productValue,
          productSelect: res.data.data.storeInfo,
          [image]: res.data.data.storeInfo.image,
          [stock]: res.data.data.storeInfo.stock,
          [store_name]: res.data.data.storeInfo.store_name,
          [price]: res.data.data.storeInfo.price,
          [unique]: '',
          productid: id
        })
        that.detail();
        }else{
          that.setData({
            productid: ''
          })
          // that.prompts();
        }
      }

    })
  },
  detail:function(){
    var that=this;
    that.setData({
      show: true,
      prostatus: true,
    })
  },
  modelbg: function (e) {
    this.setData({
      show: false,
      prostatus: false
    })
  },
  bindMinus: function () {
    var that = this;
    wxh.carmin(that)
  },
  bindPlus: function () {
    var that = this;
    wxh.carjia(that);
  },
  tapsize: function (e) {
    var that = this;
    var key = e.currentTarget.dataset.key;
    var attrValues = [];
    var attrName = that.data.attrName;
    var attrNameArr = attrName.split(",");
    var array = that.data.productAttr;
    for (var i in that.data.productAttr) {
      for (var j in that.data.productAttr[i]['attr_values']) {
        if (that.data.productAttr[i]['attr_values'][j] == key) {
          attrValues = that.data.productAttr[i]['attr_values'];
        }
      }
    }
    for (var ii in attrNameArr) {
      if (that.in_array(attrNameArr[ii], attrValues)) {
        attrNameArr.splice(ii, 1);
      }
    }
    attrName = attrNameArr.join(',');
    that.setData({
      attrName: e.currentTarget.dataset.key + ',' + attrName
    })
    attrNameArr = that.data.attrName.split(",");
    var attrNameArrSort = '';
    for (var jj in that.data.productAttr) {
      for (var jjj in that.data.productAttr[jj]['attr_values']) {
        if (that.in_array(that.data.productAttr[jj]['attr_values'][jjj], attrNameArr)) {
          attrNameArrSort += that.data.productAttr[jj]['attr_values'][jjj] + ',';
        }
      }
    }
    for (var jj in array) {
      for (var jjj in array[jj]['attr_values']) {
        if (that.in_array(array[jj]['attr_values'][jjj], attrNameArr)) {
          array[jj]['attr_value'][jjj].check = true;
        } else {
          array[jj]['attr_value'][jjj].check = false;
        }
      }
    }
    that.setData({
      productAttr: array
    })
    var attrNameArrSortArr = attrNameArrSort.split(",");
    attrNameArrSortArr.pop();
    that.setData({
      attrName: attrNameArrSortArr.join(',')
    })
    var arrAttrName = that.data.attrName.split(",");
    for (var index in that.data.productValue) {
      var strValue = that.data.productValue[index]['suk'];
      var arrValue = strValue.split(",");
      if (that.in_array_two(arrValue, arrAttrName)) {
        var image = "productSelect.image";
        var store_name = "productSelect.store_name";
        var price = "productSelect.price";
        var unique = "productSelect.unique";
        var stock = "productSelect.stock";
        that.setData({
          [image]: that.data.productValue[index]['image'],
          [price]: that.data.productValue[index]['price'],
          [unique]: that.data.productValue[index]['unique'],
          [stock]: that.data.productValue[index]['stock'],
        })
      }
      // if (index == that.data.attrName){
      //   var image = "productSelect.image";
      //   var store_name = "productSelect.store_name";
      //   var price = "productSelect.price";
      //   var unique = "productSelect.unique";
      //   var stock = "productSelect.stock";
      //   that.setData({
      //     [image] : that.data.productValue[index]['image'],
      //     [price] : that.data.productValue[index]['price'],
      //     [unique] : that.data.productValue[index]['unique'],
      //     [stock] : that.data.productValue[index]['stock'],
      //   })
      // }
    }
    // wxh.tapsize(that, e);
  },
  in_array_two: function (arr1, arr2) {
    if (arr1.sort().toString() == arr2.sort().toString()) {
      return true;
    }
    else {
      return false;
    }

  },
  in_array: function (str, arr) {
    for (var f1 in arr) {
      if (arr[f1].indexOf(str) > -1) {
        return true;
      }
    }
  },
  subBuy: function () {
    var that = this;
    
    if (that.data.num > that.data.productSelect.stock) {
      wx.showToast({
        title: '库存不足' + that.data.num,
        icon: 'none',
        duration: 2000
      })
      that.setData({
        num: that.data.productSelect.stock,
      })
    } else if (that.data.productAttr.length > 0 && that.data.productSelect.unique == '') {
      wx.showToast({
        title: '请选择属性',
        icon: 'none',
        duration: 2000
      })
    } else {
     
        var header = {
          'content-type': 'application/x-www-form-urlencoded',
        };
        wx.request({
          url: app.globalData.url + '/routine/auth_api/set_cart?uid=' + app.globalData.uid,
          method: 'GET',
          data: {
            productId: that.data.productid,
            cartNum: that.data.num,
            uniqueId: that.data.productSelect.unique
          },
          header: header,
          success: function (res) {
            if (res.data.code == 200) {
              wx.showToast({
                title: '添加购物车成功',
                icon: 'success',
                duration: 2000
              })
              that.setData({
                show: false,
                prostatus: false
              })
              that.getCartCount();
            } else {
              wx.showToast({
                title: res.data.msg,
                icon: 'none',
                duration: 2000
              })
            }
          }
        })
      
    }
  },
  prompt:function(){
    wx.showToast({
      title: this.data.productid ? "加入成功" : "加入失败",
         icon: 'success',
          duration: 800,
       mask:true
   })
  },
  prompts: function () {
    wx.showToast({
      title: '加入购物车失败！',
      icon: 'none',
      duration: 2000//持续的时间

    })
  },
  details:function (e) {
    var that = this;
    var id = e.target.dataset.aa;
    // console.log(e.target.dataset);
    // wx.request({
    //   url: app.globalData.url + '/routine/auth_api/unique',
    //   data: { productId: id },
    //   method: 'GET',
    //   success: function (res) {
    //     // that.setData({
    //     //   Arraylike: res.data.data
    //     // })
    //   }

    // })
  },
  onReachBottom: function (p) {
    var that = this;
    var news = that.data.news; 
    var sid = that.data.sid;
    var limit = 20;
    var priceOrder = that.data.price;
    var salesOrder = that.data.sales;
    var offset = that.data.offset;
    var startpage = limit * offset;
    var header = {
      'content-type': 'application/x-www-form-urlencoded',
    };
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_product_list?uid=' + app.globalData.uid,
      data: { sid: sid, priceOrder: priceOrder, salesOrder: salesOrder, news: news,first: startpage ,limit: limit},
      method: 'GET',
      header: header,
      success: function (res) {
        if(res.data.code==200){
        var len = res.data.data.length;
        var ladding = that.data.Arraylike;
        for (var i in res.data.data){
          ladding.push(res.data.data[i]);
        }
        that.setData({
          Arraylike: ladding,
          offset: offset + 1
        })
        if (len < limit) {
          that.setData({
            title: "数据已经加载完成",
            hidden: true
          });
          return false;
        }
        }
      },
      fail: function (res) {
        console.log('submit fail');
      },
      complete: function (res) {
        console.log('submit complete');
      }
    })
  },
})