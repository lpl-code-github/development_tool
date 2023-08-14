<template>
  <div class="postman-test-gen">
    <div style="width: 48%;">
      <div class="text-button">
        <p class="my-title" style="margin-bottom: 10px!important;">
          <a-input placeholder="输入Response名称" addon-after="Response" v-model="responseName"/>
        </p>
        <a-button type="primary" @click="generateInterface" style="margin-top: 10px">
          生成Interface
        </a-button>
      </div>
      <Json @updateJsonData="getJsonData"></Json>
    </div>
    <div style="width: 48%">
      <div class="text-button">
        <p class="my-title" style="margin-bottom: 10px!important;">TypeScript Response Interface:</p>
        <a-button
            class="copy-btn code-data-copy"
            icon="copy"
            v-if="codeText!==''"
            style="margin-top: 10px"
            @click="copyMessage"
            data-clipboard-action="copy"
            :data-clipboard-text="codeText">
          复制
        </a-button>
      </div>
      <a-spin size="large" :spinning="loading">
         <Code :language-class="'lang-javascript'" :code-text="codeText"><a-spin /></Code>
      </a-spin>
    </div>
  </div>
</template>

<script>
import Code from "@/components/code/Code";
import Json from "@/components/code/Json";
import clipboard from "clipboard"; // 复制组件
export default {
  name: "TsInterfaceGenerator",
  components: {Json, Code},
  data(){
    return{
      jsonData:{
        hasJsonFlag: false
      },
      codeText:"",
      loading: false,
      copySuccess: false,
      responseName:''
    }
  },
  methods:{
    // 子组件的回调
    getJsonData(jsonData){
      this.jsonData = jsonData
    },
    // 生成Interface
    generateInterface(){
      if (!this.jsonData.hasJsonFlag){
        this.$message.warning("Json格式错误或为空，请检查")
        return
      }
      // if (this.responseName === ''){
      //   this.$message.warning("请输入Response的名称")
      //   return
      // }
      this.loading = true
      var jsonReq = this.jsonData.value
      this.$request.generateTsInterface(jsonReq,this.responseName).then(res=>{
        if (res.status === 200){
          this.codeText = res.data
          this.loading = false
        }else {
          this.loading = false
        }
      })
    },
    // 复制文本
    copyMessage() {
      let _this = this;
      if (_this.codeText===""){
        _this.$message.warn("没有可以复制的内容");
        return
      }
      _this.copySuccess = false;
      let clipboardObj = new clipboard(".code-data-copy");
      clipboardObj.on("success", function (e) {
        _this.copySuccess = true;
        _this.$message.success("已复制到剪贴板");
        clipboardObj.destroy(); // 销毁,避免多次点击重复出现
      });
      clipboardObj.on("error", function () {
        _this.$message.error("复制错误");
      });
    }
  },

}
</script>

<style scoped>
.postman-test-gen{
  display: flex;
  justify-content: space-around;
  overflow-y: hidden;
}
.text-button{
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.my-title{
  margin-bottom: -12px;
  margin-top: 20px;
  font-size: 17px
}
.example-test {
  text-align: center;
}
</style>
