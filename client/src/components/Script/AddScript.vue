<template>
  <div>
    <!--  主modal 用来添加新的脚本  -->
    <a-modal v-model="visible" title="添加一个新的脚本" :on-ok="handleOk"  :afterClose="afterClose" :maskClosable="false">
      <template slot="footer">
        <a-button key="back" @click="handleCancel">
          取消
        </a-button>
        <a-button key="submit" type="primary" :loading="loading" @click="handleOk">
          添加
        </a-button>
      </template>
      <a-form ref="mainModal" :form="form" :label-col="{ span: 3 }" :wrapper-col="{ span: 20}" style="width: 100%">
        <a-form-item label="名称" prop="name">
          <a-input
              v-decorator="['name', { rules: [{ required: true, message: 'Please input name!' },{min: 5, max: 50, message: '长度必须在 5 ～ 50', trigger: 'blur'}] }]"
          />
        </a-form-item>
        <a-form-model-item label="描述" prop="description">
          <a-input v-decorator="['description']" type="textarea"/>
        </a-form-model-item>
        <a-form-model-item label="标签" prop="tags">
          <a-select
              :key="componentKey"
              show-search
              placeholder="请选择标签"
              mode="multiple"
              @change="handleSelect"
          >
            <div slot="dropdownRender" slot-scope="menu">
              <v-nodes :vnodes="menu"/>
              <a-divider style="margin: 4px 0;"/>
              <div
                  style="padding: 4px 8px; cursor: pointer;"
                  @mousedown="e => e.preventDefault()"
                  @click="addItemClick"
              >
                <a-icon type="plus"/>
                添加新的标签
              </div>
            </div>

            <a-select-option v-for="(item,index) in filteredSelectedOptions" :key="index" :value="item.id">
              <div class="color-box" :style="{ 'background-color': item.color}"></div>
              <span>{{ item.name }}</span>
            </a-select-option>
          </a-select>
        </a-form-model-item>

      <!--     动态的增加form表单     -->
        <a-form-model-item
            v-for="(domain, index) in dynamicValidateForm.properties"
            :key="domain.key"
            v-bind="index === 0 ? formItemLayout : {}"
            :label="'参数'+ incrementIndex(index)"
            :prop="'domains.' + index + '.value'"

        >

          <a-input
              v-model="domain.value"
              placeholder="please input domain"
              style="width:80%; margin-right: 8px"
              :label-col="{ span: 3 }" :wrapper-col="{ span: 20}"
          />
          <a-icon
              v-if="dynamicValidateForm.properties.length > 0"
              class="dynamic-delete-button"
              style="display: inline-grid"
              type="minus-circle-o"
              @click="removeProperties(domain)"
          />
        </a-form-model-item>
        <a-form-model-item v-bind="formItemLayoutWithOutLabel">
          <a-button type="dashed" style="width:100%" @click="addProperties">
            <a-icon type="plus" /> 添加命令行参数
          </a-button>
        </a-form-model-item>
      </a-form>



      <div style="margin: auto;width: 93%">
        <a-upload-dragger
            :directory="false"
            name="file"
            :multiple="false"
            accept=".sh"
            :before-upload="beforeUpload"
            :custom-request="uploadScriptFileAndPostScriptData"
            :file-list="fileList"
        >
          <p class="ant-upload-drag-icon">
            <a-icon type="inbox"/>
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

    <!--子modal 用来添加新的标签-->
    <a-modal v-model="childrenModelVisible"
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
  name: "AddScript",
  components: {
    VNodes: {
      functional: true,
      render: (h, ctx) => ctx.props.vnodes,
    },
  },
  props: {
    openFlag: {
      type: Boolean,
      required: true
    },
    filterOption: {
      type: Array,
      required: true
    }// 所有的tags选项
  },
  data() {
    return {
      loading: false, // modal按钮的loading
      visible: false, // modal可见开关
      childrenModelVisible: false,// 子modal的可见
      childrenModelRules: {
        name: [
          {required: true, message: '请输入标签内容', trigger: 'blur'},
          {min: 1, max: 20, message: '长度必须在 1 ～ 20', trigger: 'blur'},
        ],
      },// 子modal的校验规则
      formLayout: 'horizontal',
      form: this.$form.createForm(this, {name: 'coordinated'}), // 主modal的form对象
      dynamicValidateForm: {
        properties: [],
      }, // 动态菜单的值
      formItemLayout: {
        labelCol: {
          xs: { span: 3 },
          sm: { span: 3 },
        },
        wrapperCol: {
          xs: { span: 20 },
          sm: { span: 20 },
        },
      },// 动态菜单输入框的栅格布局
      formItemLayoutWithOutLabel: {
        wrapperCol: {
          xs: { span: 22, offset: 1 },
          sm: { span: 22, offset: 1 },
        },
      }, // 动态菜单按钮的栅格布局
      tags: [], // 所有已经在数据库的tags
      selectedItems: [], // 被选中的
      newTag: {
        name: "",
        color: '#61afd1'
      }, // 新标签的form modal中的默认值
      fileList: [], // 上传文件列表，只允许上传一个
      componentKey:0
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
    filterOption: {
      handler: function (newVal, oldVal) {
        this.tags = newVal
      },
      // 深度观察监听
      deep: true
    },
  },
  computed: {
    filteredSelectedOptions() {
      return this.tags.filter(o => !this.selectedItems.includes(o.id));
    }
  },
  mounted() {
    this.visible = this.openFlag
    this.tags = this.filterOption
  },

  methods: {
    /*
      tags选择器部分
     */
    // 选择标签
    handleSelect(selectedItems) {
      this.selectedItems = selectedItems;
    },
    // 打开添加新标签的子modal
    addItemClick() {
      this.childrenModelVisible = true
    },
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
              // 请求成功后 将新的数据添加到tags
              this.tags.push(res.data.data[0])
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
      if (this.$refs.ruleForm){
        this.$refs.ruleForm.resetFields();
      }
    },


    /*
      动态菜单 （命令行参数）
     */
    // 移除动态菜单
    removeProperties(item) {
      let index = this.dynamicValidateForm.properties.indexOf(item);
      if (index !== -1) {
        this.dynamicValidateForm.properties.splice(index, 1);
      }
    },
    // 添加动态菜单
    addProperties() {
      this.dynamicValidateForm.properties.push({
        value: '',
        key: Date.now(),
      });
    },

    /*
      一些请求事件
     */
    // 创建脚本按钮事件
    handleOk(e) {
      this.loading = true;
      e.preventDefault();
      this.form.validateFields(async (err, values) => {
        if (!err) {
          if (this.fileList.length === 0) {
            this.$message.error("还没有上传脚本文件")
            this.loading = false;
            return
          }
          this.uploadScriptFileAndPostScriptData(values)

        } else {
          this.loading = false;
        }
      });
    },
    // 文件上传 成功后添加脚本
    uploadScriptFileAndPostScriptData(formData) {
      if (this.fileList.length === 0) {
        this.$message.warning('请上传文件')
      } else {
        const file = this.fileList[0];
        let type = file.name.toLowerCase().substr(file.name.lastIndexOf('.'))
        if (type !== '.sh') {
          this.$message.warning('请上传sh文件')
          return false
        }
        this.fileList = [file]
        const fd = new FormData()
        this.fileList.forEach(file => {
          fd.append('file', file)
        })

        this.$request.uploadFile(fd, 'script').then(res => {
          if (res.status === 200) {
            var path = res.data.data.path

            // 构造参数properties
            var properties = []
            this.dynamicValidateForm.properties.forEach(item=>{
              properties.push(item.value)
            })
            // 构造参数tags
            const tags = this.selectedItems.map(el => ({id: el}));
            // post参数
            var param = {
              data: {
                name: formData.name,
                description: formData.description,
                tags: tags,
                path: path,
                properties: properties
              }
            }
            this.$request.postScriptData(param).then(res => {
              if (res.status === 200) {
                this.$message.success("添加脚本成功")
                // 请求成功后 将新增的值传给父组件
                this.$emit('submit', res.data.data)
                setTimeout(() => {
                  this.initFormData()
                  this.$emit('updateModelStatus', false)
                  this.loading = false;
                }, 100);
              } else {
                this.loading = false;
                this.$message.error("添加脚本失败")
              }
            })
          } else {
            this.loading = false;
            this.$message.error("上传脚本失败")
          }
        })
      }
    },
    // 文件上传前的事件
    beforeUpload(file) {
      return new Promise((resolve, reject) => {
        let type = file.name.toLowerCase().substr(file.name.lastIndexOf('.'))
        if (type !== '.sh') {
          this.$message.warning('请上传sh文件')
          return reject(false)
        }
        this.fileList = [file]
        return false
      })
    },

    /*
      向父组件传值：组件状态
     */
    handleCancel(e) {
      this.initTagFormData()
      this.initFormData()
      this.$emit('updateModelStatus', false)
    },
    afterClose(e) {
      this.initTagFormData()
      this.initFormData()
      this.$emit('updateModelStatus', false)
    },

    /*
      内部方法
     */
    // 将index转为字符串并加1
    incrementIndex(index) {
      return parseInt(index) + 1;
    },
    // 重置表格 重置fileList
    initFormData() {
      this.form.resetFields();
      this.tags = []
      this.fileList = []
      this.selectedItems = []
      this.dynamicValidateForm = {
        properties: [],
      }
      this.componentKey += 1
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
