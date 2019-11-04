<template>
  <GetData
    :callback="fetchData"
  >
    <b-loading :is-active="isLoading" />
    <Layout>
      <template #sidebar-title>
        Заказы
      </template>
      <template #sidebar>
        <div class="sidebar-compact">
          <div>
            <date-picker @input="setDate" />
            <div class="sidebar-tools">
              <b-select
                v-model="shopId"
              >
                <option
                  selected
                  value=""
                >
                  Все магазины
                </option>
                <option
                  v-for="shop in shops"
                  :key="'shop' + shop.id"
                  :value="shop.id"
                >
                  {{ shop.name }}
                </option>
              </b-select>
              <div>
                <create-order-popup
                  :shops="shops"
                />
                <b-button @click="isSearchOpened = true">
                  <b-icon icon="magnify" />
                </b-button>
                <findOrderPopup
                  :is-active="isSearchOpened"
                  @close="isSearchOpened = false"
                />
              </div>
            </div>
          </div>
          <div>
            <router-link
              :to="url()"
              class="sidebar-link is-exact"
            >
              <span>
                Всего поступило
              </span>
              <span class="sidebar-link_stat">
                {{ stats['total'] }}
              </span>
            </router-link>
          </div>
          <div class="sidebar-subtitle">
            Статусы
          </div>
          <div
            v-for="stat in statsFiltered"
            :key="'stat_' + stat.slug"
          >
            <router-link
              :to="url(stat.slug)"
              class="sidebar-link is-exact"
              :class="{
                'sidebar-link_sublink': stat.is_sub,
                'router-link-exact-active active': +activeStatus === +stat.id
              }"
            >
              <span>
                <table class="sidebar-table-align">
                  <tr>
                    <td>
                      <b-icon :icon="stat.icon" />
                    </td>
                    <td>
                      {{ stat.label }}
                    </td>
                  </tr>
                </table>
              </span>
              <span
                class="sidebar-link_stat"
                :class="{
                  'sidebar-link_stat_danger': stat.type === 'is-danger',
                  'sidebar-link_stat_info': stat.type === 'is-info',
                  'sidebar-link_stat_success': stat.type === 'is-success'
                }"
              >
                {{ stat.count }}
              </span>
            </router-link>
          </div>
        </div>
      </template>
      <template #content>
        <router-view
          :on-load="setActiveStatus"
          :statuses="statsFiltered"
        />
      </template>
    </Layout>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { ORDER_MODULE, SHOP_MODULE } from '@/store/modules/TYPES'
import Layout from '@/components/Layout/Layout'
import createOrderPopup from '@/components/popups/CreateOrderPopup'
import findOrderPopup from '@/components/popups/FindOrderPopup'
import datePicker from '@/components/popups/DatePicker'
import { dbFormat, setMonth } from '@/helpers/DateFormat'
const { GET_ORDERS_INFO, ORDERS_STATS } = ORDER_MODULE
const { SHOPS } = SHOP_MODULE

export default {
  components: { Layout, createOrderPopup, findOrderPopup, datePicker },
  data () {
    return {
      isSearchOpened: false,
      shopId: this.$route.query.shop || '',
      isLoading: false,
      activeStatus: null
    }
  },
  computed: {
    ...mapState({
      shops: state => state.shopModule[SHOPS],
      stats: state => state.orderModule[ORDERS_STATS]
    }),
    statsFiltered () {
      const stats = { ...this.stats }
      delete stats['total']
      return stats
    }
  },
  watch: {
    'shopId': function (v) {
      const query = {
        ...this.$route.query
      }
      delete query.shop
      if (v) {
        query.shop = v
      }
      this.$router.push({
        query
      })
      this.fetchData(!!1)
    },
    '$route': function () {
      this.activeStatus = null
    }
  },
  methods: {
    ...mapActions([GET_ORDERS_INFO]),
    setActiveStatus (statusId) {
      this.activeStatus = statusId
    },
    fetchData (setLoading) {
      if (setLoading) {
        this.isLoading = true
      }
      return this[GET_ORDERS_INFO]({
        shop: this.shopId,
        date_from: this.$route.query.date_from || dbFormat(setMonth()[0]),
        date_to: this.$route.query.date_to || dbFormat(setMonth()[1])
      }).finally(() => {
        if (setLoading) {
          this.isLoading = false
        }
      })
    },
    setDate (v) {
      const query = {
        ...this.$route.query
      }
      delete query.date_from
      delete query.date_to
      if (v.from) {
        query.date_from = dbFormat(v.from, true)
      }
      if (v.to) {
        query.date_to = dbFormat(v.to, true)
      }
      this.$router.push({
        query
      })
      this.fetchData(!!1)
    },
    url (status) {
      return {
        path: '/orders',
        query: {
          ...this.$route.query,
          status
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .sidebar-tools {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 20px;
  }
</style>
