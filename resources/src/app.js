import Vue from 'vue'
import router from './router'
import store from './store'
import Buefy from 'buefy'
import App from './components/App'
import GridSortable from './plugins/grid-sortable'
import Popup from '@/components/Popups/Popup'
import DateRange from '@/components/base/dateRange'
import AppForm from '@/components/base/AppForm'
import VueProgressBar from 'vue-progressbar'

import GetData from '@/HOCS/getData'
import NumberInput from '@/components/base/number-input'
import VueApexCharts from 'vue-apexcharts'
Vue.use(VueApexCharts)

const options = {
  color: '#ff3420',
  failedColor: '#874b4b',
  thickness: '5px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  },
  autoRevert: true,
  location: 'top',
  inverse: false
}
Vue.use(VueProgressBar, options)
Vue.component('apexchart', VueApexCharts)
Vue.use(Buefy)
Vue.use(GridSortable)
Vue.component('Popup', Popup)
Vue.component('GetData', GetData)
Vue.component('b-daterange', DateRange)
Vue.component('AppForm', AppForm)
Vue.component('v-number-input', NumberInput)

Vue.directive('focus', {
  inserted: function (el, binding, vnode) {
    vnode.componentInstance && vnode.componentInstance.focus && vnode.componentInstance.focus()
  }
})
Vue.directive('title', {
  inserted: function (el, binding) {
    if (binding.value) {
      document.querySelector('title').innerText = `CRM | ${binding.value}`
    }
  },
  update: function (el, binding) {
    if (binding.value) {
      document.querySelector('title').innerText = `CRM | ${binding.value}`
    }
  }
})
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App />'
})
