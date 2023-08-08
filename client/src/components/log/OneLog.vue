<template>
  <div>
    <a-modal
        v-model="visible"
        :title="date"
        on-ok="handleOk"
        :footer="null"
        style="height: 300px"
        :afterClose="afterClose"
        :maskClosable="false">
      <InfiniteScroll :key="componentKey" :source-data="listData"></InfiniteScroll>
    </a-modal>
  </div>
</template>
<script>
import InfiniteScroll from "@/components/log/InfiniteScroll";
export default {
  name: "OneLog",
  components: {InfiniteScroll},
  props: {
    openFlag: {
      type: Boolean,
      required: true
    },
    date: {
      type: String,
      required: true
    },
    logData: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      componentKey:0,
      visible: false,
      listData: [],
    };
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
    openFlag: {
      handler: function (newVal, oldVal) {
        this.componentKey += 1
        this.visible = newVal
      },
      // 深度观察监听
      deep: true
    },
    logData: {
      handler: function (newVal, oldVal) {
        this.listData = newVal
      },
      // 深度观察监听
      deep: true
    }
  },
  mounted() {
    this.visible = this.openFlag
  },
  methods: {
    afterClose(e){
      this.$emit('updateModelStatus', false)
    },
  }
};
</script>
<style scoped>
/deep/ .ant-modal-body {
  height: 500px;
  padding-right: 10px;
  overflow-y: auto;
}
</style>
