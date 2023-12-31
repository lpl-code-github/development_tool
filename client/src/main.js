import Vue from 'vue'
import App from './App.vue'
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import router from './router/index'
// 代码高亮
import hl from 'highlight.js';
// 代码复制组件
import clipboard from 'clipboard'
// 创建事件总线实例
const bus = new Vue();
// 引入公共css
import './assets/css/common.css'
// 引入封装好的axios请求
import {request} from '@/api/index'
// 按需引入echarts
import * as echarts from 'echarts/core';
import { TooltipComponent, LegendComponent } from 'echarts/components';
import { PieChart } from 'echarts/charts';
import { LabelLayout } from 'echarts/features';
import { CanvasRenderer } from 'echarts/renderers';
echarts.use([
    TooltipComponent,
    LegendComponent,
    PieChart,
    CanvasRenderer,
    LabelLayout
]);
// markdown编辑器
import mavonEditor from 'mavon-editor'

Vue.prototype.$request = request
Vue.prototype.$echarts = echarts
Vue.prototype.clipboard = clipboard
// 将事件总线实例挂载到Vue原型上
Vue.prototype.$bus = bus;
Vue.config.productionTip = false

Vue.use(Antd);

// use
Vue.use(mavonEditor)
// 自定义一个代码高亮指令
// Vue.directive('highlight', function (el) {
//     const blocks = el.querySelectorAll('pre code')
//     blocks.forEach((block) => {
//         hl.highlightBlock(block)
//     })
// })

Vue.directive('myHighlight', {
    deep: true,
    bind: function(el, binding) {
        // on first bind, highlight all targets
        const blocks = el.querySelectorAll('pre code')
        blocks.forEach((block) => {
            hl.highlightBlock(block)
        })
    },
    componentUpdated: function(el) {
        // 获取需要进行语法高亮的代码元素
        const blocks = el.querySelectorAll('pre code');
        blocks.forEach((block) => {
            // 使用highlight.js进行语法高亮
            hl.highlightBlock(block);
        });
    }
})
new Vue({
    router,
    render: h => h(App),
}).$mount('#app')
