<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      {{ actionName }} статью {{ label }}
    </template>
    <template slot="content">
      <b-field
        label="Название"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название"
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
        {{ actionName }}
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { PAYMENT_STATE_MODULE } from '@/store/modules/TYPES'
const { CREATE_PAYMENT_STATE, UPDATE_PAYMENT_STATE } = PAYMENT_STATE_MODULE

export default {
  props: {
    isActive: Boolean,
    type: {
      type: Number,
      default: 1
    },
    state: {
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
      return this.state ? 'изменена' : 'создана'
    },
    actionName () {
      return this.state ? 'Изменить' : 'Создать'
    },
    label () {
      return this.type === 1 ? 'дохода' : 'расхода'
    }
  },
  created () {
    if (this.state) {
      this.form.name = this.state.name
    }
  },
  methods: {
    ...mapActions([CREATE_PAYMENT_STATE, UPDATE_PAYMENT_STATE]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      const data = {
        ...this.form.data(),
        type: this.type,
        parent_id: this.$route.query.parent_id || null
      }
      const request = this.state ? {
        id: this.state.id,
        data
      } : data
      const action = this.state ? UPDATE_PAYMENT_STATE : CREATE_PAYMENT_STATE
      this[action](request).then(response => {
        this.form.name = ''
        this.values = []
        this.$buefy.toast.open(`Статья ${this.actionComplete}`)
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open(`Статья не ${this.actionComplete} :(`)
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
  .state {
    display: flex;
  }
</style>
