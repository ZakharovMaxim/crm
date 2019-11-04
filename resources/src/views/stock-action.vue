<template>
  <GetData
    v-title="title"
    :callback="fetchData"
    :update="update"
  >
    <b-loading :active="isLoading" />
    <div class="subtitle">
      <span>{{ label }} #{{ action.id }}</span>
      <span
        v-if="!action.is_submited"
        class="is-action"
        @click="confirmDelete"
      >
        <b-icon
          icon="delete"
        />
      </span>
    </div>
    <div>
      <b-field
        label="В склад"
      >
        <b-select
          v-model="toStock"
          :disabled="!!action.is_submited"
        >
          <option
            v-for="stock in stocks"
            :key="'stock_' + stock.id"
            :value="stock.id"
          >
            {{ stock.name }}
          </option>
        </b-select>
      </b-field>
      <b-field
        v-if="action.type === '3'"
        label="Со склада"
      >
        <b-select
          v-model="fromStock"
          :disabled="!!action.is_submited"
        >
          <option
            v-for="stock in stocks"
            :key="'stock_' + stock.id"
            :value="stock.id"
          >
            {{ stock.name }}
          </option>
        </b-select>
      </b-field>
      <b-field label="Примечание">
        <b-input
          v-model="notice"
          type="textarea"
        />
      </b-field>
      <div v-if="isNotSaved">
        <b-button
          type="is-primary"
          @click="submit"
        >
          Сохранить
        </b-button>
      </div>
    </div>
    <div class="subtitle">
      Товары
    </div>
    <div>
      <b-button
        v-if="!action.is_submited"
        class="button is-primary action-button"
        @click="isProductSelectOpened = true"
      >
        <b-icon
          icon="plus"
          size="is-large"
          title="Добавить товар"
          class="is-action"
        />
      </b-button>
      <div v-if="products.length">
        <div
          v-for="(row, index) in products"
          :key="'product_' + row.product.id"
          class="is-flex action-row is-gapless-nano"
        >
          <div
            class="column"
            :class="{'is-11': !action.is_submited, 'is-12': action.is_submited}"
          >
            <!-- <router-link
              :to="'/catalogs/product/' + row.product.id"
              class="card editable-card"
            > -->
            <ProductCard
              :product="row.product"
              is-presentation
              :closeable="!action.is_submited"
              @close="removeProduct(index)"
            >
              <div class="is-flex is-multiline">
                <div class="action-row-count">
                  <div>
                    Количество
                  </div>
                  <v-number-input
                    v-model="row.count"
                    :min="0"
                    :max="getMaxProductCount(row.product)"
                    :disabled="!!action.is_submited"
                  />
                </div>
                <div v-if="action.type === '1'">
                  <div>
                    На сумму
                  </div>
                  <div class="action-row-price">
                    <v-number-input
                      v-model="row.purchase_price"
                      :min="0"
                      :disabled="!!action.is_submited"
                    />
                    <span>UAH</span>
                  </div>
                </div>
              </div>
            </ProductCard>
            <!-- </router-link> -->
          </div>
        </div>
        <div
          v-if="error"
          class="error"
        >
          {{ error }}
        </div>
        <div
          v-if="!action.is_submited"
          class="action-tools"
        >
          <b-button
            type="is-primary"
            @click="saveProducts"
          >
            Сохранить товары
          </b-button>
          <b-button
            @click="submitAction"
          >
            Зачислить
          </b-button>
        </div>
      </div>
    </div>
    <FindProductsInStock
      v-if="idToFindProducts"
      :id="idToFindProducts"
      :exclude-ids="allIds"
      :is-active="isProductSelectOpened"
      @close="isProductSelectOpened = false"
      @select="createNewProducts"
    />
  </GetData>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { STOCK_ACTION_MODULE, STOCK_MODULE } from '@/store/modules/TYPES'
import FindProductsInStock from '@/components/popups/FindProductsInStock'
import ProductCard from '@/components/Cards/ProductCard'
const { DESTROY_STOCK_PRODUCT, SUBMIT_STOCK_ACTION, GET_STOCK_ACTION, STOCK_ACTION, UPDATE_STOCK_ACTION, CREATE_STOCK_PRODUCTS, DESTROY_STOCK_ACTION } = STOCK_ACTION_MODULE
const { STOCKS } = STOCK_MODULE

export default {
  components: { FindProductsInStock, ProductCard },
  props: {
    onLoad: {
      type: Function,
      default: () => () => {}
    }
  },
  data () {
    return {
      fromStock: null,
      toStock: null,
      update: false,
      isProductSelectOpened: false,
      notice: '',
      isLoading: false,
      products: [],
      error: null
    }
  },
  computed: {
    ...mapState({
      stocks: state => state.stockModule[STOCKS],
      action: state => state.stockActionModule[STOCK_ACTION]
    }),
    label () {
      return this.action.type === '1' ? 'Зачисление' : this.action.type === '2' ? 'Списание' : 'Перемещение'
    },
    isNotSaved () {
      if (this.action.type === '3') {
        return (this.notice !== this.action.notice && this.toStock !== this.fromStock) || ((this.fromStock !== this.action.from_stock || this.toStock !== this.action.to_stock) && (this.fromStock !== this.toStock))
      }
      return !this.toStock || this.toStock !== this.action.to_stock || this.notice !== this.action.notice
    },
    idToFindProducts () {
      return this.action.type === '3' ? this.action.from_stock : this.action.to_stock
    },
    allIds () {
      return this.products.map(row => row.product.id)
    },
    savedIds () {
      return this.action.products.map(product => product.id)
    },
    title () {
      return (this.action && this.action.id) ? `${this.label} #${this.action.id}` : ''
    }
  },
  watch: {
    '$route': function () {
      this.update = !this.update
    },
    'action': function () {
      if (this.action) {
        this.toStock = this.action.to_stock
        this.fromStock = this.action.from_stock
        this.products = this.formatProducts(this.action.products)
        this.sendStatus()
      }
    },
    'products': function () {
      this.error = null
    }
  },
  methods: {
    ...mapActions([GET_STOCK_ACTION, UPDATE_STOCK_ACTION, CREATE_STOCK_PRODUCTS, SUBMIT_STOCK_ACTION, DESTROY_STOCK_ACTION, DESTROY_STOCK_PRODUCT]),
    getMaxProductCount (product) {
      if (this.action.type === '1') return 10000

      return product.product_stats[0] ? +product.product_stats[0].in_stock || 0 : 0
    },
    sendStatus () {
      if (!this.action || !this.action.type) return

      this.onLoad(`${this.action.type},${this.action.is_submited}`)
    },
    fetchData () {
      return this[GET_STOCK_ACTION](this.$route.params.id).then(response => {
        this.notice = this.action.notice
        return response
      })
    },
    formatProducts (products) {
      if (!products) return {}
      return products.map(product => {
        return {
          count: product.pivot.count,
          product,
          purchase_price: product.pivot.purchase_price
        }
      })
    },
    confirmDelete () {
      this.$buefy.dialog.confirm({
        title: `Удаление ${this.label.toLowerCase()}`,
        message: `Вы действительно хотите удалить ${this.label.toLowerCase()}? Это действие нельзя отменить`,
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.delete()
      })
    },
    delete () {
      this.isLoading = true
      const type = this.action.type
      this[DESTROY_STOCK_ACTION](this.action.id).then(() => {
        // redirect
        const url = type === '1' ? 'enrollment' : type === '2' ? 'write-off' : 'movements'
        this.$router.push(`/stocks/${url}`)
      }).catch(e => {
        this.$buefy.toast.open('Произошла ошибка')
      }).finally(() => {
        this.isLoading = false
      })
    },
    createNewProducts (products) {
      const res = []
      Object.keys(products).forEach((id) => {
        res.push({
          product: products[id],
          count: 0,
          purchase_price: 0
        })
      })
      this.products = [...this.formatProducts(this.action.products), ...res]
    },
    removeProduct (index) {
      const id = this.products[index].product.id
      if (this.savedIds.includes(id)) {
        this.isLoading = true
        this[DESTROY_STOCK_PRODUCT]({
          action_id: this.action.id,
          id,
          count: this.products[index].count
        }).catch(e => {
          console.log(e)
        }).finally(() => {
          this.isLoading = false
        })
      } else {
        this.products = this.products.filter((p, i) => i !== index)
      }
    },
    saveProducts () {
      const request = this.getRequestData()
      this.isLoading = true
      this[CREATE_STOCK_PRODUCTS](request).catch(e => {
        console.log(e)
      }).finally(() => {
        this.isLoading = false
      })
    },
    validate () {
      let isOk = !this.products.some(row => row.count < 1)
      if (!isOk) {
        this.error = 'Проверьте количество товара в операции'
      }
      return isOk
    },
    submitAction () {
      if (!this.validate()) return
      const request = this.getRequestData()
      this.isLoading = true
      this[SUBMIT_STOCK_ACTION](request).catch(e => {
        console.log(e)
      }).finally(() => {
        this.isLoading = false
      })
    },
    getRequestData () {
      const request = {
        id: this.action.id
      }
      request.data = {
        rows: this.products.map(row => {
          return {
            id: row.product.id,
            count: row.count,
            purchase_price: row.purchase_price
          }
        })
      }
      request.type = +this.action.type
      return request
    },
    submit () {
      if (!this.isNotSaved) return
      this.isLoading = true
      const data = {
        to_stock: this.toStock,
        notice: this.notice
      }
      if (this.action.type === '3') {
        data.from_stock = this.fromStock
      }
      this[UPDATE_STOCK_ACTION]({
        id: this.action.id,
        data
      }).then(() => {
        this.$buefy.toast.open(`${this.label} обновлено`)
      }).catch(e => {
        console.log(e)
        this.$buefy.toast.open(`${this.label} не обновлено`)
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style scoped lang="scss">
.subtitle {
  display: flex;
  justify-content: space-between;
}
.action-row, .price-field {
  align-items: center;
}
.action-row {
  &-count {
    margin-right: 5px;
  }
  &-price {
    display: flex;
    align-items: center;
  }
}
.action-tools {
  display: flex;
  justify-content: space-between;
}
.error {
  color: #ff4e4e;
  text-align: center;
}
</style>
