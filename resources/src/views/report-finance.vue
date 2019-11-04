<template>
  <div
    v-title="'Отчет об остатках (по товарам и услугам)'"
    class="report"
  >
    <a
      ref="link"
      href="#"
      class="download-link"
      target="_blank"
    />
    <GetData
      :callback="fetch"
      :update="update"
    >
      <div class="report-header">
        <div>
          <router-link to="/">
            <b-icon icon="arrow-left" />
          </router-link>
          <span class="report-title">
            Отчет об остатках <span class="report-title_exp">(по товарам и услугам)</span>
          </span>
        </div>
        <div>
          <b-button @click="toExport = 1">
            <b-icon
              icon="cloud-print"
            />
          </b-button>
        </div>
      </div>
      <payment-stats
        :stats="stats"
      />
      <b-filter
        :filter="filter"
      />
      <div class="table-outer">
        <table class="report-table">
          <tr>
            <th>#</th>
            <th>Дата создания</th>
            <th>Платеж</th>
            <th>Категория</th>
            <th>Сумма</th>
            <th>Валюта</th>
            <th>Счёт</th>
            <th>Магазин</th>
            <th>Объект</th>
            <th>Комментарий</th>
          </tr>
          <tr
            v-for="(payment, index) in payments"
            :key="payment.id"
          >
            <td>{{ index + 1 }}</td>
            <td>{{ payment.date }}</td>
            <td>Платеж #{{ payment.id }}</td>
            <td>{{ payment.category.name }}</td>
            <td :class="+payment.type === 1 ? 'income' : 'outcome'">
              {{ payment.sum }}
            </td>
            <td>UAH</td>
            <td>{{ payment.bill.name }}</td>
            <td>{{ payment.shop && payment.shop.name }}</td>
            <td>
              <span v-if="payment.order_id">Заказ #{{ payment.order_id }}</span>
            </td>
            <td>{{ payment.comment }}</td>
          </tr>
        </table>
      </div>
    </GetData>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PAYMENT_STATE_MODULE, SHOP_MODULE, PAYMENT_MODULE, BILL_MODULE } from '@/store/modules/TYPES'
import bFilter from '@/components/base/filter'
import Filter from '@/helpers/filter'
import paymentStats from '@/components/paymentStats'
const { BILLS } = BILL_MODULE
const { PAYMENT_STATES } = PAYMENT_STATE_MODULE
const { PAYMENTS, PAYMENTS_STATS, GET_PAYMENTS_REPORT } = PAYMENT_MODULE
const { SHOPS } = SHOP_MODULE

export default {
  components: {
    bFilter,
    paymentStats
  },
  data () {
    return {
      update: false,
      types: [
        {
          id: 1,
          name: 'Входящий'
        },
        {
          id: 2,
          name: 'Исходящий'
        }
      ],
      filter: new Filter(this, [
        {
          label: 'Дата создания',
          items: 'date',
          type: 'date'
        },
        {
          label: 'Магазины',
          items: 'shops'
        },
        {
          label: 'Счета',
          items: 'bills'
        },
        {
          label: 'Категории платежа',
          items: 'categories',
          type: 'single'
        },
        {
          label: 'Тип платежа',
          items: 'types'
        }
      ]),
      toExport: null
    }
  },
  computed: {
    ...mapState({
      payments: store => store.paymentModule[PAYMENTS],
      shops: store => store.shopModule[SHOPS],
      bills: store => store.billModule[BILLS],
      categories: store => store.paymentCategoryModule[PAYMENT_STATES],
      stats: store => store.paymentModule[PAYMENTS_STATS]
    })
  },
  watch: {
    '$route': function () {
      this.update = !this.update
    },
    'toExport': function (v) {
      if (v) this.update = !this.update
    }
  },
  methods: {
    ...mapActions([GET_PAYMENTS_REPORT]),
    fetch () {
      return this[GET_PAYMENTS_REPORT]({
        per_page: 0,
        report: 1,
        export: this.toExport,
        ...this.filter.get()
      }).then((response) => {
        if (this.toExport) {
          this.$refs.link.href = `${location.origin}/${response.data}`
          this.$refs.link.click()
        } else this.filter.update()
      }).finally(() => {
        this.toExport = null
      })
    }
  }
}
</script>
