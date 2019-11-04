<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Обновить атрибут
    </template>
    <template slot="content">
      <b-field
        label="Название атрибута"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название атрибута"
          :value="form.name"
          @input="e => handleInput(e, 'name')"
        />
      </b-field>
      <table class="attributes-table">
        <tr
          v-for="value in attribute.values"
          :key="'value_' + value.id"
        >
          <td>
            <b-input
              placeholder="Значение атрибута"
              :value="value.value"
              @input="v => handleValueInput(v, value.id)"
            />
          </td>
          <td>
            <b-button
              class="button is-primary"
              :loading="isLoading"
              @click="saveAttributeValue(value.id)"
            >
              <b-icon
                icon="content-save"
              />
            </b-button>
            <b-button
              class="button is-danger"
              :loading="isLoading"
              @click="removeAttributeValue(value.id)"
            >
              <b-icon
                icon="minus-circle"
              />
            </b-button>
          </td>
        </tr>
        <tr>
          <td>
            <b-input
              v-model="newValue"
              @keydown.native.enter="addAttributeValue"
            />
          </td>
          <td>
            <b-button
              class="button is-primary"
              :loading="isLoading"
              @click="addAttributeValue"
            >
              <b-icon
                icon="plus-circle"
              />
            </b-button>
          </td>
        </tr>
      </table>
    </template>
    <template slot="footer">
      <b-button
        :loading="isLoading"
        @click="submit"
      >
        Обновить
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { ATTRIBUTE_MODULE } from '@/store/modules/TYPES'
const { UPDATE_ATTRIBUTE, DESTROY_ATTRIBUTE_VALUE, UPDATE_ATTRIBUTE_VALUE, CREATE_ATTRIBUTE_VALUE } = ATTRIBUTE_MODULE

export default {
  props: {
    isActive: Boolean,
    attribute: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      form: validations(),
      isLoading: false,
      values: {},
      newValue: ''
    }
  },
  created () {
    this.form.name = this.attribute.name
  },
  methods: {
    ...mapActions([UPDATE_ATTRIBUTE, DESTROY_ATTRIBUTE_VALUE, UPDATE_ATTRIBUTE_VALUE, CREATE_ATTRIBUTE_VALUE]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    handleValueInput (value, id) {
      this.values[id] = value
    },
    removeAttributeValue (id) {
      this.isLoading = true
      this[DESTROY_ATTRIBUTE_VALUE]({
        attribute_id: this.attribute.id,
        id
      }).then(() => {
        this.isLoading = false
        this.$buefy.toast.open('Значение удалено')
      })
    },
    saveAttributeValue (id) {
      const value = this.values[id]
      if (!value) return
      this[UPDATE_ATTRIBUTE_VALUE]({
        id,
        data: {
          value,
          attribute_id: this.attribute.id
        }
      }).then(() => {
        this.$buefy.toast.open('Значение обновлено')
      })
    },
    addAttributeValue () {
      if (!this.newValue) return
      this.isLoading = true
      this[CREATE_ATTRIBUTE_VALUE]({
        attribute_id: this.attribute.id,
        value: this.newValue
      }).then(() => {
        this.$buefy.toast.open('Значение создано')
      }).catch(e => {
        console.log(e)
        this.$buefy.toast.open('Значение не создано')
      }).finally(() => {
        this.newValue = ''
        this.isLoading = false
      })
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[UPDATE_ATTRIBUTE]({
        id: this.attribute.id,
        data: this.form.data()
      }).then(response => {
        this.form.name = ''
        this.$buefy.toast.open('Атрибут обновлен')
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Атрибут не обновлен :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .attribute-value {
    display: flex;
  }
</style>
