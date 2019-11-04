import { BILL_MODULE } from './TYPES'
import service from '../../services/BillService'
const { INCREMENT_BILL_COUNT, DECREMENT_BILL_COUNT, BILL, BILLS, SET_BILLS, SET_BILL, GET_BILLS, CREATE_BILL, REMOVE_BILL, UPDATE_BILL } = BILL_MODULE

export default {
  state: {
    [BILL]: null,
    [BILLS]: []
  },
  mutations: {
    [SET_BILLS] (state, payload) {
      state[BILLS] = payload
    },
    [SET_BILL] (state, payload) {
      state[BILL] = payload
    },
    [CREATE_BILL] (state, payload) {
      state[BILLS] = [payload, ...state[BILLS]]
    },
    [REMOVE_BILL] (state, payload) {
      state[BILLS] = state[BILLS].filter(bill => +bill.id !== +payload)
    },
    [UPDATE_BILL] (state, payload) {
      state[BILLS] = state[BILLS].map(bill => +bill.id === +payload.id ? { ...bill, ...payload.data } : bill)
    },
    [INCREMENT_BILL_COUNT] (state, payload) {
      state[BILLS] = state[BILLS].map(bill => +bill.id === +payload ? { ...bill, payments_count: (bill.payments_count + 1) || 0 } : bill)
    },
    [DECREMENT_BILL_COUNT] (state, payload) {
      state[BILLS] = state[BILLS].map(bill => +bill.id === +payload ? { ...bill, payments_count: (bill.payments_count - 1) || 0 } : bill)
    }
  },
  actions: {
    [GET_BILLS] ({ commit }, payload) {
      return service.getAll(payload).then(response => {
        commit(SET_BILLS, response.data)
        return response
      })
    },
    [SET_BILL] ({ commit }, payload) {
      commit(SET_BILL, payload)
    },
    [CREATE_BILL] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_BILL, response.data)
        return response
      })
    },
    [REMOVE_BILL] ({ commit }, payload) {
      return service.delete(payload).then(response => {
        commit(REMOVE_BILL, payload)
        return response
      })
    },
    [UPDATE_BILL] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_BILL, payload)
        return response
      })
    }
  }
}
