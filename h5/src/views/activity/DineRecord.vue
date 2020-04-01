<template>
  <div class="dine-record" ref="container">
    <div class="item" v-for="(item, index) in dine" :key="index">
      <div class="picTxt acea-row row-between-wrapper">
        <div class="pictrue"><img :src="item.image" /></div>
        <div class="text acea-row row-column-around">
          <div class="line1">{{ item.title }}</div>
          <count-down
            :is-day="true"
            :tip-text="'倒计时 '"
            :day-text="' 天 '"
            :hour-text="' 时 '"
            :minute-text="' 分 '"
            :second-text="' 秒'"
            :datatime="item.datatime"
          ></count-down>
        </div>
      </div>
      <div class="bottom acea-row row-between-wrapper">
        <div class="purple" v-if="item.status === 1">活动进行中，等待开奖</div>
        <div class="success" v-else-if="item.status === 3">恭喜中奖</div>
        <div class="end" v-else>活动已结束，很遗憾未中奖</div>
        <div class="acea-row row-middle row-right">
          <!-- <div
            class="bnt cancel"
            v-if="item.status === 1"
            @click="getDineUserCancel(item.dine_id)"
          >
            取消活动
          </div>
          <div
            class="bnt bg-color-red"
            v-if="item.status === 1"
            @click="goDetail(item.dine_id)"
          >
            继续砍价
          </div> -->
        </div>
      </div>
    </div>
    <Loading :loaded="status" :loading="loadingList"></Loading>
  </div>
</template>
<script>
import CountDown from "@components/CountDown";
import { getDineUserList } from "@api/activity";
import Loading from "@components/Loading";

export default {
  name: "DineRecord",
  components: {
    CountDown,
    Loading
  },
  props: {},
  data: function() {
    return {
      dine: [],
      status: false, //砍价列表是否获取完成 false 未完成 true 完成
      loadingList: false, //当前接口是否请求完成 false 完成 true 未完成
      page: 1, //页码
      limit: 20 //数量
    };
  },
  mounted: function() {
    this.getDineUserList();
    this.$scroll(this.$refs.container, () => {
      !this.loadingList && this.getDineUserList();
    });
  },
  methods: {
    goDetail: function(id) {
      this.$router.push({
        path: "/activity/dine_detail/" + id + "/0"
      });
    },
    goList: function() {
      this.$router.push({
        path: "/activity/dine"
      });
    },
    getDineUserList: function() {
      var that = this;
      if (that.loadingList) return;
      if (that.status) return;
      getDineUserList({ page: that.page, limit: that.limit })
        .then(res => {
          that.status = res.data.length < that.limit;
          that.dine.push.apply(that.dine, res.data);
          that.page++;
          that.loadingList = false;
        })
        .catch(res => {
          that.$dialog.error(res.msg);
        });
    }
    // getDineUserCancel: function(dineId) {
    //   var that = this;
    // getDineUserCancel({ dineId: dineId })
    //   .then(res => {
    //     that.$dialog.success(res.msg).then(() => {
    //       that.status = false;
    //       that.loadingList = false;
    //       that.page = 1;
    //       that.dine = [];
    //       that.getDineUserList();
    //     });
    //   })
    //   .catch(res => {
    //     that.$dialog.error(res.msg);
    //   });
    // }
  }
};
</script>
