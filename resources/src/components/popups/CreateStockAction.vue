<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Создать {{ label | lower }}
    </template>
    <template slot="content">
      <b-field
        v-if="type === '3'"
        label="Со склада"
      >
        <b-select
          v-model="fromStock"
          v-focus
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
      <b-field label="В склад">
        <b-select v-model="toStock">
          <option
            v-for="stock in stocks"
            :key="'stock_' + stock.id"
            :value="stock.id"
          >
            {{ stock.name }}
          </option>
        </b-select>
      </b-field>
      <b-field label="Введите примечание">
        <b-input
          v-model="notice"
          type="textarea"
        />
      </b-field>
    </template>
    <template slot="footer">
      <b-button
        :loading="isLoading"
        @click="submit"
      >
        Создать
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import { STOCK_ACTION_MODULE } from '@/store/modules/TYPES'
const { CREATE_STOCK_ACTION } = STOCK_ACTION_MODULE

export default {
  filters: {
    lower (value) {
      return value.toLowerCase()
    }
  },
  props: {
    isActive: Boolean,
    label: {
      type: String,
      default: 'Зачисление'
    },
    stocks: {
      type: Array,
      required: true
    },
    type: {
      validator (v) {
        return ['1', '2', '3'].includes(v)
      },
      required: true
    }
  },
  data () {
    return {
      isLoading: false,
      fromStock: this.stocks[0] ? this.stocks[0].id : null,
      toStock: this.stocks[0] ? this.stocks[0].id : null,
      notice: ''
    }
  },
  methods: {
    ...mapActions([CREATE_STOCK_ACTION]),
    submit () {
      if (!this.toStock || (this.type === '3' && (!this.fromStock || this.fromStock === this.toStock))) return
      this.isLoading = true
      const data = {
        to_stock: this.toStock,
        type: this.type,
        notice: this.notice
      }
      if (this.type === '3') {
        data.from_stock = this.fromStock
      }
      this[CREATE_STOCK_ACTION]({
        data,
        addToState: (this.type === this.$route.meta.type) && !this.$route.meta.isSubmited
      }).then(response => {
        this.$buefy.toast.open(`${this.label} создано`)
        this.$emit('close')
      }).catch(e => {
        console.log(e)
        this.$buefy.toast.open(`${this.label} не создано`)
      }).finally(() => {
        this.isLoading = false
      })
    },
    close () {
      this.$emit('close')
    }
  }
}
</script>

<style lang="scss" scoped>
  .attribute {
    display: flex;
    &-create {
      display: flex;
    }
  }
</style>
