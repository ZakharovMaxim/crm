import servicefactory from './service'
import queryBuilder from '../helpers/queryBuilder'
import axios from '../configs/axios'

export default servicefactory('stock-actions', {
  getProducts (payload) {
    return axios.get(`/stocks/${payload.id}/products${queryBuilder(payload.query)}`)
  },
  createProducts (payload) {
    return axios.post(`/stock-actions/${payload.id}/products`, payload.data)
  },
  submit (payload) {
    return axios.patch(`/stock-actions/${payload.id}/submit`, payload.data)
  },
  deleteProduct (payload) {
    return axios.delete(`/stock-actions/${payload.action_id}/products/${payload.id}/?count=${payload.count}`)
  }
})
