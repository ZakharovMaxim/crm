<template>
  <GetData :callback="fetch">
    <Layout>
      <template #sidebar-title>
        Пользователи
      </template>
      <template #sidebar>
        Тут будет меню, наверно
      </template>
      <template #content>
        <router-view />
      </template>
    </Layout>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
import Layout from '@/components/Layout/Layout'
const { GET_ROLES_INFO, USER } = USER_MODULE

export default {
  components: { Layout },
  computed: {
    ...mapState({
      user: store => store.userModule[USER]
    })
  },
  methods: {
    ...mapActions([GET_ROLES_INFO]),
    fetch () {
      if (!this.user.is_admin) return Promise.resolve()
      return this[GET_ROLES_INFO]()
    }
  }
}
</script>
