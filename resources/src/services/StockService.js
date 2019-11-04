import axios from '@/configs/axios'
import servicefactory from './service'

export default servicefactory('stocks', {
  setEditData (payload) {
    return axios.patch(`/stocks/${payload.id}/folders`, payload.data)
  }
})
