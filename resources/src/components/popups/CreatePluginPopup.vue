<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Создать плагин {{ title }}
    </template>
    <template slot="content">
      <b-field
        v-for="(field) in fields"
        :key="field.key"
        :label="field.label"
        :type="form.getBuefyType(field.key)"
        :message="form.errors.get(field.key)"
      >
        <b-input
          :placeholder="field.label"
          :value="form[field.key]"
          @input="e => handleInput(e, field.key)"
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
import validations from '@/validations/store-plugin'
import { PLUGIN_MODULE } from '@/store/modules/TYPES'
const { CREATE_PLUGIN } = PLUGIN_MODULE

export default {
  props: {
    isActive: Boolean,
    title: {
      type: String,
      required: true
    },
    type: {
      type: String,
      required: true
    },
    fields: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      form: validations(this.fields),
      isLoading: false
    }
  },
  methods: {
    ...mapActions([CREATE_PLUGIN]),
    close () {
      this.$emit('close')
      this.form.reset()
    },
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      const settings = []
      const data = this.form.data()
      for (let key in data) {
        settings.push({
          name: key,
          value: data[key]
        })
      }
      this.isLoading = true
      this[CREATE_PLUGIN]({
        settings,
        type: this.type,
        name: this.title
      }).then(() => {
        this.$buefy.toast.open('Плагин создан')
        this.close()
      }).catch(() => {
        this.$buefy.toast.open('Плагин не создан')
      }).finally(() => {
        this.isLoading = false
      })
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
