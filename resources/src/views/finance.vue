<template>
  <GetData
    :callback="fetchData"
  >
    <Layout>
      <template #sidebar-title>
        Финансовый учет
      </template>
      <template #sidebar>
        <div>
          <router-link
            to="/finance"
            class="sidebar-link is-exact"
          >
            <span>Все платежи</span>
            <span class="sidebar-link_stat">
              {{ paymentsCount }}
            </span>
          </router-link>
        </div>
        <div>
          <div class="sidebar-subtitle">
            Категории
          </div>
        </div>
        <div>
          <router-link
            to="/finance/income"
            class="sidebar-link"
          >
            Категории доходов
          </router-link>
        </div>
        <div>
          <router-link
            to="/finance/outcome"
            class="sidebar-link"
          >
            Категории расходов
          </router-link>
        </div>
        <div>
          <div class="sidebar-subtitle">
            Счета
          </div>
        </div>
        <div>
          <div v-if="!bills.length">
            Нет счетов
          </div>
          <div v-else>
            <div
              v-for="bill in bills"
              :key="'bill_' + bill.id"
              class="sidebar-link"
              @click="setBill(bill)"
            >
              <span v-text="bill.name" />
              <span
                class="sidebar-link_stat"
                v-text="bill.payments_count"
              />
            </div>
            <update-bill-popup
              v-if="bill"
              :is-active="!!bill"
              :bill="bill"
              @close="setBill(null)"
            />
          </div>
          <b-button
            @click="isBillCreateActive = true"
          >
            Создать счет
          </b-button>
          <create-bill-popup
            v-if="isBillCreateActive"
            :is-active="isBillCreateActive"
            @close="isBillCreateActive = false"
          />
        </div>
      </template>
      <template #content>
        <router-view />
      </template>
    </Layout>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PAYMENT_MODULE, BILL_MODULE } from '@/store/modules/TYPES'
import Layout from '@/components/Layout/Layout'
import CreateBillPopup from '@/components/popups/CreateBillPopup'
import UpdateBillPopup from '@/components/popups/UpdateBillPopup'
const { BILLS, BILL, SET_BILL } = BILL_MODULE
const { GET_PAYMENTS_INFO } = PAYMENT_MODULE

export default {
  components: { Layout, CreateBillPopup, UpdateBillPopup },
  data () {
    return {
      isBillCreateActive: false
    }
  },
  computed: {
    ...mapState({
      bills: state => state.billModule[BILLS],
      bill: state => state.billModule[BILL]
    }),
    paymentsCount () {
      return this.bills.reduce((acc, bill) => acc + (bill.payments_count || 0), 0)
    }
  },
  methods: {
    ...mapActions([GET_PAYMENTS_INFO, SET_BILL]),
    fetchData () {
      return this[GET_PAYMENTS_INFO]()
    },
    setBill (bill) {
      this[SET_BILL](bill)
    }
  }
}
</script>
