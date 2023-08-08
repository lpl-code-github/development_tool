<template>
  <div>
    <a-modal v-model="visible" title="运行postman测试" on-ok="handleOk" :afterClose="afterClose" :maskClosable="false">
      <template slot="footer">
        <a-button key="back" @click="handleCancel">
          取消
        </a-button>
        <a-button key="submit" type="primary" :loading="loading" @click="handleOk">
          提交
        </a-button>
      </template>
      <a-form-model
          ref="ruleForm"
          :model="form"
          :rules="rules"
          :label-col="{ span: 3 }"
          :wrapper-col="{ span: 20}"
          style="width: 100%">
        <a-form-model-item ref="name" label="名称" prop="name">
          <a-input v-model="form.name"/>
        </a-form-model-item>
        <a-form-model-item label="描述" prop="description">
          <a-input v-model="form.description" rows="4" type="textarea"/>
        </a-form-model-item>

      </a-form-model>
    </a-modal>
  </div>
</template>
<script>
export default {
  name: "RunNewman",
  props: {
    openFlag: {
      type: Boolean,
      required: true
    },
  },
  data() {
    return {
      loading: false,
      visible: false,
      formLayout: 'horizontal',
      form: {
        name: '',
        description: '',
      },
      rules: {
        name: [
          {required: true, message: '请输入操作名称', trigger: 'blur'},
          {min: 5, max: 50, message: '长度必须在 5 ～ 50', trigger: 'blur'},
        ],
      },
    };
  },
  watch:{
    openFlag:{
      handler: function (newVal,oldVal) {
        console.log(newVal)
        this.visible = newVal
      },
      // 深度观察监听
      deep: true
    }
  },
  mounted() {
    this.visible = this.openFlag
  },
  methods: {
    handleOk(e) {
      this.loading = true;
      this.$refs.ruleForm.validate(valid => {
        if (valid) {
          this.$confirm({
            title: '在创建测试任务前需要和您确认',
            content: h => <div style="color:red;">您当前数据库是否符合测试环境?</div>,
            okText: '是的，创建任务',
            cancelText: '去导入数据库',
            onOk:()=> {
              var param = {
                data: this.form
              }
              this.$request.postNewmanTasks(param).then(res=>{
                if (res.status === 200){
                  this.$emit('newmanTask', res.data.data)
                  this.$message.success("创建测试任务成功，请到任务详情查看")
                }else {
                  this.$message.error("创建测试失败")
                }
              })
              setTimeout(() => {
                this.$emit('updateModelStatus', false)
                this.loading = false;
                this.initFormData()
              }, 2);
            },
            onCancel:()=>{
                this.$router.push("/backup")
            },
          })

        } else {
          this.loading = false
          return false
        }
      });
    },
    handleCancel(e) {
      this.$emit('updateModelStatus', false)
    },
    afterClose(e){
      this.$emit('updateModelStatus', false)
    },
    handleChange(info) {
      const status = info.file.status;
      if (status !== 'uploading') {
        console.log(info.file, info.fileList);
      }
      if (status === 'done') {
        this.$message.success(`${info.file.name} file uploaded successfully.`);
      } else if (status === 'error') {
        this.$message.error(`${info.file.name} file upload failed.`);
      }
    },
    initFormData() {
      this.$refs.ruleForm.resetFields();
    }
  },
};
</script>
<style scoped>
/deep/ .ant-modal-body{
  max-height: 400px;
  overflow-y: auto;
}
</style>
