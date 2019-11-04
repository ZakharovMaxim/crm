<template>
  <b-icon
    v-if="!images.length"
    icon="package-variant"
    :size="size"
    class="card-icon"
  />
  <b-avatar
    v-else
    :src="src"
  />
</template>

<script>
import bAvatar from '@/components/base/avatar'

export default {
  components: {
    bAvatar
  },
  props: {
    images: {
      type: Array,
      default: () => []
    },
    alt: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: 'is-large'
    }
  },
  computed: {
    src () {
      return this.images.slice().sort((a, b) => a.pivot.order - b.pivot.order).slice(0, 1)[0].url
    }
  }
}
</script>

<style>

</style>
