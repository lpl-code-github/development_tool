<template>
  <div class="my-switch">
    导入备份接口 <a-switch :loading="loading" />
    <br />
  </div>
</template>

<script>
export default {
  name: "TestControllerSwitch",
  data(){
    return{
      loading: false
    }
  }
}
</script>

<style scoped>

</style>
