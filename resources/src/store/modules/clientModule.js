import { CLIENT_MODULE } from './TYPES'
import service from '@/services/clientService'
const { CLIENTS, CLIENT, SET_CLIENT, SET_CLIENTS, GET_CLIENTS, GET_CLIENT } = CLIENT_MODULE

export default {
  state: {
    [CLIENTS]: [],
    [CLIENT]: {}
  },
  mutations: {
    [SET_CLIENT] (state, payload) {
      state[CLIENT] = payload
    },
    [SET_CLIENTS] (state, payload) {
      state[CLIENTS] = payload
    }
  },
  actions: {
    [GET_CLIENTS] ({ commit }) {
      return service.getAll().then(response => {
        commit(SET_CLIENTS, response.data)
      })
    },
    [GET_CLIENT] ({ commit }, id) {
      return service.get(id).then(response => {
        commit(SET_CLIENT, response.data)
      })
    }
  }
}
