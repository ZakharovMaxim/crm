import Vue from 'vue'
import Router from 'vue-router'
const index = () => import('@/views/index')
const shops = () => import('@/views/shop/shops')
const shop = () => import('@/views/shop/shop')
// const storeShop = () => import('@/views/shop/store-shop')
const catalogs = () => import('@/views/catalog/catalogs')
const catalogList = () => import('@/views/catalog/catalogList')
const product = () => import('@/views/catalog/product')
const attributes = () => import('@/views/catalog/attributes')
const currencies = () => import('@/views/catalog/currencies')
const priceGroups = () => import('@/views/catalog/price-groups')
const trademarks = () => import('@/views/catalog/trademarks')
const stocks = () => import('@/views/stocks')
const stockActions = () => import('@/views/stock-actions')
const stockAction = () => import('@/views/stock-action')
const finance = () => import('@/views/finance')
const financeList = () => import('@/views/finance-content')
const paymentCategoriesList = () => import('@/views/payment-category-list')
const ordersPage = () => import('@/views/orders')
const ordersList = () => import('@/views/orders-list')
const order = () => import('@/views/order')
const reportOrders = () => import('@/views/report-orders')
const reportFinances = () => import('@/views/report-finance')
const reportStock = () => import('@/views/report-stock')
const reportProducts = () => import('@/views/report-products')
const reportFinanceCategories = () => import('@/views/report-finance-category')
const login = () => import('@/views/login')
const usersList = () => import('@/views/users-list')
const users = () => import('@/views/users')
const user = () => import('@/views/user')
const clients = () => import('@/views/clients')
const clientsList = () => import('@/views/clients-list')
const client = () => import('@/views/client')
const plugins = () => import('@/views/plugins')
const plugin = () => import('@/views/plugin')
const pluginsList = () => import('@/views/plugins-list')

const pageNotFound = () => import('@/views/404')
Vue.use(Router)

const router = new Router({
  linkActiveClass: 'active',
  routes: [
    {
      path: '/',
      component: index
    },
    {
      path: '/shops',
      component: shops,
      children: [
        {
          path: '/',
          component: shop
        },
        {
          path: ':id',
          component: shop
        }
      ]
    },
    {
      path: '/catalogs',
      component: catalogs,
      children: [
        {
          path: '/',
          component: catalogList,
          props: (route) => ({
            parentId: route.query.parent_id,
            search: route.query.search,
            page: route.query.page
          })
        },
        {
          path: 'product/:id',
          component: product
        },
        {
          path: 'attributes',
          component: attributes
        },
        {
          path: 'prices',
          component: priceGroups
        },
        {
          path: 'currencies',
          component: currencies
        },
        {
          path: 'trademarks',
          component: trademarks
        }
      ]
    },
    {
      path: '/variations',
      component: catalogs,
      children: [
        {
          path: ':id',
          component: product
        }
      ]
    },
    {
      path: '/stocks',
      component: stocks,
      children: [
        {
          path: '/',
          component: stockActions,
          meta: {
            type: '1'
          }
        },
        {
          path: 'enrollment',
          component: stockActions,
          meta: {
            type: '1'
          }
        },
        {
          path: 'enrollment/created',
          component: stockActions,
          meta: {
            type: '1',
            isSubmited: false
          }
        },
        {
          path: 'enrollment/submited',
          component: stockActions,
          meta: {
            type: '1',
            isSubmited: true
          }
        },
        {
          path: 'stock-action/:id',
          component: stockAction
        },
        {
          path: 'write-off',
          component: stockActions,
          meta: {
            type: '2'
          }
        },
        {
          path: 'write-off/created',
          component: stockActions,
          meta: {
            type: '2',
            isSubmited: false
          }
        },
        {
          path: 'write-off/submited',
          component: stockActions,
          meta: {
            type: '2',
            isSubmited: true
          }
        },
        {
          path: 'movements',
          component: stockActions,
          meta: {
            type: '3'
          }
        },
        {
          path: 'movements/created',
          component: stockActions,
          meta: {
            type: '3',
            isSubmited: false
          }
        },
        {
          path: 'movements/submited',
          component: stockActions,
          meta: {
            type: '3',
            isSubmited: true
          }
        }
      ]
    },
    {
      path: '/finance',
      component: finance,
      children: [
        {
          path: '/',
          component: financeList
        },
        {
          path: 'income',
          component: paymentCategoriesList
        },
        {
          path: 'outcome',
          component: paymentCategoriesList,
          meta: {
            type: 2
          }
        }
      ]
    },
    {
      path: '/orders',
      component: ordersPage,
      children: [
        {
          path: '/',
          component: ordersList
        },
        {
          path: ':id',
          component: order
        }
      ]
    },
    {
      path: '/report-orders',
      component: reportOrders
    },
    {
      path: '/report-stock',
      component: reportStock
    },
    {
      path: '/report-products',
      component: reportProducts
    },
    {
      path: '/report-finance',
      component: reportFinances
    },
    {
      path: '/report-finance-category',
      component: reportFinanceCategories
    },
    {
      path: '/login',
      component: login,
      meta: {
        notAuth: true
      }
    },
    {
      path: '/users',
      component: users,
      children: [
        {
          path: '/',
          component: usersList
        },
        {
          path: ':id',
          component: user
        }
      ]
    },
    {
      path: '/clients',
      component: clients,
      children: [
        {
          path: '/',
          component: clientsList
        },
        {
          path: ':id',
          component: client
        }
      ]
    },
    {
      path: '/plugins',
      component: plugins,
      children: [
        {
          path: '/',
          component: pluginsList
        },
        {
          path: ':id',
          component: plugin
        }
      ]
    },
    {
      path: '*',
      component: pageNotFound
    }
  ],
  mode: 'history'
})

router.beforeEach((to, from, next) => {
  if (to.meta.notAuth) {
    return next()
  }
  const token = localStorage.getItem('token')
  if (!token) return next('/login')
  next()
})

export default router
