<template>
  <b-button
    type="is-danger"
    size="is-small"
    :is-loading="isLoading"
    @click="confirmDelete"
  >
    <b-icon
      size="is-small"
      icon="delete"
    />
  </b-button>
</template>

<script>
import { mapActions } from 'vuex'
import { PLUGIN_MODULE } from '@/store/modules/TYPES'
const { DESTROY_PLUGIN } = PLUGIN_MODULE

export default {
  props: {
    id: {
      type: [String, Number],
      required: true
    },
    isRedirect: Boolean
  },
  data () {
    return {
      isLoading: false
    }
  },
  methods: {
    ...mapActions([DESTROY_PLUGIN]),
    confirmDelete () {
      this.$buefy.dialog.confirm({
        title: 'Удалить плагин',
        message: 'Вы действительно хотите удалить плагин?',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: this.delete
      })
    },
    delete () {
      this.isLoading = true
      this[DESTROY_PLUGIN](this.id).then(() => {
        this.$buefy.toast.open('Плагин удален')
        console.log(this.isRedirect)
        if (this.isRedirect) {
          console.log('yeeeah')
          this.$router.replace('/plugins')
        }
      }).catch(() => {
        this.$buefy.toast.open('Плагин не удален')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
