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
      jsonText: ""
    }
  },

  components: {
    vueJsonEditor
  },
  created() {
    if(this.jsonText == ''){
      this.jsonText = {
        "example_data": [
          {
            "id": "2",
            "route": {
              "properties":{
                "id":1,
                "path":[
                  {
                    "id":1,
                    "name":"path"
                  }
                ]
              },
              "info":{
                "name":{
                  "full_name":"test",
                  "last_name":"te"
                }
              }
            },
            "created_at": "2023-07-30 19:43:04",
            "updated_at": "2023-07-30 19:43:04"
          }
        ],
        "token_user": {
          "id": "1",
          "surname": "Administrator",
          "forename": "RISKID",
          "first_name": "RISKID",
          "full_name": "RISKID Administrator",
          "name": "Administrator",
          "token": "a8680ad1e02877f8962171e85a095b6b2c600855",
          "email": "administrator@riskid.nl",
          "type": "admin",
          "login_state": "1",
          "properties": {
            "lang": "en"
          },
          "hidden_folders": null,
          "active": "1",
          "created_at": "2023-07-19 14:27:22",
          "updated_at": "2023-07-19 14:34:03",
          "owner_type": "u",
          "project_order": [
            [
              "432"
            ]
          ],
          "is_observing": false
        }
      }
      this.$emit('updateJsonData', {
        value:this.jsonText,
        hasJsonFlag:true
      })
    }
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
