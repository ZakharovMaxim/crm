<template>
  <b-switch
    :value="isActive"
    @input="setState"
  >
    {{ label }}
  </b-switch>
</template>

<script>
import { mapActions } from 'vuex'
import { PLUGIN_MODULE } from '@/store/modules/TYPES'
const { SET_PLUGIN_STATE } = PLUGIN_MODULE

export default {
  props: {
    id: {
      type: [String, Number],
      required: true
    },
    isActive: {
      type: Boolean
    }
  },
  computed: {
    label () {
      return this.isActive ? 'Включен' : 'Выключен'
    }
  },
  methods: {
    ...mapActions([SET_PLUGIN_STATE]),
    setState () {
      this[SET_PLUGIN_STATE]({
        id: this.id,
        data: {
          enabled: !this.isActive
        }
      })
    }
  }
}
</script>
