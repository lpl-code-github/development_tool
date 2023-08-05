<template>
  <div>
    <div style="margin-top: 20px;display: flex;justify-content: space-between">

      <a-select
          show-search
          placeholder="选择Controller"
          option-filter-prop="children"
          style="width: 200px;"
          :filter-option="filterOption"
          @select="handleSelect"
          :key="componentKey"
          :value="existSelectController ? selectedController : undefined"
      >
        <a-select-option v-for="(item,index) in controllerLists" :key="index" :value="item">
          {{ item }}
        </a-select-option>
      </a-select>


      <a-popconfirm
          placement="leftTop"
          style="z-index: 99999!important;"
          title="编辑器中还有内容，确认获取默认模版吗？"
          ok-text="Yes"
          cancel-text="No"
          @confirm="confirm"
          @cancel="cancel"
          :disabled="buttonConfirmIsDisabled"
      >
        <a-button @click="getDefaultClick">获取默认模版</a-button>
      </a-popconfirm>
    </div>

    <a-spin size="large" :spinning="loading">
      <Markdown style="z-index: -1!important;" :doc-content="docContent"></Markdown>
    </a-spin>

  </div>
</template>

<script>
import Markdown from "@/components/code/Markdown";
import {getSlateDoc} from "@/api/request";

export default {
  name: 'SlateGenerator',
  components: {Markdown},
  data() {
    return {
      componentKey: 0,
      docContent: "",
      loading: false,
      buttonConfirmIsDisabled: true,
      controllerLists: [],
      selectedController: null,
      existSelectController: false
    }
  },
  created() {
    this.$bus.$on('markdownInput', (newVal) => {
      this.docContent = newVal
      if (newVal === "") {
        localStorage.removeItem("selected_controller")
      }
    });
    this.getControllerLists()

  },
  mounted() {
    var editDoc = localStorage.getItem("edit_doc");
    if (editDoc !== null && editDoc !== "") {
      this.docContent = editDoc

    }

    var selectedController = localStorage.getItem("selected_controller");
    if (selectedController !== null && selectedController !== "") {
      if (editDoc !== null && editDoc === "") {
        this.docContent = editDoc
        this.docContent = editDoc
        this.existSelectController = false
      } else {
        this.existSelectController = true
        this.selectedController = selectedController
      }
    }
  },
  methods: {
    getControllerLists() {
      var params = "?type=controller"
      this.$request.getFileLists(params).then(res => {
        if (res.status === 200) {
          this.controllerLists = res.data
        }
      })
    },
    getDefaultClick() {
      if (this.docContent !== "") {
        this.buttonConfirmIsDisabled = false
        return
      }
      this.existSelectController = false
      this.selectedController = ""
      this.buttonConfirmIsDisabled = true
      localStorage.removeItem("selected_controller");
      this.handleGetSlateDoc()
    },
    handleGetSlateDoc(controller) {
      this.loading = true
      var param = ""
      if (controller !== undefined) {
        param = "?controller=" + controller
      }
      this.$request.getSlateDoc(param).then(res => {
        if (res.status === 200) {
          this.docContent = res.data
        }
        this.loading = false
      })
    },
    confirm(e) {
      this.componentKey += 1
      this.existSelectController = false
      localStorage.removeItem("selected_controller")
      this.handleGetSlateDoc()
    },
    cancel(e) {
    },
    filterOption(input, option) {
      return (
          option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
      );
    },
    handleSelect(value, option) {
      localStorage.setItem("selected_controller", value)
      if (this.docContent !== "") {
        var confirm = this.$confirm({
          title: '提示',
          content: '编辑器中还有内容，确认生成新的文档吗？',
          okText: '确认',
          cancelText: '取消',
          onOk: () => { // 使用箭头函数来保持this的指向
            this.existSelectController = true
            this.selectedController = value
            this.handleGetSlateDoc(value)
            confirm.destroy();
          },
          onCancel() {
            confirm.destroy();
          },
        });
        return
      }
      this.existSelectController = true
      this.selectedController = value
      this.handleGetSlateDoc(value)
    }
  }
};
</script>
<style scoped>

.my-title {
  font-size: 15px
}
</style>
