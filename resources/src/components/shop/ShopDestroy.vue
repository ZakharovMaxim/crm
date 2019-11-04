<template>
  <b-button
    :loading="loading"
    :disabled="disabled"
    type="is-danger"
    @click="remove"
  >
    Удалить
  </b-button>
</template>

<script>
export default {
  props: {
    disabled: Boolean,
    loading: Boolean
  },
  methods: {
    remove () {
      this.$buefy.dialog.confirm({
        title: 'Удаление магазина',
        message: 'Вы действительно хотите удалить магазин? Это действие нельзя отменить',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.$emit('submit')
      })
    }
  }
}
</script>
