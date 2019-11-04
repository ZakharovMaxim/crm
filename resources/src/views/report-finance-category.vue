<template>
  <div
    v-title="'Отчёт по статьям финансов'"
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
            Отчёт по статьям финансов
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
      <div class="is-flex nav">
        <router-link :to="{query: {...$route.query, type: 1}}">
          Только доходы
        </router-link>
        <router-link :to="{query: {...$route.query, type: 2}}">
          Только расходы
        </router-link>
      </div>
      <div class="table-outer">
        <table class="report-table">
          <tr>
            <th>Категория</th>
            <th>Процент</th>
            <th>Сумма UAH</th>
          </tr>
          <tr
            v-for="(category) in categories"
            :key="category.id"
          >
            <td>{{ category.name }}</td>
            <td>{{ Math.round((category.sum / total * 100) * 100) / 100 || 0 }}%</td>
            <td>{{ category.sum }} UAH</td>
          </tr>
          <tr>
            <td>Всего</td>
            <td>100%</td>
            <td>{{ total }} UAH</td>
          </tr>
        </table>
      </div>
    </GetData>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PAYMENT_STATE_MODULE, SHOP_MODULE, BILL_MODULE } from '@/store/modules/TYPES'
import bFilter from '@/components/base/filter'
import Filter from '@/helpers/filter'
const { GET_PAYMENT_STATES_REPORT, PAYMENT_STATES } = PAYMENT_STATE_MODULE
const { BILLS } = BILL_MODULE
const { SHOPS } = SHOP_MODULE

export default {
  components: {
    'b-filter': bFilter
  },
  data () {
    return {
      update: false,
      filter: new Filter(this, [
        {
          label: 'Магазины',
          items: 'shops'
        },
        {
          label: 'Счета',
          items: 'bills'
        }
      ]),
      toExport: null
    }
  },
  computed: {
    ...mapState({
      categories: store => store.paymentCategoryModule[PAYMENT_STATES].sort((a, b) => b.sum - a.sum),
      shops: store => store.shopModule[SHOPS],
      bills: store => store.billModule[BILLS]
    }),
    total () {
      return this.categories.reduce((acc, cat) => acc + +cat.sum, 0)
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
    ...mapActions([GET_PAYMENT_STATES_REPORT]),
    fetch () {
      return this[GET_PAYMENT_STATES_REPORT]({
        type: this.$route.query.type || 1,
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

<style scoped lang="scss">
.nav {
  margin-top: 10px;
  a {
    display: inline-block;
    padding: 5px;
    background: #eee;
    &.router-link-exact-active {
      background: #00b057;
      color: #fff;
    }
  }
}
</style>
