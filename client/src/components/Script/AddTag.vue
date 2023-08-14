<template>
  <div>
    <a-modal v-model="visible"
             ref="tagForm"
             title="添加一个新的标签"
             ok-text="确认"
             cancel-text="取消"
             action=""
             :maskClosable="false"
             :afterClose="initTagFormData"
             @ok="addTag"
    >

      <a-form-model
          ref="ruleForm"
          :model="newTag"
          :rules="childrenModelRules"
          :label-col="{ span: 3 }"
          :wrapper-col="{ span: 20 }"
      >
        <a-form-model-item ref="name" label="内容" prop="name">
          <a-input v-model="newTag.name"/>
        </a-form-model-item>
        <a-form-model-item label="颜色" prop="color">
          <a-input type="color" v-model="newTag.color"/>
        </a-form-model-item>
      </a-form-model>
    </a-modal>
  </div>
</template>
<script>

export default {
  name: "AddTag",
  props: {
    openFlag: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      loading: false, // modal按钮的loading
      visible: false, // modal可见开关
      childrenModelRules: {
        name: [
          {required: true, message: '请输入标签内容', trigger: 'blur'},
          {min: 1, max: 20, message: '长度必须在 1 ～ 20', trigger: 'blur'},
        ],
      },// 子modal的校验规则
      newTag: {
        name: "",
        color: '#61afd1'
      }, // 新标签的form modal中的默认值
    };
  },
  watch: {
    openFlag: {
      handler: function (newVal, oldVal) {
        this.visible = newVal
      },
      // 深度观察监听
      deep: true
    },
  },
  mounted() {
    this.visible = this.openFlag
  },
  methods: {
    // 添加一个新标签的请求
    addTag() {
      this.$refs.ruleForm.validate(valid => {
        if (valid) {
          var data = {
            data: this.newTag
          }
          this.$request.postTags(data).then(res => {
            if (res.status === 200) {
              this.$message.success("添加标签成功")
              // 请求成功后 将新的数据添加到tags 并关闭modal
              this.$emit('updateModelStatus', false)
              this.$emit('submit', res.data.data)
            } else {
              this.$message.error("添加标签失败")
            }
          })
          this.initTagFormData()
          this.childrenModelVisible = false;
        } else {
          return false
        }
      });
    },
    // 重置添加新标签的子modal
    initTagFormData() {
      this.$refs.ruleForm.resetFields();
    },

    /*
      向父组件传值：组件状态
     */
    handleCancel(e) {
      this.initTagFormData()
      this.$emit('updateModelStatus', false)
    },
    afterClose(e) {
      this.initTagFormData()
      this.$emit('updateModelStatus', false)
    },
  },
};
</script>
<style scoped>
.color-box {
  width: 12px;
  height: 12px;
  align-items: center;
  margin: 0 5px 0 0;
  display: inline-block;
}

/deep/ .anticon .anticon-exclamation-circle {
  display: none !important;
}
/deep/ .ant-modal-body{
  max-height: 400px;
  overflow-y: auto;
}

.dynamic-delete-button {
  cursor: pointer;
  position: relative;
  top: 4px;
  font-size: 24px;
  color: #999;
  transition: all 0.3s;
}
.dynamic-delete-button:hover {
  color: #777;
}
.dynamic-delete-button[disabled] {
  cursor: not-allowed;
  opacity: 0.5;
}

</style>
