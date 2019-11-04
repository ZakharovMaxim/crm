<template>
  <div class="payments-list">
    <b-loading
      :active="loading"
      :is-full-page="false"
    />
    <create-payment-popup
      v-if="!!selectedPayment"
      :is-active="!!selectedPayment"
      :type="+selectedPayment.type"
      :payment="selectedPayment"
      @close="selectPayment(null)"
    />
    <div v-if="!payments.length && !optionsLength">
      Вы еще не создали ни одного платежа
    </div>
    <div v-else-if="!payments.length && optionsLength">
      По вашему запросу ничего не найдено
    </div>
    <div
      v-for="payment in payments"
      :key="'payment_' + payment.id"
      class="payment is-flex is-multiline is-gapless"
      :class="+payment.type === 1 ? 'payment--income' : 'payment--outcome'"
      @click="selectPayment(payment)"
    >
      <div class="is-flex w-100">
        <div class="column is-narrow is-narrow-tablet is-narrow-mobile is-centered payment-icon-outer">
          <div
            class="payment-icon"
            :class="`payment-icon--${size}`"
          >
            <b-icon
              icon="arrow-left"
              :size="size"
            />
          </div>
        </div>
        <div class="is-flex column info-col">
          <div class="column is-7">
            <div class="payment-name">
              Платеж #{{ payment.id }}
            </div>
            <div class="payment-category">
              {{ payment.category.name }}
            </div>
            <div class="payment-date">
              {{ formatDate(payment.date) }}
            </div>
          </div>
          <div class="column is-5 tar">
            <div
              v-if="payment.order_id"
              class="payment-order"
              @click.stop
            >
              <router-link :to="`/orders/${payment.order_id}`">
                Заказ #{{ payment.order_id }}
              </router-link>
            </div>
            <payment-stats
              :stats="{[+payment.type === 1 ? 'income' : 'outcome']: payment.sum}"
            />
            <div class="payment-bill">
              {{ payment.bill.name }}
            </div>
          </div>
        </div>
      </div>
      <div class="payment-comment">
        {{ payment.comment }}
      </div>
    </div>
  </div>
</template>

<script>
import createPaymentPopup from '@/components/popups/CreatePaymentPopup'
import paymentStats from '@/components/paymentStats'
import { extendFormat } from '@/helpers/DateFormat'

export default {
  components: { createPaymentPopup, paymentStats },
  props: {
    payments: {
      type: Array,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    },
    optionsLength: {
      type: Number,
      default: 0
    },
    size: {
      validator: function (v) {
        return ['is-large', 'is-medium', 'is-small'].includes(v)
      },
      default: 'is-medium'
    }
  },
  data () {
    return {
      selectedPayment: null
    }
  },
  methods: {
    formatDate (date) {
      return extendFormat(date)
    },
    selectPayment (payment) {
      this.selectedPayment = payment
    }
  }
}
</script>

<style lang="scss">
.payments-list {
  position: relative;
}
.payment {
  background: #f5f5f5;
  border: 1px solid #ccc;
  padding: 5px;
  padding-right: 25px;
  margin-top: 10px;
  transition: all 200ms;
  cursor: pointer;
  &:hover {
    background: #eee;
  }
  &.is-gapless {
    .column {
      padding: 0;
    }
  }
  &-icon-outer {
    margin-right: 5px;
  }
  &-icon {
    padding: 10px;
    background: #fff;
    border-radius: 50%;
    &--is-small {
      padding: 2px;
    }
  }
  &-name {
    color: #333;
    text-decoration: underline;
    font-size: 16px;
  }
  &-category {
    font-size: 12px;
  }
  &-bill {
    font-size: 12px;
    color: #808080;
  }
  &-comment {
    color: #3c8fd5;
    font-size: 12px;
  }
  &-date {
    font-weight: bold;
    color: #808080;
    font-size: 13px;
  }
  &--income {
    .payment {
      &-category {
        color: #00b057;
      }
      &-icon {
        color: #00b057;
        transform: rotate(-90deg);
      }
    }
  }
  &--outcome {
    .payment {
      &-category {
        color: #ff4e4e;
      }
      &-icon {
        color: #ff4e4e;
        transform: rotate(90deg);
      }
      &-sum {
        color: #ff4e4e;
        span:before {
          content: '-'
        }
      }
    }
  }
}
.info-col {
  justify-content: space-between;
}
</style>
