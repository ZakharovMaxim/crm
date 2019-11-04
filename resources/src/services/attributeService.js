import axios from '@/configs/axios'
import servicefactory from './service'

export default servicefactory('attributes', {
  deleteValue: (id) => {
    return axios.delete(`attribute-values/${id}`)
  },
  updateValue: (payload) => {
    return axios.patch(`attribute-values/${payload.id}`, payload.data)
  },
  createValue: (payload) => {
    return axios.post('attribute-values', payload)
  }
})
