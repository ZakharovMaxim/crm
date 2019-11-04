<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Установить номер ттн
    </template>
    <template slot="content">
      <b-field
        label="Номер ттн"
        :type="form.getBuefyType('ttn')"
        :message="form.errors.get('ttn')"
      >
        <b-input
          v-focus
          placeholder="Введите номер ттн"
          :value="form.ttn"
          @input="v => handleInput(v, 'ttn')"
        />
      </b-field>
    </template>
    <template slot="footer">
      <b-button
        @click="submit"
      >
        Установить
      </b-button>
    </template>
  </Popup>
</template>

<script>
import Form from '@/helpers/Form'

export default {
  props: {
    isActive: Boolean,
    defaultValue: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      form: new Form({
        ttn: {
          required: true,
          value: this.defaultValue
        }
      })
    }
  },
  methods: {
    submit () {
      if (!this.form.validate()) return
      this.$emit('submit', this.form.ttn)
      this.close()
    },
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    close () {
      this.$emit('close')
      this.form.reset()
    }
  }
}
</script>

<style lang="scss" scoped>
  .attribute {
    display: flex;
    &-create {
      display: flex;
    }
  }
</style>
