<template>
  <GetData
    v-title="title"
    :callback="fetchOrders"
    :update="update"
  >
    <b-loading :active="isLoading" />
    <OrdersChart
      v-if="orders.length"
      :xaxis="xaxis"
      :yaxis="yaxis"
    />
    <div
      v-if="orders.length"
      class="stats"
    >
      <div class="stats_total">
        <span class="stats_danger">{{ stats['total'] }}</span> заказа(ов)
      </div>
      <div class="stats_stats">
        <span class="stats_orders_total">
          {{ stats['order_total'] || 0 }}
        </span>
        <span class="stats_income">
          {{ stats['income'] || 0 }}
        </span>
        <span class="stats_outcome stats_danger">
          {{ stats['outcome'] || 0 }}
        </span>
        UAH
      </div>
    </div>
    <order-card
      v-for="order in orders"
      :key="'order_' + order.id"
      :order="order"
      :selectable="isSelectable(order)"
      :selected="isSelected(order.id)"
      @select="toggleSelect"
    />
    <div v-if="!orders.length">
      <span>Заказов не найдено, создайте новый и переведите его в данный статус</span>
    </div>
    <div v-if="!isOver">
      <b-button
        @click="nextPage"
      >
        Еще
      </b-button>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { ORDER_MODULE } from '@/store/modules/TYPES'
import orderCard from '@/components/Cards/orderCard'
import { setMonth, dbFormat, simpleFormat } from '@/helpers/DateFormat'
import OrdersChart from '@/components/Charts/OrdersChart'
const { GET_ORDERS, ORDERS, ORDERS_PAYMENT_STATS } = ORDER_MODULE
const PER_PAGE = 20

export default {
  components: { orderCard, OrdersChart },
  props: {
    statuses: {
      type: Object,
      default: () => {}
    }
  },
  data () {
    return {
      update: false,
      isOver: true,
      page: 1,
      isLoading: false,
      selected: []
    }
  },
  computed: {
    ...mapState({
      orders: state => state.orderModule[ORDERS],
      stats: state => state.orderModule[ORDERS_PAYMENT_STATS]
    }),
    xaxis () {
      return this.stats['chart'] ? this.stats['chart'].map(row => simpleFormat(row.date)) : []
    },
    yaxis () {
      return this.stats['chart'] ? this.stats['chart'].map(row => row.views) : []
    },
    title () {
      const slug = this.$route.query.status
      let statusName = 'Все'
      for (let id in this.statuses) {
        if (this.statuses[id].slug === slug) {
          statusName = this.statuses[id]['label']
        }
      }
      return `Заказы: ${statusName}`
    }
  },
  watch: {
    '$route': function () {
      this.page = 1
      this.update = !this.update
    }
  },
  methods: {
    ...mapActions([GET_ORDERS]),
    isSelected (id) {
      return !!this.selected.find(order => order.id === id)
    },
    isSelectable (order) {
      return !!(order.ttn && order.np_key)
    },
    toggleSelect (order) {
      if (this.isSelected(order.id)) {
        this.selected = this.selected.filter(item => item.id !== order.id)
      } else {
        this.selected = [...this.selected, order]
      }
    },
    fetchOrders () {
      return this[GET_ORDERS]({
        status: this.$route.query.status,
        per_page: PER_PAGE,
        page: this.page,
        shop: this.$route.query.shop,
        date_from: this.$route.query.date_from || dbFormat(setMonth()[0], true),
        date_to: this.$route.query.date_to || dbFormat(setMonth()[1], true),
        chart: true
      }).then(response => {
        this.isOver = response.data.isOver
      })
    },
    nextPage () {
      this.page++
      this.isLoading = true
      this.fetchOrders().finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.stats {
  font-size: 12px;
  font-weight: bold;
  text-align: right;
  &_income {
    color: #00b057;
  }
  &_danger {
    color: #ff4e4e;
  }
  &_stats {
    span:not(:last-child) {
      display: inline-block;
      &:after {
        content: '/';
        margin: 0 2px;
      }
    }
  }
}
</style>
