<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Обновить счет
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
        Обновить
      </b-button>
      <b-button
        :loading="isLoading"
        type="is-danger"
        @click="removeConfirm"
      >
        Удалить
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { BILL_MODULE } from '@/store/modules/TYPES'
const { UPDATE_BILL, REMOVE_BILL } = BILL_MODULE

export default {
  props: {
    isActive: Boolean,
    bill: {
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
  created () {
    this.form.setData(this.bill)
  },
  methods: {
    ...mapActions([UPDATE_BILL, REMOVE_BILL]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    removeConfirm () {
      this.$buefy.dialog.confirm({
        title: 'Удалить счет',
        message: 'Вы действительно хотите удалить счет? Все платежи этого счета будут удалены',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: this.delete
      })
    },
    delete () {
      this.isLoading = true
      this[REMOVE_BILL](this.bill.id).then(() => {
        this.$buefy.toast.open('Категория была удалена')
        this.$emit('close')
      }).finally(() => {
        this.isLoading = false
      })
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[UPDATE_BILL]({
        id: this.bill.id,
        data: this.form.data()
      }).then(response => {
        this.$buefy.toast.open('Счет обновлен')
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Счет не обновлен :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
