import axios from '@/configs/axios'
import queryBuilder from '../helpers/queryBuilder'
import servicefactory from './service'

export default servicefactory('payments', {
  getAllReport (query = {}) {
    return axios.get(`/payments/index_report${queryBuilder(query)}`)
  },
  createInfo () {
    return axios.get('/payments/create')
  },
  getInfo () {
    return axios.get('/payments/info')
  }
})
