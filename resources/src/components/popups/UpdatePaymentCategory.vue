<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Обновить категорию {{ label }}
    </template>
    <template slot="content">
      <b-field
        label="Название категории"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название категории"
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
        Обновить
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { PAYMENT_CATEGORY_MODULE } from '@/store/modules/TYPES'
const { UPDATE_PAYMENT_CATEGORY } = PAYMENT_CATEGORY_MODULE

export default {
  props: {
    isActive: Boolean,
    removeable: Boolean,
    category: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      form: validations(),
      isLoading: false
    }
  },
  computed: {
    label () {
      return +this.category.type === 1 ? 'доходов' : 'расходов'
    }
  },
  created () {
    this.form.name = this.category.name
  },
  methods: {
    ...mapActions([UPDATE_PAYMENT_CATEGORY]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[UPDATE_PAYMENT_CATEGORY]({
        id: this.category.id,
        data: {
          ...this.form.data(),
          type: this.category.type
        }
      }).then(response => {
        this.$buefy.toast.open('Категория обновлена')
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Категория не обновлена :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
