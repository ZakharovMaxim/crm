import service from '@/services/catalogService'
import router from '@/router'
import TreeHelper from '@/helpers/Tree'
import { SHOP_MODULE, ORDER_MODULE, STOCK_MODULE, PRICE_GROUP_MODULE, TRADEMARK_MODULE, IMAGE_MODULE, CATALOG_MODULE, FOLDER_MODULE, PRODUCT_MODULE, ATTRIBUTE_MODULE } from './TYPES'
const { GET_PRODUCTS_REPORT_ORDERS, GET_PRODUCTS_REPORT_STOCK, GET_PRODUCT_STATS, SET_PRODUCT_STATS, PRODUCT_STATS, SET_PRODUCTS, GET_PRODUCTS, PRODUCTS, PRODUCT, GET_PRODUCT, SET_PRODUCT, CREATE_PRODUCT, UPDATE_PRODUCT, DESTROY_PRODUCT, SET_PRODUCT_PRICE_GROUP } = PRODUCT_MODULE
const { IMPORT_CATALOG, EXPORT_CATALOG, UPDATE_CATALOG_STATS, CATALOG_STATS, SET_CATALOG_INFO, GET_CATALOG_INFO } = CATALOG_MODULE
const { SET_FOLDERS, FOLDERS_WITHOUT_CHILDS, FOLDERS, CREATE_FOLDER, DESTROY_FOLDER, UPDATE_FOLDER } = FOLDER_MODULE
const { SET_ORDER_STATUSES } = ORDER_MODULE
const { SORT_IMAGES, DESTROY_IMAGE, UPLOAD_IMAGES } = IMAGE_MODULE
const { DESTROY_PRODUCT_ATTRIBUTE, ADD_PRODUCT_ATTRIBUTE } = ATTRIBUTE_MODULE
const { SET_STOCKS } = STOCK_MODULE
const { SET_PRICE_GROUPS } = PRICE_GROUP_MODULE
const { SET_TRADEMARKS } = TRADEMARK_MODULE
const { SET_SHOPS } = SHOP_MODULE

export default {
  state: {
    [FOLDERS]: [],
    [CATALOG_STATS]: {},
    [PRODUCTS]: [],
    [PRODUCT]: null,
    [PRODUCT_STATS]: {}
  },
  getters: {
    [FOLDERS_WITHOUT_CHILDS]: (state) => id => {
      return state[FOLDERS].filter(folder => !TreeHelper.getTreePath(folder.id, state[FOLDERS]).map(f => f.id).includes(id))
    }
  },
  mutations: {
    [SET_PRODUCT_STATS] (state, payload) {
      state[PRODUCT_STATS] = payload
    },
    [SET_PRODUCTS] (state, products) {
      state[PRODUCTS] = products
    },
    [SET_FOLDERS] (state, payload) {
      state[FOLDERS] = payload
    },
    [SET_CATALOG_INFO] (state, payload) {
      state[FOLDERS] = payload.folders
      state[CATALOG_STATS] = payload.stats
    },
    [UPDATE_CATALOG_STATS] (state, payload) {
      state[CATALOG_STATS] = {
        ...state[CATALOG_STATS],
        [payload.field]: state[CATALOG_STATS][[payload.field]] + payload.value
      }
    },
    [CREATE_FOLDER] (state, catalog) {
      state[FOLDERS] = [...state[FOLDERS], catalog]
    },
    [DESTROY_FOLDER] (state, id) {
      state[FOLDERS] = state[FOLDERS].filter(catalog => +catalog.id !== +id)
    },
    [UPDATE_FOLDER] (state, payload) {
      state[FOLDERS] = state[FOLDERS].map(catalog =>
        +catalog.id === +payload.id ? { ...catalog, ...payload.data } : catalog)
    },
    [SET_PRODUCT] (state, product) {
      state[PRODUCT] = product
    },
    [CREATE_PRODUCT] (state, product) {
      if (product.is_variant) {
        state[PRODUCT].variations = [...state[PRODUCT].variations, product]
      } else {
        state[PRODUCTS] = [...state[PRODUCTS], product]
      }
    },
    [DESTROY_PRODUCT] (state, id) {
      state[PRODUCTS] = state[PRODUCTS].filter(product => product.id !== id)
    },
    [DESTROY_PRODUCT_ATTRIBUTE] (state, payload) {
      state[PRODUCT] = {
        ...state[PRODUCT],
        attributes: state[PRODUCT].attributes.filter(attr => attr.id !== payload.attribute_id)
      }
    },
    [ADD_PRODUCT_ATTRIBUTE] (state, payload) {
      state[PRODUCT] = {
        ...state[PRODUCT],
        attributes: [...state[PRODUCT].attributes, payload]
      }
    },
    [UPDATE_PRODUCT] (state, payload) {
      state[PRODUCTS] = state[PRODUCTS].map(product =>
        product.id === payload.id ? { ...product, ...payload.data } : product)
      state[PRODUCT] = {
        ...state[PRODUCT],
        ...payload.data
      }
    },
    [UPLOAD_IMAGES] (state, payload) {
      state[PRODUCT].images = payload
    },
    [DESTROY_IMAGE] (state, payload) {
      state[PRODUCT].images = state[PRODUCT].images.filter(image => image.id !== payload.id)
    },
    [SORT_IMAGES] (state, payload) {
      if (!state[PRODUCT] || !state[PRODUCT].images || !state[PRODUCT].images.length) return
      payload.list.forEach(item => {
        const image = state[PRODUCT].images.find(image => image.id === item.id)
        if (!image) return
        image.pivot.order = item.order
      })
    }
  },
  actions: {
    [GET_PRODUCT_STATS] ({ commit }, id) {
      return service.getStats(id).then(response => {
        commit(SET_PRODUCT_STATS, response.data)
        return response
      })
    },
    [EXPORT_CATALOG] ({ commit }, fields) {
      return service.export(fields).then(response => {
        return response
      })
    },
    [IMPORT_CATALOG] ({ commit }, data) {
      return service.import(data).then(response => {
        return response
      })
    },
    [GET_CATALOG_INFO] ({ commit }) {
      return service.getInfo().then(response => {
        commit(SET_CATALOG_INFO, response.data)
        return response
      })
    },
    [GET_PRODUCTS] ({ commit }, query) {
      return service.getProducts(query).then(response => {
        return setProducts(commit, response, query)
      })
    },
    [GET_PRODUCTS_REPORT_STOCK] ({ commit }, query) {
      return service.getProductsReportStock(query).then(response => {
        return setProducts(commit, response, query)
      })
    },
    [GET_PRODUCTS_REPORT_ORDERS] ({ commit }, query) {
      return service.getProductsReportOrders(query).then(response => {
        return setProducts(commit, response, query)
      })
    },
    [CREATE_FOLDER] ({ commit }, data) {
      return service.create({
        parent_id: router.currentRoute.query.parent_id || null,
        ...data
      }).then(response => {
        commit(CREATE_FOLDER, response.data)
        return response
      })
    },
    [DESTROY_FOLDER] ({ commit }, id) {
      return service.delete(id).then(response => {
        commit(DESTROY_FOLDER, id)
        return response
      })
    },
    [UPDATE_FOLDER] ({ commit }, payload) {
      return service.update(payload).then(response => {
        commit(UPDATE_FOLDER, payload)
        return response
      })
    },
    [GET_PRODUCT] ({ commit }, id) {
      return service.getProduct(id).then(response => {
        if (response.data.variation) {
          commit(SET_PRODUCT, response.data.variation)
        } else {
          commit(SET_PRODUCT, response.data.data)
          commit(SET_PRICE_GROUPS, response.data.price_groups)
          commit(SET_TRADEMARKS, response.data.trademarks)
        }
        return response
      })
    },
    [CREATE_PRODUCT] ({ commit }, data) {
      if (router.currentRoute.query.parent_id) {
        if (data.append && typeof data.append === 'function') {
          data.append('catalog_id', router.currentRoute.query.parent_id)
        } else {
          data['catalog_id'] = router.currentRoute.query.parent_id
        }
      }
      return service.createProduct(data).then(response => {
        commit(CREATE_PRODUCT, response.data)
        return response
      })
    },
    [DESTROY_PRODUCT] ({ commit }, id) {
      return service.deleteProduct(id).then(response => {
        commit(DESTROY_PRODUCT, id)
        return response
      })
    },
    [DESTROY_PRODUCT_ATTRIBUTE] ({ commit }, payload) {
      return service.deleteProductAttribute(payload).then(response => {
        commit(DESTROY_PRODUCT_ATTRIBUTE, payload)
        return response
      })
    },
    [ADD_PRODUCT_ATTRIBUTE] ({ commit }, payload) {
      return service.addProductAttribute(payload).then(response => {
        commit(ADD_PRODUCT_ATTRIBUTE, response.data)
        return response
      })
    },
    [UPDATE_PRODUCT] ({ commit }, payload) {
      return service.updateProduct(payload).then(response => {
        commit(UPDATE_PRODUCT, payload)
        return response
      })
    },
    [UPLOAD_IMAGES] ({ commit }, data) {
      return service.uploadImages(data).then((response) => {
        commit(UPLOAD_IMAGES, response.data)
        return response
      })
    },
    [DESTROY_IMAGE] ({ commit }, payload) {
      return service.deleteImage(payload.id).then((response) => {
        commit(DESTROY_IMAGE, payload)
        return response
      })
    },
    [SORT_IMAGES] ({ commit }, payload) {
      return service.sortImages(payload).then((response) => {
        commit(SORT_IMAGES, payload)
        return response
      })
    },
    [SET_PRODUCT_PRICE_GROUP] ({ commit }, payload) {
      return service.setPriceGroup(payload).then((response) => {
        return response
      })
    }
  }
}

function setProducts (commit, response, query = {}) {
  if (query.export) return response
  commit(SET_PRODUCTS, response.data.products)
  if (response.data.folders) commit(SET_FOLDERS, response.data.folders)
  if (response.data.stocks) commit(SET_STOCKS, { stocks: response.data.stocks })
  if (response.data.shops) commit(SET_SHOPS, response.data.shops)
  if (response.data.trademarks) commit(SET_TRADEMARKS, response.data.trademarks)
  if (response.data.statuses) commit(SET_ORDER_STATUSES, response.data.statuses)
  return response
}
