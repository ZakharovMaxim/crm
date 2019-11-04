<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Выбор товара
      <div class="modal-tools">
        <b-field>
          <b-select
            v-model="searchField"
            @input="onSelectChanged"
          >
            <option
              v-for="(item, i) in searchFields"
              :key="'search_' + i"
              :value="item.field"
            >
              Поиск по: {{ item.title }}
            </option>
          </b-select>
        </b-field>
        <b-field>
          <b-select
            v-model="order"
            @input="onSelectChanged"
          >
            <option
              v-for="(item, i) in orderFields"
              :key="'search_' + i"
              :value="i"
            >
              Сортировать по: {{ item.title }}
            </option>
          </b-select>
        </b-field>
        <b-input
          v-model="searchString"
          v-focus
          placeholder="Введите название товара"
        />
      </div>
    </template>
    <template slot="content">
      <div class="products-list">
        <b-loading
          :is-full-page="false"
          :active="isLoading"
        />
        <div v-if="products.length">
          <ProductCard
            v-for="product in products"
            :key="'product_' + product.id"
            :product="product"
            is-presentation
            :is-active="isProductSelected(product.id)"
            @click="selectProduct"
          />
        </div>
        <div v-else-if="!isLoading">
          Ничего не найдено
        </div>
        <div v-if="!isOver && searchString && products.length">
          <b-button @click="nextPage">
            Еще
          </b-button>
        </div>
      </div>
    </template>
    <template slot="footer">
      <div
        v-if="anyProductSelected"
        class="selected-list"
      >
        <div>
          <h3>Выбранные товары</h3>
        </div>
        <div
          v-for="(item) in selected"
          :key="'selected_' + item.id"
          class="tag is-primary"
        >
          {{ item.name }}
          <button
            class="delete is-small"
            type="button"
            @click="selectProduct(item.id)"
          />
        </div>
        <div>
          <b-button @click="submit">
            Выбрать
          </b-button>
        </div>
      </div>
    </template>
  </Popup>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { STOCK_ACTION_MODULE } from '@/store/modules/TYPES'
import ProductCard from '@/components/Cards/ProductCard'
const { GET_STOCK_PRODUCTS, STOCK_PRODUCTS, CLEAR_STOCK_PRODUCTS } = STOCK_ACTION_MODULE

const searchFields = [
  {
    title: 'имени',
    field: 'name'
  },
  {
    title: 'коду',
    field: 'id'
  },
  {
    title: 'артикулу',
    field: 'sku'
  }
]
const orderFields = [
  {
    title: 'имени (по возрастанию)',
    field: 'name',
    order: 'asc'
  },
  {
    title: 'имени (по убыванию)',
    field: 'name',
    order: 'desc'
  },
  {
    title: 'коду (по возрастанию)',
    field: 'id',
    order: 'asc'
  },
  {
    title: 'коду (по убыванию)',
    field: 'id',
    order: 'desc'
  },
  {
    title: 'артикулу (по возрастанию)',
    field: 'sku',
    order: 'asc'
  },
  {
    title: 'артикулу (по убыванию)',
    field: 'sku',
    order: 'desc'
  }
]
export default {
  components: { ProductCard },
  props: {
    isActive: Boolean,
    id: {
      type: [Number, String],
      required: true
    },
    excludeIds: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      orderFields,
      searchFields,
      order: 0,
      searchField: searchFields[0].field,
      searchString: '',
      isLoading: false,
      page: 1,
      isOver: false,
      selected: {}
    }
  },
  computed: {
    ...mapState({
      products: state => state.stockActionModule[STOCK_PRODUCTS]
    }),
    anyProductSelected () {
      return Object.keys(this.selected).length !== 0
    }
  },
  watch: {
    'searchString': function () {
      clearTimeout(this.timer)
      this.timer = setTimeout(() => {
        this.page = 1
        this.fetchData()
      }, 200)
    }
  },
  methods: {
    ...mapActions([GET_STOCK_PRODUCTS, CLEAR_STOCK_PRODUCTS]),
    reset () {},
    close () {
      this.selected = {}
      this.searchString = ''
      this[CLEAR_STOCK_PRODUCTS]()
      this.$emit('close')
    },
    selectProduct (id) {
      if (this.selected[id]) {
        delete this.selected[id]
        this.selected = {
          ...this.selected
        }
      } else {
        const product = this.products.find(product => product.id === id)
        this.selected = { [id]: product, ...this.selected }
      }
    },
    isProductSelected (id) {
      return !!this.selected[id]
    },
    nextPage () {
      this.page++
      this.fetchData()
    },
    onSelectChanged () {
      this.page = 1
      this.fetchData()
    },
    fetchData () {
      // order_direction order direction
      // order_by order field
      // name field name
      // query field query
      // per_page how much records to return
      // page pagination
      if (!this.searchString) return
      const order = this.orderFields[this.order]
      const orderDirection = order.order
      const orderBy = order.field
      const name = this.searchField
      const query = this.searchString
      const perPage = 20
      const page = this.page
      this.isLoading = true
      this[GET_STOCK_PRODUCTS]({
        id: this.id,
        query: {
          order_direction: orderDirection,
          order_by: orderBy,
          name,
          query,
          per_page: perPage,
          page,
          exclude_ids: this.excludeIds.join(',') || undefined
        }
      }).then(response => {
        this.isOver = response.data.isOver
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.isLoading = false
      })
    },
    submit () {
      this.$emit('select', this.selected)
      this.close()
    }
  }
}
</script>

<style scoped lang="scss">
.modal-tools {
  margin-top: 15px;
}
</style>
