import wxh from '../../../utils/wxh.js';
import wxParse from '../../../wxParse/wxParse.js';
import {
  getDineDetail,
  applyDine
} from '../../../api/activity.js';

const app = getApp();

Page({
  /**
   * 页面的初始数据
   */
  data: {
    id: 0,
    time: 0,
    countDownHour: "00",
    countDownMinute: "00",
    countDownSecond: "00",
    storeInfo: [],
    imgUrls: [],
    parameter: {
      'navbar': '1',
      'return': '1',
      'title': '霸王餐详情页',
      'color': false
    },
    attribute: {
      'cartAttr': false
    },
    productSelect: [],
    productAttr: [],
    productValue: [],
    isOpen: false,
    isIn: false,
    attr: '请选择',
    attrValue: '',
  },

  onLoadFun: function() {
    this.getDineDetail();
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    if (options.hasOwnProperty('id') && options.hasOwnProperty('time')) {
      this.setData({
        id: options.id,
        time: options.time
      });
      app.globalData.openPages = '/pages/activity/goods_dine_details/index?id=' + this.data.id + '&time=' + this.data.time;
    } else
      return app.Tips({
        title: '参数错误'
      }, {
        tab: 3,
        url: 1
      })
  },
  onMyEvent: function(e) {
    this.setData({
      'attribute.cartAttr': e.detail.window,
      isOpen: false
    })
  },
  /**
   * 购物车数量加和数量减
   * 
   */
  ChangeCartNum: function(e) {
    //是否 加|减
    var changeValue = e.detail;
    //获取当前变动属性
    var productSelect = this.data.productValue[this.data.attrValue];
    //如果没有属性,赋值给商品默认库存
    if (productSelect === undefined && !this.data.productAttr.length) productSelect = this.data.productSelect;
    //不存在不加数量
    if (productSelect === undefined) return;
    //提取库存
    var stock = productSelect.stock || 0;
    //设置默认数据
    if (productSelect.cart_num == undefined) productSelect.cart_num = 1;
    //数量+
    if (changeValue) {
      productSelect.cart_num++;
      //大于库存时,等于库存
      if (productSelect.cart_num > this.data.storeInfo.num) productSelect.cart_num = this.data.storeInfo.num;
      this.setData({
        ['productSelect.cart_num']: productSelect.cart_num,
        cart_num: productSelect.cart_num,
        ['productSelect.is_on']: productSelect.cart_num > this.data.storeInfo.num,
      });
    } else {
      //数量减
      productSelect.cart_num--;
      //小于1时,等于1
      if (productSelect.cart_num < 1) productSelect.cart_num = 1;
      this.setData({
        ['productSelect.cart_num']: productSelect.cart_num,
        cart_num: productSelect.cart_num,
        ['productSelect.is_on']: false,
      });
    }
  },
  /**
   * 属性变动赋值
   * 
   */
  ChangeAttr: function(e) {
    var values = e.detail;
    var productSelect = this.data.productValue[values];
    var storeInfo = this.data.storeInfo;
    if (productSelect) {
      this.setData({
        ["productSelect.image"]: productSelect.image,
        ["productSelect.price"]: productSelect.price,
        ["productSelect.stock"]: productSelect.stock,
        ['productSelect.unique']: productSelect.unique,
        ['productSelect.cart_num']: 1,
        ['productSelect.is_on']: productSelect.cart_num > this.data.storeInfo.num,
        attrValue: values,
        attr: '已选择'
      });
    } else {
      this.setData({
        ["productSelect.image"]: storeInfo.image,
        ["productSelect.price"]: storeInfo.price,
        ["productSelect.stock"]: 0,
        ['productSelect.unique']: '',
        ['productSelect.cart_num']: 0,
        ['productSelect.is_on']: false,
        attrValue: '',
        attr: '请选择'
      });
    }
  },
  selecAttr: function() {
    this.setData({
      'attribute.cartAttr': true
    })
  },
  /*
   *  下订单
   */
  goCat: function() {
    var that = this;
    var productSelect = this.data.productValue[this.data.attrValue];
    applyDine({
      productId: that.data.storeInfo.product_id,
      dineId: that.data.id,
      cartNum: that.data.cart_num,
      uniqueId: productSelect !== undefined ? productSelect.unique : '',
      'new': 1
    }).then(res => {
      that.setData({
        isOpen: false,
        isIn: true
      });
    }).catch(err => {
      return app.Tips({
        title: err
      });
    });
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    if (this.data.isClone && app.globalData.isLog) this.getDineDetail();
  },
  getDineDetail: function() {
    let that = this;
    getDineDetail(that.data.id).then(res => {
      let title = res.data.storeInfo.title;
      that.setData({
        ["parameter.title"]: title.length > 10 ? title.substring(0, 10) + '...' : title,
        storeInfo: res.data.storeInfo,
        imgUrls: res.data.storeInfo.images,
        isIn: res.data.isIn
      });
      that.setProductSelect();
      app.globalData.openPages = '/pages/activity/goods_dine_details/index?id=' + that.data.id + '&time=' + that.data.time + '&scene=' + that.data.storeInfo.uid;
      wxParse.wxParse('description', 'html', that.data.storeInfo.description || '', that, 0);
      wxh.time(that.data.time, that);
    }).catch(err => {
      app.Tips({
        title: err
      });
    });
  },
  setProductSelect: function() {
    var that = this;
    if (that.data.productSelect.length == 0) {
      that.setData({
        ['productSelect.image']: that.data.storeInfo.image,
        ['productSelect.store_name']: that.data.storeInfo.title,
        ['productSelect.price']: that.data.storeInfo.price,
        ['productSelect.stock']: that.data.storeInfo.stock,
        ['productSelect.unique']: '',
        ['productSelect.cart_num']: 1,
        ['productSelect.is_on']: that.data.storeInfo.num <= 1,
      })
    }
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },
  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {
    this.setData({
      isClone: true
    });
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {
    var that = this;
    return {
      title: that.data.storeInfo.title,
      path: app.globalData.openPages,
      imageUrl: that.data.storeInfo.image,
      success: function() {
        wx.showToast({
          title: '分享成功',
          icon: 'success',
          duration: 2000
        })
      }
    }
  }
})