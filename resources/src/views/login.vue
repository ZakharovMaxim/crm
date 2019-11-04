<template>
  <div
    v-title="'Авторизация'"
    class="login-page"
  >
    <div
      class="login-form"
    >
      <b-field
        :type="form.getBuefyType('form')"
        :message="form.errors.get('form')"
        :addons="false"
      >
        <b-field
          label="Логин"
          :type="form.getBuefyType('login')"
          :message="form.errors.get('login')"
        >
          <b-input
            name="login"
            :value="form.login"
            @input="e => handleInput(e, 'login')"
          />
        </b-field>
        <b-field
          label="Пароль"
          :type="form.getBuefyType('password')"
          :message="form.errors.get('password')"
        >
          <b-input
            name="password"
            :value="form.password"
            type="password"
            password-reveal
            @input="e => handleInput(e, 'password')"
            @keydown.native.enter="submit"
          />
        </b-field>
        <b-button
          :loading="isLoading"
          type="is-primary"
          @click="submit"
        >
          Войти
        </b-button>
      </b-field>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
import validation from '@/validations/auth'
const { AUTH } = USER_MODULE

export default {
  data () {
    return {
      form: validation(),
      isLoading: false
    }
  },
  methods: {
    ...mapActions([AUTH]),
    handleInput (v, type) {
      this.form.errors.remove(type)
      this.form.errors.remove('form')
      this.form[type] = v
    },
    submit () {
      if (this.form.validate()) {
        this.isLoading = true
        this[AUTH](this.form.data()).then((response) => {
          this.$router.push(`/`)
        }).catch(e => {
          this.form.setErrors(e.response.data.errors || {})
          this.form.setError('form', 'Проверьте правильность заполнения данных')
        }).finally(() => {
          this.isLoading = false
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .login-page {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .login-form {
    width: 430px;
    border: 1px solid #ccc;
    padding: 15px;
  }
</style>
