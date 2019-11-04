import { SHOP_MODULE, USER_MODULE, MODULES_MODULE } from './TYPES'
import service from '../../services/UserService'
import { parseJwt } from '@/helpers/JWT'
const { SET_USER_ROLE, GET_ROLES_INFO, UNSET_USER_IMAGE, SET_USER_IMAGE, UPDATE_EDITED_USER_PASSWORD, UPDATE_EDITED_USER, SET_EDITED_USER, GET_EDITED_USER, EDITED_USER, DESTROY_USER, RESTORE_USER, CREATE_USER, SET_USERS, GET_USERS, USERS, LOGOUT, SET_USER, AUTH, USER } = USER_MODULE
const { SET_ALL_MODULES } = MODULES_MODULE
const { SET_SHOPS } = SHOP_MODULE

export default {
  state: {
    [USER]: null,
    [USERS]: [],
    [EDITED_USER]: null
  },
  mutations: {
    [SET_USER] (state, payload) {
      state[USER] = payload
    },
    [SET_USERS] (state, payload) {
      state[USERS] = payload
    },
    [SET_EDITED_USER] (state, payload) {
      state[EDITED_USER] = payload
    },
    [CREATE_USER] (state, payload) {
      state[USERS] = [...state[USERS], payload]
    },
    [DESTROY_USER] (state, id) {
      if (state[EDITED_USER]) {
        state[EDITED_USER] = {
          ...state[EDITED_USER],
          is_deleted: 1
        }
      }
      state[USERS] = state[USERS].map(user => user.id === id ? { ...user, is_deleted: 1 } : user)
    },
    [RESTORE_USER] (state, id) {
      if (state[EDITED_USER]) {
        state[EDITED_USER] = {
          ...state[EDITED_USER],
          is_deleted: 0
        }
      }
      state[USERS] = state[USERS].map(user => user.id === id ? { ...user, is_deleted: 0 } : user)
    },
    [UPDATE_EDITED_USER] (state, payload) {
      if (state[EDITED_USER].id === state[USER].id) {
        state[USER] = { ...state[USER], ...payload }
        localStorage.setItem('user', JSON.stringify(state[USER]))
      }
      state[EDITED_USER] = { ...state[EDITED_USER], ...payload }
    },
    [UNSET_USER_IMAGE] (state) {
      if (state[EDITED_USER].id === state[USER].id) {
        state[USER] = { ...state[USER], image: null }
        localStorage.setItem('user', JSON.stringify(state[USER]))
      }
      state[EDITED_USER] = { ...state[EDITED_USER], image: null }
    },
    [SET_USER_IMAGE] (state, image) {
      if (state[EDITED_USER].id === state[USER].id) {
        state[USER] = { ...state[USER], image }
        localStorage.setItem('user', JSON.stringify(state[USER]))
      }
      state[EDITED_USER] = { ...state[EDITED_USER], image }
    }
  },
  actions: {
    [AUTH] ({ commit }, credentials) {
      return service.auth(credentials).then(response => {
        localStorage.setItem('token', response.data)
        const user = parseJwt(response.data)
        if (user) localStorage.setItem('user', JSON.stringify(user))
        commit(SET_USER, user)
        return response
      })
    },
    [SET_USER] ({ commit }, user) {
      commit(SET_USER, user)
    },
    [GET_USERS] ({ commit }) {
      return service.getAll().then(response => {
        commit(SET_USERS, response.data)
        return response
      })
    },
    [UPDATE_EDITED_USER] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_EDITED_USER, payload.data)
        return response
      })
    },
    [UPDATE_EDITED_USER_PASSWORD] ({ commit }, payload) {
      return service.updatePassword(payload).then(response => {
        return response
      })
    },
    [SET_USER_IMAGE] ({ commit }, payload) {
      return service.setImage(payload).then(response => {
        commit(SET_USER_IMAGE, response.data)
        return response
      })
    },
    [UNSET_USER_IMAGE] ({ commit }, id) {
      return service.unsetImage(id).then(response => {
        commit(UNSET_USER_IMAGE)
        return response
      })
    },
    [CREATE_USER] ({ commit }, payload) {
      return service.create(payload).then(response => {
        commit(CREATE_USER, response.data)
        return response
      })
    },
    [RESTORE_USER] ({ commit }, id) {
      return service.restore(id).then(response => {
        commit(RESTORE_USER, id)
        return response
      })
    },
    [DESTROY_USER] ({ commit }, id) {
      return service.delete(id).then(response => {
        commit(DESTROY_USER, id)
        return response
      })
    },
    [GET_EDITED_USER] ({ commit }, id) {
      return service.get(id).then(response => {
        commit(SET_EDITED_USER, response.data)
        return response
      })
    },
    [LOGOUT] ({ commit }) {
      localStorage.setItem('token', '')
      localStorage.setItem('user', '')
      commit(SET_USER, null)
    },
    [GET_ROLES_INFO] ({ commit }) {
      return service.getRolesInfo().then(response => {
        commit(SET_SHOPS, response.data.shops)
        commit(SET_ALL_MODULES, response.data.modules)
      })
    },
    [SET_USER_ROLE] ({ commit }, payload) {
      return service.setRole(payload)
    }
  }
}
