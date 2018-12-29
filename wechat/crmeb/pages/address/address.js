// pages/address/address.js
var app = getApp();
Page({
  data: {
    _num:'',
    cartId: '',
    addressArray:[]
  },
  onLoad: function (options) {
    app.setUserInfo();
    if (options.cartId) {
      this.setData({
        cartId: options.cartId
      })
    }
    this.getAddress();
  },
  getAddress: function () {
    var that = this;
    var header = {
      'content-type': 'application/x-www-form-urlencoded',
    };
    wx.request({
      url: app.globalData.url + '/routine/auth_api/user_address_list?uid=' + app.globalData.uid,
      method: 'POST',
      header: header,
      success: function (res) {
        if (res.data.code == 200) {
          if (res.data.data.length < 1) {
            wx.showToast({
              title: '暂无收货地址，请先添加收货地址',
              icon: 'none',
              duration: 1000,
            })
            setTimeout(function () {
              that.addAddress();
            }, 1100)
          } else {
            that.setData({
              addressArray: res.data.data
            })
            for (var i in res.data.data){
              if (res.data.data[i].is_default){
                that.setData({
                  _num: res.data.data[i].id
                })
              }
            }
            console.log(res.data.data);
          }
        }
      }
    })
  },
  addAddress:function(){

    var cartId = this.data.cartId;
    this.setData({
      cartId: ''
    })
    wx.navigateTo({ //跳转至指定页面并关闭其他打开的所有页面（这个最好用在返回至首页的的时候）
      url: '/pages/addaddress/addaddress?cartId=' + cartId
    })
  },
  goOrder:function(e){
    var id = e.currentTarget.dataset.id;
    var cartId = '';
    if (this.data.cartId && id){
      cartId = this.data.cartId;
      this.setData({
        cartId : ''
      })
      wx.navigateTo({ //跳转至指定页面并关闭其他打开的所有页面（这个最好用在返回至首页的的时候）
        url: '/pages/order-confirm/order-confirm?id=' + cartId + '&addressId='+id
      })
    }
    console.log(id);
  },
  delAddress:function(e){
    var id = e.currentTarget.dataset.id;
    var that = this;
    var header = {
      'content-type': 'application/x-www-form-urlencoded',
    };
    wx.request({
      url: app.globalData.url + '/routine/auth_api/remove_user_address?uid=' + app.globalData.uid,
      method: 'GET',
      header: header,
      data:{
        addressId:id
      },
      success: function (res) {
        if (res.data.code == 200) {
          wx.showToast({
            title: '删除成功',
            icon: 'success',
            duration: 1000,
          })
          that.getAddress();
        } else {
          wx.showToast({
            title: res.data.msg,
            icon: 'none',
            duration: 1000,
          })
        }
      }
    })
  },
  editAddress: function (e) {
    var cartId = this.data.cartId;
    this.setData({
      cartId: ''
    })
    wx.navigateTo({ //跳转至指定页面并关闭其他打开的所有页面（这个最好用在返回至首页的的时候）
      url: '/pages/addaddress/addaddress?id=' + e.currentTarget.dataset.id + '&cartId=' + cartId
    })
  },
  activetap:function(e){
    var id = e.target.dataset.idx;
    var that = this;
    var header = {
      'content-type': 'application/x-www-form-urlencoded',
    };
    wx.request({
      url: app.globalData.url + '/routine/auth_api/set_user_default_address?uid=' + app.globalData.uid,
      method: 'GET',
      header: header,
      data:{
        addressId:id
      },
      success: function (res) {
        if (res.data.code == 200) {
          wx.showToast({
            title: '设置成功',
            icon: 'success',
            duration: 1000,
          })
          that.setData({
            _num: id
          })
        } else {
          wx.showToast({
            title: res.data.msg,
            icon: 'none',
            duration: 1000,
          })
        }
      }
    })
  }
})