<template>
  <div class="index">
    <OneLog :key="oneLogComponentKey" :log-data="oneLog" :date="currentDate" @updateModelStatus="updateLogModelStatus"
            :open-flag="openLogModelFlag"></OneLog>

    <!--日历组件-->
    <a-calendar :locale="locale" @select="onSelect" @panelChange="getPanelChange" :key="calendarComponentKey">
      <ul slot="dateCellRender" slot-scope="value" class="events">
        <li v-for="(item,index) in getListData(value)" :key="index">
          <a-badge :status="item.type" :text="item.content"/>
        </li>
      </ul>
    </a-calendar>
  </div>
</template>

<script>
import zhCN from 'ant-design-vue/es/locale/zh_CN'; //引入antd中文包
import moment from 'moment';
import OneLog from "@/components/log/OneLog";
//引入moment
moment.locale('zh-cn');
export default {
  name: "Index",
  components: {OneLog},
  data() {
    return {
      locale: zhCN,//传值给a-config-provider组件
      logData: [],
      oneLog: [],
      calendarData: null,
      calendarComponentKey: 0,
      oneLogComponentKey:0,
      openLogModelFlag: false,
      currentDate: ""
    }
  },
  watch: {
    calendarData: {
      handler: function (newVal, oldVal) {
        console.log(newVal)
        this.getLogs(newVal)
      },
      // 深度观察监听
      deep: true
    }
  },
  async created() {
    await this.getLogs(new moment())
    // 监听自定义事件
    this.$bus.$on('requestCompleted', () => {
      console.log('reloadComponent called')
      this.getLogs(new moment())
      this.calendarComponentKey += 1;
    });
  },
  methods: {
    getListData(value) {
      var temp = value
      let listData = [];
      this.logData.forEach(item => {
        if (item.created_at.split(" ")[0] === temp.format('YYYY-MM-DD')) {
          listData.push({
            type: item.status === 'failure' ? 'warning' : "success",
            content: item.type + ": " + item.name
          })
        }
      })
      return listData || [];
    },

    getPanelChange(moment) {
      console.log(moment)
      this.getLogs(moment)
    },
    updateLogModelStatus(status) {
      this.openLogModelFlag = status
      if (!status){
        this.oneLogComponentKey += 1
      }
    },

    getLogs(moment) {
      const temp = moment.clone();
      var startDate = temp.startOf('month').subtract(1, 'month').format('YYYY-MM-DD');
      var endDate = temp.endOf('month').add(2, 'month').format('YYYY-MM-DD');

      var param = "?start=" + startDate + "&target=" + endDate
      this.$request.getLog(param).then(res => {
        this.logData = res.data.data
      })
    },

    onSelect(value) {
      const date = moment(value);
      const formattedDate = date.format('YYYY-MM-DD');
      this.currentDate = formattedDate
      var param = "?created_at=" + formattedDate
      this.$request.getLog(param).then(res => {
        if (res.status === 200) {
          this.oneLog = res.data.data
          this.openLogModelFlag = true
        }
      })
    },
  }
}
</script>

<style scoped>
.events {
  list-style: none;
  margin: 0;
  padding: 0;
}

.events .ant-badge-status {
  overflow: hidden;
  white-space: nowrap;
  width: 100%;
  text-overflow: ellipsis;
  font-size: 12px;
}

.notes-month {
  text-align: center;
  font-size: 28px;
}

.notes-month section {
  font-size: 28px;
}

/deep/ .ant-radio-group {
  display: none !important;
}

</style>
