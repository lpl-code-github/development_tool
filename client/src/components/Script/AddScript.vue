<template>
  <div>
    <a-modal v-model="visible" title="Title" on-ok="handleOk" :afterClose="afterClose">
      <template slot="footer">
        <a-button key="back" @click="handleCancel">
          取消
        </a-button>
        <a-button key="submit" type="primary" :loading="loading" @click="handleOk">
          提交
        </a-button>
      </template>
      <a-form :form="form" :label-col="{ span: 3 }" :wrapper-col="{ span: 20}" style="width: 100%">
        <a-form-item label="名称">
          <a-input
              v-decorator="['name', { rules: [{ required: true, message: 'Please input your name!' }] }]"
          />
        </a-form-item>
        <a-form-model-item label="描述" prop="desc">
          <a-input  v-model="form.desc" type="textarea" />
        </a-form-model-item>


      </a-form>
      <div style="margin: auto;width: 93%">
        <a-upload-dragger
            :directory="false"
            name="file"
            :multiple="false"
            action="https://www.mocky.io/v2/5cc8019d300000980a055e76"
            @change="handleChange"
        >
          <p class="ant-upload-drag-icon">
            <a-icon type="inbox" />
          </p>
          <p class="ant-upload-text">
            单击或拖动文件到此区域进行上传
          </p>
          <p class="ant-upload-hint">
            只接受一个脚本文件
          </p>
        </a-upload-dragger>
      </div>
    </a-modal>
  </div>
</template>
<script>
export default {
  name: "AddScript",
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
      form: this.$form.createForm(this, { name: 'coordinated' }),
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
      e.preventDefault();
      this.form.validateFields((err, values) => {
        if (!err) {
          console.log('Received values of form: ', values);
        }else {
          this.loading = false;
        }
      });

      setTimeout(() => {
        this.$emit('updateModelStatus', false)
        this.loading = false;
      }, 3000);
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
  },
};
</script>
