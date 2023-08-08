<template>
  <div class="my-newman">
    <RunNewman @newmanTask="getRunTaskObj" @updateModelStatus="getRunNewmanModelStatus"
               :open-flag="openRunNewmanModelFlag"></RunNewman>
    <NewmanTask :task-log="taskLog" :taskName="taskName" @updateModelStatus="getNewmanModelTaskStatus"
                :open-flag="openNewmanTaskModelFlag"></NewmanTask>
    <div class="my-n-button">
      <a-input-search placeholder="输入脚本名或描述搜索" style="width: 200px" @search="onSearch"/>
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
        <!--        <a-button @click="openNewmanLogModel" style="margin-right: 10px">-->
        <!--          日志-->
        <!--        </a-button>-->
      </div>
    </div>
    <div class="my-n-table">
      <a-table
          :columns="columns"
          :data-source="tableData"
          @change="handleChange"
          :pagination="paginationConfig"
      >
        <span slot="action" slot-scope="text, record">
          <a style="color: #286d9f">Html报告</a>
          <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #16750c">Excel报告</a>
          <a-divider type="vertical" style="background-color: #a8a7a7!important;"/>
          <a style="color: #e01735">删除记录</a>
        </span>
        <p slot="expandedRowRender" slot-scope="record" style="margin: 0">
          {{ record.description }}
        </p>
      </a-table>
    </div>
  </div>
</template>

<script>
import RunNewman from "@/components/Newman/RunNewman";
import NewmanTask from "@/components/Newman/NewmanTask";
import {getNewmanTasks} from "@/api/request";

export default {
  name: "Newman",
  components: {NewmanTask, RunNewman},
  data() {
    return {
      taskCount: 0,
      columns: [
        {title: '操作名称', width: 150, dataIndex: 'name', key: 'name'},
        {title: '开始时间', width: 100, dataIndex: 'created_at', key: 'created_at'},
        {title: '结束时间', width: 100, dataIndex: 'updated_at', key: 'updated_at'},
        {title: 'Action', width: 200, dataIndex: '', key: 'x', scopedSlots: {customRender: 'action'}},
      ],
      paginationConfig: {
        defaultCurrent: 1,
        defaultPageSize: 5,
      },
      scriptData: [],
      tableData: [],
      filteredTags: [],
      tagsFilterDropdownVisible: false,
      openRunNewmanModelFlag: false,
      openNewmanTaskModelFlag: false,
      runTestFlag: false,
      timer: null,
      taskName: "",
      // flag 代表任务打开与关闭
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
      taskLog: [],
      currentTask: null
    }
  },
  watch: {
    currentTask: {
      handler: function (newVal, oldVal) {
        if (this.currentTask.status !== "success") {

        }
      },
      // 深度观察监听
      deep: true
    }
  },
  created() {
    this.getUnfinishedNewmanTasks()
    this.getScriptData()
  },
  mounted() {
    var currentTaskId = localStorage.getItem("run_postman_flag");
    if (currentTaskId == null){
      localStorage.setItem('run_postman_flag',"0");
    }
    // if (this.timer == null) {
    //   this.timer = setInterval(() => {
    //     this.getUnfinishedNewmanTasks()
    //   }, 3000);
    // }
  },
  beforeDestroy() {
    clearInterval(this.timer);
  },
  methods: {
    onSearch(value) {
      console.log(value);
    },
    handleChange(pagination, filters) {
      var tagFilterChecked = filters.tags
      if (filters.tags.length === 0) {
        this.tableData = this.scriptData
        return
      }
      this.tableData = this.scriptData.filter(item => {
        return item.tags.some(tag => tagFilterChecked.includes(tag.text));
      })
    },
    getScriptData() {
      this.scriptData = [
        {
          id: 1,
          name: 'John Brown',
          created_at: 'New York No. 1 Lake Park',
          updated_at: 'New York No. 1 Lake Park',
          description: 'My name is John Brown, I am 32 years old, living in New York No. 1 Lake Park.',
        },
        {
          id: 2,
          name: 'Jim Green',
          created_at: 'New York No. 1 Lake Park',
          updated_at: 'New York No. 1 Lake Park',
          description: 'My name is Jim Green, I am 42 years old, living in London No. 1 Lake Park.',
        },
        {
          id: 3,
          name: 'Joe Black',
          created_at: 'New York No. 1 Lake Park',
          updated_at: 'New York No. 1 Lake Park',
          description: 'My name is Joe Black, I am 32 years old, living in Sidney No. 1 Lake Park.',
        },
        {
          id: 4,
          name: 'Joe Black',
          created_at: 'New York No. 1 Lake Park',
          updated_at: 'New York No. 1 Lake Park',
          description: 'My name is Joe Black, I am 32 years old, living in Sidney No. 1 Lake Park.',
        }
      ]

      this.tableData = this.scriptData
    },

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
        console.log(taskObj)
      }
    },

    // 获取未完成的任务
    getUnfinishedNewmanTasks() {
      var param = "?is_unfinished=true"
      this.$request.getNewmanTasks(param).then(async res => {
        if (res.status === 200) {
          var data = res.data.data
          if (data.length !== 0) {
            var task = data[0];
            this.currentTask = task
            this.taskName = task.name;
            this.taskCount = 1;
            if (task.log == null) {
              var putParam = {data: {log: this.tempTaskLog, id: task.id}}
              this.$request.putNewmanTasksLog(putParam).then(res => {
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
            this.currentTask = null
            this.taskName = "";
            this.taskCount = 0;
            this.taskLog = []
          }
        }
        setTimeout(this.getUnfinishedNewmanTasks, 3000);
      });
    },

    async startPostmanTest(taskLog) {
      for (let i = 0; i < taskLog.length; i++) {
        var item = taskLog[i];
        if (item.flag === false) { // 任务未开始
          taskLog[i].flag = true // 开始任务
          // 执行任务
          if (item.index === 1) {// 关闭测试环境的报错信息
            await this.$request.switchApi('test_env_error_message', false).then(async res => {
              if (res.status === 200) { // 关闭成功
                taskLog[i].status = "success"
                await this.modifyTaskLog(this.taskLog, null, this.currentTask.id)
              } else {
                taskLog[i].status = "error"+res.data.message;
                await this.modifyTaskLog(this.taskLog, "error", this.currentTask.id)
              }

            });
          } else if (item.index === 2) { // 导入备份/恢复数据库的测试接口
            await this.$request.switchApi('back_api', true).then(async res => {
              if (res.status === 200) { // 关闭成功
                taskLog[i].status = "success"
                await this.modifyTaskLog(this.taskLog, null, this.currentTask.id)
              } else {
                taskLog[i].status = "error"+res.data.message;
                await this.modifyTaskLog(this.taskLog, "error", this.currentTask.id)
              }
            });
          } else if (item.index === 3) { // 切换到测试环境
            await this.$request.switchApi('test_env', true).then(async res => {
              if (res.status === 200) { // 关闭成功
                taskLog[i].status = "success"
                await this.modifyTaskLog(this.taskLog, null, this.currentTask.id)
              } else {
                taskLog[i].status = "error"+res.data.message;
                await this.modifyTaskLog(this.taskLog, "error", this.currentTask.id)
              }
            });
          } else if (item.index === 4) { // 执行postman测试，只执行一次
            var currentTaskId = localStorage.getItem("run_postman_flag");
            if (currentTaskId != this.currentTask.id){
              const param = {
                data: {
                  task_id: this.currentTask.id
                }
              };
              await this.$request.runNewman(param).then(async res => {
                if (res.status === 200) { // 执行成功
                  localStorage.setItem("run_postman_flag", this.currentTask.id);
                } else {
                  taskLog[i].status = "error"+res.data.message;
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
      await this.$request.putNewmanTasksLog(putParam).then()
    },
    initTaskLog() {
      this.taskLog = [...this.tempTaskLog]
    }
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
