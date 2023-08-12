<template>
  <div class="header">
    <a-layout-header style="background: #fff; padding: 0;display: flex;justify-content: space-between;align-items: center;padding: 0 20px">
      <div style="float: right">
        <span style="font-size: medium;font-weight: bolder">RISKID开发者工具</span>
      </div>
      <div style="float: left">
        <a-button icon="setting" :size="buttonSize" style="margin-right: 20px" :loading="loadingUpdateSystem" @click="handleOpenDrawer">
          R1概览
        </a-button>

        <!--快捷开关-->
        <a-popover ref="popover" title="开关" placement="topLeft" trigger="click" @click="handleQuickSwitch" :visible="popoverIsShow">
          <template slot="content">
            <SwitchComponent
                v-for="(item,index) in switchComponentData"
                :key="index"
                :type="item.type"
                :switch-text="item.switchText"
                :tooltip-text="item.tooltipText"
                :flag = "item.checked"
            />
            <a-button type="primary" style="margin:10px 0" block @click="handleClearCache">
              R1清除缓存
            </a-button>
          </template>
          <a-button icon="setting" :size="buttonSize">
            快捷开关
          </a-button>
        </a-popover>
      </div>

    </a-layout-header>

    <Statistics @updateDrawerStatus="getDrawerStatus" :open-flag="openDrawer"></Statistics>
  </div>
</template>

<script>
import SwitchComponent from "@/components/switch/SwitchComponent";
import Statistics from "@/components/preview/Statistics";

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'MyHeader',
  components: {Statistics, SwitchComponent},
  props: {
    msg: String
  },
  data() {
    return {
      openDrawer: false,
      buttonSize: "small",
      loadingUpdateSystem: false,
      popoverIsShow: false,
      switchComponentData: [
        {
          tooltipText: "此开关打开，开发环境下，后端API将返回Symfony的报错信息，否则返回json的报错信息",
          switchText: "开发报错信息",
          type: "dev_env_error_message",
          checked: false
        },
        {
          tooltipText: "此开关打开，测试环境下，后端API将返回Symfony的报错信息，否则返回json的报错信息",
          switchText: "测试报错信息",
          type: "test_env_error_message",
          checked: false
        },
        {
          tooltipText: "此开关打开，后端代码的.env文件中将修改环境遍历APP_ENV=test，否则为dev",
          switchText: "切换测试模式",
          type: "test_env",
          checked: false
        },
        {
          tooltipText: "此开关打开，后端将增加供postman使用的/backup和/reduction端点",
          switchText: "导入备份端点",
          type: "back_api",
          checked: false
        }
      ]
    }
  },
  mounted() {
    document.addEventListener('click', this.handleOutsideClick);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleOutsideClick);
  },
  methods: {
    /*
      一些请求事件
     */
    // 开关打开与关闭
    async handleQuickSwitch() {
      await this.$request.switchStatus().then(res => {
        if (res.status !== 200) {
          this.popoverIsShow = false
        }else {
          this.popoverIsShow = true
          var data = res.data.data
          var temp = this.switchComponentData
          temp.forEach(componentDataItem => {
            const responseItem = data.find(item => item.type === componentDataItem.type);
            if (responseItem) {
              componentDataItem.checked = responseItem.checked;
            }
          });
          this.switchComponentData = temp
          console.log(this.switchComponentData)
        }
      })
    },
    // 清除缓存
    handleClearCache(){
      var message = this.$message
      var loadingMessage = message.loading('RISKID环境中正在执行php bin/console cache:clear，您可以继续进行其他操作', 0)
      this.$request.clearR1Cache().then(res=>{
        if (res.status !== 200) {
          this.popoverIsShow = false
        }else {
          if (res.data.data.handle === true){
            setTimeout(loadingMessage, 0);
            message.success('清除缓存执行成功', 2.5)
          }else {
            setTimeout(loadingMessage, 0);
            message.success('执行失败，请去手动执行命令php bin/console cache:clear', 2.5)
          }
        }
        this.$bus.$emit('requestCompleted');
      })
    },

    // 打开抽屉
    handleOpenDrawer(){
      this.openDrawer = true
    },
    // 获取抽屉状态
    getDrawerStatus(status){
      this.openDrawer = status
    },

    // 监听手动关闭popover
    handleOutsideClick(event) {
      if (this.popoverIsShow  && !this.$refs.popover.$el.contains(event.target)) {
        this.popoverIsShow = false
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
}
</style>
