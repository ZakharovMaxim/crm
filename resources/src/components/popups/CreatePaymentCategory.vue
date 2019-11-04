<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Создать категорию {{ label }}
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
        Создать
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { PAYMENT_CATEGORY_MODULE } from '@/store/modules/TYPES'
const { CREATE_PAYMENT_CATEGORY } = PAYMENT_CATEGORY_MODULE

export default {
  props: {
    isActive: Boolean,
    type: {
      type: [Number, String],
      default: 1
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
      return +this.type === 1 ? 'доходов' : 'расходов'
    }
  },
  methods: {
    ...mapActions([CREATE_PAYMENT_CATEGORY]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[CREATE_PAYMENT_CATEGORY]({
        ...this.form.data(),
        type: this.type,
        parent_id: this.$route.query.parent_id
      }).then(response => {
        this.form.name = ''
        this.$buefy.toast.open('Категория создана')
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Категория не создана :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
