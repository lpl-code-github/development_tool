<template>
  <div>
    <vue-json-editor
        v-model="jsonText"
        :mode="'code'"
        lang="zh"
        @json-change="onJsonChange"
        @json-save="onJsonSave"
        @has-error="onError">
    </vue-json-editor>
  </div>
</template>

<script>
import vueJsonEditor from 'vue-json-editor'

export default {
  name: "Json",
  data() {
    return {
      hasJsonFlag: true, // json是否验证通过
      jsonText: {}
    }
  },

  components: {
    vueJsonEditor
  },
  created() {
  },
  methods: {
    onJsonChange(value) {
      // 实时保存
      this.onJsonSave(value)
    },
    onJsonSave(value) {
      this.hasJsonFlag = true
      this.$emit('updateJsonData', {
        value:value,
        hasJsonFlag:this.hasJsonFlag
      })
    },
    onError(value) {
      this.hasJsonFlag = false
      this.$emit('updateJsonData', {
        value:value,
        hasJsonFlag:this.hasJsonFlag
      })
    }
  }
}
</script>
<style scoped>
/deep/.jsoneditor-vue{
  height: 60vh
}
/deep/.jsoneditor-poweredBy{
  display: none;
}
/deep/ .jsoneditor{
  border: none;
}
</style>
