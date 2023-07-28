<template>
  <div class="header">
    <a-layout-header style="background: #fff; padding: 0;display: flex;justify-content: space-between;align-items: center;padding: 0 20px">
      <div style="float: right">
        <span style="font-size: medium;font-weight: bolder">RISKID开发者工具</span>
      </div>
      <div style="float: left">
        <a-button icon="setting" :size="buttonSize" style="margin-right: 20px" :loading="loadingUpdateSystem">
          更新本系统
        </a-button>

        <!--快捷开关-->
        <a-popover title="开关" placement="topLeft" trigger="click" @click="handleQuickSwitch" :visible="popoverIsShow">
          <template slot="content">
            <SwitchComponent
                v-for="(item,index) in switchComponentData"
                :key="index"
                :type="item.type"
                :switch-text="item.switchText"
                :tooltip-text="item.tooltipText"
                :flag = "item.checked"
            />
          </template>
          <a-button icon="setting" :size="buttonSize">
            快捷开关
          </a-button>
        </a-popover>
      </div>

    </a-layout-header>


  </div>
</template>

<script>
import SwitchComponent from "@/components/switch/SwitchComponent";

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'MyHeader',
  components: {SwitchComponent},
  props: {
    msg: String
  },
  data() {
    return {
      buttonSize: "small",
      loadingUpdateSystem: true,
      popoverIsShow: false,
      switchComponentData: [
        {
          tooltipText: "此开关打开，后端API将返回Symfony的报错信息，否则返回json的报错信息",
          switchText: "后台报错信息",
          type: "error_message",
          checked: false
        },
        {
          tooltipText: "此开关打开，后端代码的.env文件中将修改环境遍历APP_ENV=test，否则为dev",
          switchText: "切换测试模式",
          type: "evn",
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
  methods: {
    async handleQuickSwitch() {
      await this.$request.switchStatus().then(res => {
        if (res.status !== 200) {
          this.popoverIsShow = false
        }else {
          this.popoverIsShow = true
        }

        var response = {
          data:[
            {
              type: "error_message",
              checked: false
            },
            {
              type: "evn",
              checked: true
            },
            {
              type: "back_api",
              checked: true
            }
          ]
        }
        this.switchComponentData.forEach(componentDataItem => {
          const responseItem = response.data.find(item => item.type === componentDataItem.type);
          if (responseItem) {
            componentDataItem.checked = responseItem.checked;
          }
        });
      })
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
