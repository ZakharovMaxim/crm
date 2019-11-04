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
              Остатки
            </th>
            <th />
            <th colspan="2">
              В НАЛИЧИИ
            </th>
            <th />
            <th colspan="2">
              В РЕЗЕРВЕ
            </th>
            <th />
            <th>
              ПОДГОТОВЛЕНО
            </th>
            <th colspan="4" />
          </tr>
          <tr>
            <th>Код</th>
            <th />
            <th>Наименование</th>
            <th>Доп. описание</th>
            <th>Артикул</th>
            <th />
            <th>Единиц</th>
            <th>Себест</th>
            <th />
            <th>Единиц</th>
            <th>Себест.</th>
            <th />
            <th>Единиц</th>
            <th>Себест.</th>
            <th />
            <th>Единиц</th>
            <th />
            <th>Цена продажи</th>
            <th />
            <th>Мин. кол-во</th>
          </tr>
          <template
            v-for="(row, index) in rows"
          >
            <tr
              :key="`row_${index}`"
            >
              <td colspan="20">
                {{ row.name }}
              </td>
            </tr>
            <tr
              v-for="product in row.products"
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
                {{ product.name }}
              </td>
              <td>
                {{ product.additional_info }}
              </td>
              <td>
                {{ product.sku }}
              </td>
              <td />
              <td>
                {{ getProductStatSum(product.product_stats, 'in_reserve') + getProductStatSum(product.product_stats, 'in_stock') }}
              </td>
              <td>
                {{ (getProductStatSum(product.product_stats, 'in_reserve') + getProductStatSum(product.product_stats, 'in_stock')) * product.selling_price }}
              </td>
              <td />
              <td>
                {{ getProductStatSum(product.product_stats, 'in_stock') || '-' }}
              </td>
              <td>
                {{ getProductStatPrice(product, 'in_stock') || '-' }}
              </td>
              <td />
              <td>
                {{ getProductStatSum(product.product_stats, 'in_reserve') || '-' }}
              </td>
              <td>
                {{ getProductStatPrice(product, 'in_reserve') || '-' }}
              </td>
              <td />
              <td>
                {{ getProductStatSum(product.product_stats, 'in_prepare') || '-' }}
              </td>
              <td />
              <td>{{ product.selling_price || 0 }}</td>
              <td />
              <td>{{ product.min_count || '-' }}</td>
            </tr>
          </template>
        </table>
      </div>
    </GetData>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { FOLDER_MODULE, PRODUCT_MODULE, TRADEMARK_MODULE, STOCK_MODULE } from '@/store/modules/TYPES'
import productImage from '@/components/Cards/ProductImage'
import bFilter from '@/components/base/filter'
import Filter from '@/helpers/filter'
const { FOLDERS } = FOLDER_MODULE
const { GET_PRODUCTS_REPORT_STOCK, PRODUCTS } = PRODUCT_MODULE
const { TRADEMARKS } = TRADEMARK_MODULE
const { STOCKS } = STOCK_MODULE

export default {
  components: { productImage, bFilter },
  data () {
    return {
      update: false,
      filter: new Filter(this, [
        {
          label: 'Склады',
          items: 'stocks'
        },
        {
          label: 'Торговые марки',
          items: 'trademarks'
        },
        {
          label: 'Категории товаров',
          items: 'categories'
        }
      ]),
      toExport: null
    }
  },
  computed: {
    ...mapState({
      products: store => store.catalogModule[PRODUCTS],
      categories: store => store.catalogModule[FOLDERS],
      stocks: store => store.stockModule[STOCKS],
      trademarksList: store => store.trademarkModule[TRADEMARKS]
    }),
    trademarks () {
      return [{
        id: 0,
        name: '---'
      }, ...this.trademarksList]
    },
    rows () {
      return [ ...this.categories, { name: 'без категории', id: null } ]
        .map(folder => ({ ...folder, products: this.products.filter(product => product.catalog_id === folder.id) }))
        .filter(folder => folder.products.length)
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
    ...mapActions([GET_PRODUCTS_REPORT_STOCK]),
    getProductStatSum (priceGroups = [], status) {
      return priceGroups.reduce((acc, group) => acc + group[status], 0) || 0
    },
    getProductStatPrice (product, status) {
      return this.getProductStatSum(product.product_stats, status) * product.selling_price
    },
    fetch () {
      return this[GET_PRODUCTS_REPORT_STOCK]({
        limit: 0,
        report: 1,
        parent_id: 'all',
        export: this.toExport,
        ...this.filter.get()
      }).then((response) => {
        if (this.toExport) {
          this.$refs.link.href = `${location.origin}/${response.data}`
          this.$refs.link.click()
        } else this.filter.update()
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
