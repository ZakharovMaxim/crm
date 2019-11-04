import { PRICE_GROUP_MODULE, CATALOG_MODULE } from './TYPES'
import service from '../../services/PriceGroupService'
const { PRICE_GROUPS, PRICE_GROUP, SET_PRICE_GROUPS, GET_PRICE_GROUPS, SET_PRICE_GROUP, CREATE_PRICE_GROUP, REMOVE_PRICE_GROUP, UPDATE_PRICE_GROUP } = PRICE_GROUP_MODULE
const { UPDATE_CATALOG_STATS } = CATALOG_MODULE

export default {
  state: {
    [PRICE_GROUP]: null,
    [PRICE_GROUPS]: []
  },
  mutations: {
    [SET_PRICE_GROUPS] (state, payload) {
      state[PRICE_GROUPS] = payload
    },
    [SET_PRICE_GROUP] (state, payload) {
      state[PRICE_GROUP] = payload
    },
    [CREATE_PRICE_GROUP] (state, payload) {
      state[PRICE_GROUPS] = [...state[PRICE_GROUPS], payload]
    },
    [REMOVE_PRICE_GROUP] (state, payload) {
      state[PRICE_GROUPS] = state[PRICE_GROUPS].filter(category => category.id !== payload)
    },
    [UPDATE_PRICE_GROUP] (state, payload) {
      state[PRICE_GROUPS] = state[PRICE_GROUPS].map(category => category.id === payload.id ? { ...category, ...payload.data } : category)
    }
  },
  actions: {
    [GET_PRICE_GROUPS] ({ commit }, payload) {
      return service.getAll(payload).then(response => {
        commit(SET_PRICE_GROUPS, response.data)
        return response
      })
    },
    [SET_PRICE_GROUP] ({ commit }, payload) {
      commit(SET_PRICE_GROUP, payload)
    },
    [CREATE_PRICE_GROUP] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_PRICE_GROUP, response.data)
        commit(UPDATE_CATALOG_STATS, {
          field: 'price_group',
          value: 1
        })
        return response
      })
    },
    [REMOVE_PRICE_GROUP] ({ commit }, payload) {
      return service.delete(payload).then(response => {
        commit(REMOVE_PRICE_GROUP, payload)
        commit(UPDATE_CATALOG_STATS, {
          field: 'price_group',
          value: -1
        })
        return response
      })
    },
    [UPDATE_PRICE_GROUP] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_PRICE_GROUP, payload)
        return response
      })
    }
  }
}
