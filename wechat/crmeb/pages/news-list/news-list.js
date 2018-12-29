// pages/news-list/news-list.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    newtext:"查看全文>>",
    _num:'',
    newsList:[
      {
        "id":20,
        "name":"系统管理员",
        "time":"2017/12/18 16:14",
        "title":"通知标题通知标题通知标题通知标题",
        "content": "通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内..."
      },
      {
        "id": 21,
        "name": "系统管理员",
        "time": "2017/12/18 16:14",
        "title": "通知标题通知标题通知标题通知标题",
        "content": "通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内..."
      },
      {
        "id": 22,
        "name": "系统管理员",
        "time": "2017/12/18 16:14",
        "title": "通知标题通知标题通知标题通知标题",
        "content": "通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内..."
      }
    ]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
  },
  lookMore:function(e){
    var obj = this.data.newtext;
    if (obj =="查看全文>>"){
      this.setData({
        _num: e.currentTarget.dataset.id,
        newtext: "点击收起>>"
      })
    }else{
      this.setData({
        _num: -1,
        newtext: "查看全文>>"
      })
    } 
  }
})