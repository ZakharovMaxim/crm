<template>
  <div v-if="isLoading">
    <b-loading
      :is-full-page="false"
      :active.sync="isLoading"
    />
  </div>
  <div v-else-if="error">
    <div>
      {{ errorStatus }}
    </div>
    <div>
      {{ errorMessage }}
    </div>
  </div>
  <div
    v-else
    class="h-100"
  >
    <slot
      :data="data"
    />
  </div>
</template>

<script>
export default {
  props: {
    callback: {
      required: true,
      type: Function
    },
    update: Boolean
  },
  data () {
    return {
      isLoading: true,
      error: null,
      data: null
    }
  },
  computed: {
    errorStatus () {
      return this.error && this.error.response ? this.error.response.status : ''
    },
    errorMessage () {
      return this.error && this.error.response ? this.error.response.statusText : ''
    }
  },
  watch: {
    'update': {
      handler: 'trigger',
      immediate: true
    }
  },
  methods: {
    trigger () {
      this.isLoading = true
      this.callback().then(response => {
        this.error = null
        if (!response) return {}
        this.data = response.data
      }).catch(e => {
        console.dir(e)
        this.error = e
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style>

</style>
