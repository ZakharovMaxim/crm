<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      {{ actionName }} группу цен
    </template>
    <template slot="content">
      <b-field
        label="Название группы"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название группы"
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
import { PRICE_GROUP_MODULE } from '@/store/modules/TYPES'
const { CREATE_PRICE_GROUP, UPDATE_PRICE_GROUP } = PRICE_GROUP_MODULE

export default {
  props: {
    isActive: Boolean,
    priceGroup: {
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
      return this.priceGroup ? 'изменена' : 'создана'
    },
    actionName () {
      return this.priceGroup ? 'Изменить' : 'Создать'
    }
  },
  created () {
    console.log(this.priceGroup)
    if (this.priceGroup) {
      this.form.name = this.priceGroup.name
    }
  },
  methods: {
    ...mapActions([CREATE_PRICE_GROUP, UPDATE_PRICE_GROUP]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      const data = this.priceGroup ? {
        id: this.priceGroup.id,
        data: this.form.data()
      } : this.form.data()
      const action = this.priceGroup ? UPDATE_PRICE_GROUP : CREATE_PRICE_GROUP
      this[action](data).then(response => {
        this.form.name = ''
        this.values = []
        this.$buefy.toast.open(`Группа ${this.actionComplete}`)
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open(`Группа не ${this.actionComplete} :(`)
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
  .priceGroup {
    display: flex;
  }
</style>
