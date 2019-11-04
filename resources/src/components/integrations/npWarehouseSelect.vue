<template>
  <div>
    <b-field
      :type="types[0]"
      :message="errors[0]"
      label="Город"
    >
      <b-autocomplete
        v-model="cityLocal"
        rounded
        :data="filteredCities"
        icon="city"
        field="name"
        open-on-focus
        @select="opt => select('city', opt)"
      >
        <template slot="empty">
          Ничего не найдено
        </template>
      </b-autocomplete>
    </b-field>
    <b-field
      :type="types[1]"
      :message="errors[1]"
      label="Отделение"
    >
      <b-autocomplete
        v-model="warehouseLocal"
        rounded
        :data="filteredWarehouses"
        icon="map-marker"
        open-on-focus
        field="name"
        :loading="isLoading"
        @focus="focus"
        @select="opt => select('warehouse', opt)"
      >
        <template slot="header">
          <b-progress v-if="isLoading" />
        </template>
        <template slot="empty">
          Ничего не найдено
        </template>
      </b-autocomplete>
    </b-field>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { NP_MODULE } from '@/store/modules/TYPES'
const { GET_WAREHOUSES_BY_CODE, WAREHOUSES } = NP_MODULE

export default {
  props: {
    cities: {
      type: Array,
      required: true
    },
    apiKey: {
      type: String,
      default: ''
    },
    types: {
      type: Array,
      default: () => []
    },
    errors: {
      type: Array,
      default: () => []
    },
    warehouse: {
      type: Object,
      default: () => ({})
    },
    city: {
      type: Object,
      default: () => ({})
    }
  },
  data () {
    return {
      warehouses: [],
      warehouseLocal: this.warehouse.name || '',
      cityLocal: this.city.name || '',
      isLoading: true
    }
  },
  computed: {
    ...mapState({
      warehousesStore: store => store.orderModule[WAREHOUSES]
    }),
    filteredCities () {
      return this.cities.filter((option) => {
        return option.name
          .toLowerCase()
          .indexOf(this.cityLocal.toLowerCase()) >= 0
      })
    },
    filteredWarehouses () {
      console.log(this.warehouses)
      return this.warehouses.filter((option) => {
        return option.name
          .toLowerCase()
          .indexOf(this.warehouseLocal.toLowerCase()) >= 0
      })
    }
  },
  watch: {
    'warehousesStore': function () {
      if (this.city && this.city.code && this.warehousesStore[this.city.code]) {
        this.warehouses = this.warehousesStore[this.city.code]
        console.log(this.warehouses)
      }
    },
    'city': function () {
      if (this.city) this.cityLocal = this.city.name
    },
    'warehouse': function () {
      if (this.warehouse) this.warehouseLocal = this.warehouse.name
    }
  },
  methods: {
    ...mapActions([GET_WAREHOUSES_BY_CODE]),
    // blur (type) {
    //   console.log('blur')
    //   if (type === 'city') {
    //     this.cityLocal = this.city.name
    //   } else {
    //     this.warehouseLocal = this.warehouse.name
    //   }
    // },
    focus (type) {
      // if (type === 'city') {
      //   this.cityLocal = this.citySearch
      // } else {
      //   this.warehouseLocal = this.warehouseSearch
      if (this.city && this.city.code) {
        this.isLoading = true
        this[GET_WAREHOUSES_BY_CODE]({
          code: this.city.code,
          data: {
            key: this.apiKey
          }
        }).finally(() => {
          this.isLoading = false
        })
      }
      // }
    },
    select (type, opt) {
      this.$emit(`select-${type}`, opt)
      if (type === 'city') {
        this.$emit(`select-warehouse`, null)
        this.warehouseLocal = ''
        this.warehouses = []
      }
    }
    // input (type, v) {
    //   if (type === 'city') {
    //     this.citySearch = v
    //   } else {
    //     this.warehouseSearch = v
    //   }
    // }
  }
}
</script>
