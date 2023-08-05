<template>
  <div class="my-code">
     <pre v-highlight style="margin: 0!important;">
        <code style="height: 60vh" class="lang-javascript" v-text="codeText"></code>
    </pre>
  </div>
</template>

<script>

export default {
  props:{
    codeText: {
      type: String,
      required: true
    },
  },
  watch:{
    codeText:{
      handler: function (newVal,oldVal) {
        this.codeText = newVal
        this.updateSourceCode(newVal)
      },
      // 深度观察监听
      deep: true
    }
  },
  name: "Code",
  data(){
    return{
    }
  },
  methods:{
    updateSourceCode(code) {
      this.codeText = code
    },
  }
}
</script>

<style scoped>
@import 'highlight.js/styles/atom-one-dark.css';
.my-code{
  margin-top: -20px!important;
  overflow-y: visible;
}
</style>
