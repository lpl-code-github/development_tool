/*
    router配置
 */
import Vue from 'vue'
import Router from 'vue-router'


// Vue.use(),使用Router
Vue.use(Router)

//使用ES提出的方法：路由懒加载，优化前端页面
const routes = [
    {
        path: '/', redirect: '/index',
    },
    {
        path: '/index', component: () => import('@/Index')
    },
    {
        path: '/script', component: () => import('@/views/Script')
    },
    {
        path: '/debug', component: () => import('@/views/Debug')
    },
    {
        path: '/backup', component: () => import('@/views/Backup')
    },
    {
        path: '/newman', component: () => import('@/views/Newman')
    },
    {
        path: '/code-generator', component: () => import('@/views/CodeGenerator')
    },
    {
        path: '/slate-generator', component: () => import('@/views/SlateGenerator')
    },
    {
        path: '/postman-test-generator', component: () => import('@/views/PostmanTestGenerator')
    },
    //
    // {
    //     path: '/home', component: () => import('@/views/Home')
    // },
    // {
    //     path: '/collection', component: () => import('@/views/Collection')
    // },
    // {
    //     path: '/book', component: () => import('@/views/Book')
    // }
    /**
     * children 写法
     */
    // {
    //     path: '/console',
    //     component: () => import('@/views/Console'),
    //     redirect:'/overview',
    //     children:[
    //         {path: '/overview', component: () => import('@/components/Overview')},
    //         {path: '/ossAdmin', component: () => import('@/components/OssAdmin')},
    //         {path: '/system', component: () => import('@/components/System')},
    //         {path: '/log', component: () => import('@/components/Log')}
    //     ]
    // }
]


//这里使用了history模式，这种模式充分利用 history.pushState API来完成URL跳转而无须重新加载页面
const router = new Router({
    // mode: 'history', // base: process.env.BASE_URL,
    mode:'hash',// 打包app需要修改
    routes
})

//暴露router供main.js全局调用
export default router
