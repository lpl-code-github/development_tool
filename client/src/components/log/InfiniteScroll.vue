<template>
  <div
      v-infinite-scroll="handleInfiniteOnLoad"
      class="demo-infinite-container"
      :infinite-scroll-disabled="busy"
      :infinite-scroll-distance="10"
  >
    <a-list
        item-layout="horizontal"
        :data-source="showData"
    >

      <!--          <div-->
      <!--              v-if="showLoadingMore"-->
      <!--              slot="loadMore"-->
      <!--              :style="{ textAlign: 'center', marginTop: '12px', height: '32px', lineHeight: '32px' }"-->
      <!--          >-->
      <!--            <a-spin v-if="loadingMore"/>-->
      <!--            <a-button v-else @click="onLoadMore">-->
      <!--              loading more-->
      <!--            </a-button>-->
      <!--          </div>-->

      <a-list-item slot="renderItem" slot-scope="item, index">
        <a-popover placement="bottom" slot="actions">
          <template slot="content">
            <p>{{ item.message ? item.message : '没有记录' }}</p>
          </template>
          <a v-if="item.status_code !== 200" size="small">
            报错信息
          </a>
        </a-popover>
        <a-list-item-meta>
              <span slot="title" style="display: flex;justify-content: space-between">
                <span>
                   <a-tag :color="item.status_code === 200? '#1ba81e' :'#f50' ">
                  {{ item.status_code }}
                  </a-tag>

                  <span>{{ item.type }}</span>
                </span>
                <span style="color: #a6a3a3">
                    {{ splitTime(item.created_at) }}
                </span>
              </span>
        </a-list-item-meta>
        {{ item.name }}
      </a-list-item>
      <div v-if="loading && !busy" class="demo-loading-container">
        <a-spin/>
      </div>
    </a-list>

  </div>
</template>

<script>
import infiniteScroll from 'vue-infinite-scroll';
export default {
  name: "InfiniteScroll",
  props: {
    sourceData: {
      type: Array,
      required: true
    }
  },
  directives: {infiniteScroll},
  data(){
    return{
      loading: false,
      busy: false,
      listData: [],
      showData: [],
      loadedCount: 0, // 已经加载的数据数量
      perPage: 20, // 每页的数据数量
      showMessage: false
    }
  },
  computed: {
    splitTime() {
      return function (item) {
        const [date, time] = item.split(' ');
        return time;
      }
    }
  },
  watch: {
    sourceData: {
      handler: function (newVal, oldVal) {

      },
      // 深度观察监听
      deep: true
    },
  },
  mounted() {
    this.loading = true
    this.loadedCount = 0
    this.showData = []
    this.listData = this.sourceData

    const startIndex = this.loadedCount;
    const endIndex = this.loadedCount + this.perPage;
    // 根据需要加载的数据范围从数据源中截取数据
    const newData = this.listData.slice(startIndex, endIndex);
    // 将新加载的数据添加到已有数据列表中
    this.showData = [...this.showData, ...newData];
    // 更新已加载数据的数量
    this.loadedCount = endIndex;
    this.loading = false
    this.showMessage = true
  },
  methods:{
    handleInfiniteOnLoad() {
      // 开始加载
      this.loading = true;

      // 模拟异步加载数据
      setTimeout(() => {
        const startIndex = this.loadedCount;
        const endIndex = this.loadedCount + this.perPage;

        // 根据需要加载的数据范围从数据源中截取数据
        const newData = this.listData.slice(startIndex, endIndex);

        // 将新加载的数据添加到已有数据列表中
        this.showData = [...this.showData, ...newData];

        // 更新已加载数据的数量
        this.loadedCount = endIndex;

        // 加载结束

        this.loading = false;

        if (newData.length < this.perPage) {
          // 如果新加载的数据少于 perPage 条，说明已经加载完全部数据
          if (this.showMessage){
            this.$message.info("已经到底了")
            this.showMessage = false
          }
          this.busy = true;
        } else {
          this.busy = false;
        }
      }, 1000);
    },
  }
}
</script>

<style scoped>
.demo-infinite-container {
  overflow: auto;
  height: 450px;
  padding-right: 10px;
  width: 100%;
}

.demo-loading-container {
  position: absolute;
  bottom: 40px;
  width: 100%;
  text-align: center;
}
</style>
