<template>
  <div class="my-newman">
    <RunNewman @newmanTask="getRunTaskObj" @updateModelStatus="getRunNewmanModelStatus"
               :open-flag="openRunNewmanModelFlag"></RunNewman>
    <NewmanTask :task-log="taskLog" :taskName="taskName" @updateModelStatus="getNewmanModelTaskStatus"
                :open-flag="openNewmanTaskModelFlag"></NewmanTask>

    <!--表格上方的搜索框 按钮等 -->
    <div class="my-n-button">
      <a-input-search placeholder="输入操作名称或描述搜索" style="width: 200px" @search="onSearch"/>
      <div>
        <a-button type="primary" @click="openRunNewmanModel" style="margin-right: 10px">
          创建任务
        </a-button>
        <a-badge :count="taskCount" style="margin-right: 10px">
          <a-button @click="openNewmanTaskModel">
            任务详情
            <a-icon v-if="taskCount !== 0" type="sync" spin/>
          </a-button>
        </a-badge>
      </div>
    </div>

    <div class="my-n-table">
      <a-table
          :columns="columns"
          :data-source="tableData"
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
        <span slot="status" slot-scope="status">
          <a-tag
              :color="status === '成功'? '#1ba81e' :'#d92f09' "
          >
            {{ status }}
          </a-tag>
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
          <a style="color: #286d9f" @click="downloadFile(record.html_report_path)">Html报告</a>
          <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #16750c" @click="downloadFile(record.excel_report_path)">Excel报告</a>
          <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #e01735" @click="deleteNewmanTask(record.key)">删除</a>
        </span>
          </div>
        </template>
        <p slot="expandedRowRender" slot-scope="record" style="margin: 0">
          newman cli输出：&nbsp;<a @click="downloadFile(record.cli_output_path)">{{ record.cli_output_path }}</a>
        </p>
      </a-table>
    </div>
  </div>
</template>

<script>
import RunNewman from "@/components/Newman/RunNewman";
import NewmanTask from "@/components/Newman/NewmanTask";
import fileDownload from "js-file-download";
import {getNewmanTasks} from "@/api/request";

export default {
  name: "Newman",
  components: {NewmanTask, RunNewman},
  data() {
    return {
      taskCount: 0, // 当前任务的数量，只能是0和1
      columns: [
        {title: '名称', width: 150, dataIndex: 'name', scopedSlots: {customRender: 'name'}},
        {title: '描述', width: 250, dataIndex: 'description', scopedSlots: {customRender: 'description'}},
        {title: '状态', width: 50, dataIndex: 'status', key: 'status', scopedSlots: {customRender: 'status'}},
        {
          title: '开始时间', width: 100, dataIndex: 'created_at', key: 'created_at',
          sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
        },
        {
          title: '结束时间', width: 100, dataIndex: 'updated_at', key: 'updated_at',
          sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
        },
        {title: 'Action', width: 200, dataIndex: '', key: 'x', scopedSlots: {customRender: 'action'}},
      ], // 表格属性
      paginationConfig: {
        defaultCurrent: 1,
        defaultPageSize: 5,
      }, // table的分页
      newmanTaskList: [], // 从后台拿来的数据
      tableData: [], // table的数据
      cacheData: [], // 当编辑table取消时，需要从这里获取原来的数据
      filters: [], // 对status的filters，所有筛选选项
      filteredValue: [],// // 对status的filters，被选中的选项
      openRunNewmanModelFlag: false, // 显示RunNewman的Model开关
      openNewmanTaskModelFlag: false,// 显示NewmanTask的Model开关
      runTestFlag: false,// 是否开始执行newman的开关
      taskName: "", // 当前正在进行的task name
      // 这是任务初始化的log，由前台定义，TaskLog中 flag 代表任务打开与关闭
      tempTaskLog: [
        {
          name: "关闭测试环境的报错信息",
          flag: false,
          status: "",
          index: 1
        },
        {
          name: "导入备份及恢复数据库的测试接口",
          flag: false,
          status: "",
          index: 2
        },
        {
          name: "切换到测试环境",
          flag: false,
          status: "",
          index: 3
        },
        {
          name: "执行newman测试命令",
          flag: false,
          status: "",
          index: 4
        }
      ],
      taskLog: [],// 当前任务的taskLog
      currentTask: null, // 当前任务的对象
      editingKey: '', // table当前编辑选中行
    }
  },
  watch: {
    currentTask: {
      handler: function (newVal, oldVal) {
        // 如果currentTask值变化 从不是null变为null 说明正在进行的任务已完成，刷新table
        if (oldVal !== null && newVal === null) {
          this.getAllTaskList();
        }
      },
      deep: true
    }
  },
  created() {
    this.getUnfinishedNewmanTasks()
    this.getAllTaskList();
  },
  mounted() {
    var currentTaskId = localStorage.getItem("run_postman_flag");
    if (currentTaskId == null) {
      localStorage.setItem('run_postman_flag', "0");
    }
  },
  methods: {
    /*
      对table的一些处理
     */
    // 表格变化的处理，这里主要对filters做处理
    handleChange(pagination, filters) {
      var tagFilterChecked = filters.status
      if (filters.status !== undefined && filters.status.length === 0) {
        this.tableData = this.newmanTaskList
        //重置columns的filteredValue
        var columns = this.columns;
        columns.forEach(item => {
          if (item.key === 'status') {
            this.$set(item, 'filteredValue', [])
          }
        })
        this.$set(this, 'columns', [...columns]);
        return
      }

      if (tagFilterChecked !== undefined) {
        this.tableData = this.newmanTaskList.filter(item => {
          return tagFilterChecked.includes(item.status);
        })
        var columns = this.columns;
        columns.forEach(item => {
          if (item.key === 'status') {
            this.$set(item, 'filteredValue', tagFilterChecked)
          }
        })
        this.$set(this, 'columns', [...columns]);
      }
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
    // 编辑表格某一行数据
    edit(key) {
      const newData = [...this.tableData];
      const target = newData.find(item => key === item.key);
      this.editingKey = key;
      if (target) {
        target.editable = true;
        this.tableData = newData;
      }
    },
    // 保存一个更新
    save(key) {
      const newData = [...this.tableData];
      const newCacheData = [...this.cacheData];
      const target = newData.find(item => key === item.key);
      const targetCache = newCacheData.find(item => key === item.key);
      if (target && targetCache) {
        let nameLength = target.name.length;
        let descriptionLength = target.description.length;

        if (!(nameLength >= 5 && length <= 50)) {
          this.$message.warning("名称长度应该在5～50之间")
          return
        }
        if (descriptionLength === 0) {
          this.$message.warning("描述不能为空")
          return
        }
        var param = {
          data: {
            id: target.id,
            name: target.name,
            description: target.description
          }
        }
        this.$request.putNewmanTasks(param).then(res => {
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
    // 取消一个更新，从cacheData中恢复表格数据tableData
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
    // 删除一个NewmanTask
    deleteNewmanTask(key) {
      this.$confirm({
        title: '确认删除测试记录吗?',
        content: '将会删除您的html报告、Excel报告等',
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
          this.$request.deleteNewmanTasks(param).then(res => {
            if (res.status === 200) {
              // 删除newmanTaskList，重新设置tableData
              const newNewmanTaskList = [...this.newmanTaskList]
              this.newmanTaskList = newNewmanTaskList.filter(item => item.key !== key);
              this.tableData = [...this.newmanTaskList]

              this.$message.success("删除成功")

              // 重新分配筛选器
              const statusFilters = [];
              this.newmanTaskList.forEach(item => {
                const existingStatus = statusFilters.find(filter => filter.text === item.status);
                if (!existingStatus) {
                  statusFilters.push({text: item.status, value: item.status});
                }
                item.key = item.id
              })
              var columns = this.columns;
              columns.forEach(item => {
                if (item.key === 'status') {
                  item.filters = [...statusFilters]
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

    /*
      一些请求事件
     */
    // 搜索组件，拼接模糊匹配的参数 请求查询接口
    onSearch(value) {
      var params = "?key=" + value
      this.getAllTaskList(params)
    },
    // 获取全部任务列表
    getAllTaskList(params) {
      if (params == null) {
        params = ""
      }
      this.$request.getNewmanTasks(params).then(res => {
        if (res.status === 200) {
          this.newmanTaskList = res.data.data

          const statusFilters = [];

          this.newmanTaskList.forEach(item => {
            const existingStatus = statusFilters.find(filter => filter.text === item.status);
            if (!existingStatus) {
              statusFilters.push({text: item.status, value: item.status});
            }
            item.key = item.id
          })
          this.columns.forEach(item => {
            if (item.key === 'status') {
              item.filters = statusFilters
            }
          })
          this.tableData = this.newmanTaskList
          this.cacheData = this.tableData.map(item => ({...item}));
        }
      })
    },
    // 获取未完成的任务
    getUnfinishedNewmanTasks() {
      var param = "?is_unfinished=true"
      this.$request.getNewmanTasks(param).then(async res => {
        if (res.status === 200) {
          var data = res.data.data
          if (data.length !== 0) {
            var task = data[0];
            sessionStorage.setItem('currentTask',task.id)
            this.currentTask = task
            this.taskName = task.name;
            this.taskCount = 1;
            if (task.log == null) {
              var putParam = {data: {log: this.tempTaskLog, id: task.id}}
              this.$request.putNewmanTasks(putParam).then(res => {
                if (res.status === 200) {
                  if (res.data.data.log != null && res.data.data.log.length !== 0) {
                    this.taskLog = res.data.data.log
                  } else {
                    this.taskLog = []
                  }
                } else {
                  this.taskLog = []
                }
              })
            } else {
              this.taskLog = task.log
              // start
              if (task.status == null) {
                await this.startPostmanTest(this.taskLog)
              }
            }
          } else {
            sessionStorage.removeItem('currentTask')
            this.currentTask = null
            this.taskName = "";
            this.taskCount = 0;
            this.taskLog = []
          }
        }
        // 当路由不在newman时，如果sessionStorage不存在currentTask，就不再定时请求
        if (this.$route.path === '/newman' ||
            sessionStorage.getItem('currentTask') !== null
        ){
          setTimeout(this.getUnfinishedNewmanTasks, 3000);
        }
      });
    },
    // 开始进行newman的测试任务
    async startPostmanTest(taskLog) {
      for (let i = 0; i < taskLog.length; i++) {
        var item = taskLog[i];
        if (item.flag === false) { // 任务未开始
          taskLog[i].flag = true // 开始任务
          // 执行任务
          if (item.index === 1) {// 关闭测试环境的报错信息
            await this.$request.switchApi('test_env_error_message', false).then(async res => {
              if (res.status === 200) { // 关闭成功
                taskLog[i].status = "成功"
                await this.modifyTaskLog(this.taskLog, null, this.currentTask.id)
              } else {
                taskLog[i].status = "error" + res.data.message;
                await this.modifyTaskLog(this.taskLog, "error", this.currentTask.id)
              }

            });
          } else if (item.index === 2) { // 导入备份/恢复数据库的测试接口
            await this.$request.switchApi('back_api', true).then(async res => {
              if (res.status === 200) { // 关闭成功
                taskLog[i].status = "成功"
                await this.modifyTaskLog(this.taskLog, null, this.currentTask.id)
              } else {
                taskLog[i].status = "error" + res.data.message;
                await this.modifyTaskLog(this.taskLog, "error", this.currentTask.id)
              }
            });
          } else if (item.index === 3) { // 切换到测试环境
            await this.$request.switchApi('test_env', true).then(async res => {
              if (res.status === 200) { // 关闭成功
                taskLog[i].status = "成功"
                await this.modifyTaskLog(this.taskLog, null, this.currentTask.id)
              } else {
                taskLog[i].status = "error" + res.data.message;
                await this.modifyTaskLog(this.taskLog, "error", this.currentTask.id)
              }
            });
          } else if (item.index === 4) { // 执行postman测试，只执行一次
            var currentTaskId = localStorage.getItem("run_postman_flag");
            if (currentTaskId != this.currentTask.id) {
              const param = {
                data: {
                  task_id: this.currentTask.id
                }
              };
              await this.$request.runNewman(param).then(async res => {
                if (res.status === 200) { // 执行成功
                  localStorage.setItem("run_postman_flag", this.currentTask.id);
                } else {
                  taskLog[i].status = "error" + res.data.message;
                  await this.modifyTaskLog(this.taskLog, "error", this.currentTask.id);
                }
              });
            }
          }
          break;
        }
      }
    },
    // 更新日志及状态
    async modifyTaskLog(log, status, id) {
      var putParam = {data: {log: log, status: status, id: id}}
      await this.$request.putNewmanTasks(putParam).then()
    },

    /*
      modal框的回调
     */
    openRunNewmanModel() {
      this.openRunNewmanModelFlag = true
    },
    openNewmanTaskModel() {
      this.openNewmanTaskModelFlag = true
    },
    getRunNewmanModelStatus(status) {
      this.openRunNewmanModelFlag = status
    },
    getNewmanModelTaskStatus(status) {
      this.openNewmanTaskModelFlag = status
    },
    getRunTaskObj(taskObj) {
      if (taskObj != null) {
        this.runTestFlag = true
      }
    },

    /*
      下载文件
     */
    downloadFile(path) {
      let fileName = path.substring(path.lastIndexOf('/') + 1);
      this.$request.downloadFile(path).then(res => {
        if (res.status === 200) {
          fileDownload(res.data, fileName);
        } else {
          const hasHTML = fileName.includes("html");
          const hasExcel = fileName.includes(".csv");
          const hasTxt = fileName.includes(".txt");
          console.log(hasExcel)
          if (hasHTML || hasExcel) {
            this.$message.error("文件可能丢失，也可能不存在，详情请下载cli报告看")
          }
          if (hasTxt){
            this.$message.error("cli报告文件已经丢失")
          }
        }
      })
    },
  }
}
</script>

<style scoped>
.my-n-button {
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
}

.my-n-table {
  margin-top: 10px;
}
</style>
