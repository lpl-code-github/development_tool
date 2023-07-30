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
            method: 'get',
        })
        .then(res=> res)
}
