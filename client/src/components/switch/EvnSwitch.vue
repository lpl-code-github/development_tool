<template>
  <div class="my-switch">
    切换测试模式 <a-switch :loading="loading" />
    <br />
  </div>
</template>

<script>
export default {
  name: "EvnSwitch",
  data(){
    return{
      loading: false
    }
  }
}
</script>

<style scoped>

</style>
