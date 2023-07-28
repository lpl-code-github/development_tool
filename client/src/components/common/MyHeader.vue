<template>
  <div class="header">
    <a-layout-header style="background: #fff; padding: 0;display: flex;justify-content: flex-end;align-items: center;" >
      <a-button  icon="setting" :size="buttonSize" style="margin-right: 20px" :loading="loadingUpdateSystem" >
        更新本系统
      </a-button>
      <a-button  icon="setting" :size="buttonSize" style="margin-right: 20px" @click="showDrawer">
        快捷开关
      </a-button>
    </a-layout-header>

    <div>
      <a-drawer
          title="快捷开关"
          placement="right"
          :closable="false"
          :visible="visible"
          :after-visible-change="afterVisibleChange"
          @close="onClose"
      >
        <ErrorMessageSwitch></ErrorMessageSwitch>
        <EvnSwitch></EvnSwitch>
        <TestControllerSwitch></TestControllerSwitch>
      </a-drawer>
    </div>
  </div>
</template>

<script>
import ErrorMessageSwitch from "@/components/switch/ErrorMessageSwitch";
import EvnSwitch from "@/components/switch/EvnSwitch";
import TestControllerSwitch from "@/components/switch/TestControllerSwitch";
export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'MyHeader',
  components: {TestControllerSwitch, EvnSwitch, ErrorMessageSwitch},
  props: {
    msg: String
  },
  data(){
    return{
      buttonSize:"small",
      loadingUpdateSystem:true,
      visible: false,
    }
  },
  methods:{
    afterVisibleChange(val) {
      console.log('visible', val);
    },
    showDrawer() {
      this.visible = true;
    },
    onClose() {
      this.visible = false;
    },
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
