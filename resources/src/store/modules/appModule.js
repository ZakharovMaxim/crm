import { IS_SIDEBAR_OPEN, SET_SIDEBAR_STATE, MODULES_MODULE } from './TYPES'
import service from '@/services/appService'
const { ALL_MODULES, SET_ALL_MODULES, GET_MODULES, SET_MODULES, MODULES, IS_MODULES_LOADED } = MODULES_MODULE

export default {
  state: {
    [ALL_MODULES]: [],
    [MODULES]: [],
    [IS_MODULES_LOADED]: false,
    [IS_SIDEBAR_OPEN]: true
  },
  mutations: {
    [SET_ALL_MODULES] (state, payload) {
      state[ALL_MODULES] = payload
    },
    [SET_MODULES] (state, payload) {
      state[MODULES] = payload
      state[IS_MODULES_LOADED] = true
    },
    [SET_SIDEBAR_STATE] (state, payload) {
      state[IS_SIDEBAR_OPEN] = payload
    }
  },
  actions: {
    [SET_SIDEBAR_STATE] ({ commit }, payload) {
      commit(SET_SIDEBAR_STATE, !!payload)
    },
    [GET_MODULES] ({ commit, state }) {
      if (state[IS_MODULES_LOADED]) {
        return Promise.resolve(state[MODULES])
      }
      return service.getAll().then(response => {
        commit(SET_MODULES, response.data)
      })
    }
  }
}
