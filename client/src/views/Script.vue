<template>
  <div class="my-script">
    <AddScript @updateModelStatus="getAddScriptModelStatus"
               @submit="updateTable"
               :open-flag="openAddScriptModel"
               :filter-option="allTags"
    />

    <AddTag @updateModelStatus="getAddTagModelStatus"
            :open-flag="openAddTagModel"
            @submit="updateTags"
    />

    <div class="my-s-button">
      <a-input-search placeholder="输入脚本名或描述搜索" style="width: 200px" @search="onSearch"/>
      <a-button type="primary" @click="addScript">
        添加
      </a-button>
    </div>

    <div class="my-s-table">
      <a-table :columns="columns"
               :data-source="tableData"
               @expandedRowsChange="handleExpandedRowsChange"
               :expandedRowKeys="expandedRowKeys"
               @change="handleChange"
               :pagination="paginationConfig"
      >
        <template
            v-for="col in ['name','description']"
            :slot="col"
            slot-scope="text, record, index"
        >
          <div :key="col">
            <a-input
                :type="col=== 'description'?'textarea':''"
                rows="1"
                v-if="record.editable"
                style="margin: -5px 0"
                :value="text"
                @change="e => handleChangeEdit(e.target.value, record.key, col)"
            />
            <template v-else>
              {{ text === "" || text === null ? "/" : text }}
            </template>
          </div>
        </template>
        <span slot="tags" slot-scope="tags,record">
          <a-tag
              v-for="(t,index) in tags"
              :key="index"
              :closable="record.editable"
              @close="removeTagItem(t.id,record.key,$event)"
              :color="t.color"
          >
            {{ t.name }}
          </a-tag>

          <a-select
              style="width: 100px;border: 1px"
              size="small"
              show-search
              option-filter-prop="children"
              v-if="record.editable"
              :filter-option="filterOption"
              @select="handleEditModeSelectTag"
              :dropdownMatchSelectWidth="false"
              :value="selectedTagsItems"
          >
            <div slot="dropdownRender" slot-scope="menu">
              <v-nodes :vnodes="menu"/>
              <a-divider style="margin: 4px 0;"/>
              <div
                  style="padding: 4px 8px; cursor: pointer;"
                  @mousedown="e => e.preventDefault()"
                  @click="addTags"
              >
                <a-icon type="plus"/> 添加新的标签
              </div>
            </div>
            <a-select-option v-for="(item,index) in tmpTags" :key="index" :value="item.id">
              <div class="color-box" :style="{ 'background-color': item.color}"></div>
              {{ item.name }}
            </a-select-option>
          </a-select>
          <span v-if="tags !== null && tags.length === 0 && !record.editable">/</span>
        </span>


        <template slot="action" slot-scope="text, record, index">
          <div class="editable-row-operations">
              <span v-if="record.editable">
                <a style="color: #5f92ef" @click="() => save(record.key)">更新</a>
                <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
                <a-popconfirm title="确定取消吗？" @confirm="() => cancel(record.key)">
                  <a style="color: #5f6062">取消</a>
                </a-popconfirm>
              </span>
            <span v-else>
          <a style="color: #5f92ef" :disabled="editingKey !== ''" @click="() => edit(record.key)">编辑</a>
              <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #2c982c" @click="() => runScript(record.key)">运行</a>
              <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #f17878" @click="deleteScript(record.key)">删除</a>
        </span>
          </div>
        </template>

        <!--表格展开菜单-->
        <div slot="expandedRowRender" slot-scope="record" style="margin: 0">
          <span>
            <span style="font-weight: bolder">脚本文件路径：</span> &nbsp;
            <span v-if="!record.editable"><a @click="downloadScriptFile(record.path)">{{ record.path }}</a></span>
            <span v-else>{{ record.path }}</span>
            &nbsp;&nbsp;
            <!-- 如果在编辑状态 显示重新上传 -->
             <a-popconfirm
                 v-if="record.editable"
                 title="确认重新上传脚本文件?"
                 ok-text="确认"
                 cancel-text="不了"
                 @confirm="confirmUpload"
             >
               <a style="color: #04628a">重新上传</a>
              <a-upload
                  ref="uploadComponent"
                  :directory="false"
                  name="file"
                  :multiple="false"
                  accept=".sh"
                  :before-upload="beforeUpload"
                  :custom-request="() => uploadUpdateScriptFileAndOther(record.key)"
                  :file-list="fileList"
              >
              </a-upload>
             </a-popconfirm>
          </span>
          <br/>
          <!-- 显示命令行参数 -->
          <div style="margin-top: 15px" v-if="record.properties != null">
            <a-list size="small" style="width: 95%" bordered :data-source="record.properties">
              <a-list-item slot="renderItem" slot-scope="item, index">
                <!--命令行参数编辑模式-->
                <div v-if="record.editable">
                  <a-input
                      style="width: 400px"
                      :value="item"
                      @change="e => handleChangeEdit(e.target.value, record.key, 'properties',index)"
                  />
                  <a-icon
                      style="display: inline-grid;margin-left: 20px"
                      type="minus-circle-o"
                      @click="removePropertiesItems(record.key,index)"
                  />
                </div>
                <span v-else>{{ item }} </span>
              </a-list-item>

              <div slot="header">
                <span v-if="!record.editable" style="font-weight: bolder">命令行参数</span>
                <span v-else>
                  <span style="font-weight: bolder">命令行参数</span>
                  <a-icon @click="addPropertiesItems(record.key)" style="margin-left: 20px" type="plus-circle-o"/>
                </span>
              </div>
            </a-list>
          </div>
        </div>
      </a-table>
    </div>
  </div>
</template>

<script>
import AddScript from "@/components/Script/AddScript";
import fileDownload from "js-file-download";
import AddTag from "@/components/Script/AddTag";

export default {
  name: "Script",
  components: {
    AddTag,
    AddScript,
    VNodes: {
      functional: true,
      render: (h, ctx) => ctx.props.vnodes,
    },
  },
  data() {
    return {
      columns: [
        {title: '名称', width: 200, dataIndex: 'name', key: 'name', scopedSlots: {customRender: 'name'}},
        {title: '描述', width: 350, dataIndex: 'description', scopedSlots: {customRender: 'description'}},
        {
          title: '标签',
          key: 'tags',
          width: 250,
          dataIndex: 'tags',
          filters: [],
          scopedSlots: {customRender: 'tags'},
        },
        {
          title: '创建时间', width: 150, dataIndex: 'created_at', key: 'created_at',
          sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
        },
        {title: 'Action', dataIndex: '', key: 'x', scopedSlots: {customRender: 'action'}},
      ], // table信息
      paginationConfig: {
        defaultCurrent: 1,
        defaultPageSize: 5,
      }, // 分页数据
      allTags: [],// 所有tags
      tmpTags: [],// 编辑模式下某行数据的允许新添加的tag
      scriptData: [], // api请求来的数据
      tableData: [], // table的数据
      cacheData: [],// 缓存数据 用于编辑表格时，缓存之前的数据
      editingKey: '', // 被编辑的行
      expandedRowKeys: [],//表格默认展开的行
      filteredTags: [],
      tagsFilterDropdownVisible: false,
      openAddScriptModel: false,
      openAddTagModel: false,
      fileList: [], // 更新某行数据时，上传组件的file列表
      componentKey: 0,
      selectedTagsItems: '',// 编辑模式下，选中的tag
    }
  },
  created() {
    this.getScriptData()
    this.getAllTag()
  },
  methods: {
    // 获取所有tag并打开add的modal框
    getAllTag() {
      this.$request.getTags().then(res => {
        if (res.status === 200) {
          this.allTags = res.data.data
        } else {
          this.$message.error("获取tags列表错误")
        }
      })
    },
    //获取全部ScriptData
    getScriptData(params) {
      if (params == null) {
        params = ""
      }
      this.$request.getScriptData(params).then(res => {
        if (res.status === 200) {
          this.scriptData = res.data.data

          const tagsFilters = [];

          this.scriptData.forEach(item => {
            item.tags.forEach(tag => {
              const existingTag = tagsFilters.find(filter => filter.text === tag.name);
              if (!existingTag) {
                tagsFilters.push({text: tag.name, value: tag.name});
              }
            });
            item.key = item.id
          });
          this.columns.forEach(item => {
            if (item.key === 'tags') {
              item.filters = tagsFilters
            }
          })
          this.tableData = [...this.scriptData]
          this.cacheData = this.tableData.map(item => JSON.parse(JSON.stringify(item)));
        }

      })
    },

    /*
      table编辑功能
     */
    // 编辑状态默认展开行
    handleExpandedRowsChange(expandedRowKeys) {
      this.expandedRowKeys = expandedRowKeys
    },
    // 编辑事件
    handleChangeEdit(value, key, column, index) {
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);

      if (target) {
        if (column === 'properties') {
          target[column][index] = value
        } else {
          target[column] = value;
        }
        this.tableData = newData;
      }
    },
    edit(key) {
      const newData = [...this.tableData]
      const target = newData.find(item => key === item.key);
      this.handleExpandedRowsChange([key])
      this.editingKey = key;
      if (target) {
        var tmp = []
        this.allTags.forEach(item => {
          if (!target.tags.find(i => item.id === i.id)) {
            tmp.push(item);
          }
        })
        this.tmpTags = [...tmp];
        target.editable = true;
        this.tableData = newData;
      }
    },
    save(key) {
      const newData = [...this.tableData];
      const newCacheData = JSON.parse(JSON.stringify(this.cacheData));
      const target = newData.find(item => key === item.key);
      const targetCache = newCacheData.find(item => key === item.key);
      if (target && targetCache) {
        let nameLength = target.name.length;
        if (!(nameLength >= 5 && nameLength <= 50)) {
          this.$message.warning("名称长度应该在5～50之间")
          return
        }
        var message = ''
        target.properties.forEach((item, index) => {
          if (item === "") {
            const num = parseInt(index) + 1;
            message = "命令行参数存在空字段";
          }
        })
        if (message !== '') {
          this.$message.warning(message)
          return;
        }
        var param = {
          data: {
            id: target.id,
            name: target.name,
            description: target.description,
            properties: target.properties,
            path: target.path,
            tags: target.tags
          }
        }
        this.$request.putScriptData(param).then(res => {
          if (res.status === 200) {
            delete target.editable;
            this.tableData = newData;
            Object.assign(targetCache, target);
            this.cacheData = this.tableData.map(item => JSON.parse(JSON.stringify(item)));
            this.$message.success("更新成功")
          } else {
            this.$message.error("更新失败")
          }
        })
      }
      this.expandedRowKeys = []
      this.editingKey = '';
    },
    cancel(key) {
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);
      this.editingKey = '';
      if (target) {
        Object.assign(target, this.cacheData.find(item => key === item.key));
        delete target.editable;
        this.tableData = newData;
        this.cacheData = this.tableData.map(item => JSON.parse(JSON.stringify(item)));
      }
      this.expandedRowKeys = []
    },
    // 确认重新上传后点击upload组件
    confirmUpload() {
      this.$refs.uploadComponent.$el.querySelector('input[type=file]').click();
    },
    // 修改上传的文件 上传前：
    beforeUpload(file) {
      // return new Promise((resolve, reject) => {
      let type = file.name.toLowerCase().substr(file.name.lastIndexOf('.'))
      if (type !== '.sh') {
        this.$message.warning('请上传sh文件')
        // return reject(false)
        return false
      }
      this.fileList = [file]
      return true
      // })

    },
    // 文件上传 并保存其他属性
    uploadUpdateScriptFileAndOther(key) {
      if (this.fileList.length !== 0) { // 如果上传了新的文件
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
        // 上传文件
        this.$request.uploadFile(fd, 'script').then(res => {
          if (res.status === 200) {
            const path = res.data.data.path;
            this.handleChangeEdit(path, key, 'path')
            this.fileList = []
            this.$message.success("更新脚本文件成功")
          } else {
            this.$message.error("更新脚本文件失败")
          }
        })
      }
    },
    // 移除PropertiesItems
    removePropertiesItems(key, index) {
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);

      if (target) {
        target['properties'].splice(index, 1);
        this.tableData = newData;
      }
    },
    // 添加新的PropertiesItems
    addPropertiesItems(key) {
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);

      if (target) {
        target['properties'].push("")
        this.tableData = newData;
      }
    },
    // 移除一个标签
    removeTagItem(tagId, key, event) {
      event.preventDefault();
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);
      if (target) {
        const tmpTags = [];
        target.tags.forEach(item => {
          if (item.id !== tagId) {
            tmpTags.push(item)
          }
        })
        target.tags = tmpTags
        this.tableData = newData
      }
    },
    // 添加一个新的标签
    addTags() {
      this.openAddTagModel = true
    },
    // 添加tag的select选择器 搜索功能
    filterOption(input, option) {
      return (
          option.componentOptions.children[1].text.toLowerCase().indexOf(input.toLowerCase()) >= 0
      );
    },
    // 选中事件
    handleEditModeSelectTag(value, option) {
      this.selectedTagsItems = '';
      var key = this.editingKey;
      const newData = this.tableData.slice(); // 创建新的数组副本
      const target = newData.find((item) => key === item.key);

      var tag = this.tmpTags.find((item) => value === item.id);
      if (target && tag) {
        target.tags.push(tag);
        this.tableData = newData; // 更新 this.tableData
        this.componentKey += 1;

        var tmp = [];
        this.allTags.forEach((item) => {
          if (!target.tags.find((i) => item.id === i.id)) {
            tmp.push(item);
          }
        });
        this.tmpTags = [...tmp];
      }
    },

    // 删除脚本
    deleteScript(key) {
      this.$confirm({
        title: '确认删除脚本吗?',
        content: '将会删除您的脚本文件',
        okText: '删除',
        okType: 'danger',
        cancelText: '不了',
        onOk: () => {
          var target = this.cacheData.find(item => key === item.key);
          var param = {
            data: {
              id: target.id
            }
          }
          this.$request.deleteScriptData(param).then(res => {
            if (res.status === 200) {
              // 删除backupList，重新设置tableData
              const newBackupList = [...this.scriptData]
              this.scriptData = newBackupList.filter(item => item.key !== key);
              this.tableData = [...this.scriptData]

              this.$message.success("删除成功")

              // 重新分配筛选器
              const tagsFilters = [];
              this.scriptData.forEach(item => {
                item.tags.forEach(tag => {
                  const existingTag = tagsFilters.find(filter => filter.text === tag.name);
                  if (!existingTag) {
                    tagsFilters.push({text: tag.name, value: tag.name});
                  }
                });
                item.key = item.id
              });
              var columns = this.columns;
              columns.forEach(item => {
                if (item.key === 'tags') {
                  item.filters = tagsFilters
                }
              })
              this.$set(this, 'columns', [...columns]);
              this.tableData = [...this.scriptData]
              // 重新分配cacheData
              this.cacheData = this.tableData.map(item => JSON.parse(JSON.stringify(item)));

            } else {
              this.$message.error("删除失败")
            }
          })
        },
        onCancel() {
          console.log('Cancel');
        },
      });

    },

    // 搜索查询
    onSearch(value) {
      var params = "?key=" + value
      this.getScriptData(params)
    },
    // 表格变化事件
    handleChange(pagination, filters) {
      var tagFilterChecked = filters.tags
      if (filters.tags.length === 0) {
        this.tableData = [...this.scriptData]
        return
      }
      this.tableData = this.scriptData.filter(item => {
        return item.tags.some(tag => tagFilterChecked.includes(tag.name));
      })
    },
    // 添加新脚本
    addScript() {
      this.openAddScriptModel = true
    },
    // 执行一个脚本 并获取响应 直接下载
    runScript(key) {
      var message = this.$message
      var loadingMessage = message.loading('正在执行脚本，您可以继续进行其他操作，但不要刷新页面', 0)
      var target = this.scriptData.find(item => key === item.key);
      var param = {
        data: {
          script_id: target.id
        }
      }
      this.$request.runScript(param).then(res=>{
        if (res.status === 200){
          setTimeout(loadingMessage, 0);
          message.success('脚本执行成功', 2.5)
          // 下载响应
          const fileName = 'run_'+ target.name +'_' + this.getNow();
          fileDownload(res.data, fileName);
        }else {
          setTimeout(loadingMessage, 0);
          message.error('脚本执行失败', 2.5)
        }
      })
    },

    // 当新增数据后 更新表格数据
    updateTable(object) {
      if (object !== null) {
        var newData = object[0];
        newData.key = newData.id;

        // 在scriptData头部添加元素
        this.scriptData.unshift(newData);
        this.$set(this, 'scriptData', [...this.scriptData]);

        // 添加过滤器 如果存在就不添加
        var columns = this.columns;
        columns.forEach(item => {
          if (item.key === 'tags') {
            var filters = [...item.filters]; // 创建 filters 的副本

            newData.tags.forEach(tag => {
              const existingTag = filters.find(filter => filter.text === newData.name);
              if (!existingTag) {
                filters.push({text: tag.name, value: tag.name});
              }
            });

            item.filters = filters;
          }
        });
        this.$set(this, 'columns', [...columns]);

        // 更新tableData
        this.tableData = [...this.scriptData];
        this.cacheData = this.tableData.map(item => JSON.parse(JSON.stringify(item)));
      }
    },
    // 更新新添加的tag到数组中
    updateTags(object) {
      if (object !== null) {
        const newData = object[0];
        newData.key = newData.id;
        // 在tags头部添加元素
        this.allTags.unshift(newData);
        this.tmpTags.unshift(newData);
        this.$set(this, 'tags', [...this.allTags]);
      }
    },

    // 获取modal的状态
    getAddScriptModelStatus(status) {
      this.openAddScriptModel = status
    },
    getAddTagModelStatus(status) {
      this.openAddTagModel = status
    },

    // 下载文件
    downloadScriptFile(path) {
      let fileName = path.substring(path.lastIndexOf('/') + 1);
      this.$request.downloadFile(path).then(res => {
        if (res.status === 200) {
          fileDownload(res.data, fileName);
        }
      })
    },

    // 获取当前时间
    getNow(){
      var date = new Date();

      var year = date.getFullYear();
      var month = ("0" + (date.getMonth() + 1)).slice(-2); //月份从0开始，所以要加1
      var day = ("0" + date.getDate()).slice(-2);
      var hours = ("0" + date.getHours()).slice(-2);
      var minutes = ("0" + date.getMinutes()).slice(-2);
      var seconds = ("0" + date.getSeconds()).slice(-2);

      return year + month + day + hours + minutes + seconds
    }
  }
}
</script>

<style scoped>
.my-script {

}

.my-s-button {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
}

.my-s-table {
  margin-top: 10px;
}

.color-box {
  width: 12px;
  height: 12px;
  align-items: center;
  margin: 0 5px 0 0;
  display: inline-block;
}
</style>
