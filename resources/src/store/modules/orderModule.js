import { SHOP_MODULE, ORDER_MODULE, NP_MODULE, BILL_MODULE, PRODUCT_MODULE, PAYMENT_MODULE } from './TYPES'
import service from '@/services/OrderService'
import npService from '@/services/NpService'
const { SET_ORDER_KEY, GET_ORDERS_SEARCH, NP_ENABLED, SET_NP_ENABLE, SET_ORDER_TTN, GET_ORDERS_REPORT, SET_DELIVERIES, SET_PAYMENT_TYPES, GET_ORDER, SET_ORDER_STATUSES, ORDER_STATUSES, GET_ORDER_CHART_INFO, ORDERS_PAYMENT_STATS, CLEAR_ORDERS_SEARCH, SET_ORDERS_SEARCH, ORDERS_SEARCH, SET_ORDER_DISCOUNT, UPDATE_ORDER_STATUS, UPDATE_ORDER_PRODUCT, REMOVE_ORDER_PRODUCT, ADD_ORDER_PRODUCTS, UPDATE_ORDER_FIELD, PAYMENT_CHANNELS, SET_PAYMENT_CHANNELS, ORDER, SET_ORDER, DELIVERIES, PAYMENT_TYPES, CREATE_ORDER, SET_ORDERS, GET_ORDERS, ORDERS, ORDERS_STATS, SET_ORDERS_INFO, GET_ORDERS_INFO, CREATE_ORDER_INFO } = ORDER_MODULE
const { SET_SHOPS } = SHOP_MODULE
const { GET_TTNS, CREATE_TTN, SET_SENDERS, SENDERS, CREATE_TNN_FORM, SET_CITIES, CITIES, WAREHOUSES, GET_WAREHOUSES_BY_CODE } = NP_MODULE
const { SET_BILLS } = BILL_MODULE
const { SET_PRODUCT } = PRODUCT_MODULE
const { SET_PAYMENTS } = PAYMENT_MODULE

export default {
  state: {
    [ORDERS]: [],
    [ORDERS_STATS]: {},
    [ORDERS_PAYMENT_STATS]: {},
    [DELIVERIES]: [],
    [PAYMENT_TYPES]: [],
    [ORDER]: {},
    [PAYMENT_CHANNELS]: [],
    [ORDERS_SEARCH]: [],
    [ORDER_STATUSES]: [],
    [CITIES]: [],
    [WAREHOUSES]: {},
    SENDERS: [],
    [NP_ENABLED]: false
  },
  mutations: {
    [SET_ORDER_KEY] (state, key) {
      state[ORDER] = {
        ...state[ORDER],
        np_key: key
      }
    },
    [SET_ORDERS] (state, payload) {
      if (!payload.query.page || payload.query.page === 1) {
        state[ORDERS] = payload.data.data.data
        state[ORDERS_PAYMENT_STATS] = payload.data.stats
      } else {
        state[ORDERS] = [...state[ORDERS], ...payload.data.data.data]
      }
    },
    [SET_PAYMENT_CHANNELS] (state, payload) {
      state[PAYMENT_CHANNELS] = payload
    },
    [SET_DELIVERIES] (state, payload) {
      state[DELIVERIES] = payload
    },
    [SET_ORDER_STATUSES] (state, payload) {
      state[ORDER_STATUSES] = payload
    },
    [CLEAR_ORDERS_SEARCH] (state) {
      state[ORDERS_SEARCH] = []
    },
    [SET_ORDERS_SEARCH] (state, payload) {
      state[ORDERS_SEARCH] = payload
    },
    [SET_ORDERS_INFO] (state, payload) {
      state[ORDERS_STATS] = payload
    },
    [CREATE_ORDER] (state, payload) {
      state[ORDERS_STATS] = {
        ...state[ORDERS_STATS],
        1: {
          ...state[ORDERS_STATS][1],
          count: state[ORDERS_STATS][1].count + 1
        }
      }
      state[ORDERS] = [...state[ORDERS], payload]
    },
    [CREATE_ORDER_INFO] (state, payload) {
      state[DELIVERIES] = payload.delivery
      state[PAYMENT_TYPES] = payload.payments
    },
    [SET_PAYMENT_TYPES] (state, payload) {
      state[PAYMENT_TYPES] = payload
    },
    [SET_ORDER] (state, payload) {
      state[ORDER] = payload.order
      state[DELIVERIES] = payload.delivery
      state[PAYMENT_TYPES] = payload.payments
      state[PAYMENT_CHANNELS] = payload.channels
    },
    [UPDATE_ORDER_STATUS] (state, payload) {
      state[ORDERS_STATS] = {
        ...state[ORDERS_STATS],
        [state[ORDER].status.id]: {
          ...state[ORDERS_STATS][state[ORDER].status.id],
          count: state[ORDERS_STATS][state[ORDER].status.id].count - 1
        },
        [payload.to]: {
          ...state[ORDERS_STATS][payload.to],
          count: state[ORDERS_STATS][payload.to].count + 1
        }
      }
      state[ORDER] = {
        ...state[ORDER],
        status: payload.status
      }
    },
    [SET_ORDER_TTN] (state, payload) {
      state[ORDER] = {
        ...state[ORDER],
        ...payload
      }
    },
    [SET_CITIES] (state, payload) {
      state[CITIES] = payload
    },
    [GET_WAREHOUSES_BY_CODE] (state, payload) {
      state[WAREHOUSES] = {
        ...state[WAREHOUSES],
        [payload.code]: payload.data
      }
    },
    [SET_SENDERS] (state, payload) {
      state[SENDERS] = payload
    },
    [GET_TTNS] (state, payload) {
      const { data, isList } = payload
      if (state[ORDER] && !isList) {
        const obj = Object.values(data)[0]
        state[ORDER] = {
          ...state[ORDER],
          ttn: obj ? obj.ttn : null,
          np_status: obj
        }
      } else {
        state[ORDERS] = state[ORDERS].map(order => {
          if (!order.ttn || !order.np_key) return order
          const foundInTtns = data.find(ttn => ttn.ttn === order.ttn)
          if (!foundInTtns) {
            return order
          }
          return {
            ...order,
            np_status: foundInTtns.status
          }
        })
      }
    },
    [SET_NP_ENABLE] (state, payload) {
      state[NP_ENABLED] = payload
    }
  },
  actions: {
    [GET_ORDER_CHART_INFO] (store, query = {}) {
      return service.getChartInfo(query)
    },
    [GET_ORDERS_SEARCH] ({ commit }, query) {
      return service.getAll(query).then(response => {
        commit(SET_ORDERS_SEARCH, response.data.data.data)
        return response
      })
    },
    [CLEAR_ORDERS_SEARCH] ({ commit }) {
      commit(CLEAR_ORDERS_SEARCH)
    },
    [GET_ORDERS_REPORT] ({ commit }, query = {}) {
      return service.getAllReport(query).then(response => {
        return setOrders(commit, response, query)
      })
    },
    [GET_ORDERS] ({ commit, dispatch }, query = {}) {
      return service.getAll(query).then(response => {
        const orders = response.data.data.data
        orders.forEach(order => {
          if (order.ttn && order.np_key) {
            order.np_status = '...'
          }
        })
        const ttns = {
          'ttns': orders.filter(order => order.ttn && order.np_key).map(order => ({ key: order.np_key, ttn: order.ttn, phone: order.customer_phone }))
        }
        dispatch(GET_TTNS, {
          ttns,
          isList: true
        })
        return setOrders(commit, response, query)
      })
    },
    [GET_ORDERS_INFO] ({ commit }, query = {}) {
      return service.getOrdersInfo(query).then(response => {
        commit(SET_ORDERS_INFO, response.data.stats)
        commit(SET_SHOPS, response.data.shops)
        return response
      })
    },
    [CREATE_ORDER] ({ commit }, data) {
      return service.create(data).then(response => {
        commit(CREATE_ORDER, response.data)
        return response
      })
    },
    [CREATE_ORDER_INFO] ({ commit }) {
      return service.createInfo().then(response => {
        commit(CREATE_ORDER_INFO, response.data)
        return response
      })
    },
    [GET_ORDER] ({ commit }, id) {
      return service.get(id, true).then(response => {
        commit(SET_SHOPS, response.data.shops)
        commit(SET_BILLS, response.data.bills)
        commit(SET_NP_ENABLE, response.data.novaposhta_enabled)
        commit(SET_PAYMENTS, {
          query: {},
          data: {
            items: response.data.order.payments
          }
        })
        delete response.data.order.payments
        commit(SET_ORDER, response.data)
        return response
      })
    },
    [UPDATE_ORDER_FIELD] (store, payload) {
      return service.updateField(payload).then(response => {
        return response
      })
    },
    [ADD_ORDER_PRODUCTS] (store, payload) {
      return service.addProducts(payload).then(response => {
        return response
      })
    },
    [REMOVE_ORDER_PRODUCT] (store, payload) {
      return service.deleteProduct(payload).then(response => {
        return response
      })
    },
    [UPDATE_ORDER_PRODUCT] (store, payload) {
      return service.updateProduct(payload).then(response => {
        return response
      })
    },
    [UPDATE_ORDER_STATUS] ({ commit }, payload) {
      return service.updateStatus(payload).then(response => {
        commit(UPDATE_ORDER_STATUS, {
          to: payload.to,
          status: response.data.new_status
        })
        return response
      })
    },
    [SET_ORDER_DISCOUNT] (store, payload) {
      return service.setDiscount(payload)
    },
    [SET_ORDER_TTN] ({ commit, dispatch }, payload) {
      return service.setTtn(payload).then((response) => {
        dispatch(GET_TTNS, {
          ttns: {
            ttns: [
              {
                ttn: payload.data.ttn,
                key: payload.key,
                phone: payload.phone
              }
            ]
          }
        })
        commit(SET_ORDER_TTN, {
          ttn: payload.data.ttn,
          np_key: response.data.key
        })
      })
    },
    [CREATE_TNN_FORM] ({ commit }, id) {
      return npService.getForm(id).then(response => {
        commit(SET_CITIES, response.data.cities.map(city => ({ name: city.DescriptionRu, code: city.Ref })))
        commit(SET_ORDER_KEY, response.data.key)
        commit(SET_SENDERS, response.data.senders)
      })
    },
    [GET_WAREHOUSES_BY_CODE] ({ state, commit }, payload) {
      if (state[WAREHOUSES][payload.code]) {
        commit(GET_WAREHOUSES_BY_CODE, {
          code: payload.code,
          data: state[WAREHOUSES][payload.code]
        })
        return Promise.resolve()
      }
      return npService.getWarehouses(payload).then(response => {
        commit(GET_WAREHOUSES_BY_CODE, {
          code: payload.code,
          data: response.data.warehouses
        })
      })
    },
    [CREATE_TTN] ({ commit }, payload) {
      return npService.createTtn(payload).then(({ data }) => {
        if (data.waybill) {
          const newTtn = [{
            ttn: data.waybill.number,
            status: 'Нова пошта очікує надходження від відправника',
            address: payload.data.recipient_warehouse.name,
            cost: data.waybill.cost,
            sender_city: payload.data.sender_city.name,
            recipient_city: payload.data.recipient_city.name
          }]
          console.log(newTtn)
          commit(GET_TTNS, {
            data: newTtn
          })
        } else {
          throw new Error('Накладная не создана')
        }
      })
    },
    [GET_TTNS] ({ commit }, payload) {
      return npService.getTtns(payload.ttns).then(response => {
        commit(GET_TTNS, {
          data: response.data,
          isList: payload.isList
        })
      })
    }
  }
}

function setOrders (commit, response, query) {
  if (query.export) return response
  commit(SET_ORDERS, {
    data: response.data,
    query
  })
  if (response.data.data.product) commit(SET_PRODUCT, response.data.data.product)
  if (response.data.data.payments) commit(SET_PAYMENT_TYPES, response.data.data.payments)
  if (response.data.data.statuses) commit(SET_ORDER_STATUSES, response.data.data.statuses)
  if (response.data.data.deliveries) commit(SET_DELIVERIES, response.data.data.deliveries)
  if (response.data.data.bills) commit(SET_BILLS, response.data.data.bills)
  if (response.data.data.channels) commit(SET_PAYMENT_CHANNELS, response.data.data.channels)
  if (response.data.data.shops) commit(SET_SHOPS, response.data.data.shops)
  return response
}
