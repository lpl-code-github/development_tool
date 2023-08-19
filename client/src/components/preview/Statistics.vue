<template>
  <div>
    <a-drawer
        ref="drawer"
        title="R1项目概览"
        placement="right"
        :closable="false"
        :visible="visible"
        :after-visible-change="afterVisibleChange"
        @close="onClose"
    >
      <div class="api-info">
        <a-statistic v-if="!showApiInfoSkeleton" st title="Api总数" suffix="个" :value="apiInfo && apiInfo.sum.total_num" style="width: 20%"/>
        <div v-if="showApiInfoSkeleton" style="width: 20%">
          <a-skeleton  style="width: 70px" active :paragraph="false"/>
          <a-skeleton  style="width: 70px" active :paragraph="false"/>
        </div>

        <div style="width: 80%;height: 100%">
          <div id="main" ref="main" :style="echartsStyles"></div>
        </div>
      </div>

      <div class="hardware-info">
        <div style="color: rgba(0, 0, 0, 0.45);margin-bottom: 10px">容器信息</div>

        <a-row :gutter="16">
          <a-col :span="12">
            <a-card>
              <a-statistic
                  v-if="!showStatisticSkeleton"
                  title="CPU使用率"
                  :value="systemStatus.cpu_usage"
                  suffix="%"
                  :value-style="{ color: computedColor(systemStatus.cpu_usage) }"
                  style="margin-right: 50px"
              >
              </a-statistic>
              <a-skeleton v-if="showStatisticSkeleton" active :paragraph="false"/>
              <a-skeleton v-if="showStatisticSkeleton" active :paragraph="false"/>
            </a-card>
          </a-col>
          <a-col :span="12">
            <a-card>
              <a-statistic
                  v-if="!showStatisticSkeleton"
                  title="内存使用率"
                  :value="systemStatus.memory_usage"
                  suffix="%"
                  class="demo-class"
                  :value-style="{ color: computedColor(systemStatus.memory_usage) }"
              >
              </a-statistic>
              <a-skeleton v-if="showStatisticSkeleton" active :paragraph="false"/>
              <a-skeleton v-if="showStatisticSkeleton" active :paragraph="false"/>
            </a-card>
          </a-col>
        </a-row>
      </div>
      <div style="color: rgba(0, 0, 0, 0.45);margin: 30px 0 -10px 0">进程信息Top5</div>
      <pre v-if="!showStatisticSkeleton" v-myHighlight style="margin: 0!important;padding: 0!important;">
        <code style="height: 100%;margin: 0!important;padding: 0!important;" class="language-pf" v-text="top5ps"></code>
      </pre>
      <a-skeleton v-if="showStatisticSkeleton" active :paragraph="false"/>
      <a-skeleton v-if="showStatisticSkeleton" active :paragraph="false"/>
      <a-skeleton v-if="showStatisticSkeleton" active :paragraph="false"/>
    </a-drawer>
  </div>
</template>

<script>

import Code from "@/components/code/Code";

export default {
  name: "Statistics",
  components: {Code},
  props: {
    openFlag: {
      type: Boolean,
      required: true
    },
    systemStatus: {
      type: Object,
      required: true
    },
    top5ps: {
      type: String,
      required: true
    },
    statisticLoading: {
      type: Boolean,
      required: true
    },
  },
  data() {
    return {
      echartsStyles: {
        width: '100%',
        height: "100%"
      },
      myChart: null,
      visible: false,
      apiInfo: null,
      apiCountInfo: [
        {value: 0, name: 'Demo'}
      ],
      showStatisticSkeleton: false,
      showApiInfoSkeleton: false,
    }
  },
  watch: {
    openFlag: {
      handler: function (newVal, oldVal) {
        this.visible = newVal
        if (newVal === true) {
          this.getApiInfo()
        }
      },
      deep: true
    },
    statisticLoading: {
      handler: function (newVal, oldVal) {
        this.showStatisticSkeleton = newVal
      },
      deep: true
    },
  },
  computed: {
    // 对cpu、内存占用率值判断 设置对应的颜色
    computedColor() {
      return function (item) {
        if (item <= 30) {
          return '#139606';
        }
        if (item > 30 && item <= 80) {
          return '#e55013'
        }
        return '#cf1322';
      }
    }
  },
  mounted() {
    this.visible = this.openFlag
    this.showStatisticSkeleton = true
    // 监听抽屉的打开事件
    this.$nextTick(() => {
      this.$refs.drawer.$on("afterVisibleChange", (visible) => {
        if (visible) {
          this.getApiInfo()
        }
      })
    })
  },
  methods: {
    // 抽屉关闭
    onClose() {
      // 向父组件发送状态
      this.$emit('updateDrawerStatus', false)
    },

    /*
      一些请求事件
     */
    // 获取api信息并打开渲染echarts图表
    getApiInfo() {
      this.showApiInfoSkeleton = true

      if (this.myChart != null && this.myChart !== "" && this.myChart !== undefined) {
        this.myChart.dispose();
      }
      // 延迟获取加载
      setTimeout(() => {
        this.myChart = this.$echarts.init(document.getElementById('main'));
        this.myChart.showLoading({
          text: 'loading',
          color: '#c23531',
          textColor: '#000',
          maskColor: 'rgba(255, 255, 255, 0.8)',
          zlevel: 0,
        });
        this.myChart.setOption({
          tooltip: {
            trigger: 'item'
          },
          series: [
            {
              name: 'Access From',
              type: 'pie',
              radius: ['40%', '70%'],
              // adjust the start angle
              startAngle: 180,
              label: {
                show: true,
                formatter(param) {
                  // correct the percentage
                  return param.name + ' (' + param.percent + '%)';
                }
              }
            }
          ]
        })
        this.$request.getR1Api().then(res => {
          if (res.status !== 200) {
            this.$message.error("接口信息获取失败");
            // this.$emit('updateDrawerStatus', false)
            this.showApiInfoSkeleton = true
          } else {
            var data = res.data.data;
            this.visible = true
            this.apiInfo = data

            var temp = [];
            for (var key in data.sum.detail) {
              var newObj = {
                value: data.sum.detail[key],
                name: key
              };
              temp.push(newObj);
            }
            this.apiCountInfo = temp

            this.showApiInfoSkeleton = false

            this.echartsSetOption()
          }
        })
      }, 500)
      this.$request.getR1Api().then(res => {
        if (res.status !== 200) {
          this.$message.error("接口信息获取失败");
          // this.$emit('updateDrawerStatus', false)
          this.showApiInfoSkeleton = true
        } else {
          var data = res.data.data;
          this.visible = true
          this.apiInfo = data

          var temp = [];
          for (var key in data.sum.detail) {
            var newObj = {
              value: data.sum.detail[key],
              name: key
            };
            temp.push(newObj);
          }
          this.apiCountInfo = temp

          this.showApiInfoSkeleton = false

          this.echartsSetOption()
        }
      })
    },

    echartsSetOption() {
      this.myChart.setOption({
        tooltip: {
          trigger: 'item'
        },
        series: [
          {
            name: 'Access From',
            type: 'pie',
            radius: ['40%', '70%'],
            // adjust the start angle
            startAngle: 180,
            label: {
              show: true,
              formatter(param) {
                // correct the percentage
                return param.name + ' (' + param.percent + '%)';
              }
            },
            data: this.apiCountInfo
          }
        ]
      })
      setTimeout(() => {
        this.myChart.hideLoading();
      },500)
      // 让图表跟随屏幕自动的去适应
      window.addEventListener('resize', () => {
        this.myChart.resize()
      })
    },

    afterVisibleChange(val) {
      // console.log('visible', val);
    },
  }
}
</script>

<style scoped>
.statistics {
  /*width: 100px!important;*/
}

/deep/ .ant-drawer-content-wrapper {
  width: 750px !important;
  right: 0 !important;
}

.api-info {
  display: flex;
  justify-content: space-around;
  align-items: center;
  height: 200px;
}

.hardware-info {
  margin-top: 50px;
}
/deep/ .ant-skeleton.ant-skeleton-active .ant-skeleton-avatar{
  height: 150px;
  width: 150px;

}
/deep/.ant-skeleton-header{
  display: flex;
  justify-content: center;
}
</style>
