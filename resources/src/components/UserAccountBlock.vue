<template>
  <b-button
    v-if="isDeleted"
    type="is-danger"
    @click="block"
  >
    <b-icon
      icon="account-remove"
      size="is-small"
    />
  </b-button>
  <b-button
    v-else
    @click="restore"
  >
    <b-icon
      icon="account-plus"
      size="is-small"
    />
  </b-button>
</template>

<script>
import { mapActions } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
const { RESTORE_USER, DESTROY_USER } = USER_MODULE

export default {
  props: {
    isDeleted: {
      type: Boolean,
      default: false
    },
    id: {
      type: [Number, String],
      required: true
    }
  },
  methods: {
    ...mapActions([RESTORE_USER, DESTROY_USER]),
    block () {
      this.$buefy.dialog.confirm({
        title: 'Блок пользователя',
        message: 'Вы действительно хотите заблокировать пользователя?',
        confirmText: 'Заблокировать',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => {
          this.isLoading = true
          this[DESTROY_USER](this.id).then(() => {
            this.$buefy.toast.open('Пользователь был заблокирован')
          }).catch(e => {
            this.$buefy.toast.open('Пользователь не был заблокирован')
          }).finally(() => {
            this.isLoading = false
          })
        }
      })
    },
    restore () {
      this.$buefy.dialog.confirm({
        title: 'Разблокировка пользователя',
        message: 'Вы действительно хотите разблокировать пользователя?',
        confirmText: 'Разблокировать',
        cancelText: 'Отменить',
        onConfirm: () => {
          this.isLoading = true
          this[RESTORE_USER](this.id).then(() => {
            this.$buefy.toast.open('Пользователь был разблокирован')
          }).catch(e => {
            this.$buefy.toast.open('Пользователь не был разблокирован')
          }).finally(() => {
            this.isLoading = false
          })
        }
      })
    }
  }
}
</script>
