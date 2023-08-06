<template>
  <div>
    <a-modal v-model="visible" title="备份数据库" on-ok="handleOk"  :afterClose="afterClose" :maskClosable="false">
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
        <a-form-model-item label="描述" prop="desc">
          <a-input v-model="form.desc" rows="4" type="textarea"/>
        </a-form-model-item>
        <a-form-model-item label="DB" prop="db">
          <a-select
              show-search
              placeholder="选择数据库"
              option-filter-prop="children"
              :filter-option="filterOption"
              v-model="form.db"
          >
            <a-select-option v-for="(item,index) in dbList" :key="index" :value="item">
              {{ item }}
            </a-select-option>
          </a-select>
        </a-form-model-item>
      </a-form-model>
    </a-modal>
  </div>
</template>
<script>
import {postDatabaseBackup} from "@/api/request";

export default {
  name: "AddBackUp",
  props: {
    openFlag: {
      type: Boolean,
      required: true
    },
    dbList: {
      type: Array,
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
        desc: '',
        db: ''
      },
      rules: {
        name: [
          {required: true, message: '请输入操作名称', trigger: 'blur'},
          {min: 5, max: 50, message: '长度必须在 5 ～ 50', trigger: 'blur'},
        ],
        db: [
          {required: true, message: '请选择数据库', trigger: 'blur'},
        ],
      },
    };
  },
  watch: {
    openFlag: {
      handler: function (newVal, oldVal) {
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
          var data = {
            data:this.form
          }
          var message = this.$message
          var loadingMessage = message.loading('正在备份数据库，该操作有点耗时，请等待....', 0)
          this.$request.postDatabaseBackup(data).then(res=>{
            if (res.status === 200){
              setTimeout(loadingMessage, 0);
              message.success("备份成功")
              this.$emit('submit', res.data.data)
            }else {
              setTimeout(loadingMessage, 0);
            }
          })
          this.initFormData()
          setTimeout(() => {
            this.$emit('updateModelStatus', false)
            this.loading = false;
          }, 1);
        } else {
          this.loading = false
          return false
        }
      });
    },
    handleCancel(e) {
      this.initFormData()
      this.$emit('updateModelStatus', false)
    },
    afterClose(e) {
      this.initFormData()
      this.$emit('updateModelStatus', false)
    },
    filterOption(input, option) {
      return (
          option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
      );
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
