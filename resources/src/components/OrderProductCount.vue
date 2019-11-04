<template>
  <div class="is-flex input-row">
    <div class="is-flex input-row_values">
      <v-number-input
        v-model="count"
        type="text"
        class="small-input"
        :disabled="disabled"
        @blur="fieldChanged('count')"
      />
      <span>
        <b-icon
          icon="close"
          size="is-small"
        />
      </span>
      <v-number-input
        v-model="price"
        type="text"
        class="small-input"
        :disabled="disabled"
        @blur="fieldChanged('price')"
      />
      <span>=</span>
      <v-number-input
        v-model="total"
        type="text"
        class="small-input"
        :disabled="disabled"
        @blur="fieldChanged('total')"
      />
    </div>
    <OrderDiscount
      :actual-price="row.price"
      :selling-price="row.selling_price"
      :disabled="disabled"
      @submit="setDiscount"
    />
  </div>
</template>

<script>
import OrderDiscount from '@/components/OrderDiscount'
import Form from '@/helpers/Form'

const defaultValues = {
  count: 1,
  price: 0,
  total: 0
}
export default {
  components: { OrderDiscount },
  props: {
    row: {
      required: true,
      type: Object
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      count: this.row.count || defaultValues['count'],
      price: this.row.price || defaultValues['price'],
      total: (this.row.count || defaultValues['count']) * (this.row.price || defaultValues['price'])
    }
  },
  watch: {
    'row': function (v) {
      this.price = v.price
      this.total = this.price * this.count
    }
  },
  methods: {
    setDiscount (discount) {
      if (this.disabled) return
      if (!Form.isNumber(discount)) return
      this.price = this.row.selling_price - (this.row.selling_price / 100 * discount)
      this.fieldChanged('price')
    },
    fieldChanged (field) {
      if (this.disabled) return
      if (this[field] === this.row[field]) return
      if (field === 'total' && this.total === this.count * this.price) return

      if (field === 'total') {
        this.price = this.total / this.count
        if (!Form.isNumber(this.price)) this.price = defaultValues['price']
        this.$emit('changed', {
          prop: 'price',
          value: this.price,
          productId: this.row.product.id,
          discount: Math.abs(this.row.selling_price - this.price) / this.row.selling_price * 100
        })
      } else if (field === 'price' || field === 'count') {
        if (!Form.isNumber(this[field])) this[field] = defaultValues[field]
        const data = {
          prop: field,
          value: this[field],
          productId: this.row.product.id
        }
        if (field === 'price') {
          data.discount = Math.abs(this.row.selling_price - this.price) / this.row.selling_price * 100
        }
        this.$emit('changed', data)
        this.total = this.count * this.price
      }
    }
  }
}
</script>

<style scoped lang="scss">
.input-row {
  align-items: center;
  flex-wrap: wrap;
  &_values {
    align-items: center;
    margin-right: 10px;
  }
}
.small-input {
  padding: 0;
  text-align: center;
  width: 70px;
}
@media screen and (max-width: 470px) {
  .input-row_values {
    order: 2;
    width: 100%;
    margin-top: 5px;
    margin-right: 0;
  }
}
</style>
