import service from '@/services/ShopService'
import { PLUGIN_MODULE, SHOP_MODULE } from './TYPES'
const { SET_SHOPS, SET_SHOP, CREATE_SHOP, UPDATE_SHOP, SHOPS, SHOP, DESTROY_SHOP, GET_SHOPS, GET_SHOP } = SHOP_MODULE
const { SET_PLUGINS } = PLUGIN_MODULE

export default {
  state: {
    [SHOPS]: [],
    [SHOP]: null
  },
  mutations: {
    [SET_SHOPS] (state, payload) {
      state[SHOPS] = payload
    },
    [SET_SHOP] (state, payload) {
      state[SHOP] = payload
    },
    [CREATE_SHOP] (state, payload) {
      state[SHOPS] = [...state[SHOPS], payload]
    },
    [UPDATE_SHOP] (state, payload) {
      state[SHOPS] = state[SHOPS].map(shop => +shop.id === +payload.id ? ({ ...shop, ...payload.data }) : shop)
    },
    [DESTROY_SHOP] (state, id) {
      state[SHOPS] = state[SHOPS].filter(shop => shop.id !== id)
      state[SHOP] = null
    }
  },
  actions: {
    [GET_SHOPS] ({ commit }) {
      return service.getAll().then(response => {
        commit(SET_SHOPS, response.data.shops)
        commit(SET_PLUGINS, response.data.plugins)
        return response
      })
    },
    [SET_SHOP] ({ commit }, payload) {
      commit(SET_SHOP, payload)
    },
    [GET_SHOP] ({ commit }, id) {
      return service.get(id).then(response => {
        commit(SET_SHOP, response.data)
        return response
      })
    },
    [CREATE_SHOP] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_SHOP, response.data)
        return response
      })
    },
    [UPDATE_SHOP] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_SHOP, payload)
        return response
      })
    },
    [DESTROY_SHOP] ({ commit }, id) {
      return service.delete(id).then(response => {
        commit(DESTROY_SHOP, id)
        return response
      })
    }
  }
}
