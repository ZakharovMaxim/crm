<template>
  <GetData
    v-title="title"
    :callback="fetchData"
  >
    <div class="client">
      <div class="subtitle">
        Информация о пользователе
      </div>
      <table class="table">
        <tr>
          <td>Имя</td>
          <td>{{ user.name }} {{ user.surname }} {{ user.fathername }}</td>
        </tr>
        <tr>
          <td>Номер телефона</td>
          <td>{{ user.phone }}</td>
        </tr>
        <tr>
          <td>Email</td>
          <td>{{ user.email || 'Не указан' }}</td>
        </tr>
      </table>
      <div
        v-if="user.orders"
        class="client-orders"
      >
        <div class="subtitle">
          Заказы
        </div>
        <div>
          <order-card
            v-for="o in user.orders"
            :key="'order-' + o.id"
            :order="o"
            with-status
          />
        </div>
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { CLIENT_MODULE } from '@/store/modules/TYPES'
import orderCard from '@/components/Cards/orderCard'
const { GET_CLIENT, CLIENT } = CLIENT_MODULE

export default {
  components: { orderCard },
  computed: {
    ...mapState({
      user: store => store.clientModule[CLIENT]
    }),
    title () {
      if (this.user && this.user.name) {
        return `Пользователь: ${this.user.name} ${this.user.surname}`
      }
      return ''
    }
  },
  methods: {
    ...mapActions([GET_CLIENT]),
    fetchData () {
      return this[GET_CLIENT](this.$route.params.id)
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
