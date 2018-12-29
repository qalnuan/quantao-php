// pages/pink-list/index.js
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    url: app.globalData.url,
    offset:0,
    limit:20,
    CombinationList:[],
    Banner:[],
    indicatorDots: true,//是否显示面板指示点;
    autoplay: true,//是否自动播放;
    interval: 4000,//动画间隔的时间;
    duration: 500,//动画播放的时长;
    indicatorColor: "rgba(51, 51, 51, .3)",
    indicatorActivecolor: "#ffffff",
    circular: true
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getCombinationList();
  },
  getCombinationList:function(){
     var that = this;
     wx.request({
       url: app.globalData.url + '/routine/auth_api/get_combination_list?uid=' + app.globalData.uid,
       data:{
         offset: that.data.offset,
         limit:that.data.limit
       },
       method: 'GET',
       dataType: 'json',
       success: function(res) {
         that.setData({
           CombinationList: res.data.data.store_combination,
           Banner:res.data.data.banner
         })
       }
     })
  },
  goDetail:function(e){
     wx.request({
       url: app.globalData.url + '/routine/auth_api/get_form_id?uid=' + app.globalData.uid,
       method: 'GET',
       data: {
         formId: e.detail.formId
       },
       success: function (res) { }
     })
     wx.navigateTo({
       url: '/pages/product-pinke/index?id=' + e.detail.value.id,
     })
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})