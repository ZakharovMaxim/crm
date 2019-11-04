<template>
  <div class="app-content">
    <vue-progress-bar />
    <router-view />
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
const { SET_USER } = USER_MODULE

export default {
  name: 'App',
  mounted () {
    //  [App.vue specific] When App.vue is finish loading finish the progress bar
    this.$Progress.finish()
  },
  created () {
    const user = localStorage.getItem('user')
    if (user) {
      try {
        this[SET_USER](JSON.parse(user))
      } catch (e) {
        console.log(e)
      }
    }
    //  [App.vue specific] When App.vue is first loaded start the progress bar
    this.$Progress.start()
    //  hook the progress bar to start before we move router-view
    this.$router.beforeEach((to, from, next) => {
      //  does the page we want to go to have a meta.progress object
      if (to.meta.progress !== undefined) {
        let meta = to.meta.progress
        // parse meta tags
        this.$Progress.parseMeta(meta)
      }
      //  start the progress bar
      this.$Progress.start()
      //  continue to next page
      next()
    })
    //  hook the progress bar to finish after we've finished moving router-view
    this.$router.afterEach((to, from) => {
      //  finish the progress bar
      this.$Progress.finish()
    })
  },
  methods: {
    ...mapActions([SET_USER])
  }
}
</script>

<style lang="scss" scoped>
.app-content {
  height: 100%;
}
</style>
