<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Новый пользователь
    </template>
    <template slot="content">
      <b-field
        label="Имя"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-model="form.name"
          v-focus
          placeholder="Имя"
          @input="handleInput('name')"
        />
      </b-field>
      <b-field
        label="Фамилия"
        :type="form.getBuefyType('surname')"
        :message="form.errors.get('surname')"
      >
        <b-input
          v-model="form.surname"
          placeholder="Фамилия"
          @input="handleInput('surname')"
        />
      </b-field>
      <b-field
        label="Логин, от 6-и символов"
        :type="form.getBuefyType('login')"
        :message="form.errors.get('login')"
      >
        <b-input
          v-model="form.login"
          placeholder="Логин"
          @input="handleInput('login')"
        />
      </b-field>
      <b-field
        label="Пароль, от 6-и символов"
        :type="form.getBuefyType('password')"
        :message="form.errors.get('password')"
      >
        <b-input
          v-model="form.password"
          placeholder="Пароль"
          type="password"
          password-reveal
          autocomplete="new-password"
          @input="handleInput('password')"
        />
      </b-field>
      <b-field
        label="Повторите пароль"
        :type="form.getBuefyType('password_confirmation')"
        :message="form.errors.get('password_confirmation')"
      >
        <b-input
          v-model="form.password_confirmation"
          placeholder="Пароль"
          type="password"
          password-reveal
          @input="handleInput('password_confirmation')"
        />
      </b-field>
      <b-field
        label="Установите права"
        :type="form.getBuefyType('role')"
        :message="form.errors.get('role')"
      >
        <user-rules
          @change="setRole"
        />
      </b-field>
      <b-button
        :is-loading="isLoading"
        @click="submit"
      >
        Создать
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
import validation from '@/validations/new-user'
import UserRules from '@/components/UserRules'
const { CREATE_USER } = USER_MODULE

export default {
  components: { UserRules },
  props: {
    isActive: Boolean
  },
  data () {
    return {
      isLoading: false,
      form: validation(),
      roleError: null
    }
  },
  methods: {
    ...mapActions([CREATE_USER]),
    handleInput (field) {
      this.form.errors.remove(field)
    },
    close () {
      this.$emit('close')
    },
    setRole (payload) {
      this.form.role = payload.data
      this.roleError = payload.error
      if (!payload.error) this.form.setError('role', null)
    },
    submit () {
      if (!this.form.validate()) return
      if (this.roleError) {
        this.form.setError('role', this.roleError)
        return
      }
      this.isLoading = true
      this[CREATE_USER](this.form.data()).then((response) => {
        this.$buefy.toast.open('Пользователь успешно создан')
        this.close()
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Пользователь не был создан')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
