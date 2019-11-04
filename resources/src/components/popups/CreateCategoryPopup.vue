<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Создать каталог
    </template>
    <template slot="content">
      <b-field
        label="Название каталога"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название каталога"
          :value="form.name"
          @input="e => handleInput(e, 'name')"
        />
      </b-field>
    </template>
    <template slot="footer">
      <b-button
        :loading="isLoading"
        @click="submit"
      >
        Создать
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { FOLDER_MODULE } from '@/store/modules/TYPES'
const { CREATE_FOLDER } = FOLDER_MODULE

export default {
  props: {
    isActive: Boolean
  },
  data () {
    return {
      form: validations(),
      isLoading: false
    }
  },
  methods: {
    ...mapActions([CREATE_FOLDER]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[CREATE_FOLDER](this.form.data()).then(response => {
        this.form.name = ''
        this.$buefy.toast.open('Каталог создан')
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Каталог не создан :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
