<template>
  <div>
    <a-select
        show-search
        placeholder="选择Entity"
        option-filter-prop="children"
        style="width: 200px;margin: 20px 20px 20px 0"
        :filter-option="filterOption"
        @change="handleChange"
    >
      <a-select-option v-for="(item,index) in entityLists" :key="index" :value="item">
        {{item}}
      </a-select-option>
    </a-select>


    <a-radio-group :value="selectedType" size="default" @change="handleChangeType" buttonStyle="solid" v-model="selectedType">
      <a-radio-button value="dto">
        Dto
      </a-radio-button>
      <a-radio-button value="factory">
        Factory
      </a-radio-button>
      <a-radio-button value="controller">
        Controller
      </a-radio-button>
      <a-radio-button value="service">
        Service
      </a-radio-button>
    </a-radio-group>

    <a-button
        class="copy-btn code-data-copy"
        icon="copy"
        v-if="codeText!==''"
        style="float: right;margin-top: 20px"
        @click="copyMessage"
        data-clipboard-action="copy"
        :data-clipboard-text="codeText">
      复制
    </a-button>

    <a-spin size="large" :spinning="loading">
      <Code :language-class="'language-php'" :code-text="codeText"><a-spin /></Code>
    </a-spin>
  </div>
</template>

<script>
import Code from "@/components/code/Code";
import clipboard from "clipboard";
import {getFileLists} from "@/api/request";

export default {
  name: "CodeGenerator",
  components: {Code},
  data(){
    return{
      entityLists:[],
      selectEntity:"",
      selectedType:"dto",
      codeText: "",
      copySuccess: false,
      loading:false,
    }
  },
  created() {
    this.getEntityLists()
  },
  methods: {
    // entity选择器发生变化时触发
    handleChange(value) {
      this.loading = true // 打开code显示 的loading
      this.selectEntity = value
      this.$request.generateCode(this.selectEntity,this.selectedType).then(res=>{
        if (res.status === 200){
          this.codeText = res.data
        }
        this.loading = false
      })
    },
    // entity选择器搜索
    filterOption(input, option) {
      return (
          option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
      );
    },

    /**
     * 一些请求事件
     */
    // 获取r1所有的entity列表
    getEntityLists(){
      var params = "?type=entity"
      this.$request.getFileLists(params).then(res=>{
        if (res.status === 200){
          this.entityLists = res.data
        }
      })
    },
    // radio发生变化 也就是需要生成代码类型发生变化时调用
    handleChangeType(e) {
      this.loading = true // 打开code显示 的loading

      this.selectedType = e.target.value
      if (this.selectEntity !== ""){
        this.$request.generateCode(this.selectEntity,this.selectedType).then(res=>{
          if (res.status === 200){
            this.codeText = res.data
            this.loading = false
          }else {
            this.loading =false
          }
        })
      }else {
        this.$message.warning("请先选择实体类")
        this.loading = false
      }
    },

    // 复制功能
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
/deep/ .ant-select-selection--single{
  height: 35px;
}
</style>
