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

export const getEntityLists = () => {
    return service
        .request({
            url: '/functional/getEntityLists',
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
