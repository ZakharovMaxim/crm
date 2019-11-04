<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      {{ actionName }} валюту
    </template>
    <template slot="content">
      <b-field
        label="Название валюты"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название валюты"
          :value="form.name"
          @input="e => handleInput(e, 'name')"
        />
      </b-field>
      <b-field
        label="Курс"
        :type="form.getBuefyType('rate')"
        :message="form.errors.get('rate')"
      >
        <b-input
          placeholder="Курс"
          :value="form.rate"
          @input="e => handleInput(e, 'rate')"
        />
      </b-field>
    </template>
    <template slot="footer">
      <b-button
        :loading="isLoading"
        @click="submit"
      >
        {{ actionName }}
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/currency'
import { CURRENCY_MODULE } from '@/store/modules/TYPES'
const { CREATE_CURRENCY, UPDATE_CURRENCY } = CURRENCY_MODULE

export default {
  props: {
    isActive: Boolean,
    currency: {
      type: Object,
      default: null
    }
  },
  data () {
    return {
      form: validations(),
      isLoading: false
    }
  },
  computed: {
    actionComplete () {
      return this.currency ? 'изменена' : 'создана'
    },
    actionName () {
      return this.currency ? 'Изменить' : 'Создать'
    }
  },
  created () {
    console.log(this.currency)
    if (this.currency) {
      this.form.name = this.currency.name
    }
  },
  methods: {
    ...mapActions([CREATE_CURRENCY, UPDATE_CURRENCY]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      const data = this.currency ? {
        id: this.currency.id,
        data: this.form.data()
      } : this.form.data()
      const action = this.currency ? UPDATE_CURRENCY : CREATE_CURRENCY
      this[action](data).then(response => {
        this.form.name = ''
        this.values = []
        this.$buefy.toast.open(`Валюта ${this.actionComplete}`)
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open(`Валюта не ${this.actionComplete} :(`)
      }).finally(() => {
        this.isLoading = false
      })
    },
    close () {
      this.$emit('close')
      this.form.reset()
    }
  }
}
</script>

<style lang="scss" scoped>
  .currency {
    display: flex;
  }
</style>
