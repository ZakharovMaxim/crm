<template>
  <GetData
    v-title="title"
    :callback="fetchOrder"
    :update="update"
  >
    <template #default="scopedSlots">
      <b-loading :active="isLoading" />
      <div class="is-flex order-page is-multiline">
        <div
          v-if="order.user_comment"
          class="column is-12 order_comment"
        >
          <span>Коментарий клиента:</span> {{ order.user_comment }}
        </div>
        <div class="column is-6-desktop is-12-tablet is-12-mobile">
          <b-field
            label="Магазин"
          >
            <b-select
              v-model="order.shop_id"
              placeholder="Выберите магазин"
              icon="store"
              :disabled="disabled"
              @input="e => updateField('shop_id', e)"
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
          <div class="subtitle">
            Информация о покупателе
          </div>
          <div class="is-flex gapless is-multiline">
            <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
              <b-input
                v-model="order.customer_name"
                placeholder="Имя"
                icon="account"
                :disabled="disabled"
                @focus="focus('customer_name')"
                @blur="blur('customer_name')"
              />
            </div>
            <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
              <b-input
                v-model="order.customer_surname"
                placeholder="Фамилия"
                icon="account"
                :disabled="disabled"
                @focus="focus('customer_surname')"
                @blur="blur('customer_surname')"
              />
            </div>
            <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
              <b-input
                v-model="order.customer_phone"
                placeholder="Номер телефона"
                icon="phone"
                :disabled="disabled"
                @focus="focus('customer_phone')"
                @blur="blur('customer_phone')"
              />
            </div>
            <div class="form-group column is-6-desktop is-12-tablet is-12-mobile">
              <b-input
                v-model="order.customer_fathername"
                placeholder="Отчество"
                icon="account"
                :disabled="disabled"
                @focus="focus('customer_fathername')"
                @blur="blur('customer_fathername')"
              />
            </div>
          </div>
          <div class="form-group">
            <b-input
              v-model="order.customer_email"
              placeholder="Email"
              icon="email"
              :disabled="disabled"
              @focus="focus('customer_email')"
              @blur="blur('customer_email')"
            />
          </div>
          <div class="subtitle">
            Информация о доставке
          </div>
          <b-field
            label="Доставка"
          >
            <b-select
              v-model="order.delivery_id"
              placeholder="Выберите способ доставки"
              icon="truck"
              :disabled="disabled"
              @input="e => updateField('delivery_id', e)"
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
            label="Плательщик доставки"
          >
            <b-select
              v-model="order.delivery_payer"
              icon="account-group"
              :disabled="disabled"
              @input="e => updateField('delivery_payer', e)"
            >
              <option value="1">
                Отправитель
              </option>
              <option value="2">
                Получатель
              </option>
            </b-select>
          </b-field>
          <div class="form-group">
            <b-input
              v-model="order.delivery_city"
              placeholder="Город доставки"
              icon="city"
              :disabled="disabled"
              @focus="focus('delivery_city')"
              @blur="blur('delivery_city')"
            />
          </div>
          <div class="form-group">
            <b-input
              v-model="order.delivery_address"
              placeholder="Адрес доставки"
              icon="map-marker"
              :disabled="disabled"
              @focus="focus('delivery_address')"
              @blur="blur('delivery_address')"
            />
          </div>
          <div class="form-group">
            <b-input
              v-model="order.check_comment"
              type="textarea"
              placeholder="Примечание к чеку"
              :disabled="disabled"
              @focus="focus('check_comment')"
              @blur="blur('check_comment')"
            />
          </div>
          <div class="subtitle">
            Информация о оплате
          </div>
          <b-field
            label="Оплата"
          >
            <b-select
              v-model="order.payment_type_id"
              placeholder="Выберите способ оплаты"
              icon="credit-card"
              :disabled="disabled"
              @input="e => updateField('payment_type_id', e)"
            >
              <option
                v-for="payment in paymentTypes"
                :key="'popup_paymentType_' + payment.id"
                :value="payment.id"
              >
                {{ payment.name }}
              </option>
            </b-select>
          </b-field>
          <b-field
            label="Счет для оплаты"
          >
            <b-select
              v-model="order.bill_id"
              placeholder="Выберите счет для оплаты"
              icon="currency-usd"
              :disabled="disabled"
              @input="e => updateField('bill_id', e)"
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
          <div class="form-group">
            <b-input
              v-model="order.order_comment"
              type="textarea"
              placeholder="Примечание к заказу"
              :disabled="disabled"
              @focus="focus('order_comment')"
              @blur="blur('order_comment')"
            />
          </div>
        </div>
        <div class="column is-6-desktop is-12-tablet is-12-mobile">
          <b-dropdown
            :disabled="disabled"
            aria-role="list"
          >
            <button
              slot="trigger"
              class="button"
            >
              <b-icon icon="source-fork" />
              <span>{{ channelValue }}</span>
              <b-icon icon="menu-down" />
            </button>
            <div class="channels">
              <div
                v-for="(channel, key) in channels"
                :key="'chanel' + key"
                class="channel"
              >
                <div
                  v-if="key !== 'default'"
                  class="channel-title"
                >
                  {{ key }}
                </div>
                <div class="channel-items">
                  <b-dropdown-item
                    v-for="item in channel"
                    :key="'channel_item' + item.id"
                    :disabled="disabled"
                    aria-role="listitem"
                    @click="updateField('payment_source_id', item.id)"
                  >
                    {{ item.title }}
                  </b-dropdown-item>
                </div>
              </div>
            </div>
          </b-dropdown>
          <div class="form-group">
            <b-input
              v-model="order.payment_source_link"
              icon="link-variant"
              placeholder="Ссылка на источник:"
              :disabled="disabled"
              @focus="focus('payment_source_link')"
              @blur="blur('payment_source_link')"
            />
          </div>
          <div class="form-group">
            <b-input
              v-model="order.roistat_visit_id"
              icon="link-variant"
              placeholder="RoistatVisitID:"
              :disabled="disabled"
              @focus="focus('roistat_visit_id')"
              @blur="blur('roistat_visit_id')"
            />
          </div>
          <div class="subtitle">
            Платежи
          </div>
          <div>
            <create-payment
              v-if="scopedSlots.data.payment_access"
              :order-id="order.id"
            />
            <finance-list
              :payments="payments"
              :disabled="scopedSlots.data.payment_access"
              size="is-small"
            />
          </div>
          <div v-if="order.customer_orders && order.customer_orders.length">
            <div class="subtitle">
              Предыдущие заказы
            </div>
            <div>
              <order-card
                v-for="o in order.customer_orders"
                :key="'order-' + o.id"
                :order="o"
                with-status
              />
            </div>
          </div>
          <novaposhta
            v-if="isNovaposhta"
            :order="storeOrder"
            :order-sum="orderSum"
          />
        </div>
      </div>
      <div>
        <FindProductsInStock
          v-if="idToFindProducts"
          :id="idToFindProducts"
          :exclude-ids="allIds"
          :is-active="isProductSelectOpened"
          @close="isProductSelectOpened = false"
          @select="createNewProducts"
        />
        <b-button
          v-if="order.status && order.status.editable"
          @click="isProductSelectOpened = true"
        >
          <b-icon
            icon="plus"
          />
        </b-button>
        <div v-if="products.length">
          <div
            v-for="(row) in products"
            :key="'product_' + row.product.id"
            class="is-flex action-rowis-gapless-nano"
          >
            <!-- <div
              :to="'/catalogs/product/' + row.product.id"
              class="card editable-card"
            > -->
            <ProductCard
              :product="row.product"
              is-presentation
              :closeable="!disabled"
              @close="removeProductConfirm(row.product.id)"
            >
              <OrderProductCount
                :row="row"
                :disabled="disabled"
                @changed="rowChanged"
              />
            </ProductCard>
          </div>
          <div
            v-if="!disabled"
            class="order-discount is-flex"
          >
            <v-number-input
              v-model="discount"
            />
            <b-button @click="setDiscount">
              Дать скидку
            </b-button>
          </div>
          <div class="order-total">
            <table class="table">
              <tr>
                <td>Общее количество товаров:</td>
                <td>{{ orderLength }}</td>
              </tr>
              <tr>
                <td>Сумма заказа:</td>
                <td>{{ orderSum }} UAH</td>
              </tr>
            </table>
          </div>
        </div>
        <div
          v-if="order.status && order.status.statuses"
          class="order-actions tar"
        >
          <b-message
            v-if="notices.length"
            title="Предупреждения"
            type="is-warning"
            aria-close-label="Закрыть"
          >
            <ul>
              <li
                v-for="(notice, index) in notices"
                :key="`notice${index}`"
              >
                {{ notice }}
              </li>
            </ul>
          </b-message>
          <div
            v-for="status in order.status.statuses"
            :key="'status_' + status.id"
            class="order-action"
          >
            <b-button
              :type="status.type"
              @click="updateStatus(status.id)"
            >
              {{ status.label }}
            </b-button>
          </div>
        </div>
      </div>
    </template>
  </GetData>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { SHOP_MODULE, ORDER_MODULE, BILL_MODULE, PAYMENT_MODULE } from '@/store/modules/TYPES'
import FindProductsInStock from '@/components/popups/FindProductsInStock'
import ProductCard from '@/components/Cards/ProductCard'
import OrderProductCount from '@/components/OrderProductCount'
import CreatePayment from '@/components/CreatePayment'
import FinanceList from '@/components/FinanceList'
import Form from '@/helpers/Form'
import orderCard from '@/components/Cards/orderCard'
import Novaposhta from '@/components/integrations/novaposhta'

const { NP_ENABLED, SET_ORDER_DISCOUNT, UPDATE_ORDER_STATUS, UPDATE_ORDER_PRODUCT, REMOVE_ORDER_PRODUCT, ADD_ORDER_PRODUCTS, UPDATE_ORDER_FIELD, PAYMENT_CHANNELS, GET_ORDER, ORDER, DELIVERIES, PAYMENT_TYPES } = ORDER_MODULE
const { PAYMENTS } = PAYMENT_MODULE
const { BILLS } = BILL_MODULE
const { SHOPS } = SHOP_MODULE
const npIds = [1, 2, 3]

export default {
  components: { Novaposhta, FindProductsInStock, ProductCard, OrderProductCount, CreatePayment, FinanceList, orderCard },
  props: {
    onLoad: {
      type: Function,
      default: () => () => {}
    }
  },
  data () {
    return {
      order: {},
      editInput: '',
      isProductSelectOpened: false,
      products: [],
      isLoading: false,
      discount: 0,
      update: false,
      notices: []
    }
  },
  computed: {
    ...mapState({
      storeOrder: state => state.orderModule[ORDER],
      paymentTypes: state => state.orderModule[PAYMENT_TYPES],
      payments: state => state.paymentModule[PAYMENTS],
      deliveries: state => state.orderModule[DELIVERIES],
      shops: state => state.shopModule[SHOPS],
      bills: state => state.billModule[BILLS],
      channels: state => state.orderModule[PAYMENT_CHANNELS],
      isNpEnabled: state => state.orderModule[NP_ENABLED]
    }),
    isNovaposhta () {
      return npIds.includes(+this.order.delivery_id) && this.isNpEnabled
    },
    idToFindProducts () {
      const shop = this.shops.find(shop => shop.id === this.order.shop_id)
      return shop ? shop.stock_id : 0
    },
    allIds () {
      return this.products.map(p => p.product.id)
    },
    disabled () {
      return this.order.status && !this.order.status.editable
    },
    channelValue () {
      let found = null
      for (let field in this.channels) {
        const channel = this.channels[field].find(channel => (channel.id === this.order.payment_source_id) && this.order.payment_source_id)
        if (channel) {
          found = channel.title
          break
        }
      }

      return found || 'Выберите канал продаж'
    },
    title () {
      if (this.order && this.order.id) {
        return `Заказ #${this.order.id}`
      }
      return ''
    },
    orderSum () {
      return this.products.reduce((acc, row) => acc + row.count * row.price, 0)
    },
    orderLength () {
      return this.products.reduce((acc, row) => acc + row.count, 0)
    }
  },
  watch: {
    'storeOrder': function (v) {
      if (v && v.status) {
        this.onLoad(v.status.id)
        this.order = {
          ...this.order,
          status: v.status
        }
      }
    },
    '$route': function (from, to) {
      if (from.params.id !== to.params.id) this.update = !this.update
    }
  },
  methods: {
    ...mapActions([SET_ORDER_DISCOUNT, GET_ORDER, UPDATE_ORDER_FIELD, ADD_ORDER_PRODUCTS, REMOVE_ORDER_PRODUCT, UPDATE_ORDER_PRODUCT, UPDATE_ORDER_STATUS]),
    setDiscount () {
      if (!Form.isNumber(this.discount) || this.disabled) return
      this[SET_ORDER_DISCOUNT]({
        id: this.order.id,
        discount: this.discount
      })
      this.products = this.products.map(product => {
        return {
          ...product,
          discount: +this.discount,
          price: product.selling_price - (product.selling_price / 100 * this.discount)
        }
      })
    },
    fetchOrder () {
      return this[GET_ORDER](this.$route.params.id).then(response => {
        this.order = this.storeOrder
        this.onLoad(this.order.status.id)
        this.products = this.formatProducts(this.order.products) || []
        return response
      })
    },
    createNewProducts (products) {
      const res = []
      Object.keys(products).forEach((id) => {
        res.push({
          product: products[id],
          count: 1,
          selling_price: products[id].selling_price || 0,
          price: products[id].selling_price || 0,
          purchase_price: products[id].purchase_price || 0,
          discount: 0
        })
      })
      this.products = [...this.products, ...res]
      this[ADD_ORDER_PRODUCTS](this.getRequestData(res))
    },
    getRequestData (products = this.products) {
      const request = {
        id: this.order.id
      }
      request.data = {
        rows: products.map(row => {
          return {
            id: row.product.id,
            count: row.count,
            selling_price: row.selling_price,
            purchase_price: row.purchase_price,
            price: row.price,
            discount: row.discount
          }
        })
      }
      return request
    },
    formatProducts (products) {
      if (!products) return {}
      return products.map(product => {
        return {
          count: product.pivot.count,
          product,
          selling_price: product.pivot.selling_price,
          purchase_price: product.pivot.purchase_price,
          discount: product.pivot.discount,
          price: product.pivot.price
        }
      })
    },
    removeProductConfirm (id) {
      if (this.disabled) return
      this.$buefy.dialog.confirm({
        title: 'Удаление товара из заказа',
        message: 'Вы действительно хотите удалить товар из заказа?',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.removeProduct(id)
      })
    },
    removeProduct (id) {
      if (this.disabled) return
      this[REMOVE_ORDER_PRODUCT]({
        productId: id,
        orderId: this.order.id
      })
      this.order.products = this.order.products.filter(prod => prod.id !== id)
      this.products = this.products.filter((row, i) => row.product.id !== id)
    },
    updateStatus (to) {
      this.isLoading = true
      this[UPDATE_ORDER_STATUS]({
        orderId: this.order.id,
        to
      }).then((response) => {
        this.$buefy.toast.open('Статус изменен')
        console.log(response.data.products)
        if (response.data.notices) this.notices = response.data.notices
        if (!response.data.products) return
        this.products = this.products.map(row => {
          return {
            ...row,
            product: {
              ...row.product,
              product_stats: [{ ...response.data.products[row.product.id] }]
            }
          }
        })
      }).catch((e) => {
        console.dir(e)
        this.$buefy.toast.open(`Статус не изменен: ${e.response.data}`)
      }).finally(() => {
        this.isLoading = false
      })
    },
    blur (field) {
      const oldValue = this.editInput
      this.editInput = ''
      if (this.order[field] === oldValue) return
      this[UPDATE_ORDER_FIELD]({
        id: this.order.id,
        field,
        value: this.order[field]
      })
    },
    focus (field) {
      this.editInput = this.order[field]
    },
    updateField (field, value) {
      if (this.disabled) return
      this.order = {
        ...this.order,
        [field]: value
      }
      this[UPDATE_ORDER_FIELD]({
        id: this.order.id,
        field,
        value
      })
    },
    rowChanged (newRow) {
      if (this.disabled) return
      this[UPDATE_ORDER_PRODUCT]({
        ...newRow,
        orderId: this.order.id
      })
      this.products = this.products.map(row => row.product.id === newRow.productId ? {
        ...row,
        [newRow.prop]: +newRow.value
      } : row)
    }
  }
}
</script>

<style scoped lang="scss">
.order_comment {
  padding: 10px;
  font-size: 0.9rem;
  background: #dcf0fa;
  color: #2483b3;
  border-width: 0px 0px 0px 5px;
  border-style: solid;
  border-color: #2483b3;
  span {
    font-weight: bold;
  }
}
.channels {
  max-height: 200px;
  overflow: auto;
}
.form-group {
  padding: 5px;
}
.order-action {
  margin-bottom: 5px;
}
.order-total {
  display: flex;
  justify-content: flex-end;
}
</style>
