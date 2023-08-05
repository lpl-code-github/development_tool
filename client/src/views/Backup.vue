<template>
  <div>
    <AddBackUp
        @updateModelStatus="getModelStatus"
        :open-flag="openAddScriptModel"
        :db-list="dbList"
    >
    </AddBackUp>
    <div class="my-b-button">
      <a-input-search placeholder="输入备份文件或描述搜索" style="width: 200px" @search="onSearch"/>
      <a-button type="primary" @click="backUp">
        一键备份
      </a-button>
    </div>

    <div class="my-s-table">
      <a-table
          :columns="columns"
          :data-source="tableData"
          style="height: 40vh"
          :pagination="paginationConfig"
          :scrollToFirstRowOnChange="true"
          @change="handleChange"
      >

        <span slot="action" slot-scope="text, record">
          <a style="color: #2c982c">导入</a>
          <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #f17878">删除</a>
        </span>
        <p slot="expandedRowRender" slot-scope="record" style="margin: 0">
          {{ record.description ? record.description : "无描述" }}
        </p>
      </a-table>
    </div>
  </div>
</template>

<script>
import AddBackUp from "@/components/backup/AddBackUp";
export default {
  name: "Backup",
  components: {AddBackUp},
  data() {
    return {
      openAddScriptModel: false,
      dbList: [],
      getDatabaseListFlag : false,
      columns: [
        {title: '名称',width: 200, dataIndex: 'name', key: 'name'},
        {title: '路径', width: 350,dataIndex: 'path', key: 'path'},
        {title: '数据库',width: 200, dataIndex: 'db_name',
          key: 'db_name',
          filters: []
        },
        {title: '创建时间',width: 150, dataIndex: 'created_at',
          key: 'created_at',
          sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
        },
        {title: 'Action', dataIndex: '', key: 'x', scopedSlots: {customRender: 'action'}},
      ],
      backupList:[],
      paginationConfig:{
        defaultCurrent:1,
        defaultPageSize:5,
      },
      tableData:[]
    }
  },
  created() {
    this.getBackupList()
  },

  methods: {
    handleChange(pagination, filters) {
      console.log(filters)
      var tagFilterChecked = filters.db_name
      if (filters.db_name !== undefined && filters.db_name.length === 0) {
        this.tableData = this.backupList
        return
      }
      if (tagFilterChecked !== undefined){
        this.tableData = this.backupList.filter(item => {
          return tagFilterChecked.includes(item.db_name);
        })
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
    onSearch() {

    },
    getBackupList(){
      this.$request.getDatabaseBackup().then(res=>{
        if (res.status === 200){
          this.backupList = res.data.data

          const dbFilters = [];

          this.backupList.forEach(item => {
              const existingTag = dbFilters.find(filter => filter.text === item.db_name);
              if (!existingTag) {
                dbFilters.push({text: item.db_name, value: item.db_name});
              }
          })
          this.columns.forEach(item => {
            if (item.key === 'db_name') {
              item.filters = dbFilters
            }
          })
          this.tableData = this.backupList
        }
      })
    },
    getModelStatus(status) {
      this.openAddScriptModel = status
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
