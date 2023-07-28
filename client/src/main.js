import Vue from 'vue'
import App from './App.vue'
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import router from './router/index'
// 引入公共css
import './assets/css/common.css'
// 引入封装好的axios请求
import {request} from '@/api/index'

Vue.prototype.$request = request

Vue.config.productionTip = false

Vue.use(Antd);

new Vue({
    router,
    render: h => h(App),
}).$mount('#app')
