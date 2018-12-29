var app = getApp();
// pages/main/main.js
Page({
  data: {
    url: app.globalData.url,
    now_money:'',
    mainArray:[]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    var now = options.now;
    that.setData({
      now_money: now
        });
    var header = {
      'content-type': 'application/x-www-form-urlencoded',
      'cookie': app.globalData.sessionId//读取cookie
    };
   
    wx.request({
      url: app.globalData.url + '/routine/auth_api/user_balance_list?uid=' + app.globalData.uid,
      method: 'GET',
      header: header,
      success: function (res) {
        that.setData({
          mainArray: res.data.data
        })
      }
    })
  },
  indexs:function(){
    wx.switchTab({
      url: '../../pages/index/index',
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