import {service} from '@/api/index'


/*
    快捷开关的请求
 */
export const switchApi = (type, flag) => {
    return service
        .request({
            url: '/functional/quickSwitch',
            method: 'put',
            data: {
                data:{
                    type: type,
                    flag: flag,
                }
            }
        })
        .then(res=> res)
}
export const switchStatus = () => {
    return service
        .request({
            url: '/functional/switchStatus',
            method: 'get',
        })
        .then(res=> res)
}
export const clearR1Cache = () => {
    return service
        .request({
            url: '/functional/clearCache',
            method: 'post',
        })
        .then(res=> res)
}

/*
    r1概览的请求
 */
// docker容器硬件信息
export const getDockerSystemStatus = () => {
    return service
        .request({
            url: '/functional/getSystemStatus',
            method: 'get',
        })
        .then(res=> res)
}
// 获取容器top5的进程
export const getPs = () => {
    return service
        .request({
            url: '/functional/getPs',
            method: 'get',
        })
        .then(res=> res)
}

// r1所有api信息
export const getR1Api = () => {
    return service
        .request({
            url: '/functional/getApiInfo',
            method: 'get',
        })
        .then(res=> res)
}

/*
    生成器的一些请求
 */
export const generatePostmanTest = (param) => {
    return service
        .request({
            url: '/functional/generatePostmanTest',
            method: 'post',
            data: param
        })
        .then(res=> res)
}
export const generateCode = (entityName,type) => {
    return service
        .request({
            url: '/functional/generateCode?entity_name='+entityName+"&type="+type,
            method: 'get',
        })
        .then(res=> res)
}
export const getSlateDoc = (param) => {
    return service
        .request({
            url: '/functional/generateSlateDoc'+param,
            method: 'get',
        })
        .then(res=> res)
}
export const generateTsInterface = (data,param) => {
    return service
        .request({
            url: '/functional/generateTsInterface'+param,
            method: 'post',
            data: data
        })
        .then(res=> res)
}
export const generateTsServiceCode = (param) => {
    return service
        .request({
            url: '/functional/generateTsService'+param,
            method: 'get',
        })
        .then(res=> res)
}
export const generateAllTsServiceCode = () => {
    return service
        .request({
            url: '/functional/generateTsService?all=true',
            method: 'get',
            responseType: 'blob',
        })
        .then(res=> res)
}


/*
    获取日志的请求
 */
export const getLog = (param) => {
    return service
        .request({
            url: '/resource/operation_log'+param,
            method: 'get',
        })
        .then(res=> res)
}

/*
    获取r1下某个目录下文件列表
 */
export const getFileLists = (param) => {
    return service
        .request({
            url: '/functional/getFileLists' + param,
            method: 'get',
        })
        .then(res=> res)
}

/*
    获取r1所在数据库服务器下所有数据库列表
 */
export const getDatabaseList = () => {
    return service
        .request({
            url: '/functional/getDatabaseList',
            method: 'get',
        })
        .then(res=> res)
}

/*
    DatabaseBackup 的接口
 */
export const getDatabaseBackup = (params) => {
    return service
        .request({
            url: '/resource/databaseBackup'+params,
            method: 'get',
        })
        .then(res=> res)
}
export const postDatabaseBackup = (param) => {
    return service
        .request({
            url: '/resource/databaseBackup',
            method: 'post',
            data:param
        })
        .then(res=> res)
}
export const putDatabaseBackup = (param) => {
    return service
        .request({
            url: '/resource/databaseBackup',
            method: 'put',
            data:param
        })
        .then(res=> res)
}
export const deleteDatabaseBackup = (param) => {
    return service
        .request({
            url: '/resource/databaseBackup',
            method: 'delete',
            data:param
        })
        .then(res=> res)
}
export const importDatabaseBackup = (param) => {
    return service
        .request({
            url: '/resource/databaseBackup/import',
            method: 'post',
            data:param
        })
        .then(res=> res)
}

/*
    NewmanTasks 以及 runNewman的接口
 */
export const getNewmanTasks = (param) => {
    return service
        .request({
            url: '/resource/newman_tasks'+param,
            method: 'get'
        })
        .then(res=> res)
}
export const postNewmanTasks = (param) => {
    return service
        .request({
            url: '/resource/newman_tasks',
            method: 'post',
            data:param
        })
        .then(res=> res)
}
export const putNewmanTasks = (param) => {
    return service
        .request({
            url: '/resource/newman_tasks',
            method: 'put',
            data:param
        })
        .then(res=> res)
}
export const deleteNewmanTasks = (param) => {
    return service
        .request({
            url: '/resource/newman_tasks',
            method: 'delete',
            data:param
        })
        .then(res=> res)
}
export const runNewman = (param) => {
    return service
        .request({
            url: '/functional/runNewman',
            method: 'post',
            data: param
        })
        .then(res=> res)
}

/*
    ScriptData的接口
 */
export const getScriptData = (param) => {
    return service
        .request({
            url: '/resource/scripts'+param,
            method: 'get'
        })
        .then(res=> res)
}
export const postScriptData = (param) => {
    return service
        .request({
            url: '/resource/scripts',
            data: param,
            method: 'post'
        })
        .then(res=> res)
}
export const putScriptData = (param) => {
    return service
        .request({
            url: '/resource/scripts',
            data: param,
            method: 'put'
        })
        .then(res=> res)
}
export const deleteScriptData = (params) => {
    return service
        .request({
            url: '/resource/scripts',
            data: params,
            method: 'delete'
        })
        .then(res=> res)
}
export const runScript = (params) => {
    return service
        .request({
            url: '/functional/runScript',
            data: params,
            method: 'post'
        })
        .then(res=> res)
}

/*
    Tags的接口
 */
export const getTags = () => {
    return service
        .request({
            url: '/resource/tags',
            method: 'get'
        })
        .then(res=> res)
}
export const postTags = (params) => {
    return service
        .request({
            url: '/resource/tags',
            data: params,
            method: 'post'
        })
        .then(res=> res)
}

/*
    文件相关的接口
 */
export const downloadFile = (path) => {
    return service
        .request({
            url: '/functional/downloadFile',
            data:{
                data:{
                    path:path
                }
            },
            responseType: 'blob',
            method: 'post'
        })
        .then(res=> res)
}
export const uploadFile = (data,type) => {
    return service
        .request({
            url: '/functional/uploadFile?type='+type,
            maxBodyLength: Infinity,
            data : data,
            method: 'post'
        })
        .then(res=> res)
}
