// 引入axios
import axios from 'axios'

// 顶部loading
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

//axios打开允许跨域携带cookie信息
axios.defaults.withCredentials = true

// ant design的notification组件
import notification from 'ant-design-vue/lib/notification';
// 引入封装好的接口模块
import * as request from './request.js'

// 请求超时时间
axios.defaults.timeout = 100000

// 代理接口 部署时应该换为后台实际地址
const service = axios.create({
    baseURL: '/apis'
})

// 请求拦截器(在请求之前进行一些配置)
service.interceptors.request.use(
    config => {
        var url = config.url
        if (url !== '/functional/getSystemStatus'){
            // 加载顶部loading
            NProgress.start()
        }
        return config
    },
    err => {
        // Promise.reject()方法返回一个带有拒绝原因的Promise对象
        return Promise.reject(err)
    }
)

// 响应拦截器(响应之后对数据做一些处理)
service.interceptors.response.use(
    response => {
        // 关闭顶部loading
        NProgress.done()

        // 返回响应体
        return response
    },
    error => {
        const { data } = error.response
        // duration代表notification设置了不关闭，因为有些报错信息内容中可能存在需要复制的场景
        if (data.code ===404){
            notification.error({
                message: '未找到',
                description: error.response.data.message,
                duration: null
            });
        }else {
            notification.error({
                message: '错误的请求',
                description: error.response.data.message,
                duration: null
            });
        }

        // 关闭顶部loading
        NProgress.done()
        // 请求超时
        if (error.message.indexOf('timeout') !== -1) {
            notification.error({
                message: '请求超时，请稍后再试'
            });
        }
        return error
    }
)

export {
    request,
    service
}
