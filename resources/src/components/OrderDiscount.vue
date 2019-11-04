<template>
  <div
    class="product-discount"
  >
    <b-dropdown
      position="is-top-left"
      aria-role="list"
      :disabled="disabled"
    >
      <b-button
        slot="trigger"
        :class="className"
      >
        <span v-if="actualPrice !== sellingPrice">
          {{ label }}{{ discount | decimal }}%
        </span>
        <span v-else>Дать скидку</span>
      </b-button>
      <div class="is-flex product-discount_input">
        <v-number-input
          v-model="discountLocal"
          type="number"
          :disabled="disabled"
          @focus="select"
          @submit="submit"
        />
        <b-dropdown-item
          aria-role="listitem"
          @click="submit"
        >
          OK
        </b-dropdown-item>
      </div>
    </b-dropdown>
  </div>
</template>

<script>
export default {
  filters: {
    decimal (num) {
      return Math.round(num * 100) / 100
    }
  },
  props: {
    sellingPrice: {
      type: Number,
      required: true
    },
    actualPrice: {
      type: Number,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      discountLocal: Math.abs(this.sellingPrice - this.actualPrice) / this.sellingPrice * 100
    }
  },
  computed: {
    label () {
      return this.sellingPrice > this.actualPrice ? 'Скидка ' : 'Наценка '
    },
    className () {
      return this.sellingPrice > this.actualPrice ? 'is-success' : this.sellingPrice < this.actualPrice ? 'is-danger' : ''
    },
    discount () {
      const d = Math.abs(this.sellingPrice - this.actualPrice) / this.sellingPrice * 100
      return Number.isFinite(d) ? d : 100
    }
  },
  watch: {
    'actualPrice': function () {
      this.discountLocal = Math.abs(this.sellingPrice - this.actualPrice) / this.sellingPrice * 100
    }
  },
  methods: {
    submit () {
      this.$emit('submit', this.discountLocal)
    },
    select (e) {
      e.target.select()
    }
  }
}
</script>

<style lang="scss" scoped>
.product-discount {
  display: inline-block;
  &_input {
    padding-left: 7px;
    input {
      padding-left: 5px;
    }
  }
}
</style>
