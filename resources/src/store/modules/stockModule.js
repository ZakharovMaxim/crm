import { STOCK_MODULE } from './TYPES'
import service from '../../services/StockService'
const { UPDATE_STOCK_STATS, STOCK_STATS, SET_STOCKS, GET_STOCKS, STOCKS, ROOT_FOLDERS, GET_STOCK_CATALOGS, SET_STOCK_CATALOGS, STOCK, UPDATE_STOCK_CATALOGS } = STOCK_MODULE

export default {
  state: {
    [STOCKS]: [],
    [ROOT_FOLDERS]: [],
    [STOCK]: null,
    [STOCK_STATS]: {}
  },
  mutations: {
    [SET_STOCKS] (state, payload) {
      state[STOCKS] = payload.stocks
      state[STOCK_STATS] = payload.stats
    },
    [SET_STOCK_CATALOGS] (state, payload) {
      state[ROOT_FOLDERS] = payload.folders
      state[STOCK] = payload.stock
    },
    [UPDATE_STOCK_STATS] (state, payload) {
      const value = payload.isAdd ? 1 : -1
      const createdField = payload.type === 1 ? 'created_enrollment' : payload.type === 2 ? 'created_writeoff' : 'created_movement'
      const submitedField = payload.type === 1 ? 'submited_enrollment' : payload.type === 2 ? 'submited_writeoff' : 'submited_movement'
      const totalField = payload.type === 1 ? 'total_enrollment' : payload.type === 2 ? 'total_writeoff' : 'total_movement'
      const newStats = { ...state[STOCK_STATS] }
      if (payload.isSubmit) {
        newStats[submitedField] = state[STOCK_STATS][submitedField] + 1
        newStats[createdField] = state[STOCK_STATS][createdField] - 1
      } else {
        newStats[createdField] = state[STOCK_STATS][createdField] + value
        newStats[totalField] = state[STOCK_STATS][totalField] + value
      }
      state[STOCK_STATS] = newStats
    }
  },
  actions: {
    [GET_STOCKS] ({ commit }) {
      return service.getAll().then(response => {
        commit(SET_STOCKS, response.data)
        return response
      })
    },
    [GET_STOCK_CATALOGS] ({ commit }, id) {
      return service.getEditData(id).then(response => {
        commit(SET_STOCK_CATALOGS, response.data)
        return response
      })
    },
    [UPDATE_STOCK_CATALOGS] ({ commit }, payload) {
      return service.setEditData(payload).then(response => {
        return response
      })
    }
  }
}
