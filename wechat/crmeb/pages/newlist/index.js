var app = getApp();
var wxh = require('../../utils/wxh.js');
Page({
  data:{
      url: app.globalData.url,
      loading: 0,
      loadover: 0,
      list: new Array(),
      offset:0,
      title: "玩命加载中...",
      hidden: false
  },
  onLoad:function(){
    wx.showShareMenu({ withShareTicket: true });
    wx.showLoading({ title: "加载中……" });
    var that = this;
    wx.request({
      url: app.globalData.url + '/routine/auth_api/get_news_list?uid=' + app.globalData.uid,
      method: 'GET',
      success: function (res) {
        if (res.data.code == 200) {
          that.setData({
            list: res.data.data,
            title: "数据已经加载完成",
            hidden: true
          })
        } else {
          that.setData({
            Arraylike: []
          })
        }
      }
    })
    wx.hideLoading();
  },
  onReachBottom:function(){
    var limit = 20;
    var that = this;
    var offset = that.data.offset;
    if (!offset) offset = 1;
    var startpage = limit * offset;
    if (!that.data.loading && !that.data.loadover) {
      //请求前处理
      that.data.loading = 1;
      that.setData({ list: that.data.list });
      wx.showLoading({ title: "加载中……" });
      wx.request({
        url: app.globalData.url + '/routine/auth_api/get_news_list?uid=' + app.globalData.uid,
        data: { limit: limit, offset: startpage },
        method: 'POST',
        success: function (res) {
          var len = res.data.data.length;
          
          if (res.data.data.lastpage)//判断当前是否加载最后一页
            that.data.loadover = 1;
          else
            that.data.nowpage++;
          if (res.data.data) {
            for (var i = 0; i < len; i++) {
              that.data.likeList.push(res.data.data[i])
            }
          }
          if (len < limit) {
            that.setData({
              title: "数据已经加载完成",
              hidden: true
            });
            return false;
          }
        }
      })
    }
  },
  contentLower: function (event) {//商品下拉底部事件
    this.getNewList();
  }
})