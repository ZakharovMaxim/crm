<template>
  <div>
    <b-button @click="setTnnOpen">
      Указать ТТН
    </b-button>
    <set-ttn-popup
      :is-active="isSetTnnOpened"
      :default-value="defaultValue"
      @submit="setTnn"
      @close="setTnnClose"
    />
  </div>
</template>

<script>
import SetTtnPopup from './setTtnPopup'

export default {
  components: {
    SetTtnPopup
  },
  props: {
    defaultValue: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      value: this.defaultValue,
      isSetTnnOpened: false
    }
  },
  methods: {
    setTnnOpen () {
      this.isSetTnnOpened = true
    },
    setTnnClose () {
      this.isSetTnnOpened = false
    },
    setTnn (ttn) {
      this.$emit('submit', ttn)
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
