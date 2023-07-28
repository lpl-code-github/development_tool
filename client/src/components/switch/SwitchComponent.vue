<template>
  <div class="my-switch">
    <a-tooltip placement="left">
      <template slot="title">
        {{ tooltipText }}
      </template>
      {{ switchText }}
    </a-tooltip>
    <a-switch v-model="flag" @click="handleSwitch">
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
      loading: false,
      checked: false
    };
  },
  created() {
    this.checked = this.flag
  },
  methods: {
    handleSwitch() {
      var checked = this.checked;
      this.$request.switchApi(this.type, checked).then(res => {
        if (res.status !== 200) {
          this.checked = !checked;
        }
      });
    }
  }
};
</script>

<style scoped>

</style>
