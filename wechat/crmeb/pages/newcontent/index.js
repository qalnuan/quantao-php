var app = getApp();
var wxh = require('../../utils/wxh.js');
var WxParse = require('../../wxParse/wxParse.js');
Page({
  data:{
    content:[],
    description:''
  },
	onLoad: function (options) {
        wx.showShareMenu({ withShareTicket: true });
        var that = this;
        wx.request({
          url: app.globalData.url + '/routine/auth_api/get_news_list?uid=' + app.globalData.uid,
          data: { id: options.id },
          method: "post",
          header: { "content-type": "application/x-www-form-urlencoded" },
          success: function (res) {
            console.log(res)
            that.setData({
              content:res.data.data,
              description: res.data.data.content,
            })
            WxParse.wxParse('description', 'html', that.data.description, that, 0);
          }
        })
        
    }
})