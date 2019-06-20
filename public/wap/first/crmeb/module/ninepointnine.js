require(['vue', 'better-scroll', 'store'], function (Vue, BScroll, storeApi) {
  new Vue({
    el: "#guide",
    data: {
      type: 'article',
      article: {
        first: 1,
        limit: 8,
        list: [],
        loaded: false,
        top: 0
      },
      loading: false,
      scroll: null
    },
    watch: {
      type: function (v, old) {
        this[old].top = this.scroll.startY;
        this[v].list.length || this.getList();
        this.$nextTick(function () {
          this.scroll.scrollTo(0, this[v].top);
          this.scroll.refresh();
        });
      }
    },
    computed: {
      loaded: function () {
        return this[this.type].loaded;
      }
    },
    methods: {
      getList: function () {
        if (this.loading) return;
        this.getNinePointNineList();
      },
      getNinePointNineList: function () {
        var that = this,
          type = this.type,
          group = that[type];
        if (group.loaded) return;
        this.loading = true;
        storeApi.getNinePointNineList({
          pageno: group.first,
          pagesize: group.limit
        }, function (res) {
          var list = res.data.data;
          // group.loaded = list.length < group.limit;
          group.first ++;
          group.list = group.list.concat(list);
          that.$set(that, type, group);
          that.loading = false;
          that.$nextTick(function () {
            if (list.length) that.scroll.refresh();
            that.scroll.finishPullUp();
          });
        }, function () {
          that.loading = false
        });
      },
      bScrollInit: function () {
        var that = this;
        if (this.scroll === null) {
          this.$refs.bsDom.style.height = (document.documentElement.clientHeight) + 'px';
          this.$refs.bsDom.style.overflow = 'hidden';
          this.scroll = new BScroll(this.$refs.bsDom, {
            click: true,
            probeType: 1,
            cancelable: false,
            deceleration: 0.005,
            snapThreshold: 0.1
          });
          this.scroll.on('pullingUp', function () {
            that.loading == false && that.getList();
          })
        } else {
          this.scroll.refresh();
        }
      }
    },
    mounted: function () {
      this.bScrollInit();
      this.getList();
    }
  })
});