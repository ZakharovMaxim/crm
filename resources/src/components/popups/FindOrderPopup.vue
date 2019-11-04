<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Поиск заказа
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
      <div class="order-list">
        <b-loading
          :is-full-page="false"
          :active="isLoading"
        />
        <div v-if="orders.length">
          <order-card
            v-for="o in orders"
            :key="'order-' + o.id"
            :order="o"
            with-status
            @click="close"
          />
        </div>
        <div v-else-if="!isLoading">
          Ничего не найдено
        </div>
        <div v-if="!isOver && searchString && orders.length">
          <b-button @click="nextPage">
            Еще
          </b-button>
        </div>
      </div>
    </template>
  </Popup>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { ORDER_MODULE } from '@/store/modules/TYPES'
import orderCard from '@/components/Cards/orderCard'
const { CLEAR_ORDERS_SEARCH, GET_ORDERS_SEARCH, ORDERS_SEARCH } = ORDER_MODULE

const searchFields = [
  {
    title: 'номеру заказа',
    field: 'id'
  },
  {
    title: 'по имени клиента',
    field: 'customer_name'
  },
  {
    title: 'по номеру клиента',
    field: 'customer_phone'
  }
]
const orderFields = [
  {
    title: 'номеру заказа (по возрастанию)',
    field: 'id',
    order: 'asc'
  },
  {
    title: 'номеру заказа (по убыванию)',
    field: 'id',
    order: 'desc'
  },
  {
    title: 'дате (по возрастанию)',
    field: 'created_at',
    order: 'asc'
  },
  {
    title: 'дате (по убыванию)',
    field: 'created_at',
    order: 'desc'
  }
]
export default {
  components: { orderCard },
  props: {
    isActive: Boolean
  },
  data () {
    return {
      orderFields,
      searchFields,
      searchField: searchFields[0].field,
      searchString: '',
      isLoading: false,
      page: 1,
      order: 0,
      isOver: false
    }
  },
  computed: {
    ...mapState({
      orders: state => state.orderModule[ORDERS_SEARCH]
    })
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
    ...mapActions([CLEAR_ORDERS_SEARCH, GET_ORDERS_SEARCH]),
    reset () {},
    close () {
      this.selected = {}
      this.searchString = ''
      this[CLEAR_ORDERS_SEARCH]()
      this.$emit('close')
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
      this[GET_ORDERS_SEARCH]({
        order_direction: orderDirection,
        order_by: orderBy,
        name,
        query,
        per_page: perPage,
        page
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
