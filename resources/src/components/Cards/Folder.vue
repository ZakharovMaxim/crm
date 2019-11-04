<template>
  <div
    class="card editable-card folder-card"
    @click="$emit('click')"
  >
    <div class="card-content">
      <div
        class="card-tools"
        @click.stop
      >
        <b-tooltip
          :label="`Изменить ${label}`"
          position="is-top"
        >
          <div
            class="card-tool"
            @click="edit"
          >
            <b-icon
              icon="pencil"
            />
          </div>
        </b-tooltip>
        <b-tooltip
          v-if="removable"
          :label="`Удалить ${label}`"
          position="is-top"
        >
          <div
            class="card-tool card-tool--danger"
            @click="confirmDelete"
          >
            <b-icon
              icon="delete"
            />
          </div>
        </b-tooltip>
      </div>
      <b-icon
        :icon="icon"
        size="is-large"
        class="card-icon"
      />
      <div>
        {{ name }}
      </div>
    </div>
  </div>
</template>

<script>
/**
 * Component for folder presentation in list
 * @param {string} name folder name
 * @param {number} id folder id
 */
export default {
  props: {
    name: {
      type: String,
      required: true
    },
    parentId: {
      type: [Number, String],
      default: null
    },
    id: {
      type: Number,
      required: true
    },
    removeTitle: {
      type: String,
      default: 'Удаление каталога'
    },
    removeMessage: {
      type: String,
      default: 'Вы действительно хотите удалить каталог, все товары этого каталога будут удалены.'
    },
    label: {
      type: String,
      default: 'Каталог'
    },
    icon: {
      type: String,
      default: 'folder'
    },
    removable: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    /**
     * Create confirm dialog for folder remove
     */
    confirmDelete () {
      this.$buefy.dialog.confirm({
        title: this.removeTitle,
        message: this.removeMessage,
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: this.delete
      })
    },
    /**
     * Emit delete event to parent
     */
    delete () {
      this.$emit('delete', this.id)
    },
    /**
     * Emit update event to parent
     * @returns {object}
     */
    edit () {
      this.$emit('update', {
        id: this.id,
        name: this.name,
        parent_id: this.parentId
      })
    }
  }
}
</script>
