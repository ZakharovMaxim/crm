<template>
  <GetData
    :callback="fetchStocks"
  >
    <Layout>
      <template #sidebar-title>
        Склад
      </template>
      <template #sidebar>
        <div>
          <router-link
            :to="url('/stocks')"
            class="sidebar-link is-exact"
          >
            <span>
              Все зачисления
            </span>
            <span class="sidebar-link_stat">
              {{ stats['total_enrollment'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/enrollment/created')"
            class="sidebar-link sidebar-link_sublink"
            :class="{
              'active': activeStockStatus === '1,0'
            }"
          >
            <span>Подготовленные</span>
            <span class="sidebar-link_stat">
              {{ stats['created_enrollment'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/enrollment/submited')"
            class="sidebar-link sidebar-link_sublink"
            :class="{
              'active': activeStockStatus === '1,1'
            }"
          >
            <span>
              Зачисленные
            </span>
            <span class="sidebar-link_stat">
              {{ stats['submited_enrollment'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/write-off')"
            class="sidebar-link is-exact"
          >
            <span>Все списания</span>
            <span class="sidebar-link_stat sidebar-link_stat_danger">
              {{ stats['total_writeoff'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/write-off/created')"
            class="sidebar-link sidebar-link_sublink"
            :class="{
              'active': activeStockStatus === '2,0'
            }"
          >
            <span>
              Подготовленные
            </span>
            <span class="sidebar-link_stat sidebar-link_stat_danger">
              {{ stats['created_writeoff'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/write-off/submited')"
            class="sidebar-link sidebar-link_sublink"
            :class="{
              'active': activeStockStatus === '2,1'
            }"
          >
            <span>Зачисленные</span>
            <span class="sidebar-link_stat sidebar-link_stat_danger">
              {{ stats['submited_writeoff'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/movements')"
            class="sidebar-link is-exact"
          >
            <span>
              Перемещение
            </span>
            <span class="sidebar-link_stat">
              {{ stats['total_movement'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/movements/created')"
            class="sidebar-link sidebar-link_sublink"
            :class="{
              'active': activeStockStatus === '3,0'
            }"
          >
            <span>
              Подготовленные
            </span>
            <span class="sidebar-link_stat">
              {{ stats['created_movement'] }}
            </span>
          </router-link>
        </div>
        <div>
          <router-link
            :to="url('/stocks/movements/submited')"
            class="sidebar-link sidebar-link_sublink"
            :class="{
              'active': activeStockStatus === '3,1'
            }"
          >
            <span>
              Зачисленные
            </span>
            <span class="sidebar-link_stat">
              {{ stats['submited_movement'] }}
            </span>
          </router-link>
        </div>
      </template>
      <template #content>
        <router-view
          :stocks="stocks"
          :on-load="onLoad"
        />
      </template>
    </Layout>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { STOCK_MODULE } from '@/store/modules/TYPES'
import Layout from '@/components/Layout/Layout'
const { GET_STOCKS, STOCKS, STOCK_STATS } = STOCK_MODULE

export default {
  components: { Layout },
  data () {
    return {
      activeStockStatus: null
    }
  },
  computed: {
    ...mapState({
      stocks: state => state.stockModule[STOCKS],
      stats: state => state.stockModule[STOCK_STATS]
    })
  },
  watch: {
    '$route': function () {
      this.activeStockStatus = null
    }
  },
  methods: {
    ...mapActions([GET_STOCKS]),
    fetchStocks () {
      return this[GET_STOCKS]()
    },
    onLoad (status) {
      this.activeStockStatus = status
    },
    url (url) {
      if (!url) return {}
      return {
        path: url,
        query: {
          ...this.$route.query
        }
      }
    }
  }
}
</script>
