<template>
  <router-link
    :to="`/orders/${order.id}`"
    class="is-flex order is-multiline"
    :class="`order--${order.status_slug}`"
    @click="$emit('click')"
  >
    <div class="is-flex w-100 is-gapless-nano">
      <div
        class="column is-narrow is-narrow-tablet is-narrow-mobile is-centered order_icon"
        :class="{'order_icon--selected': selected, 'order_icon--selectable': selectable}"
        @click.stop.prevent="iconClicked"
      >
        <b-icon
          icon="package"
          size="is-large"
        />
      </div>
      <div class="column is-flex">
        <div class="column is-8-desktop is-9-tablet is-9-mobile is-flex gapless is-multiline">
          <div class="column is-6 is-12-mobile">
            <div class="is-flex">
              <div class="order_name">
                Заказ #{{ order.id }}
              </div>
              <productList :products="order.products" />
            </div>
            <div class="order_customer is-flex is-multiline">
              <div class="order_phone">
                {{ order.customer_phone }}
              </div>
              <div
                v-if="order.customer_name || order.customer_surname"
                class="order_customer_name"
              >
                ({{ order.customer_name }} {{ order.customer_surname }})
              </div>
            </div>
            <div class="order_date">
              <span
                v-if="withStatus"
                class="order_status"
              >
                {{ order.status }}
              </span>от <span>{{ formatDate(order.status_updated_at) }}</span>
            </div>
          </div>
          <div
            v-if="order.delivery"
            class="column is-6 order_delivery is-12-mobile"
          >
            {{ order.delivery.name }}
            <div v-if="order.ttn">
              Номер накладной: {{ order.ttn }}
            </div>
            <div v-if="order.np_status">
              {{ order.np_status }}
            </div>
          </div>
        </div>
        <div class="column is-4-desktop is-3-tablet is-3-mobile tar">
          <div class="order_price">
            <div
              class="order_price-total"
              :class="{
                'order_price-total--complete': price(order) <= order.payment_income,
                'order_price-total--incomplete': (price(order) > order.payment_income) && order.payment_income
              }"
            >
              {{ price(order) }}UAH
            </div>
            <div>
              <span class="order_price-income">
                {{ order.payment_income || 0 }}
              </span>
              <span>/</span>
              <span class="order_price-outcome">
                {{ order.payment_outcome || 0 }}
              </span>
            </div>
          </div>
          <div
            v-if="order.payment"
            class="order_payment"
          >
            {{ order.payment.name }}
          </div>
        </div>
      </div>
    </div>
    <div class="order_comment">
      {{ order.order_comment }}
    </div>
  </router-link>
</template>

<script>
import productList from '@/components/stockActionImages'
import { extendFormat } from '@/helpers/DateFormat'

export default {
  components: { productList },
  props: {
    order: {
      type: Object,
      required: true
    },
    withStatus: {
      type: Boolean,
      default: false
    },
    selectable: Boolean,
    selected: Boolean
  },
  methods: {
    price (order) {
      if (order.products && order.products.length) {
        return order.products.reduce((acc, prod) => acc + (prod.pivot.count * prod.pivot.price), 0)
      } else {
        return 0
      }
    },
    formatDate (date) {
      return extendFormat(date)
    },
    iconClicked (e) {
      if (this.selectable) {
        this.$emit('select', this.order)
      }
    }
  }
}
</script>

<style scoped lang="scss">
  .order {
  border: 1px solid #eee;
  background: #f8f8f8;
  margin-top: 10px;
  transition: all 200ms;
  &--new {
    background: #ffffb6;
  }
  &--canceled {
    background: #ffc0c0;
  }
  &:hover {
    background: #fff;
    cursor: pointer;
  }
  &_icon {
    &--selected {
      color: #222;
    }
    &:not(&--selectable) {
      cursor: default;
    }
  }
  &_name {
    color: #333333;
    font-size: 0.95rem;
    text-decoration: underline;
  }
  &_date {
    font-size: 0.70rem;
    color: #808080;
    span {
      font-weight: bold;
    }
  }
  &_customer_name {
    font-size: 0.80rem;
    color: #808080;
  }
  &_delivery {
    font-size: 0.7rem;
    color: #808080;
  }
  &_phone {
    color: #237db9;
    font-size: 0.8rem;
    margin-right: 5px;
  }
  &_status {
    color: #237db9;
  }
  &_payment {
    color: #237db9;
    font-size: 0.7rem;
  }
  &_comment {
    color: #ff0087;
    font-size: 0.70rem;
    font-style: italic;
  }
  &_price {
    font-size: 12px;
    font-weight: bold;
    &-total {
      font-size: 14px;
      color: #808080;
      &--complete {
        color: #00b057;
      }
      &--incomplete {
        color: #c5c500;
      }
      &--empty {
        color: #808080;
      }
    }
    &-income {
      color: #00b057;
    }
    &-outcome {
      color: #ff4e4e;
    }
  }
}
</style>
