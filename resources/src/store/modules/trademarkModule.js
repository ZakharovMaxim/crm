import { TRADEMARK_MODULE, CATALOG_MODULE } from './TYPES'
import service from '../../services/TrademarkService'
const { TRADEMARKS, TRADEMARK, GET_TRADEMARKS, SET_TRADEMARKS, SET_TRADEMARK, CREATE_TRADEMARK, REMOVE_TRADEMARK, UPDATE_TRADEMARK } = TRADEMARK_MODULE
const { UPDATE_CATALOG_STATS } = CATALOG_MODULE

export default {
  state: {
    [TRADEMARK]: null,
    [TRADEMARKS]: []
  },
  mutations: {
    [SET_TRADEMARKS] (state, payload) {
      state[TRADEMARKS] = payload
    },
    [SET_TRADEMARK] (state, payload) {
      state[TRADEMARK] = payload
    },
    [CREATE_TRADEMARK] (state, payload) {
      state[TRADEMARKS] = [...state[TRADEMARKS], payload]
    },
    [REMOVE_TRADEMARK] (state, payload) {
      state[TRADEMARKS] = state[TRADEMARKS].filter(category => category.id !== payload)
    },
    [UPDATE_TRADEMARK] (state, payload) {
      state[TRADEMARKS] = state[TRADEMARKS].map(category => category.id === payload.id ? { ...category, ...payload.data } : category)
    }
  },
  actions: {
    [GET_TRADEMARKS] ({ commit }, payload) {
      return service.getAll(payload).then(response => {
        commit(SET_TRADEMARKS, response.data)
        return response
      })
    },
    [SET_TRADEMARK] ({ commit }, payload) {
      commit(SET_TRADEMARK, payload)
    },
    [CREATE_TRADEMARK] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_TRADEMARK, response.data)
        commit(UPDATE_CATALOG_STATS, {
          field: 'trademark',
          value: 1
        })
        return response
      })
    },
    [REMOVE_TRADEMARK] ({ commit }, payload) {
      return service.delete(payload).then(response => {
        commit(REMOVE_TRADEMARK, payload)
        commit(UPDATE_CATALOG_STATS, {
          field: 'trademark',
          value: -1
        })
        return response
      })
    },
    [UPDATE_TRADEMARK] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_TRADEMARK, payload)
        return response
      })
    }
  }
}
