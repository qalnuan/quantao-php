//app.js
var app = getApp();
// var wxh = require('../../utils/wxh.js');
App({
  onLaunch: function () {
    // 展示本地存储能力
    var that = this;
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)
  },
  globalData: {
    uid: null,
    openPages:'',
    spid:0,
    url: 'http://www.crmeb.net'
  },
  setUserInfo : function(){
    var that = this;
    if (that.globalData.uid == null) {//是否存在用户信息，如果不存在跳转到首页
      wx.showToast({
        title: '用户信息获取失败',
        icon: 'none',
        duration: 1500,
      })
      setTimeout(function () {
        wx.navigateTo({
          url: '/pages/enter/enter',
        })
      }, 2000)
    }
  },
  getUserInfo: function () {
    var that = this;
    wx.getUserInfo({
      lang: 'zh_CN',
      success: function (res) {
        var userInfo = res.userInfo
        wx.login({
          success: function (res) {
            if (res.code) {
              userInfo.code = res.code;
              userInfo.spid = that.globalData.spid;
              wx.request({
                url: that.globalData.url + '/routine/login/index',
                method: 'post',
                dataType  : 'json',
                data: {
                  info: userInfo
                },
                success: function (res) {
                  debugger;
                  that.globalData.uid = res.data.data.uid;
                  if (that.globalData.openPages != '') {
                    wx.reLaunch({
                      url: that.globalData.openPages
                    })
                  } else {
                    wx.switchTab({
                      url: '/pages/index/index'
                    })
                  }
                },
                fail: function () {
                  console.log('获取用户信息失败');
                  wx.navigateTo({
                    url: '/pages/enter/enter',
                  })
                },
              })
            } else {
              console.log('登录失败！' + res.errMsg)
            }
          },
          fail: function () {
            console.log('获取用户信息失败');
            wx.navigateTo({
              url: '/pages/enter/enter',
            })
          },
        })
      },
      fail: function () {
        console.log('获取用户信息失败');
        wx.navigateTo({
          url: '/pages/enter/enter',
        })
      },
    })
  },
  getUserInfoEnter: function () {
    var that = this;
    wx.getUserInfo({
      lang: 'zh_CN',
      success: function (res) {
        var userInfo = res.userInfo
        wx.login({
          success: function (res) {
            if (res.code) {
              userInfo.code = res.code;
              userInfo.spid = that.globalData.spid;
              wx.request({
                url: that.globalData.url + '/routine/login/index',
                method: 'post',
                dataType  : 'json',
                data: {
                  info: userInfo
                },
                success: function (res) {
                  that.globalData.uid = res.data.data.uid;
                  if (that.globalData.openPages != '') {
                    wx.reLaunch({
                      url: that.globalData.openPages
                    })
                  } else {
                    wx.reLaunch({
                      url: '/pages/index/index'
                    })
                  }
                }
              })
            } else {
              console.log('登录失败！' + res.errMsg)
            }
          }
        })
      }
    })
  }
})