import axios from '@/configs/axios'
import queryBuilder from '../helpers/queryBuilder'

export default (name, extend) => {
  const result = {
    getAll: (query) => {
      return axios.get(`/${name + (query ? queryBuilder(query) : '')}`)
    },
    get: (id, additionalInfo) => {
      if (!id) throw new Error('id is required')
      return axios.get(`/${name}/${id}${additionalInfo ? '?info=1' : ''}`)
    },
    create: (data) => {
      return axios.post(`/${name}`, data)
    },
    update: (payload) => {
      return axios.patch(`/${name}/${payload.id}`, payload.data)
    },
    delete: (id) => {
      return axios.delete(`/${name}/${id}`)
    },
    getEditData (id) {
      return axios.get(`/${name}/${id}/edit`)
    },
    getCreateData (query) {
      return axios.get(`/${name}/create${query ? queryBuilder(query) : ''}`)
    }
  }
  if (extend && typeof extend === 'object') {
    return {
      ...result,
      ...extend
    }
  } else {
    return result
  }
}
