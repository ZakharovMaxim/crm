import { CATALOG_MODULE, CURRENCY_MODULE } from './TYPES'
import service from '../../services/CurrencyService'
const { CURRENCIES, CURRENCY, SET_CURRENCIES, GET_CURRENCIES, SET_CURRENCY, CREATE_CURRENCY, REMOVE_CURRENCY, UPDATE_CURRENCY } = CURRENCY_MODULE
const { UPDATE_CATALOG_STATS } = CATALOG_MODULE

export default {
  state: {
    [CURRENCY]: null,
    [CURRENCIES]: []
  },
  mutations: {
    [SET_CURRENCIES] (state, payload) {
      state[CURRENCIES] = payload
    },
    [SET_CURRENCY] (state, payload) {
      state[CURRENCY] = payload
    },
    [CREATE_CURRENCY] (state, payload) {
      state[CURRENCIES] = [...state[CURRENCIES], payload]
    },
    [REMOVE_CURRENCY] (state, payload) {
      state[CURRENCIES] = state[CURRENCIES].filter(category => category.id !== payload)
    },
    [UPDATE_CURRENCY] (state, payload) {
      state[CURRENCIES] = state[CURRENCIES].map(category => category.id === payload.id ? { ...category, ...payload.data } : category)
    }
  },
  actions: {
    [GET_CURRENCIES] ({ commit }, payload) {
      return service.getAll(payload).then(response => {
        commit(SET_CURRENCIES, response.data)
        return response
      })
    },
    [SET_CURRENCY] ({ commit }, payload) {
      commit(SET_CURRENCY, payload)
    },
    [CREATE_CURRENCY] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_CURRENCY, response.data)
        commit(UPDATE_CATALOG_STATS, {
          field: 'currencies',
          value: 1
        })
        return response
      })
    },
    [REMOVE_CURRENCY] ({ commit }, payload) {
      return service.delete(payload).then(response => {
        commit(REMOVE_CURRENCY, payload)
        commit(UPDATE_CATALOG_STATS, {
          field: 'currencies',
          value: -1
        })
        return response
      })
    },
    [UPDATE_CURRENCY] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_CURRENCY, payload)
        return response
      })
    }
  }
}
