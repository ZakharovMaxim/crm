import axios from '@/configs/axios'
import queryBuilder from '../helpers/queryBuilder'
import servicefactory from './service'

export default servicefactory('orders', {
  getAllReport (query = {}) {
    return axios.get(`/orders/index_report${queryBuilder(query)}`)
  },
  getOrdersInfo (query = {}) {
    return axios.get(`/orders/info/all${queryBuilder(query)}`)
  },
  createInfo () {
    return axios.get('/orders/create')
  },
  updateField (payload) {
    return axios.patch(`/orders/${payload.id}/fields`, payload)
  },
  addProducts (payload) {
    return axios.post(`/orders/${payload.id}/products`, payload.data)
  },
  deleteProduct (payload) {
    return axios.delete(`/orders/${payload.orderId}/products/${payload.productId}`)
  },
  updateProduct (payload) {
    return axios.patch(`/orders/${payload.orderId}/products`, payload)
  },
  updateStatus (payload) {
    return axios.patch(`/orders/${payload.orderId}/status`, { to: payload.to })
  },
  setDiscount (payload) {
    return axios.patch(`/orders/${payload.id}/discount`, payload)
  },
  getChartInfo (query) {
    return axios.get(`/orders/info/stats${queryBuilder(query)}`)
  },
  setTtn (payload) {
    return axios.patch(`/orders/${payload.id}/ttn`, payload.data)
  }
})
