<template>
    <div>
      <a-drawer
          title="R1项目概览"
          placement="right"
          :closable="false"
          :visible="visible"
          :after-visible-change="afterVisibleChange"
          @close="onClose"
      >
        <div class="api-info">
          <a-statistic st title="Api总数" suffix="个" :value="apiInfo && apiInfo.sum.total_num" style="width: 20%" />
          <div style="width: 80%;height: 100%">
            <div id="main" :style="echartsStyles"></div>
          </div>
        </div>
      </a-drawer>
    </div>
</template>

<script>

export default {
  name: "Statistics",
  props: {
    openFlag: {
      type: Boolean,
      required: true
    },
  },
  data(){
    return{
      echartsStyles: {
        width: '100%',
        height: "100%"
      },
      myChart: null,
      visible: false,
      apiInfo: null,
      apiCountInfo:[
        { value: 0, name: 'Demo' }
      ]
    }
  },
  watch:{
    openFlag:{
      handler: function (newVal, oldVal) {
        if (newVal === true){
          this.getApiInfo()
          this.echarts()
        }else {
          this.visible = newVal
        }

      },
      // 深度观察监听
      deep: true
    }
  },
  mounted() {
    this.visible = this.openFlag
    // this.getApiInfo()
  },
  methods:{
    afterVisibleChange(val) {
      console.log('visible', val);
    },
    onClose() {
      this.$emit('updateDrawerStatus', false)
    },
    async getApiInfo() {
       await this.$request.getR1Api().then(res => {
         if (res.status !== 200) {
           this.$emit('updateDrawerStatus', false)
         }else {
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
         }
       })
      this.echarts()
     },
    echarts() {
      if (this.myChart != null && this.myChart !== "" && this.myChart !== undefined) {
        this.myChart.dispose();
      }
      this.myChart = this.$echarts.init(document.getElementById('main'));

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

      // 让图表跟随屏幕自动的去适应
      window.addEventListener('resize', () => {
        this.myChart.resize()
      })
    },
  }
}
</script>

<style scoped>
.statistics{
  /*width: 100px!important;*/
}
/deep/ .ant-drawer-content-wrapper{
  width: 600px!important;
  right: 0!important;
}
.api-info{
  display: flex;
  justify-content: space-around;
  align-items: center;
  height: 200px;
}
</style>
