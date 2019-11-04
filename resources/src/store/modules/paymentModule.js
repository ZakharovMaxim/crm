import { BILL_MODULE, SHOP_MODULE, PAYMENT_MODULE, PAYMENT_STATE_MODULE, PAYMENT_CATEGORY_MODULE } from './TYPES'
import service from '../../services/PaymentService'
const { GET_PAYMENTS_REPORT, GET_PAYMENTS_INFO, CREATE_PAYMENT_INFO, IS_PAYMENT_FETCHED, PAYMENTS_STATS, PAYMENTS, PAYMENT, GET_PAYMENTS, SET_PAYMENTS, SET_PAYMENT, CREATE_PAYMENT,
  REMOVE_PAYMENT, UPDATE_PAYMENT } = PAYMENT_MODULE
const { SET_BILLS, INCREMENT_BILL_COUNT, DECREMENT_BILL_COUNT } = BILL_MODULE
const { SET_SHOPS } = SHOP_MODULE
const { SET_PAYMENT_STATES } = PAYMENT_STATE_MODULE
const { SET_PAYMENT_CATEGORIES } = PAYMENT_CATEGORY_MODULE

export default {
  state: {
    [PAYMENT]: null,
    [PAYMENTS]: [],
    [PAYMENTS_STATS]: {},
    [IS_PAYMENT_FETCHED]: false
  },
  mutations: {
    [CREATE_PAYMENT_INFO] (state) {
      state[IS_PAYMENT_FETCHED] = true
    },
    [SET_PAYMENTS] (state, payload) {
      if (!payload.query.page || payload.query.page === 1) {
        state[PAYMENTS] = payload.data.items
        if (payload.data.stats) state[PAYMENTS_STATS] = payload.data.stats
      } else {
        state[PAYMENTS] = [...state[PAYMENTS], ...payload.data.items]
      }
    },
    [SET_PAYMENT] (state, payload) {
      state[PAYMENT] = payload
    },
    [CREATE_PAYMENT] (state, payload) {
      state[PAYMENTS] = [payload, ...state[PAYMENTS]]
      if (state[PAYMENTS_STATS]) {
        const newStats = {
          ...state[PAYMENTS_STATS]
        }
        newStats.total++
        if (+payload.type === 1) {
          newStats.income += +payload.sum
        } else {
          newStats.outcome += +payload.sum
        }
        state[PAYMENTS_STATS] = newStats
      }
    },
    [REMOVE_PAYMENT] (state, payload) {
      const paymentToRemove = state[PAYMENTS].find(payment => payment.id === payload)
      state[PAYMENTS] = state[PAYMENTS].filter(payment => payment.id !== payload)
      if (state[PAYMENTS_STATS] && paymentToRemove) {
        const newStats = {
          ...state[PAYMENTS_STATS]
        }
        newStats.total--
        if (+paymentToRemove.type === 1) {
          newStats.income -= paymentToRemove.sum
        } else {
          newStats.outcome -= paymentToRemove.sum
        }
        state[PAYMENTS_STATS] = newStats
      }
    },
    [UPDATE_PAYMENT] (state, payload) {
      state[PAYMENTS] = state[PAYMENTS].map(payment => payment.id === payload.id ? { ...payment, ...payload.data } : payment)
    }
  },
  actions: {
    [GET_PAYMENTS_INFO] ({ commit }) {
      return service.getInfo().then(response => {
        commit(SET_PAYMENT_CATEGORIES, response.data.categories)
        commit(SET_BILLS, response.data.bills)
      })
    },
    [GET_PAYMENTS] ({ commit }, query) {
      return service.getAll(query).then(response => {
        return setPayments(commit, response, query)
      })
    },
    [GET_PAYMENTS_REPORT] ({ commit }, query) {
      return service.getAllReport(query).then(response => {
        return setPayments(commit, response, query)
      })
    },
    [SET_PAYMENT] ({ commit }, payload) {
      commit(SET_PAYMENT, payload)
    },
    [CREATE_PAYMENT] (store, payload) {
      return service.create(payload).then(response => {
        store.commit(CREATE_PAYMENT, response.data)
        store.commit(INCREMENT_BILL_COUNT, response.data.bill_id)
        return response
      })
    },
    [REMOVE_PAYMENT] ({ commit, state }, payload) {
      return service.delete(payload).then(response => {
        const payment = state[PAYMENTS].find(payment => payment.id === payload)
        if (payment) commit(DECREMENT_BILL_COUNT, payment.bill_id)
        commit(REMOVE_PAYMENT, payload)
        return response
      })
    },
    [UPDATE_PAYMENT] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_PAYMENT, payload)
        return response
      })
    },
    [CREATE_PAYMENT_INFO] ({ state, commit }) {
      if (state[IS_PAYMENT_FETCHED]) {
        return new Promise((resolve) => resolve())
      } else {
        return service.createInfo().then(response => {
          commit(CREATE_PAYMENT_INFO, response.data)
          let categories = [].concat.apply([], response.data.shops.map(shop => shop.categories))
          const unique = []
          categories.forEach(cat => {
            const found = unique.find(c => c.id === cat.id)
            if (!found) {
              unique.push(cat)
            }
          })
          if (unique.length) commit(SET_PAYMENT_STATES, unique)
          commit(SET_SHOPS, response.data.shops)
          commit(SET_BILLS, response.data.bills)
          return response
        })
      }
    }
  }
}

function setPayments (commit, response, query) {
  if (query.export) {
    return response
  }
  commit(SET_PAYMENTS, {
    data: response.data,
    query
  })

  if (response.data.shops) {
    let categories = [].concat.apply([], response.data.shops.map(shop => shop.categories))
    const unique = []
    categories.forEach(cat => {
      const found = unique.find(c => c.id === cat.id)
      if (!found) {
        unique.push(cat)
      }
    })
    commit(SET_PAYMENT_STATES, unique)
    commit(CREATE_PAYMENT_INFO)
    commit(SET_SHOPS, response.data.shops)
  }
  if (response.data.bills) {
    commit(SET_BILLS, response.data.bills)
  }
  return response
}
