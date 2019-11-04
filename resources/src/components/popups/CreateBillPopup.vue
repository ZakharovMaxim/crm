<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Создать счет
    </template>
    <template slot="content">
      <b-field
        label="Название счета"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название счета"
          :value="form.name"
          @input="e => handleInput(e, 'name')"
        />
      </b-field>
      <b-field
        label="Дополнительная информация"
      >
        <b-input
          placeholder="Дополнительная информация"
          :value="form.info"
          @input="e => handleInput(e, 'info')"
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
import { BILL_MODULE } from '@/store/modules/TYPES'
const { CREATE_BILL } = BILL_MODULE

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
    ...mapActions([CREATE_BILL]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[CREATE_BILL](this.form.data()).then(response => {
        this.form.reset()
        this.$buefy.toast.open('Счет создан')
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Счет не создан :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
