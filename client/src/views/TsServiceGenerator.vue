<template>
  <div>
    <a-select
        show-search
        placeholder="选择Controller"
        option-filter-prop="children"
        style="width: 200px;margin: 20px 20px 20px 0"
        :filter-option="filterOption"
        @change="handleChange"
    >
      <a-select-option v-for="(item,index) in controllerLists" :key="index" :value="item">
        {{item}}
      </a-select-option>
    </a-select>
    <a-button type="primary" @click="exportAllService">
      导出全部Endpoints Service.zip
    </a-button>


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
import fileDownload from "js-file-download";

export default {
  name: "TsServiceGenerator",
  components: {Code},
  data(){
    return{
      controllerLists:[],
      selectController:"",
      codeText: "",
      copySuccess: false,
      loading:false,
    }
  },
  created() {
    this.getControllerLists()
  },
  methods: {
    // entity选择器发生变化时触发
    handleChange(value) {
      this.loading = true // 打开code显示 的loading
      this.selectController = value
      var param = "?controller="+this.selectController;
      this.$request.generateTsServiceCode(param).then(res=>{
        if (res.status === 200){
          this.codeText = res.data
        }
        this.loading = false
      })
    },
    // controller选择器搜索
    filterOption(input, option) {
      return (
          option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
      );
    },
    exportAllService(){
      this.$confirm({
        title: '确认一键导出?',
        content: '需要和您确认的是，本工具所在数据库是否存在record_api_info表？表中是否有记录？如果没有请取消',
        okText: '确认导出',
        okType: 'danger',
        cancelText: '我再想想',
        cancelType: 'danger',
        onOk: () => {
          var message = this.$message
          var loadingMessage = message.loading('正在生成相关文件，您可以继续进行其他操作，但不要刷新页面', 0)

          this.$request.generateAllTsServiceCode().then(res => {
            if (res.status === 200) {
              setTimeout(loadingMessage, 0);
              message.success('导出成功', 2.5)
              const contentDisposition = res.headers['content-disposition'];
              const fileName = contentDisposition.split('filename=')[1].trim();

              fileDownload(res.data, fileName);
            } else {
              message.error('导出失败', 2.5)
              setTimeout(loadingMessage, 0);
            }
          })
        },
        onCancel() {
          console.log('Cancel');
        },
      });
    },
    /**
     * 一些请求事件
     */
    // 获取r1所有的controller列表
    getControllerLists() {
      var params = "?type=controller"
      this.$request.getFileLists(params).then(res => {
        if (res.status === 200) {
          this.controllerLists = res.data
        }
      })
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
