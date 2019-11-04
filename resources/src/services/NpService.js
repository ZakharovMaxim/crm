import axios from '@/configs/axios'

export default {
  getForm (id) {
    return axios.get(`/orders/${id}/ttn`)
  },
  getWarehouses (payload) {
    return axios.get(`/orders/warehouses/${payload.code}`, payload.data)
  },
  createTtn (payload) {
    return axios.post(`/orders/${payload.orderId}/ttn`, payload.data)
  },
  getTtns (payload) {
    return axios.post(`/orders/ttn/get`, payload)
  }
}
