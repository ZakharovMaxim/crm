import service from '@/services/AttributeService'
import { ATTRIBUTE_MODULE, CATALOG_MODULE } from './TYPES'
const { GET_ATTRIBUTES, SET_ATTRIBUTES, CREATE_ATTRIBUTE, UPDATE_ATTRIBUTE_VALUE, DESTROY_ATTRIBUTE_VALUE, UPDATE_ATTRIBUTE, DESTROY_ATTRIBUTE, ATTRIBUTES, CREATE_ATTRIBUTE_VALUE } = ATTRIBUTE_MODULE
const { UPDATE_CATALOG_STATS } = CATALOG_MODULE
// CREATE_ATTRIBUTE_VALUE
export default {
  state: {
    [ATTRIBUTES]: []
  },
  mutations: {
    [SET_ATTRIBUTES] (state, attrs) {
      state[ATTRIBUTES] = attrs
    },
    [CREATE_ATTRIBUTE] (state, attr) {
      state[ATTRIBUTES] = [...state[ATTRIBUTES], attr]
    },
    [UPDATE_ATTRIBUTE] (state, payload) {
      state[ATTRIBUTES] = state[ATTRIBUTES].map(attr =>
        attr.id === payload.id ? { ...attr, ...payload.data } : attr)
    },
    [DESTROY_ATTRIBUTE] (state, id) {
      state[ATTRIBUTES] = state[ATTRIBUTES].filter(attr => attr.id !== id)
    },
    [UPDATE_ATTRIBUTE_VALUE] (state, payload) {
      const attribute = state[ATTRIBUTES].find(attr => attr.id === payload.data.attribute_id)
      if (attribute) {
        attribute.values = attribute.values.map(value => value.id === payload.id ? { ...value, ...payload.data } : value)
      }
    },
    [DESTROY_ATTRIBUTE_VALUE] (state, payload) {
      const attribute = state[ATTRIBUTES].find(attr => attr.id === payload.attribute_id)
      if (attribute) {
        attribute.values = attribute.values.filter(value => value.id !== payload.id)
      }
    },
    [CREATE_ATTRIBUTE_VALUE] (state, payload) {
      const attribute = state[ATTRIBUTES].find(attr => attr.id === payload.attribute_id)
      if (attribute) {
        attribute.values = [...attribute.values, payload]
      }
    }
  },
  actions: {
    [GET_ATTRIBUTES] ({ commit }) {
      return service.getAll().then(response => {
        commit(SET_ATTRIBUTES, response.data)
        return response
      })
    },
    [CREATE_ATTRIBUTE] ({ commit }, data) {
      return service.create(data).then(response => {
        commit(CREATE_ATTRIBUTE, response.data)
        commit(UPDATE_CATALOG_STATS, {
          field: 'attrs',
          value: 1
        })
        return response
      })
    },
    [UPDATE_ATTRIBUTE] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_ATTRIBUTE, payload)
        return response
      })
    },
    [DESTROY_ATTRIBUTE] ({ commit }, id) {
      return service.delete(id).then(response => {
        commit(DESTROY_ATTRIBUTE, id)
        commit(UPDATE_CATALOG_STATS, {
          field: 'attrs',
          value: -1
        })
        return response
      })
    },
    [DESTROY_ATTRIBUTE_VALUE] ({ commit }, payload) {
      return service.deleteValue(payload.id).then(response => {
        commit(DESTROY_ATTRIBUTE_VALUE, payload)
        return response
      })
    },
    [UPDATE_ATTRIBUTE_VALUE] ({ commit }, payload) {
      return service.updateValue(payload).then(response => {
        commit(UPDATE_ATTRIBUTE_VALUE, payload)
        return response
      })
    },
    [CREATE_ATTRIBUTE_VALUE] ({ commit }, payload) {
      return service.createValue(payload).then(response => {
        commit(CREATE_ATTRIBUTE_VALUE, response.data)
        return response
      })
    }
  }
}
