import service from '../../services/stockActionService'
import { STOCK_ACTION_MODULE, STOCK_MODULE } from './TYPES'
const { DESTROY_STOCK_PRODUCT, CLEAR_STOCK_PRODUCTS, DESTROY_STOCK_ACTION, SUBMIT_STOCK_ACTION, CREATE_STOCK_PRODUCTS,
  STOCK_PRODUCTS, GET_STOCK_PRODUCTS, SET_STOCK_PRODUCTS, CREATE_STOCK_ACTION, STOCK_ACTIONS, SET_STOCK_ACTIONS, SET_STOCK_ACTION, GET_STOCK_ACTION, GET_STOCK_ACTIONS, STOCK_ACTION, UPDATE_STOCK_ACTION } = STOCK_ACTION_MODULE
const { UPDATE_STOCK_STATS } = STOCK_MODULE
export default {
  state: {
    [STOCK_ACTIONS]: [],
    [STOCK_ACTION]: {},
    [STOCK_PRODUCTS]: []
  },
  mutations: {
    [CREATE_STOCK_ACTION] (state, payload) {
      if (payload.addToState) {
        state[STOCK_ACTIONS] = [payload.data, ...state[STOCK_ACTIONS]]
      }
    },
    [SET_STOCK_ACTIONS] (state, payload) {
      if (payload.page === 1) state[STOCK_ACTIONS] = payload.data
      else state[STOCK_ACTIONS] = [...state[STOCK_ACTIONS], ...payload.data]
    },
    [SET_STOCK_ACTION] (state, payload) {
      state[STOCK_ACTION] = payload
    },
    [UPDATE_STOCK_ACTION] (state, payload) {
      state[STOCK_ACTION] = {
        ...state[STOCK_ACTION],
        ...payload
      }
    },
    [SET_STOCK_PRODUCTS] (state, payload) {
      if (+payload.query.page === 1) {
        state[STOCK_PRODUCTS] = payload.data.items
      } else {
        state[STOCK_PRODUCTS] = [...state[STOCK_PRODUCTS], ...payload.data.items]
      }
    },
    [CREATE_STOCK_PRODUCTS] (state, payload) {
      state[STOCK_ACTION] = {
        ...state[STOCK_ACTION],
        products: payload
      }
    },
    [SUBMIT_STOCK_ACTION] (state, payload) {
      state[STOCK_ACTION] = {
        ...state[STOCK_ACTION],
        products: payload,
        is_submited: true
      }
    },
    [DESTROY_STOCK_ACTION] (state) {
      state[STOCK_ACTION] = {}
    },
    [CLEAR_STOCK_PRODUCTS] (state) {
      state[STOCK_PRODUCTS] = []
    },
    [DESTROY_STOCK_PRODUCT] (state, payload) {
      state[STOCK_ACTION] = {
        ...state[STOCK_ACTION],
        products: state[STOCK_ACTION].products.filter(p => p.id !== payload.id)
      }
    }
  },
  actions: {
    [CREATE_STOCK_ACTION] ({ commit }, payload) {
      return service.create(payload.data).then(response => {
        commit(CREATE_STOCK_ACTION, {
          data: response.data,
          addToState: payload.addToState
        })
        console.log(payload)
        commit(UPDATE_STOCK_STATS, {
          isAdd: true,
          type: +payload.data.type
        })
        return response
      })
    },
    [GET_STOCK_ACTIONS] ({ commit }, query) {
      return service.getAll(query).then(response => {
        commit(SET_STOCK_ACTIONS, {
          data: response.data.data,
          page: +query.page || 1
        })
        return response
      })
    },
    [GET_STOCK_ACTION] ({ commit }, id) {
      return service.get(id).then(response => {
        commit(SET_STOCK_ACTION, response.data)
        return response
      })
    },
    [UPDATE_STOCK_ACTION] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_STOCK_ACTION, payload.data)
        return response
      })
    },
    [GET_STOCK_PRODUCTS] ({ commit }, payload) {
      return service.getProducts(payload).then(response => {
        commit(SET_STOCK_PRODUCTS, {
          data: response.data,
          query: payload.query
        })
        return response
      })
    },
    [CREATE_STOCK_PRODUCTS] ({ commit }, payload) {
      return service.createProducts(payload).then(response => {
        commit(CREATE_STOCK_PRODUCTS, response.data)
        return response
      })
    },
    [SUBMIT_STOCK_ACTION] ({ commit }, payload) {
      return service.submit(payload).then(response => {
        commit(UPDATE_STOCK_STATS, {
          type: +payload.type,
          isSubmit: true
        })
        commit(SUBMIT_STOCK_ACTION, response.data)
        return response
      })
    },
    [DESTROY_STOCK_ACTION] ({ commit, state }, payload) {
      return service.delete(payload).then(response => {
        commit(UPDATE_STOCK_STATS, {
          type: +state[STOCK_ACTIONS].find(action => action.id === payload).type
        })
        commit(DESTROY_STOCK_ACTION, response.data)
        return response
      })
    },
    [CLEAR_STOCK_PRODUCTS] ({ commit }) {
      commit(CLEAR_STOCK_PRODUCTS)
    },
    [DESTROY_STOCK_PRODUCT] ({ commit }, payload) {
      return service.deleteProduct(payload).then(response => {
        commit(DESTROY_STOCK_PRODUCT, payload)
        return response
      })
    }
  }
}
