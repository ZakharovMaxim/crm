import { PAYMENT_STATE_MODULE, PAYMENT_CATEGORY_MODULE, BILL_MODULE, SHOP_MODULE } from './TYPES'
import service from '../../services/PaymentCategoryService'
import stateService from '../../services/PaymentStateService'
const { GET_PAYMENT_STATES_REPORT, INCOME_PAYMENT_STATES, OUTCOME_PAYMENT_STATES, PAYMENT_STATES, PAYMENT_STATE, SET_PAYMENT_STATES, SET_PAYMENT_STATE, CREATE_PAYMENT_STATE, REMOVE_PAYMENT_STATE, UPDATE_PAYMENT_STATE } = PAYMENT_STATE_MODULE
const { INCOME_PAYMENT_CATEGORIES, OUTCOME_PAYMENT_CATEGORIES, PAYMENT_CATEGORIES, PAYMENT_CATEGORY, GET_PAYMENT_CATEGORIES, SET_PAYMENT_CATEGORIES, SET_PAYMENT_CATEGORY, CREATE_PAYMENT_CATEGORY, REMOVE_PAYMENT_CATEGORY, UPDATE_PAYMENT_CATEGORY } = PAYMENT_CATEGORY_MODULE
const { SET_BILLS } = BILL_MODULE
const { SET_SHOPS } = SHOP_MODULE

export default {
  state: {
    [PAYMENT_CATEGORY]: null,
    [PAYMENT_CATEGORIES]: [],
    [PAYMENT_STATE]: null,
    [PAYMENT_STATES]: []
  },
  getters: {
    [INCOME_PAYMENT_STATES] (state) {
      return state[PAYMENT_STATES].filter(cat => +cat.type === 1)
    },
    [OUTCOME_PAYMENT_STATES] (state) {
      return state[PAYMENT_STATES].filter(cat => +cat.type === 2)
    },
    [INCOME_PAYMENT_CATEGORIES] (state) {
      return state[PAYMENT_CATEGORIES].filter(cat => +cat.type === 1)
    },
    [OUTCOME_PAYMENT_CATEGORIES] (state) {
      return state[PAYMENT_CATEGORIES].filter(cat => +cat.type === 2)
    }
  },
  mutations: {
    [SET_PAYMENT_CATEGORIES] (state, payload) {
      state[PAYMENT_CATEGORIES] = payload
    },
    [SET_PAYMENT_CATEGORY] (state, payload) {
      state[PAYMENT_CATEGORY] = payload
    },
    [CREATE_PAYMENT_CATEGORY] (state, payload) {
      state[PAYMENT_CATEGORIES] = [...state[PAYMENT_CATEGORIES], payload]
    },
    [REMOVE_PAYMENT_CATEGORY] (state, payload) {
      state[PAYMENT_CATEGORIES] = state[PAYMENT_CATEGORIES].filter(category => category.id !== payload)
    },
    [UPDATE_PAYMENT_CATEGORY] (state, payload) {
      state[PAYMENT_CATEGORIES] = state[PAYMENT_CATEGORIES].map(category => category.id === payload.id ? { ...category, ...payload.data } : category)
    },
    [SET_PAYMENT_STATES] (state, payload) {
      state[PAYMENT_STATES] = payload
    },
    [SET_PAYMENT_STATE] (state, payload) {
      state[PAYMENT_STATE] = payload
    },
    [CREATE_PAYMENT_STATE] (state, payload) {
      state[PAYMENT_STATES] = [...state[PAYMENT_STATES], payload]
    },
    [REMOVE_PAYMENT_STATE] (state, payload) {
      state[PAYMENT_STATES] = state[PAYMENT_STATES].filter(state => state.id !== payload)
    },
    [UPDATE_PAYMENT_STATE] (state, payload) {
      state[PAYMENT_STATES] = state[PAYMENT_STATES].map(state => state.id === payload.id ? { ...state, ...payload.data } : state)
    }
  },
  actions: {
    [GET_PAYMENT_CATEGORIES] ({ commit }, payload) {
      return service.getAll(payload).then(response => {
        commit(SET_PAYMENT_CATEGORIES, response.data)
        return response
      })
    },
    [SET_PAYMENT_CATEGORY] ({ commit }, payload) {
      commit(SET_PAYMENT_CATEGORY, payload)
    },
    [CREATE_PAYMENT_CATEGORY] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_PAYMENT_CATEGORY, response.data)
        return response
      })
    },
    [REMOVE_PAYMENT_CATEGORY] ({ commit }, payload) {
      return service.delete(payload).then(response => {
        commit(REMOVE_PAYMENT_CATEGORY, payload)
        return response
      })
    },
    [UPDATE_PAYMENT_CATEGORY] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_PAYMENT_CATEGORY, payload)
        return response
      })
    },
    [SET_PAYMENT_STATES] ({ commit }, query) {
      return stateService.getAll(query).then(response => {
        return setPaymentStates(commit, response, query)
      })
    },
    [GET_PAYMENT_STATES_REPORT] ({ commit }, query) {
      return stateService.getAllReport(query).then(response => {
        return setPaymentStates(commit, response, query)
      })
    },
    [SET_PAYMENT_STATE] ({ commit }, payload) {
      commit(SET_PAYMENT_STATE, payload)
    },
    [CREATE_PAYMENT_STATE] ({ commit }, payload) {
      return stateService.create(payload).then(response => {
        commit(CREATE_PAYMENT_STATE, response.data)
        return response
      })
    },
    [REMOVE_PAYMENT_STATE] ({ commit }, payload) {
      return stateService.delete(payload).then(response => {
        commit(REMOVE_PAYMENT_STATE, payload)
        return response
      })
    },
    [UPDATE_PAYMENT_STATE] ({ commit }, payload) {
      return stateService.update(payload).then(response => {
        commit(UPDATE_PAYMENT_STATE, payload)
        return response
      })
    }
  }
}

function setPaymentStates (commit, response, query) {
  if (!query.export) commit(SET_PAYMENT_STATES, response.data.data)
  if (response.data.shops) {
    commit(SET_SHOPS, response.data.shops)
  }
  if (response.data.bills) {
    commit(SET_BILLS, response.data.bills)
  }
  return response
}
