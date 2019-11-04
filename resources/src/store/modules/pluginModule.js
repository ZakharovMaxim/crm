import { PLUGIN_MODULE } from './TYPES'
import service from '@/services/pluginService'
const { PLUGINS, PLUGIN, GET_PLUGINS, SET_PLUGINS, SET_PLUGIN, GET_PLUGIN, CREATE_PLUGIN, DESTROY_PLUGIN, UPDATE_PLUGIN, SET_PLUGIN_STATE } = PLUGIN_MODULE

export default {
  state: {
    [PLUGINS]: [],
    [PLUGIN]: null
  },
  mutations: {
    [SET_PLUGINS] (state, payload) {
      state[PLUGINS] = payload
    },
    [SET_PLUGIN] (state, payload) {
      state[PLUGIN] = payload
    },
    [CREATE_PLUGIN] (state, payload) {
      state[PLUGINS] = [...state[PLUGINS], payload]
    },
    [DESTROY_PLUGIN] (state, payload) {
      state[PLUGINS] = state[PLUGINS].filter(plugin => plugin.id !== payload)
      state[PLUGIN] = null
    },
    [UPDATE_PLUGIN] (state, payload) {
      state[PLUGINS] = state[PLUGINS].map(plugin => plugin.id === payload.id ? ({ ...plugin, ...payload.data }) : plugin)
      if (state[PLUGIN]) {
        state[PLUGIN] = {
          ...state[PLUGIN],
          ...payload.data
        }
      }
    },
    [SET_PLUGIN_STATE] (state, payload) {
      state[PLUGINS] = state[PLUGINS].map(plugin => plugin.id !== payload.id ? plugin : { ...plugin, enabled: payload.data.enabled })
      if (state[PLUGIN]) {
        state[PLUGIN] = {
          ...state[PLUGIN],
          enabled: payload.data.enabled
        }
      }
    }
  },
  actions: {
    [GET_PLUGINS] ({ commit }, payload = {}) {
      return service.getAll(payload).then(response => {
        commit(SET_PLUGINS, response.data)
        return response
      })
    },
    [GET_PLUGIN] ({ commit }, payload) {
      return service.get(payload).then(response => {
        commit(SET_PLUGIN, response.data)
        return response
      })
    },
    [CREATE_PLUGIN] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_PLUGIN, response.data)
        return response
      })
    },
    [DESTROY_PLUGIN] ({ commit }, payload) {
      return service.delete(payload).then(response => {
        commit(DESTROY_PLUGIN, payload)
        return response
      })
    },
    [UPDATE_PLUGIN] ({ commit }, payload) {
      return service.updateSettings(payload).then(response => {
        commit(UPDATE_PLUGIN, payload)
        return response
      })
    },
    [SET_PLUGIN_STATE] ({ commit }, payload) {
      commit(SET_PLUGIN_STATE, payload)
      return service.setStatus(payload).then(response => {
        return response
      })
    }
  }
}
