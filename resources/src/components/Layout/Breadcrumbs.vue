<template>
  <div class="breadcrumbs">
    <ul>
      <li
        v-for="(link, index) in breadcrumbs"
        :key="'link_to_' + link.id"
      >
        <router-link
          v-if="index !== breadcrumbs.length - 1"
          :to="link.link"
        >
          {{ link.name }}
        </router-link>
        <span v-else>{{ link.name }}</span>
      </li>
    </ul>
  </div>
</template>

<script>
/**
 * Component for breadcrumbs in hierarchical lists
 */
export default {
  props: {
    breadcrumbs: {
      type: Array,
      default: () => []
    }
  }
}
</script>

<style scoped lang="scss">
.breadcrumbs {
  margin-bottom: 10px;
  ul {
    display: flex;
    li {
      &:not(:last-child):after {
        content: '/';
        margin: 0 5px;
      }
    }
  }
}
</style>
