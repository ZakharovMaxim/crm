<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Импорт каталога
    </template>
    <template slot="content">
      <input
        ref="input"
        type="file"
        @change="loadFile"
      >
      <div
        v-if="error"
        class="error"
      >
        {{ error }}
      </div>
    </template>
    <template slot="footer">
      <b-button
        @click="submit"
      >
        Импортировать
      </b-button>
    </template>
  </Popup>
</template>

<script>

export default {
  props: {
    isActive: Boolean
  },
  data () {
    return {
      file: null,
      error: null
    }
  },
  methods: {
    loadFile (e) {
      const file = e.target.files[0]
      if (!file.name.endsWith('.csv')) {
        e.target.value = ''
        this.error = 'Выберите файл формата csv'
        return
      }
      this.error = null
      this.file = file
    },
    submit () {
      if (!this.file) {
        this.error = 'Выберите файл для импорта'
        return
      }
      this.$emit('submit', this.file)
      this.close()
    },
    close () {
      this.error = null
      this.$refs.input.value = ''
      this.file = null
      this.$emit('close')
    }
  }
}
</script>

<style lang="scss" scoped>
.error {
  font-weight: bold;
  color: #ff4e4e;
  font-size: 12px;
  text-align: center;
}
</style>
