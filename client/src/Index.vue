<template>
  <div class="index">
    <a-calendar :locale="locale" @select="onSelect">
      <ul slot="dateCellRender" slot-scope="value" class="events" >
        <li v-for="item in getListData(value)" :key="item.content">
          <a-badge :status="item.type" :text="item.content" />
        </li>
      </ul>
      <template slot="monthCellRender" slot-scope="value">
        <div v-if="getMonthData(value)" class="notes-month">
          <section>{{ getMonthData(value) }}</section>
          <span>Backlog number</span>
        </div>
      </template>
    </a-calendar>
  </div>
</template>

<script>
import zhCN from 'ant-design-vue/es/locale/zh_CN';//引入antd中文包
import moment from 'moment';//引入moment
moment.locale('zh-cn');//配置moment中文环境
export default {
  name: "Index",
  data(){
    return{
      locale:zhCN,//传值给a-config-provider组件
    }
  },
  methods: {
    getListData(value) {
      let listData;
      switch (value.date()) {
        case 8:
          listData = [
            { type: 'warning', content: 'This is warning event.' },
            { type: 'success', content: 'This is usual event.' },
          ];
          break;
        case 10:
          listData = [
            { type: 'warning', content: 'This is warning event.' },
            { type: 'success', content: 'This is usual event.' },
            { type: 'error', content: 'This is error event.' },
          ];
          break;
        case 15:
          listData = [
            { type: 'warning', content: 'This is warning event' },
            { type: 'success', content: 'This is very long usual event。。....' },
            { type: 'error', content: 'This is error event 1.' },
            { type: 'error', content: 'This is error event 2.' },
            { type: 'error', content: 'This is error event 3.' },
            { type: 'error', content: 'This is error event 4.' },
          ];
          break;
        default:
      }
      return listData || [];
    },

    getMonthData(value) {
      if (value.month() === 8) {
        return 1394;
      }
    },

    onSelect(value) {
      const date = moment(value);
      const formattedDate = date.format('YYYY-MM-DD');

      console.log(formattedDate);
    },
  },
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
