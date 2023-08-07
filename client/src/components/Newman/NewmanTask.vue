<template>
  <div>
    <a-modal
        v-model="visible"
        title="当前测试任务"
        on-ok="handleOk"
        :footer="null"
        :afterClose="afterClose"
        :maskClosable="false">

      <div v-if="taskName !=null && taskName !== undefined && taskName !== ''">
        <span style="font-weight: bolder">任务名称：</span> <span> {{ taskName }}</span>

        <div style="margin-top: 30px">
          <a-timeline >
            <a-timeline-item
                :pendingDot="true"
                :color="computedColor(item)"
                v-for="(item,index) in taskLog"
                :key="item.index"
               >
              <a-icon v-if="item.flag && item.status === ''" slot="dot" type="loading" style="font-size: 16px;" />
                {{ item.name }} &nbsp; <span v-if="item.status !== ''"> 状态：{{item.status}}</span>
            </a-timeline-item>
          </a-timeline>
        </div>
      </div>
      <a-empty v-else/>
    </a-modal>
  </div>
</template>
<script>
export default {
  name: "NewmanTask",
  props: {
    openFlag: {
      type: Boolean,
      required: true
    },
    taskName: {
      type: String,
      required: true
    },
    taskLog: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      loading: false,
      visible: false,
    };
  },
  computed: {
    computedColor() {
      return function(item) {
        if (item.flag && item.status === "成功") {
          return 'green';
        }
        if (!item.flag && item.status === ""){
          return 'gray'
        }
        return 'blue';
      }
    }
  },
  watch: {
    openFlag: {
      handler: function (newVal, oldVal) {
        console.log(newVal)
        this.visible = newVal
      },
      // 深度观察监听
      deep: true
    }
  },
  mounted() {
    console.log(this.taskName)
    this.visible = this.openFlag
  },
  methods: {
    afterClose(e) {
      this.$emit('updateModelStatus', false)
    },
  }
};
</script>
<style scoped>
/deep/ .ant-modal-body {
  max-height: 400px;
  overflow-y: auto;
}
</style>
