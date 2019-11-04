import axios from '@/configs/axios'
import queryBuilder from '../helpers/queryBuilder'
import servicefactory from './service'

export default servicefactory('payment-states', {
  getAllReport (query = {}) {
    return axios.get(`/payment-states/index_report${queryBuilder(query)}`)
  }
})
