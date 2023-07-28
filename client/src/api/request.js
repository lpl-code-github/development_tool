import {service} from '@/api/index'


export const switchApi = (type, flag) => {
    return service
        .request({
            url: '/quickSwitch',
            method: 'put',
            data: {
                data:{
                    type: type,
                    flag: flag,
                }
            }
        })
        .then(res=> res.response)
}

export const switchStatus = () => {
    return service
        .request({
            url: '/switchStatus',
            method: 'get',
        })
        .then(res=> res.response)
}
