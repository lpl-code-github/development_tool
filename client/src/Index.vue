<template>
  <div class="index">
    <a-calendar :locale="locale" @select="onSelect"  @panelChange="getPanelChange" :key="componentKey">
      <ul slot="dateCellRender" slot-scope="value" class="events" >
        <li v-for="(item,index) in getListData(value)" :key="index">
          <a-badge :status="item.type" :text="item.content" />
        </li>
      </ul>
    </a-calendar>
  </div>
</template>

<script>
import zhCN from 'ant-design-vue/es/locale/zh_CN'; //引入antd中文包
import moment from 'moment';
//引入moment
moment.locale('zh-cn');//配置moment中文环境
export default {
  name: "Index",
  data(){
    return{
      locale:zhCN,//传值给a-config-provider组件
      logData:[],
      calendarData:null,
      componentKey: 0
    }
  },
  watch: {
    calendarData: {
      handler: function (newVal,oldVal) {
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
      this.componentKey += 1;
    });
  },
  methods: {
    getListData(value) {
      var temp = value
      let listData = [];
      this.logData.forEach(item=>{
        if (item.created_at.split(" ")[0] === temp.format('YYYY-MM-DD')){
          listData.push({ type: item.status === 'failure' ? 'warning' : "success", content:item.type +": "+item.name })
        }
      })
      return listData || [];
    },

    getPanelChange(moment){
      console.log(moment)
      this.getLogs(moment)
    },

    getLogs(moment){
      const temp = moment.clone();
      var startDate =  temp.startOf('month').subtract(1, 'month').format('YYYY-MM-DD');
      var endDate =  temp.endOf('month').add(2, 'month').format('YYYY-MM-DD');

      var param = "?start="+startDate+"&target="+endDate
      this.$request.getLog(param).then(res=>{
        this.logData = res.data.data
      })
    },

    onSelect(value) {
      const date = moment(value);
      const formattedDate = date.format('YYYY-MM-DD');
      var param = "?created_at="+formattedDate
      this.$request.getLog(param).then(res=>{
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
  display: none!important;
}

</style>
