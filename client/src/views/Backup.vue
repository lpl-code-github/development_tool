<template>
  <div>
    <AddBackUp
        @updateModelStatus="getModelStatus"
        @submit="updateTable"
        :open-flag="openAddScriptModel"
        :db-list="dbList"
    >
    </AddBackUp>
    <div class="my-b-button">
      <a-input-search placeholder="输入名称或描述搜索" style="width: 200px" @search="onSearch"/>
      <a-button type="primary" @click="backUp">
        一键备份
      </a-button>
    </div>

    <div class="my-s-table">
      <a-table
          :key="componentKey"
          :columns="columns"
          :data-source="tableData"
          style="height: 40vh"
          :pagination="paginationConfig"
          :scrollToFirstRowOnChange="true"
          @change="handleChange"
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
          <a style="color: #16750c" :disabled="editingKey !== ''" @click="() => importDb(record.key)">导入</a>
              <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #e01735" :disabled="editingKey !== ''" @click="() => deleteDatabaseBackup(record.key)">删除</a>
        </span>
          </div>
        </template>

        <p slot="expandedRowRender" slot-scope="record" style="margin: 0">
          SQL文件路径：&nbsp;<a @click="downloadSQLFile(record.path)">{{ record.path }}</a>
        </p>
      </a-table>
    </div>
  </div>
</template>

<script>
import AddBackUp from "@/components/backup/AddBackUp";
import fileDownload from 'js-file-download';

export default {
  name: "Backup",
  components: {AddBackUp},
  data() {
    return {
      openAddScriptModel: false,
      dbList: [],
      getDatabaseListFlag: false,
      columns: [
        {title: '名称', width: 200, dataIndex: 'name', scopedSlots: {customRender: 'name'}},
        {title: '描述', width: 350, dataIndex: 'description', scopedSlots: {customRender: 'description'}},
        {
          title: '数据库', width: 200, dataIndex: 'db_name',
          key: 'db_name',
          filters: [],
          filteredValue: []
        },
        {
          title: '创建时间', width: 150, dataIndex: 'created_at',
          key: 'created_at',
          sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
        },
        {title: 'Action', dataIndex: '', key: 'x', scopedSlots: {customRender: 'action'}},
      ],
      backupList: [],
      paginationConfig: {
        defaultCurrent: 1,
        defaultPageSize: 5,
      },
      tableData: [],
      cacheData: [],
      editingKey: '',
      componentKey: 0,
    }
  },
  created() {
    this.getBackupList()
  },

  methods: {
    handleChange(pagination, filters) {
      var tagFilterChecked = filters.db_name
      if (filters.db_name !== undefined && filters.db_name.length === 0) {
        this.tableData = this.backupList
        //重置columns的filteredValue
        var columns = this.columns;
        columns.forEach(item => {
          if (item.key === 'db_name') {
            this.$set(item, 'filteredValue',[])
          }
        })
        this.$set(this, 'columns', [...columns]);
        return
      }

      if (tagFilterChecked !== undefined) {
        this.tableData = this.backupList.filter(item => {
          return tagFilterChecked.includes(item.db_name);
        })
        var columns = this.columns;
        columns.forEach(item => {
          if (item.key === 'db_name') {
            this.$set(item, 'filteredValue', tagFilterChecked)
          }
        })
        this.$set(this, 'columns', [...columns]);
      }
    },
    async backUp() {
      await this.getDatabaseList();

      if (this.getDatabaseListFlag) {
        this.openAddScriptModel = true
      } else {
        this.$message.error("获取数据库列表失败")
      }
    },
    onSearch(value) {
      var params = "?key=" + value
      this.getBackupList(params)
    },
    getBackupList(params) {
      if (params == null) {
        params = ""
      }
      this.$request.getDatabaseBackup(params).then(res => {
        if (res.status === 200) {
          this.backupList = res.data.data

          const dbFilters = [];

          this.backupList.forEach(item => {
            const existingTag = dbFilters.find(filter => filter.text === item.db_name);
            if (!existingTag) {
              dbFilters.push({text: item.db_name, value: item.db_name});
            }
            item.key = item.id
          })
          this.columns.forEach(item => {
            if (item.key === 'db_name') {
              item.filters = dbFilters
            }
          })
          this.tableData = this.backupList
          this.cacheData = this.tableData.map(item => ({...item}));
        }
      })
    },
    getModelStatus(status) {
      this.openAddScriptModel = status
    },
    updateTable(object) {
      if (object !== null) {
        var newData = object[0];
        newData.key = newData.id;

        // 在backupList头部添加元素
        this.backupList.unshift(newData);
        this.$set(this, 'backupList', [...this.backupList]);

        // 添加过滤器 如果存在就不添加
        var columns = this.columns;
        columns.forEach(item => {
          if (item.key === 'db_name') {
            var filters = [...item.filters]; // 创建 filters 的副本
            const existingTag = filters.find(filter => filter.text === newData.db_name);
            if (!existingTag) {
              filters.push({text: newData.db_name, value: newData.db_name});
            }
            item.filters = filters;
          }
        });
        this.$set(this, 'columns', [...columns]);

        // 更新tableData
        this.tableData = [...this.backupList];
        this.cacheData = this.tableData.map(item => ({...item})); // 更新 cacheData
      }
    },
    async getDatabaseList() {
      await this.$request.getDatabaseList().then(res => {
        if (res.status === 200) {
          this.dbList = res.data.data
          this.getDatabaseListFlag = true
        } else {
          this.getDatabaseListFlag = false
        }
      })
    },

    // table编辑
    handleChangeEdit(value, key, column) {
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);

      if (target) {
        target[column] = value;
        this.tableData = newData;
      }
    },
    edit(key) {
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);
      this.editingKey = key;
      if (target) {
        target.editable = true;
        this.tableData = newData;
      }
    },
    save(key) {
      const newData = [...this.tableData];
      const newCacheData = [...this.cacheData];
      const target = newData.find(item => key === item.key);
      const targetCache = newCacheData.find(item => key === item.key);
      if (target && targetCache) {
        // 校验

        let nameLength = target.name.length;

        if (!(nameLength >= 5 && length <= 50)) {
          this.$message.warning("名称长度应该在5～50之间")
          return
        }
        var param = {
          data: {
            id:target.id,
            name:target.name,
            description:target.description
          }
        }
        this.$request.putDatabaseBackup(param).then(res => {
          if (res.status === 200) {
            delete target.editable;
            this.tableData = newData;
            Object.assign(targetCache, target);
            this.cacheData = newCacheData;
            this.$message.success("更新成功")
          } else {
            this.$message.error("更新失败")
          }
        })
      }
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
      }
    },
    importDb(key) {
      this.$confirm({
        title: '确认导入数据库吗？',
        content: h => <div style="color:red;">这将会覆盖您当前的数据库</div>,
        okText: '导入',
        cancelText: '不了',
        onOk:()=> {
          var message = this.$message
          var target = this.cacheData.find(item => key === item.key);
          var param = {
            data: {
              id: target.id
            }
          }
          var loadingMessage = message.loading('正在导入数据库，该操作有点耗时，请等待....', 0)
          this.$request.importDatabaseBackup(param).then(res => {
            if (res.status === 200) {
              message.success("导入成功")
              setTimeout(loadingMessage, 0);
            } else {
              setTimeout(loadingMessage, 0);
            }
          })
        },
        onCancel() {
          console.log('Cancel');
        },
        class: 'test',
      });

    },
    deleteDatabaseBackup(key) {
      this.$confirm({
        title: '确认删除备份吗?',
        content: '将会删除您的SQL备份文件',
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
          this.$request.deleteDatabaseBackup(param).then(res => {
            if (res.status === 200) {
              // 删除backupList，重新设置tableData
              const newBackupList = [...this.backupList]
              this.backupList = newBackupList.filter(item => item.key !== key);
              this.tableData = [...this.backupList]

              this.$message.success("删除成功")

              // 重新分配筛选器
              const dbFilters = [];
              this.backupList.forEach(item => {
                const existingTag = dbFilters.find(filter => filter.text === item.db_name);
                if (!existingTag) {
                  dbFilters.push({text: item.db_name, value: item.db_name});
                }
                item.key = item.id
              })
              var columns = this.columns;
              columns.forEach(item => {
                if (item.key === 'db_name') {
                  item.filters = [...dbFilters]
                  this.$set(item, 'filteredValue', [])
                }
              })
              this.$set(this, 'columns', [...columns]);

              // 重新分配cacheData
              this.cacheData = this.tableData.map(item => ({...item}));
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
    downloadSQLFile(path){
      let fileName = path.substring(path.lastIndexOf('/') + 1);
      this.$request.downloadFile(path).then(res=>{
        if (res.status === 200){
          fileDownload(res.data, fileName);
        }
      })
    }
  }
}
</script>

<style scoped>
.my-b-button {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
}

.my-s-table {
  margin-top: 10px;
}
</style>
