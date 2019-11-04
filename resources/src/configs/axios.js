import axios from 'axios'
import router from '@/router'

const instance = axios.create({
  baseURL: '/api',
  headers: { 'X-Requested-With': 'XMLHttpRequest' }
})

instance.interceptors.request.use(function (config) {
  // Do something before request is sent\
  config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
  return config
})

// Add a response interceptor
instance.interceptors.response.use(function (response) {
  // Do something with response data
  return response
}, function (error) {
  // Do something with response error
  if (error.response.status === 401) {
    localStorage.setItem('token', '')
    router.push('/login')
  }
  if (error.response.status === 403) {
    // router.push('/')
  }
  return Promise.reject(error)
})

export default instance
