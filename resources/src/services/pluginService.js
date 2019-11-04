import servicefactory from './service'
import axios from '@/configs/axios'
export default servicefactory('plugins', {
  setStatus (payload) {
    return axios.patch(`/plugins/${payload.id}/settings/state`, payload.data)
  },
  updateSettings (payload) {
    return axios.patch(`/plugins/${payload.id}/settings`, payload.data)
  }
})
