<template>
  <div
    v-title="'Отчёт о продажах (по заказам)'"
    class="report"
  >
    <a
      ref="link"
      href="#"
      target="_blank"
      class="download_link"
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
            Отчёт о продажах <span class="report-title_exp">(по заказам)</span>
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
      <b-filter
        :filter="filter"
      />
      <div class="table-outer">
        <table class="report-table">
          <tr>
            <th>#</th>
            <th class="empty" />
            <th>Дата создания</th>
            <th>Заказ</th>
            <th>Cтатус</th>
            <th>Магазин</th>
            <th>Канал-продаж</th>
            <th>Валюта</th>
            <th>Получатель</th>
            <th>Телефон</th>
            <th>Сумма</th>
            <th>Входящие</th>
            <th>Исходящие</th>
            <th>Способ оплаты</th>
            <th>Счёт для оплаты</th>
            <th>Способ доставки</th>
            <th>Адрес доставки</th>
            <th>Номер ТТН</th>
            <th>Заметки к заказу</th>
            <th>Создатель</th>
            <th>Менеджер</th>
          </tr>
          <tr
            v-for="(order, index) in orders"
            :key="order.id"
          >
            <td>{{ index + 1 }}</td>
            <td />
            <td>{{ order.created_at | date }}</td>
            <td>Заказ #{{ order.id }}</td>
            <td>{{ order.status }}</td>
            <td>{{ order.shop && order.shop.name }}</td>
            <td>{{ order.channel && order.channel.title }}</td>
            <td>UAH</td>
            <td>{{ customer(order) }}</td>
            <td>{{ order.customer_phone }}</td>
            <td>{{ sum(order) }}</td>
            <td>{{ order.payment_income }}</td>
            <td>{{ order.payment_outcome }}</td>
            <td>{{ order.payment && order.payment.name }}</td>
            <td>{{ order.bill && order.bill.name }}</td>
            <td>{{ order.delivery && order.delivery.name }}</td>
            <td>{{ order.delivery_city }} {{ order.delivery_address }}</td>
            <td>{{ order.ttn }}</td>
            <td>{{ order.order_comment }}</td>
            <td>{{ creator(order) }}</td>
            <td>{{ manager(order) }}</td>
          </tr>
        </table>
      </div>
    </GetData>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { ORDER_MODULE, PRODUCT_MODULE, SHOP_MODULE, BILL_MODULE } from '@/store/modules/TYPES'
import bFilter from '@/components/base/filter'
import Filter from '@/helpers/filter'
import { simpleFormat } from '@/helpers/DateFormat'
const { GET_ORDERS_REPORT, ORDERS, ORDER_STATUSES, PAYMENT_CHANNELS, PAYMENT_TYPES, DELIVERIES } = ORDER_MODULE
const { PRODUCT } = PRODUCT_MODULE
const { BILLS } = BILL_MODULE
const { SHOPS } = SHOP_MODULE

export default {
  filters: {
    date (v) {
      return simpleFormat(v, true)
    }
  },
  components: { bFilter },
  data () {
    return {
      update: false,
      filter: new Filter(this, [
        {
          label: 'Дата создания',
          items: 'created_at',
          type: 'date'
        },
        {
          label: 'Дата проведения',
          items: 'status_updated_at',
          type: 'date'
        },
        {
          label: 'Магазины',
          items: 'shops'
        },
        {
          label: 'Канал продаж',
          items: 'channels',
          displayName: 'title',
          type: 'single'
        },
        {
          label: 'Статус заказа',
          items: 'statuses',
          displayName: 'label',
          type: 'single',
          addNullOption: true
        },
        {
          label: 'Способ доставки',
          items: 'deliveries',
          type: 'single'
        },
        {
          label: 'Способ оплаты',
          items: 'payments',
          type: 'single'
        },
        {
          label: 'Счет оплаты',
          items: 'bills'
        },
        {
          label: 'Товар',
          items: 'product_id',
          fetch: 'orders.0.products.0',
          type: 'removable'
        }
      ]),
      toExport: null
    }
  },
  computed: {
    ...mapState({
      orders: store => store.orderModule[ORDERS],
      channelList: store => store.orderModule[PAYMENT_CHANNELS],
      shops: store => store.shopModule[SHOPS],
      bills: store => store.billModule[BILLS],
      payments: store => store.orderModule[PAYMENT_TYPES],
      product: store => store.catalogModule[PRODUCT],
      deliveries: store => store.orderModule[DELIVERIES],
      statusesList: store => store.orderModule[ORDER_STATUSES]
    }),
    channels () {
      let result = []
      for (let group in this.channelList) {
        result = [...result, ...this.channelList[group]]
      }
      return result
    },
    statuses () {
      let result = []
      for (let key in this.statusesList) {
        result = [...result, {
          id: key,
          ...this.statusesList[key]
        }]
      }
      return result
    }
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
    ...mapActions([GET_ORDERS_REPORT]),
    fetch () {
      return this[GET_ORDERS_REPORT]({
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
    },
    sum (order) {
      return order.products.reduce((acc, row) => acc + row.pivot.price * row.pivot.count, 0)
    },
    customer (order) {
      return `${order.customer_name || ''}${order.customer_surname ? ' ' + order.customer_surname : ''}`
    },
    creator (order) {
      return order.creator ? (order.creator.name + ' ' + order.creator.surname) : ''
    },
    manager (order) {
      return order.manager ? (order.manager.name + ' ' + order.manager.surname) : ''
    }
  }
}
</script>
