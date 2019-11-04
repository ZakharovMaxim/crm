import axios from '@/configs/axios'
import service from './service'

export default service('users', {
  auth: function (credentials) {
    return axios.post('/authenticate', credentials)
  },
  restore: function (id) {
    return axios.patch(`/users/${id}/restore`)
  },
  updatePassword: function (payload) {
    return axios.patch(`/users/${payload.id}/password`, payload.data)
  },
  setImage: function (payload) {
    return axios.post(`/users/${payload.id}/image`, payload.data)
  },
  unsetImage: function (id) {
    return axios.delete(`/users/${id}/image`)
  },
  getRolesInfo: function () {
    return axios.get('/users/roles_info')
  },
  setRole: function (payload) {
    return axios.patch(`/users/${payload.id}/role`, {
      'rules': payload.data
    })
  }
})
