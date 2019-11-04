<template>
  <GetData :callback="fetchData">
    <table
      v-title="'Клиенты'"
      class="report-table"
    >
      <tr>
        <th>Код</th>
        <th>Имя клиента</th>
        <th>Номер</th>
        <th>Почта</th>
        <th>Количество заказов</th>
      </tr>
      <tr
        v-for="user in users"
        :key="user.id"
      >
        <td>
          {{ user.id }}
        </td>
        <td>
          <router-link :to="`/clients/${user.id}`">
            {{ user.name }} {{ user.surname }}
          </router-link>
        </td>
        <td>
          {{ user.phone }}
        </td>
        <td>
          {{ user.email }}
        </td>
        <td>
          {{ user.orders_count }}
        </td>
      </tr>
    </table>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { CLIENT_MODULE } from '@/store/modules/TYPES'
const { GET_CLIENTS, CLIENTS } = CLIENT_MODULE

export default {
  computed: {
    ...mapState({
      users: store => store.clientModule[CLIENTS]
    })
  },
  methods: {
    ...mapActions([GET_CLIENTS]),
    fetchData () {
      return this[GET_CLIENTS]()
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
