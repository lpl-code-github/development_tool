import {service} from '@/api/index'


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

export const getR1Api = () => {
    return service
        .request({
            url: '/functional/getApiInfo',
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


export const generatePostmanTest = (data) => {
    return service
        .request({
            url: '/functional/generatePostmanTest',
            method: 'post',
            data: data
        })
        .then(res=> res)
}

export const getLog = (param) => {
    return service
        .request({
            url: '/resource/operation_log'+param,
            method: 'get',
        })
        .then(res=> res)
}

export const getFileLists = (param) => {
    return service
        .request({
            url: '/functional/getFileLists' + param,
            method: 'get',
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

export const getDatabaseList = () => {
    return service
        .request({
            url: '/functional/getDatabaseList',
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

export const getDatabaseBackup = (params) => {
    return service
        .request({
            url: '/resource/databaseBackup'+params,
            method: 'get',
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

export const getNewmanTasks = (param) => {
    return service
        .request({
            url: '/resource/newman_tasks'+param,
            method: 'get'
        })
        .then(res=> res)
}


export const putNewmanTasksLog = (param) => {
    return service
        .request({
            url: '/resource/newman_tasks',
            method: 'put',
            data: param
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
