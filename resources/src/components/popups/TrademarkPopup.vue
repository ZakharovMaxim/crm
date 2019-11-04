<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      {{ actionName }} торговую марку
    </template>
    <template slot="content">
      <b-field
        label="Название марки"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название марки"
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
import { TRADEMARK_MODULE } from '@/store/modules/TYPES'
const { CREATE_TRADEMARK, UPDATE_TRADEMARK } = TRADEMARK_MODULE

export default {
  props: {
    isActive: Boolean,
    trademark: {
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
      return this.trademark ? 'изменена' : 'создана'
    },
    actionName () {
      return this.trademark ? 'Изменить' : 'Создать'
    }
  },
  created () {
    console.log(this.trademark)
    if (this.trademark) {
      this.form.name = this.trademark.name
    }
  },
  methods: {
    ...mapActions([CREATE_TRADEMARK, UPDATE_TRADEMARK]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      const data = this.trademark ? {
        id: this.trademark.id,
        data: this.form.data()
      } : this.form.data()
      const action = this.trademark ? UPDATE_TRADEMARK : CREATE_TRADEMARK
      this[action](data).then(response => {
        this.form.name = ''
        this.values = []
        this.$buefy.toast.open(`Торговая марка ${this.actionComplete}`)
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open(`Торговая марка не ${this.actionComplete} :(`)
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
  .trademark {
    display: flex;
  }
</style>
