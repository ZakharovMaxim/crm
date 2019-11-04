<template>
  <GetData
    :callback="fetchPayments"
  >
    <div>
      <create-payment />
    </div>
    <div v-title="'Платежи'">
      <div class="tar">
        <payment-stats
          :stats="stats"
          align="right"
        />
      </div>
      <div class="filters">
        <payment-filter
          :outcome-categories="outcomeCategories"
          :income-categories="incomeCategories"
          :shops="shops"
          :bills="bills"
          @options-changed="filterChanged"
        />
      </div>
      <finance-list
        :loading="isLoading"
        :payments="payments"
        :options-length="Object.keys(options).length"
      />
      <div v-if="!isOver">
        <b-button
          :loading="isLoading"
          @click="nextPage"
        >
          Еще
        </b-button>
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState, mapGetters } from 'vuex'
import { PAYMENT_MODULE, SHOP_MODULE, BILL_MODULE, PAYMENT_STATE_MODULE } from '@/store/modules/TYPES'
import createPayment from '@/components/CreatePayment'
import paymentFilter from '@/components/paymentFilter'
import financeList from '@/components/FinanceList'
import paymentStats from '@/components/paymentStats'

const { BILLS } = BILL_MODULE
const { GET_PAYMENTS, PAYMENTS, PAYMENTS_STATS } = PAYMENT_MODULE
const { OUTCOME_PAYMENT_STATES, INCOME_PAYMENT_STATES } = PAYMENT_STATE_MODULE
const { SHOPS } = SHOP_MODULE
const PER_PAGE = 20

export default {
  components: { paymentFilter, createPayment, financeList, paymentStats },
  data () {
    return {
      page: 1,
      isOver: true,
      isLoading: false,
      options: {},
      selectedPayment: null
    }
  },
  computed: {
    ...mapState({
      payments: state => state.paymentModule[PAYMENTS],
      bills: state => state.billModule[BILLS],
      stats: state => state.paymentModule[PAYMENTS_STATS],
      shops: state => state.shopModule[SHOPS]
    }),
    ...mapGetters({
      'outcomeCategories': OUTCOME_PAYMENT_STATES,
      'incomeCategories': INCOME_PAYMENT_STATES
    })
  },
  methods: {
    ...mapActions([GET_PAYMENTS]),
    filterChanged (options) {
      this.page = 1
      this.options = { ...options }
      this.isLoading = true
      this.fetchPayments(!!1).finally(() => {
        this.isLoading = false
      })
    },
    nextPage () {
      this.page++
      this.isLoading = true
      this.fetchPayments(!!1).finally(() => {
        this.isLoading = false
      })
    },
    fetchPayments (notInit) {
      const query = {
        page: this.page,
        per_page: PER_PAGE,
        ...this.options
      }
      if (!notInit) {
        query.init = true
      }
      return this[GET_PAYMENTS](query).then((response) => {
        this.isOver = this.stats.total <= this.page * PER_PAGE
        return response
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.filters {
  margin: 10px 0;
}
</style>
