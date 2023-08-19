<template>
  <div class="my-switch">
    <a-tooltip placement="left">
      <template slot="title">
        {{ tooltipText }}
      </template>
      {{ switchText }}
    </a-tooltip>
    <a-switch :loading="loading" v-model="flag" @click="handleSwitch">
      <a-icon slot="checkedChildren" type="check" />
      <a-icon slot="unCheckedChildren" type="close" />
    </a-switch>
    <br />
  </div>
</template>

<script>
export default {
  name: "SwitchComponent",
  props: {
    loading: {
      type: Boolean,
      required: true
    },
    flag: {
      type: Boolean,
      required: true
    },
    tooltipText: {
      type: String,
      required: true
    },
    switchText: {
      type: String,
      required: true
    },
    type: {
      type: String,
      required: true
    }
  },
  data() {
    return {
    };
  },
  methods: {
    handleSwitch() {
      var checked = this.flag;
      this.$request.switchApi(this.type, checked).then(res => {
        if (res.status !== 200) {
          this.flag = !checked;
        }else {
          var status = checked ? "已打开" : "已关闭"
          if (res.data.data.handle){
            this.$message.success(this.switchText+'操作成功：' + status, 3)
          }else {
            this.$message.error(this.switchText+'操作失败：' + status, 3)
          }
        }
        this.$bus.$emit('requestCompleted');
      });
    }
  }
};
</script>

<style scoped>

</style>
