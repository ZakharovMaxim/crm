<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Изображение пользователя
    </template>
    <template slot="content">
      <b-button
        type="is-primary"
        @click="submit"
      >
        Новое изображение
      </b-button>
      <b-button
        type="is-danger"
        @click="remove"
      >
        Удалить изображение
      </b-button>
      <b-button
        @click="close"
      >
        Отмена
      </b-button>
    </template>
  </Popup>
</template>

<script>
export default {
  props: {
    isActive: Boolean
  },
  methods: {
    submit () {
      this.$emit('submit')
      this.close()
    },
    remove () {
      this.$buefy.dialog.confirm({
        title: 'Удаление изображения',
        message: 'Вы действительно хотите удалить изображение, это действие нельзя отменить?',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => {
          this.$emit('remove')
          this.close()
        }
      })
    },
    close () {
      this.$emit('close')
    }
  }
}
</script>
