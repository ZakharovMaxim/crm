import axios from '@/configs/axios'
import queryBuilder from '../helpers/queryBuilder'

export default {
  getAll: () => {
    return axios.get('/folders')
  },
  export: (fields) => {
    return axios.get(`/products/export?fields=${fields}`)
  },
  import: (data) => {
    return axios.post(`/products/import`, data)
  },
  getInfo: () => {
    return axios.get('/folders/info')
  },
  getStats: (id) => {
    return axios.get(`/products/${id}/info`)
  },
  getProducts: (query) => {
    return axios.get('/products' + queryBuilder(query))
  },
  getProductsReportStock: (query) => {
    return axios.get('/products/index_report_stocks' + queryBuilder(query))
  },
  getProductsReportOrders: (query) => {
    return axios.get('/products/index_report_products' + queryBuilder(query))
  },
  create: (data) => {
    return axios.post(`/folders`, data)
  },
  createProduct: (data) => {
    return axios.post(`/products`, data)
  },
  getProduct: (id) => {
    return axios.get(`/products/${id}?info=1`)
  },
  updateProduct: (payload) => {
    return axios.patch(`/products/${payload.id}`, payload.data)
  },
  deleteProduct: (id) => {
    return axios.delete(`/products/${id}`)
  },
  setPriceGroup: (payload) => {
    return axios.patch(`/products/${payload.id}/price-groups`, { data: payload.data })
  },
  deleteProductAttribute: (data) => {
    return axios.post(`/delete-products-attribute`, data)
  },
  addProductAttribute: (data) => {
    return axios.post(`/add-products-attribute`, data)
  },
  update: (id, data) => {
    return axios.patch(`/folders/${id}`, data)
  },
  delete: (id) => {
    return axios.delete(`/folders/${id}`)
  },
  uploadImages: (payload) => {
    return axios.post(`/images`, payload)
  },
  deleteImage: (id) => {
    return axios.delete(`/images/${id}`)
  },
  sortImages: (payload) => {
    return axios.post(`/images/sort`, payload)
  }
}
