<template>
  <div>
    <mavon-editor
        ref="md"
        v-model="content"
        style="height: 60vh;margin: 20px 0;z-index: 1"
        @change="change"
    />
  </div>
</template>

<script>
export default {
  name: "Markdown",
  props:["docContent"],
  watch: {
    content: {
      deep: true,
      handler(newVal, oldVal) {
        this.$bus.$emit('markdownInput',newVal);
      }
    },
    docContent: {
      deep: true,
      handler(newVal, oldVal) {
        this.content = newVal
      }
    }
  },
  data(){
    return{
      content:"",
      html: "",
    }
  },
  methods:{
    // 所有操作都会被解析重新渲染
    change(value, render) {
      // render 为 markdown 解析后的结果[html]
      // this.html = render
      // 保存
      sessionStorage.setItem("edit_doc",value)
    },
  }
}
</script>

<style scoped>
@import 'mavon-editor/dist/css/index.css';
</style>
