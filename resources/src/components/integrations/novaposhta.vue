<template>
  <div class="novaposhta">
    <b-loading
      :active="isLoading"
      :is-full-page="false"
    />
    <div class="subtitle">
      Новая почта
    </div>
    <div v-if="order.ttn">
      <div class="tac">
        <span>Номер накладной: </span>
        <span>{{ order.ttn }}</span>
        <div v-if="order.np_status">
          {{ order.np_status.status }}
        </div>
      </div>
      <div v-if="order.np_status">
        <div class="ttn_cities">
          <div>
            <span v-if="order.np_status.sender_city">
              {{ order.np_status.sender_city }}
            </span>
            <span v-if="order.np_status.created_date">
              {{ order.np_status.created_date }}
            </span>
            <span v-if="order.np_status.cost">
              {{ order.np_status.cost }}грн.
            </span>
          </div>
          <div class="tar">
            <span v-if="order.np_status.recipient_city">
              {{ order.np_status.recipient_city }}
            </span>
            <span v-if="order.np_status.delivery_date">
              {{ order.np_status.delivery_date }}
            </span>
          </div>
        </div>
        <div class="ttn_timeline" />
        <div
          v-if="order.np_status.address"
          class="tar"
        >
          {{ order.np_status.address }}
        </div>
      </div>
    </div>
    <div
      v-if="isSetTtnVisible"
    >
      <div class="is-flex">
        <set-ttn
          :default-value="order.ttn || ''"
          @submit="setTtn"
        />
        <b-button @click="isCreateTtnOpen = true">
          Создать накладную
        </b-button>
      </div>
      <create-ttn
        v-if="isCreateTtnOpen"
        :api-key="order.np_key"
        :order="order"
        :order-sum="orderSum"
      />
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { NP_MODULE, ORDER_MODULE } from '@/store/modules/TYPES'
import SetTtn from './setTtn'
import CreateTtn from './createTtn'
const { SET_ORDER_TTN } = NP_MODULE
const { GET_TTNS } = ORDER_MODULE

export default {
  components: { SetTtn, CreateTtn },
  props: {
    order: {
      type: Object,
      required: true
    },
    orderSum: {
      type: Number,
      required: true
    }
  },
  data () {
    return {
      isLoading: false,
      isCreateTtnOpen: false
    }
  },
  computed: {
    isSetTtnVisible () {
      return !this.order.ttn || !this.order.np_status || (this.order.np_status && +this.order.np_status.status_code === 3)
    }
  },
  created () {
    if (this.order.ttn && this.order.np_key) {
      const ttns = [{
        ttn: this.order.ttn,
        phone: this.order.customer_phone,
        key: this.order.np_key
      }]
      this[GET_TTNS]({
        ttns: {
          ttns
        }
      })
    }
  },
  methods: {
    ...mapActions([SET_ORDER_TTN, GET_TTNS]),
    setTtn: async function (ttn) {
      this.isLoading = true
      try {
        await this[SET_ORDER_TTN]({
          id: this.order.id,
          key: this.order.np_key,
          phone: this.order.customer_phone,
          data: {
            ttn
          }
        })
        this.$buefy.toast.open('Номер ттн установлен')
      } catch (e) {
        this.$buefy.toast.open('Номер ттн не установлен')
      }
      this.isLoading = false
    }
  }
}
</script>

<style lang="scss" scoped>
.novaposhta {
  position: relative;
}
.ttn {
  &_cities {
    display: flex;
    justify-content: space-between;
    & > div > span {
      display: block;
    }
  }
  &_timeline {
    position: relative;
    height: 1px;
    background: #000;
    margin-bottom: 10px;
    &:before, &:after {
      content: '';
      display: block;
      width: 10px;
      height: 10px;
      background: #000;
      position: absolute;
      top: -5px;
      border-radius: 50%;
    }
    &:before {
      left: -5px;
    }
    &:after {
      right: -5px;
    }
  }
}
</style>
