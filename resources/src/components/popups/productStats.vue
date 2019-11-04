<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Отчет по остаткам({{ name }})
    </template>
    <template slot="content">
      <GetData :callback="fetchData">
        <div v-if="!stats.length">
          Ничего не найдено
        </div>
        <table
          v-else
          class="report-table"
        >
          <tr>
            <th>Склад</th>
            <th>В наличии</th>
            <th>В подготовке</th>
            <th>В резерве</th>
            <th>Упаковано</th>
            <th>В доставке</th>
          </tr>
          <tr
            v-for="(stat, index) in stats"
            :key="'stat_' + index"
          >
            <td class="stat_stock">
              {{ stat.stock.name }}
            </td>
            <td class="stat_in_stock">
              <span v-if="stat.in_stock">
                {{ stat.in_stock }}
              </span>
              <span
                v-else
                class="stat_not_in_stock"
              >
                0
              </span>
            </td>
            <td class="stat_in_prepare">
              {{ stat.in_prepare || '-' }}
            </td>
            <td class="stat_in_reserve">
              {{ stat.in_reserve || '-' }}
            </td>
            <td class="stat_in_package">
              {{ stat.in_package || '-' }}
            </td>
            <td class="stat_in_delivery">
              {{ stat.in_delivery || '-' }}
            </td>
          </tr>
        </table>
      </GetData>
    </template>
  </Popup>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PRODUCT_MODULE } from '@/store/modules/TYPES'
const { GET_PRODUCT_STATS, PRODUCT_STATS } = PRODUCT_MODULE

export default {
  props: {
    isActive: Boolean,
    id: {
      type: Number,
      required: true
    },
    name: {
      type: String,
      required: true
    }
  },
  computed: {
    ...mapState({
      stats: store => store.catalogModule[PRODUCT_STATS]
    })
  },
  methods: {
    ...mapActions([GET_PRODUCT_STATS]),
    fetchData () {
      return this[GET_PRODUCT_STATS](this.id)
    },
    close () {
      this.$emit('close')
    }
  }
}
</script>

<style lang="scss" scoped>
table {
  width: 100%;
  td {
    text-align: center;
  }
}
.stat {
  &_in_stock {
    color: #00b057;
  }
  &_not_in_stock {
    color: #ff4e4e;
  }
  &_in_prepare {
    color: #96a900;
  }
  &_in_reserve {
    color: #aaa;
  }
}
</style>
