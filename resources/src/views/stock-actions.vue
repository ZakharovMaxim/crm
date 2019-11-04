<template>
  <GetData
    v-title="title"
    :callback="fetchData"
    :update="queryChanged"
  >
    <div>
      <div v-if="!stocks.length">
        Вы еще не создали склад, создайте магазин и склад будет создан автоматически
      </div>
      <div
        v-else
        class="columns is-multiline stocks-list"
      >
        <b-dropdown aria-role="list">
          <button
            slot="trigger"
            class="button is-primary action-button"
          >
            <b-icon
              icon="plus"
              size="is-large"
              title="Создать складскую запись"
              class="is-action"
            />
          </button>
          <b-dropdown-item
            aria-role="listitem"
            @click="isCreateActionActive = true"
          >
            Создать зачисление
          </b-dropdown-item>
          <b-dropdown-item
            aria-role="listitem"
            @click="isWriteOffActive = true"
          >
            Создать списание
          </b-dropdown-item>
          <b-dropdown-item
            aria-role="listitem"
            @click="isMovementActive = true"
          >
            Создать перемещение
          </b-dropdown-item>
        </b-dropdown>
        <create-stock-action
          :is-active="isCreateActionActive"
          :stocks="stocks"
          type="1"
          @close="isCreateActionActive = false"
        />
        <create-stock-action
          :is-active="isWriteOffActive"
          label="Списание"
          type="2"
          :stocks="stocks"
          @close="isWriteOffActive = false"
        />
        <create-stock-action
          :is-active="isMovementActive"
          label="Перемещение"
          type="3"
          :stocks="stocks"
          @close="isMovementActive = false"
        />
      </div>
      <div class="column is-12 stocks-list-title">
        Фильтр по складам
      </div>
      <b-dropdown
        v-for="stock in stocks"
        :key="'stock_' + stock.id"
        hoverable
        aria-role="list"
        position="is-top-right"
      >
        <b-checkbox-button
          slot="trigger"
          v-model="activeStocks"
          :native-value="stock.id"
        >
          <b-icon icon="package-variant" />
          <span>{{ stock.name }}</span>
        </b-checkbox-button>
        <b-dropdown-item
          aria-role="listitem"
          @click="() => editableStock = stock"
        >
          <span>Изменить </span>
          <b-icon
            icon="pencil"
          />
        </b-dropdown-item>
      </b-dropdown>
      <UpdateStockPopup
        v-if="!!editableStock"
        :id="editableStock.id"
        :is-active="!!editableStock"
        :name="editableStock.name"
        @close="editableStock = null"
      />
    </div>
    <div>
      {{ actions.length }} {{ label }}
    </div>
    <router-link
      v-for="action in actions"
      :key="'action_' + action.id"
      :to="`/stocks/stock-action/${action.id}`"
    >
      <stock-action
        :action="action"
      />
    </router-link>
    <div>
      <b-button
        v-if="!isOver"
        @click="loadMore"
      >
        Загрузить еще
      </b-button>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import UpdateStockPopup from '@/components/Popups/UpdateStockPopup'
import CreateStockAction from '@/components/Popups/CreateStockAction'
import StockAction from '@/components/Cards/StockActionCard'
import { STOCK_ACTION_MODULE } from '@/store/modules/TYPES'
const { GET_STOCK_ACTIONS, STOCK_ACTIONS } = STOCK_ACTION_MODULE
const PER_PAGE = 20

export default {
  components: { UpdateStockPopup, CreateStockAction, StockAction },
  props: {
    stocks: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      queryChanged: false,
      editableStock: null,
      isLoading: false,
      isCreateActionActive: false,
      activeStocks: [],
      isWriteOffActive: false,
      isMovementActive: false,
      isOver: false,
      page: 1
    }
  },
  computed: {
    ...mapState({
      actions: state => state.stockActionModule[STOCK_ACTIONS]
    }),
    label () {
      return this.$route.meta.type === '1' ? 'зачислений' : this.$route.meta.type === '2' ? 'списаний' : 'перемещений'
    },
    title () {
      let res = 'Склад: '
      res += this.$route.meta.isSubmited === undefined ? 'Все' : this.$route.meta.isSubmited ? 'Зачисленные' : 'Подготовленные'
      res += this.$route.meta.type === '1' ? ' зачисления' : this.$route.meta.type === '2' ? ' списания' : ' перемещения'
      return res
    }
  },
  watch: {
    '$route': function () {
      this.page = 1
      this.queryChanged = !this.queryChanged
      this.setStocksUrl()
    },
    'activeStocks': function () {
      if (this.activeStocks.length) {
        this.$router.push({
          query: {
            stocks: this.activeStocks.join(',')
          }
        })
      } else {
        this.$router.push({ query: {} })
      }
    }
  },
  created () {
    this.setStocksUrl()
  },
  methods: {
    ...mapActions([GET_STOCK_ACTIONS]),
    fetchData () {
      return this[GET_STOCK_ACTIONS]({
        type: this.$route.meta.type,
        is_submited: this.$route.meta.isSubmited === undefined ? undefined : this.$route.meta.isSubmited ? 1 : 0,
        per_page: PER_PAGE,
        stocks: this.$route.query.stocks,
        page: this.page || 1
      }).then(response => {
        this.isOver = response.data.isOver
        return response
      })
    },
    setStocksUrl () {
      this.activeStocks = (this.$route.query && this.$route.query.stocks) ? this.$route.query.stocks.split(',').map(num => +num) : []
    },
    isStockActive (id) {
      return this.activeStocks.includes(id)
    },
    loadMore () {
      this.page++
      console.log(this.page)
      this.queryChanged = !this.queryChanged
    },
    toggleStockStatus (id) {
      if (this.isStockActive(id)) {
        this.activeStocks = this.activeStocks.filter(stockId => stockId !== id)
      } else {
        this.activeStocks = [...this.activeStocks, id]
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .stocks-list {
      margin: 25px 0;
      &-title {
        margin-bottom: 5px;
        font-size: 20px;
        text-align: center;
      }
    }
    .stock {
      position: relative;
      &-tools {
        position: absolute;
        top: 50%;
        right: 2px;
        transform: translateY(-50%);
        opacity: 0;
        transition: opacity 200ms;
      }
      &:hover {
        .stock-tools {
          opacity: 1;
        }
      }
      &-content {
        padding: 5px;
        border: 1px solid #008fe2;
        transition: all 200ms;
        cursor: pointer;
        &:hover, &.stock-active {
          background: #008fe2;
          color: #fff;
        }
      }
    }
</style>
