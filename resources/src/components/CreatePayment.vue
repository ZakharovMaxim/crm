<template>
  <div>
    <b-button @click="isCreatePaymentOpen = true">
      <b-icon icon="plus" />
    </b-button>
    <b-button @click="isCreatePaymentNegativeOpen = true">
      <b-icon icon="minus" />
    </b-button>
    <create-payment-popup
      v-if="isCreatePaymentOpen"
      :is-active="isCreatePaymentOpen"
      :type="1"
      :order-id="orderId"
      @close="isCreatePaymentOpen = false"
    />
    <create-payment-popup
      v-if="isCreatePaymentNegativeOpen"
      :is-active="isCreatePaymentNegativeOpen"
      :type="2"
      :order-id="orderId"
      @close="isCreatePaymentNegativeOpen = false"
    />
  </div>
</template>

<script>
import createPaymentPopup from '@/components/popups/CreatePaymentPopup'

export default {
  components: { createPaymentPopup },
  props: {
    orderId: {
      type: Number,
      default: null
    }
  },
  data () {
    return {
      isCreatePaymentNegativeOpen: false,
      isCreatePaymentOpen: false
    }
  }
}
</script>

<style>

</style>
