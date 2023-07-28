import Vue from 'vue'
import App from './App.vue'
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import router from './router/index'
// 引入公共css
import './assets/css/common.css'

Vue.use(Antd);
Vue.config.productionTip = false

new Vue({
    router,
    render: h => h(App),
}).$mount('#app')
