<template>
  <div
    class="payment-sum"
    :class="{'payment-sum--right': align === 'right'}"
  >
    <div
      v-if="stats.total !== undefined"
      class="payment-sum--total"
    >
      {{ stats.total || 0 }} платежей
    </div>
    <div
      v-if="stats.income !== undefined"
      class="payment-sum--value payment-sum--income"
    >
      <span>{{ stats.income || 0 }} UAH</span>
    </div>
    <div
      v-if="stats.outcome !== undefined"
      class="payment-sum--value payment-sum--outcome"
    >
      <span>{{ stats.outcome || 0 }} UAH</span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stats: {
      type: Object,
      required: true
    },
    align: {
      type: String,
      default: ''
    }
  }
}
</script>

<style lang="scss" scoped>
  .payment-sum {
    &--value {
      font-weight: bold;
      font-size: 12px;
    }
    &--income {
      color: #00b057;
      span:before {
        content: '+'
      }
    }
    &--outcome {
      color: #ff4e4e;
      span:before {
        content: '-'
      }
    }
  }
</style>
