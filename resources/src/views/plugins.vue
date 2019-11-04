<template>
  <Layout>
    <template #sidebar-title>
      <span>
        <b-icon
          icon="toolbox"
          size="is-small"
        />
        Плагины
      </span>
    </template>
    <template #sidebar>
      Тут будет меню, наверное
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

<style>

</style>
