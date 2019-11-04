<template>
  <GetData :callback="fetchData">
    <b-loading :active="isLoading" />
    <b-button @click="createUserActive = true">
      <b-icon icon="account-plus" />
    </b-button>
    <createUserPopup
      :is-active="createUserActive"
      @close="createUserActive = false"
    />
    <div>
      <b-switch v-model="showBlockedUsers">
        Показывать заблокированных пользователей
      </b-switch>
    </div>
    <table
      v-title="'Пользователи'"
      class="report-table"
    >
      <tr>
        <th>Имя пользователя</th>
        <th>Логин</th>
        <th />
      </tr>
      <tr
        v-for="user in filtered"
        :key="user.id"
        :class="{'is-danger': user.is_deleted}"
      >
        <td>
          {{ user.name }} {{ user.surname }}
        </td>
        <td>
          {{ user.login }}
        </td>
        <td>
          <b-button>
            <router-link :to="`/users/${user.id}`">
              <b-icon
                icon="account-edit"
                size="is-small"
              />
            </router-link>
          </b-button>
          <user-account-block
            :id="user.id"
            :is-deleted="!user.is_deleted"
          />
        </td>
      </tr>
    </table>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
import createUserPopup from '@/components/popups/userPopup'
import UserAccountBlock from '@/components/UserAccountBlock'
const { GET_USERS, USERS } = USER_MODULE

export default {
  components: { createUserPopup, UserAccountBlock },
  data () {
    return {
      createUserActive: false,
      showBlockedUsers: false,
      isLoading: false
    }
  },
  computed: {
    ...mapState({
      users: store => store.userModule[USERS]
    }),
    filtered () {
      const list = this.showBlockedUsers ? this.users : this.users.filter(user => !user.is_deleted)
      return list.map(user => ({ ...user, login: user.login.replace('[DELETED]', '') }))
    }
  },
  methods: {
    ...mapActions([GET_USERS]),
    fetchData () {
      return this[GET_USERS]()
    }
  }
}
</script>

<style lang="scss" scoped>
.is-danger {
  background: #ffb5b5;
}
</style>
