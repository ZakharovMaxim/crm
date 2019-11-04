<template>
  <div
    v-title="'Отчет об остатках (по товарам и услугам)'"
    class="report"
  >
    <a
      ref="link"
      href="#"
      class="download-link"
      target="_blank"
    />
    <GetData
      :callback="fetch"
      :update="update"
    >
      <div class="report-header">
        <div>
          <router-link to="/">
            <b-icon icon="arrow-left" />
          </router-link>
          <span class="report-title">
            Отчет об остатках <span class="report-title_exp">(по товарам и услугам)</span>
          </span>
        </div>
        <div>
          <b-button @click="toExport = 1">
            <b-icon
              icon="cloud-print"
            />
          </b-button>
        </div>
      </div>
      <b-filter
        :filter="filter"
      />
      <div class="table-outer">
        <table class="report-table">
          <tr>
            <th colspan="6" />
            <th colspan="2">
              В заказах
            </th>
            <th />
            <th colspan="3">
              Отгружено
            </th>
            <th />
            <th colspan="5">
              Продано
            </th>
          </tr>
          <tr>
            <th>Код</th>
            <th />
            <th>Наименование</th>
            <th>Артикул</th>
            <th />
            <th>Единиц</th>
            <th>На сумму</th>
            <th />
            <th>В резерве</th>
            <th>Упакованы</th>
            <th>В доставке</th>
            <th />
            <th>Единиц</th>
            <th>На сумму</th>
            <th>Себест.</th>
            <th>Прибыль</th>
            <th>ROS %</th>
          </tr>
          <tr
            v-for="product in products"
            :key="product.id"
          >
            <td>
              {{ product.id }}
            </td>
            <td>
              <span class="product-avatar">
                <product-image
                  :images="product.images"
                  :alt="product.name"
                  size="is-small"
                />
              </span>
            </td>
            <td>
              {{ product.name }}({{ product.additional_info }})
            </td>
            <td>
              {{ product.sku }}
            </td>
            <td />
            <td>
              {{ product.in_order_count }}
            </td>
            <td>
              {{ product.in_order_sum }}
            </td>
            <td />
            <td>
              {{ product.in_reserve || '-' }}
            </td>
            <td>
              {{ product.in_package || '-' }}
            </td>
            <td>
              {{ product.in_delivery || '-' }}
            </td>
            <td />
            <td>
              {{ product.sold_count }}
            </td>
            <td>{{ product.sold_sum }}</td>
            <td>{{ product.sold_purchase_sum }}</td>
            <td>{{ product.sold_sum - product.sold_purchase_sum }}</td>
            <td>{{ ROS(product) }}</td>
          </tr>
        </table>
      </div>
    </GetData>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { SHOP_MODULE, PRODUCT_MODULE, FOLDER_MODULE, TRADEMARK_MODULE, ORDER_MODULE } from '@/store/modules/TYPES'
import productImage from '@/components/Cards/ProductImage'
import bFilter from '@/components/base/filter'
import Filter from '@/helpers/filter'
const { FOLDERS } = FOLDER_MODULE
const { GET_PRODUCTS_REPORT_ORDERS, PRODUCTS } = PRODUCT_MODULE
const { TRADEMARKS } = TRADEMARK_MODULE
const { ORDER_STATUSES } = ORDER_MODULE
const { SHOPS } = SHOP_MODULE

export default {
  components: { productImage, bFilter },
  data () {
    return {
      update: false,
      filter: new Filter(this, [
        {
          label: 'Дата создания',
          items: 'date',
          type: 'date'
        },
        {
          label: 'Магазины',
          items: 'shops'
        },
        {
          label: 'Торговые марки',
          items: 'trademarks'
        },
        {
          label: 'Категории товаров',
          items: 'categories'
        },
        {
          label: 'Статусы заказов',
          items: 'statuses'
        }
      ]),
      toExport: null
    }
  },
  computed: {
    ...mapState({
      products: store => store.catalogModule[PRODUCTS],
      categories: store => store.catalogModule[FOLDERS],
      shops: store => store.shopModule[SHOPS],
      statuses: store => store.orderModule[ORDER_STATUSES],
      trademarksList: store => store.trademarkModule[TRADEMARKS]
    }),
    trademarks () {
      return [
        {
          id: 0,
          name: 'Без ТМ'
        },
        ...this.trademarksList
      ]
    }
  },
  watch: {
    '$route': function () {
      this.update = !this.update
    },
    'toExport': function (v) {
      if (v) this.update = !this.update
    }
  },
  methods: {
    ...mapActions([GET_PRODUCTS_REPORT_ORDERS]),
    ROS (product) {
      const res = ((product.sold_sum - product.sold_purchase_sum) / (product.sold_sum) * 100)
      return res ? res.toFixed(2) : 0
    },
    fetch () {
      return this[GET_PRODUCTS_REPORT_ORDERS]({
        limit: 0,
        report: 2,
        parent_id: 'all',
        export: this.toExport,
        ...this.filter.get()
      }).then((response) => {
        if (this.toExport) {
          this.$refs.link.href = `${location.origin}/${response.data}`
          this.$refs.link.click()
        } else {
          this.filter.update()
        }
      }).finally(() => {
        this.toExport = null
      })
    }
  }
}
</script>

<style scoped lang="scss">
.product-avatar {
  display: inline-block;
  width: 30px;
  height: 30px;
  overflow: hidden;
}
</style>
