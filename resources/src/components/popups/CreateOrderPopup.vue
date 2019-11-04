<template>
  <span>
    <b-button @click="open">
      <b-icon icon="plus" />
    </b-button>
    <Popup
      v-if="isActive"
      :is-active="isActive"
      @close="close"
    >
      <template slot="header">
        Создать заказ
      </template>
      <template slot="content">
        <GetData :callback="fetchData">
          <b-field
            label="Магазин"
            :type="form.getBuefyType('shop_id')"
            :message="form.errors.get('shop_id')"
          >
            <b-select
              v-focus
              placeholder="Выберите магазин"
              @input="e => handleInput(e, 'shop_id')"
            >
              <option
                v-for="shop in shops"
                :key="'popup_shop_' + shop.id"
                :value="shop.id"
              >
                {{ shop.name }}
              </option>
            </b-select>
          </b-field>
          <div class="subtitle">Информация о покупателе</div>
          <div class="form-group">
            <b-input
              placeholder="Имя"
              @input="e => handleInput(e, 'customer_name')"
            />
          </div>
          <div class="form-group">
            <b-input
              placeholder="Фамилия"
              @input="e => handleInput(e, 'customer_surname')"
            />
          </div>
          <div class="form-group">
            <b-input
              placeholder="Номер телефона"
              :value="form.customer_phone"
              @input="e => handleInput(e, 'customer_phone')"
            />
          </div>
          <div class="subtitle">Информация о доставке и оплате</div>
          <b-field
            label="Доставка"
            :type="form.getBuefyType('delivery_id')"
            :message="form.errors.get('delivery_id')"
          >
            <b-select
              placeholder="Выберите способ доставки"
              @input="e => handleInput(e, 'delivery_id')"
            >
              <option
                v-for="delivery in deliveries"
                :key="'popup_delivery_' + delivery.id"
                :value="delivery.id"
              >
                {{ delivery.name }}
              </option>
            </b-select>
          </b-field>
          <b-field
            label="Оплата"
            :type="form.getBuefyType('payment_type_id')"
            :message="form.errors.get('payment_type_id')"
          >
            <b-select
              placeholder="Выберите способ оплаты"
              @input="e => handleInput(e, 'payment_type_id')"
            >
              <option
                v-for="payment in payments"
                :key="'popup_payment_' + payment.id"
                :value="payment.id"
              >
                {{ payment.name }}
              </option>
            </b-select>
          </b-field>
          <b-button
            :loading="isLoading"
            @click="submit"
          >
            Создать
          </b-button>
        </GetData>
      </template>
    </Popup>
  </span>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import validations from '@/validations/order'
import { ORDER_MODULE } from '@/store/modules/TYPES'
const { CREATE_ORDER, CREATE_ORDER_INFO, DELIVERIES, PAYMENT_TYPES } = ORDER_MODULE

export default {
  props: {
    shops: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      form: validations(),
      isLoading: false,
      isActive: false
    }
  },
  computed: {
    ...mapState({
      deliveries: state => state.orderModule[DELIVERIES],
      payments: state => state.orderModule[PAYMENT_TYPES]
    })
  },
  methods: {
    ...mapActions([CREATE_ORDER, CREATE_ORDER_INFO]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    close () {
      this.isActive = false
      this.form.reset()
    },
    open () {
      this.isActive = true
    },
    fetchData () {
      return this[CREATE_ORDER_INFO]()
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[CREATE_ORDER](this.form.data()).then(response => {
        this.form.shop_id = ''
        this.$buefy.toast.open('Заказ создан')
        this.close()
        this.$router.push(`/orders/${response.data.id}`)
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Заказ не создан :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
