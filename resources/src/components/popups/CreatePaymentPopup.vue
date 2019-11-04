<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      <span v-if="payment">Платеж #{{ payment.id }}</span>
      <span v-else>Создать {{ label }} платеж</span>
    </template>
    <template slot="content">
      <GetData :callback="fetchData">
        <div class="popup-content">
          <b-field
            label="Магазин"
            :type="form.getBuefyType('shop_id')"
            :message="form.errors.get('shop_id')"
          >
            <b-select
              :value="form.shop_id"
              @input="e => handleInput(e, 'shop_id')"
            >
              <option
                v-for="shop in shops"
                :key="'shop_' + shop.id"
                :value="shop.id"
              >
                {{ shop.name }}
              </option>
            </b-select>
          </b-field>
          <b-field
            :label="'Категория ' + inputLabel"
            :type="form.getBuefyType('payment_category_id')"
            :message="form.errors.get('payment_category_id')"
          >
            <b-select
              :value="form.payment_category_id"
              @input="e => handleInput(e, 'payment_category_id')"
            >
              <option
                v-for="category in categories"
                :key="'category_' + category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </b-select>
          </b-field>
          <div class="is-flex is-multiline">
            <div class="column is-6">
              <b-field
                label="Счет"
                :type="form.getBuefyType('bill_id')"
                :message="form.errors.get('bill_id')"
              >
                <b-select
                  :value="form.bill_id"
                  @input="e => handleInput(e, 'bill_id')"
                >
                  <option
                    v-for="bill in bills"
                    :key="'bill_' + bill.id"
                    :value="bill.id"
                  >
                    {{ bill.name }}
                  </option>
                </b-select>
              </b-field>
            </div>
            <div class="column is-6 payment-sum-col">
              <b-field
                :type="form.getBuefyType('sum')"
                :message="form.errors.get('sum')"
                label="Введите сумму платежа"
              >
                <b-numberinput
                  v-focus
                  :value="+form.sum"
                  @input="e => handleInput(e, 'sum')"
                />
              </b-field>
            </div>
          </div>
          <b-field label="Фактическая дата платежа">
            <div class="is-flex is-multiline">
              <b-datepicker
                placeholder="Введите дату"
                icon="calendar-today"
                :value="form.date"
                :month-names="monthes"
                :day-names="days"
                :date-formatter="simpleFormat"
                @input="e => handleInput(e, 'date')"
              />
              <b-clockpicker
                :value="form.time"
                hour-format="24"
                @input="e => handleInput(e, 'time')"
              />
            </div>
          </b-field>
          <b-field label="Комментарий к платежу">
            <b-input
              type="textarea"
              :value="form.comment"
              @input="e => handleInput(e, 'comment')"
            />
          </b-field>
        </div>
      </GetData>
    </template>
    <template
      slot="footer"
    >
      <div v-if="!fetching">
        <b-button
          :loading="isLoading"
          @click="submit"
        >
          {{ actionLabel }}
        </b-button>
        <b-button
          v-if="payment"
          type="is-danger"
          @click="deleteConfirm"
        >
          Удалить
        </b-button>
      </div>
    </template>
  </Popup>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import validations from '@/validations/payment'
import { dbFormat, monthes, days, simpleFormat } from '@/helpers/DateFormat'
import { SHOP_MODULE, PAYMENT_MODULE, BILL_MODULE } from '@/store/modules/TYPES'
const { CREATE_PAYMENT, UPDATE_PAYMENT, REMOVE_PAYMENT, CREATE_PAYMENT_INFO } = PAYMENT_MODULE
const { BILLS } = BILL_MODULE
const { SHOPS } = SHOP_MODULE

export default {
  props: {
    isActive: Boolean,
    type: {
      type: [Number],
      default: 1
    },
    payment: {
      type: Object,
      default: null
    },
    orderId: {
      type: Number,
      default: null
    }
  },
  data () {
    return {
      form: validations(),
      isLoading: false,
      fetching: true,
      monthes,
      days
    }
  },
  computed: {
    ...mapState({
      shops: state => state.shopModule[SHOPS],
      bills: state => state.billModule[BILLS]
    }),
    categories () {
      const shop = this.shops.find(shop => shop.id === this.form.shop_id)
      if (!shop) return []
      const categories = shop.categories
      if (!categories) return []

      return categories.filter(cat => +cat.type === +this.type)
    },
    label () {
      return +this.type === 1 ? 'входящий' : 'исходящий'
    },
    inputLabel () {
      return +this.type === 1 ? 'дохода' : 'расхода'
    },
    actionLabel () {
      return this.payment ? 'Обновить' : 'Создать'
    },
    actionResult () {
      return this.payment ? 'обновлен' : 'создан'
    }
  },
  methods: {
    ...mapActions([CREATE_PAYMENT, UPDATE_PAYMENT, REMOVE_PAYMENT, CREATE_PAYMENT_INFO]),
    fetchData () {
      return this[CREATE_PAYMENT_INFO]().then(() => {
        this.onCreated()
        this.fetching = false
      })
    },
    simpleFormat,
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
      if (type === 'shop_id') {
        const firstCategory = this.categories[0]
        if (firstCategory) {
          this.form['payment_category_id'] = firstCategory.id
        } else {
          this.form['payment_category_id'] = null
        }
      }
    },
    deleteConfirm () {
      if (!this.payment) return
      this.$buefy.dialog.confirm({
        title: 'Удаление платеж',
        message: 'Вы действительно хотите удалить платеж? Это действие нельзя отменить',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: this.delete
      })
    },
    onCreated () {
      if (!this.payment) {
        if (this.shops[0]) {
          this.form.shop_id = this.shops[0].id
        }
        if (this.categories[0]) {
          this.form.payment_category_id = this.categories[0].id
        }
        if (this.bills[0]) {
          this.form.bill_id = this.bills[0].id
        }
      } else {
        this.form.shop_id = this.payment.shop_id
        this.form.bill_id = this.payment.bill_id
        this.form.payment_category_id = this.payment.payment_category_id
        this.form.sum = this.payment.sum
        this.form.date = new Date(this.payment.date)
        this.form.time = new Date(this.payment.date)
        this.form.comment = this.payment.comment
      }
    },
    delete () {
      if (!this.payment) return
      this.isLoading = true
      this[REMOVE_PAYMENT](this.payment.id).then(() => {
        this.$buefy.toast.open('Платеж был удален')
        this.$emit('close')
      }).catch(e => {
        this.$buefy.toast.open('Платеж не был удален')
      }).finally(() => {
        this.isLoading = false
      })
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this.form.date = new Date(this.form.date)
      if (this.form.time) this.form.date.setHours(this.form.time.getHours())
      if (this.form.time) this.form.date.setMinutes(this.form.time.getMinutes())
      const action = this.payment ? UPDATE_PAYMENT : CREATE_PAYMENT
      const data = {
        ...this.form.data(),
        date: dbFormat(this.form.date),
        type: this.type
      }
      if (this.orderId) {
        data.order_id = this.orderId
      }
      const request = this.payment ? { id: this.payment.id, data } : data
      this[action](request).then(response => {
        this.form.comment = ''
        this.$buefy.toast.open(`Платеж ${this.actionResult}`)
        this.$emit('close')
      }).catch(e => {
        throw e
        // this.form.setErrors(e.response)
        // this.$buefy.toast.open(`Платеж не ${this.actionResult} :(`)
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.popup-content {
  padding-bottom: 25px;
}
@media screen and (max-width: 550px) {
  .payment-sum-col {
    width: 100%;
    flex: 100%;
  }
}
</style>
