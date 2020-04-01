<template>
  <div class="order-details pos-order-details">
    <div class="header acea-row row-middle">
      <div class="state">{{ title }}</div>
      <div class="data">
        <div class="order-num">订单：{{ orderInfo.order_id }}</div>
        <div>
          <span class="time">{{ orderInfo.add_time }}</span>
        </div>
      </div>
    </div>
    <div class="orderingUser acea-row row-middle">
      <span class="iconfont icon-yonghu2"></span>{{ orderInfo.nickname }}
    </div>
    <div class="address">
      <div class="name">
        {{ orderInfo.real_name
        }}<span class="phone">{{ orderInfo.user_phone }}</span>
      </div>
    </div>
    <div class="line"><img src="@assets/images/line.jpg" /></div>
    <div class="pos-order-goods">
      <div
        class="goods acea-row row-between-wrapper"
        v-for="(item, index) in orderInfo._info"
        :key="index"
      >
        <div class="picTxt acea-row row-between-wrapper">
          <div class="pictrue">
            <img :src="item.cart_info.productInfo.image" />
          </div>
          <div class="text acea-row row-between row-column">
            <div class="info line2">
              {{ item.cart_info.productInfo.store_name }}
            </div>
            <div class="attr">{{ item.cart_info.productInfo.suk }}</div>
          </div>
        </div>
        <div class="money">
          <div class="x-money">￥{{ item.cart_info.productInfo.price }}</div>
          <div class="num">x{{ item.cart_info.cart_num }}</div>
          <div class="y-money">￥{{ item.cart_info.productInfo.ot_price }}</div>
        </div>
      </div>
    </div>
    <div class="public-total">
      共{{ orderInfo.total_num }}件商品，应支付
      <span class="money">￥{{ orderInfo.pay_price }}</span> ( 邮费 ¥{{
        orderInfo.pay_postage
      }}
      )
    </div>
    <div class="wrapper">
      <div class="item acea-row row-between">
        <div>订单编号：</div>
        <div class="conter acea-row row-middle row-right">
          {{ orderInfo.order_id }}
        </div>
      </div>
      <div class="item acea-row row-between">
        <div>下单时间：</div>
        <div class="conter">{{ orderInfo.add_time }}</div>
      </div>
      <div class="item acea-row row-between">
        <div>支付状态：</div>
        <div class="conter">
          {{ orderInfo.paid == 1 ? "已支付" : "未支付" }}
        </div>
      </div>
      <div class="item acea-row row-between">
        <div>支付方式：</div>
        <div class="conter">{{ payType }}</div>
      </div>
      <div class="item acea-row row-between">
        <div>买家留言：</div>
        <div class="conter">{{ orderInfo.mark }}</div>
      </div>
    </div>
    <div class="wrapper">
      <div class="item acea-row row-between">
        <div>支付金额：</div>
        <div class="conter">￥{{ orderInfo.total_price }}</div>
      </div>
      <div class="item acea-row row-between">
        <div>优惠券抵扣：</div>
        <div class="conter">-￥{{ orderInfo.coupon_price }}</div>
      </div>
      <div class="item acea-row row-between">
        <div>运费：</div>
        <div class="conter">￥{{ orderInfo.freight_price }}</div>
      </div>
      <div class="actualPay acea-row row-right">
        实付款：<span class="money font-color-red"
          >￥{{ orderInfo.pay_price }}</span
        >
      </div>
    </div>
    <div
      class="wrapper"
      v-if="
        orderInfo.delivery_type != 'fictitious' && orderInfo._status._type === 2
      "
    >
      <div class="item acea-row row-between">
        <div>配送方式：</div>
        <div class="conter" v-if="orderInfo.delivery_type === 'express'">
          快递
        </div>
        <div class="conter" v-if="orderInfo.delivery_type === 'send'">送货</div>
      </div>
      <div class="item acea-row row-between">
        <div v-if="orderInfo.delivery_type === 'express'">快递公司：</div>
        <div v-if="orderInfo.delivery_type === 'send'">送货人：</div>
        <div class="conter">{{ orderInfo.delivery_name }}</div>
      </div>
      <div class="item acea-row row-between">
        <div v-if="orderInfo.delivery_type === 'express'">快递单号：</div>
        <div v-if="orderInfo.delivery_type === 'send'">送货人电话：</div>
        <div class="conter">
          {{ orderInfo.delivery_id
          }}<span
            class="copy copy-data"
            :data-clipboard-text="orderInfo.delivery_id"
            >复制</span
          >
        </div>
      </div>
    </div>
    <div style="height:1.2rem;"></div>
    <div class="footer acea-row row-right row-middle">
      <div
        class="bnt delivery"
        v-if="orderInfo.is_mer_check === 0"
        @click="verifyOrder"
      >
        确认核销
      </div>
      <div class="" v-else>
        订单已核销
      </div>
    </div>
  </div>
</template>
<script>
import { getVerifyOrderDetail, verifyOrder } from "../../api/order";
export default {
  name: "VerifyOrder",
  components: {},
  props: {},
  data: function() {
    return {
      order: false,
      change: false,
      order_id: "",
      orderInfo: {
        _status: {}
      },
      status: "",
      title: "",
      payType: "",
      types: ""
    };
  },
  watch: {
    "$route.params.oid": function(newVal) {
      let that = this;
      if (newVal != undefined) {
        that.order_id = newVal;
        that.getIndex();
      }
    }
  },
  mounted: function() {
    this.order_id = this.$route.params.oid;
    this.getIndex();
  },
  methods: {
    getIndex: function() {
      let that = this;
      getVerifyOrderDetail(that.order_id).then(
        res => {
          that.orderInfo = res.data;
          that.types = res.data._status._type;
          that.title = res.data._status._title;
          that.payType = res.data._status._payType;
        },
        err => {
          that.$dialog.error(err.msg);
        }
      );
    },
    verifyOrder: function() {
      verifyOrder({ order_id: this.orderInfo.order_id }).then(
        res => {
          this.$dialog.success(res.msg);
          this.getIndex();
        },
        err => {
          this.$dialog.error(err.msg);
        }
      );
    }
  }
};
</script>
