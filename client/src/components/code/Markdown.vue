<template>
  <div>
    <mavon-editor
        ref="md"
        v-model="content"
        :ishljs="false"
        @save="saveDoc"
        :toolbars="toolbars"
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
      toolbars: {
        bold: true, // 粗体
        italic: true, // 斜体
        header: true, // 标题
        underline: true, // 下划线
        strikethrough: true, // 中划线
        mark: true, // 标记
        superscript: true, // 上角标
        subscript: true, // 下角标
        quote: true, // 引用
        ol: true, // 有序列表
        ul: true, // 无序列表
        link: true, // 链接

        code: true, // code
        table: true, // 表格

        readmodel: true, // 沉浸式阅读
        htmlcode: true, // 展示html源码
        help: true, // 帮助
        /* 1.3.5 */
        undo: true, // 上一步
        redo: true, // 下一步
        trash: true, // 清空
        save: true, // 保存（触发events中的save事件）
        /* 1.4.2 */
        navigation: true, // 导航目录
        /* 2.1.8 */
        alignleft: true, // 左对齐
        aligncenter: true, // 居中
        alignright: true, // 右对齐
        /* 2.2.1 */
        subfield: true, // 单双栏模式
        // preview: true, // 预览
      }
    }
  },
  methods:{
    // 所有操作都会被解析重新渲染
    change(value, render) {
      // render 为 markdown 解析后的结果[html]
      // this.html = render
      // 保存
      localStorage.setItem("edit_doc",value)
    },
    saveDoc(e){
      this.saveTextToFile(e)
    },
    saveTextToFile(text) {
      const currentDate = new Date();
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');
      const hours = String(currentDate.getHours()).padStart(2, '0');
      const minutes = String(currentDate.getMinutes()).padStart(2, '0');
      const seconds = String(currentDate.getSeconds()).padStart(2, '0');

      const fileName = `${year}${month}${day}${hours}${minutes}${seconds}.md`;
      const file = new Blob([text], { type: 'text/plain' });

      const a = document.createElement('a');
      const url = URL.createObjectURL(file);

      a.href = url;
      a.download = fileName;
      a.click();

      URL.revokeObjectURL(url);
    }
  }
}
</script>

<style scoped>
@import 'mavon-editor/dist/css/index.css';
</style>
