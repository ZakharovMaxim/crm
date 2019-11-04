<template>
  <div v-title="'Minimal CRM'">
    <div class="title">
      Minimal CRM
    </div>
    <modules-list />
  </div>
</template>

<script>
import ModulesList from '@/components/ModulesList'

export default {
  components: { ModulesList }
}
</script>

<style scoped lang="scss">
  .title {
    text-align: center;
    font-size: 35px;
    padding: 25px 0;
    font-weight: bold;
    /* text-transform: uppercase; */
  }
</style>
