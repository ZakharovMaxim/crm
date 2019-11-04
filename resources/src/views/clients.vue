<template>
  <Layout>
    <template #sidebar-title>
      Клиенты
    </template>
    <template #sidebar>
      Тут будет меню, наверно
    </template>
    <template #content>
      <router-view />
    </template>
  </Layout>
</template>

<script>
import Layout from '@/components/Layout/Layout'

export default {
  components: { Layout }
}
</script>
