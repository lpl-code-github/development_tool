<template>
  <div class="my-switch">
    后台报错信息 <a-switch :loading="loading" />
    <br />
  </div>
</template>

<script>
export default {
  name: "ErrorMessageSwitch",
  data(){
    return{
      loading: false
    }
  }
}
</script>

<style scoped>

</style>
