<template>
  <GetData
    v-title="title"
    :update="update"
    :callback="fetchData"
  >
    <b-loading :active="isLoading" />
    <b-message
      v-if="disabled"
      title="Пользователь был заблокирован"
      type="is-danger"
      has-icon
      aria-close-label="Закрыть сообщение"
    >
      Чтобы редактировать страницу пользователя, сначала разблокируйте его
    </b-message>
    <b-tabs>
      <b-tab-item label="Информация">
        <user-account-block
          v-if="canBeBlocked"
          :id="user.id"
          :is-deleted="!user.is_deleted"
        />
        <div
          class="avatar-container"
          @click="avatarOpen"
        >
          <div
            v-if="!poster && !disabled"
            class="avatar-sign"
          >
            <b-icon icon="plus" />
          </div>
          <b-avatar
            :src="poster"
          />
        </div>
        <update-image-popup
          :is-active="updateImageOpen"
          @submit="newImageOpen"
          @remove="removeImage"
          @close="updateImageOpen = false"
        />
        <input
          ref="file"
          type="file"
          @change="setImage"
        >
        <b-field
          label="Имя"
          :type="form.getBuefyType('name')"
          :message="form.errors.get('name')"
        >
          <b-input
            v-model="form.name"
            v-focus
            placeholder="Имя"
            :disabled="disabled"
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
            :disabled="disabled"
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
            :disabled="disabled"
            @input="handleInput('login')"
          />
        </b-field>
        <b-button
          :disabled="disabled"
          @click="submit"
        >
          Сохранить
        </b-button>
      </b-tab-item>
      <b-tab-item
        v-if="!disabled && !isAdmin && isCurrentUserAdmin"
        label="Права"
      >
        <div class="error">
          {{ roleError }}
        </div>
        <user-rules
          :role="(user && user.role) || {}"
          @change="setUserRoleTemp"
        />
        <b-button
          v-if="role"
          type="is-primary"
          :disabled="roleDisabled"
          @click="setRole"
        >
          Сохранить
        </b-button>
      </b-tab-item>
      <b-tab-item
        v-if="!disabled"
        label="Пароль"
      >
        <b-field
          label="Пароль, от 6-и символов"
          :type="passwordForm.getBuefyType('password')"
          :message="passwordForm.errors.get('password')"
        >
          <b-input
            v-model="passwordForm.password"
            placeholder="Пароль"
            :disabled="disabled"
            type="password"
            password-reveal
            autocomplete="new-password"
            @input="handleInput('password')"
          />
        </b-field>
        <b-field
          label="Повторите пароль"
          :type="passwordForm.getBuefyType('password_confirmation')"
          :message="passwordForm.errors.get('password_confirmation')"
        >
          <b-input
            v-model="passwordForm.password_confirmation"
            placeholder="Пароль"
            type="password"
            password-reveal
            :disabled="disabled"
            @input="handleInput('password_confirmation')"
          />
        </b-field>
        <b-button
          :disabled="disabled"
          @click="submitPassword"
        >
          Сохранить
        </b-button>
      </b-tab-item>
    </b-tabs>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
import validation from '@/validations/new-user'
import bAvatar from '@/components/base/avatar'
import updateImagePopup from '@/components/popups/updateImagePopup'
import UserAccountBlock from '@/components/UserAccountBlock'
import UserRules from '@/components/UserRules'
const { SET_USER_ROLE, GET_EDITED_USER, EDITED_USER, USER, RESTORE_USER, DESTROY_USER, UPDATE_EDITED_USER, UPDATE_EDITED_USER_PASSWORD, SET_USER_IMAGE, UNSET_USER_IMAGE } = USER_MODULE

export default {
  components: {
    bAvatar,
    updateImagePopup,
    UserAccountBlock,
    UserRules
  },
  data () {
    return {
      isLoading: false,
      form: validation('info'),
      passwordForm: validation('password'),
      update: false,
      updateImageOpen: false,
      role: null,
      roleError: null
    }
  },
  computed: {
    ...mapState({
      user: store => store.userModule[EDITED_USER],
      authUser: store => store.userModule[USER]
    }),
    poster () {
      return this.user && this.user.image
    },
    disabled () {
      return !!(this.user && this.user.is_deleted)
    },
    roleDisabled () {
      return this.disabled || this.roleError
    },
    isAdmin () {
      return this.user && this.user.is_admin
    },
    isCurrentUserAdmin () {
      return this.authUser && this.authUser.is_admin
    },
    canBeBlocked () {
      if (!this.authUser || !this.user) return false
      return this.authUser.is_admin && this.authUser.id !== this.user.id && !this.user.is_admin
    },
    title () {
      if (this.user && this.user.name) {
        return `Пользователь: ${this.user.name} ${this.user.surname}`
      }
      return ''
    }
  },
  watch: {
    '$route': function () {
      this.update = !this.update
    }
  },
  methods: {
    ...mapActions([SET_USER_ROLE, GET_EDITED_USER, RESTORE_USER, DESTROY_USER, UPDATE_EDITED_USER, UPDATE_EDITED_USER_PASSWORD, SET_USER_IMAGE, UNSET_USER_IMAGE]),
    fetchData () {
      return this[GET_EDITED_USER](this.$route.params.id).then(response => {
        this.form.setData(this.user)
      })
    },
    setUserRoleTemp (payload) {
      this.role = payload.data
      this.roleError = payload.error
    },
    setRole () {
      if (this.disabled || !this.role || this.roleError) return
      this.isLoading = true
      this[SET_USER_ROLE]({
        id: this.user.id,
        data: this.role
      }).then(() => {
        this.$buefy.toast.open('Права изменены')
      }).catch(e => {
        this.$buefy.toast.open('Права не изменены')
      }).finally(() => {
        this.isLoading = false
      })
    },
    setImage (e) {
      if (this.disabled) return
      this.isLoading = true
      const data = new FormData()
      data.append('image', e.target.files[0])
      data.append('test', 't')

      this[SET_USER_IMAGE]({
        id: this.user.id,
        data
      }).then(() => {
        this.$buefy.toast.open('Изображение изменено')
      }).catch(e => {
        this.$buefy.toast.open('Изображение не было изменено')
      }).finally(() => {
        this.isLoading = false
        e.target.value = ''
      })
    },
    avatarOpen () {
      if (this.disabled) return
      if (this.user.image) {
        this.updateImageOpen = true
      } else this.newImageOpen()
    },
    removeImage () {
      if (this.disabled) return
      this.isLoading = true
      this[UNSET_USER_IMAGE](this.user.id).then(() => {
        this.$buefy.toast.open('Изображение удалено')
      }).catch(e => {
        this.$buefy.toast.open('Изображение не было удалено')
      }).finally(() => {
        this.isLoading = false
      })
    },
    newImageOpen () {
      if (this.disabled) return
      this.$refs.file.click()
    },
    handleInput (field) {
      if (this.disabled) return
      this.form.errors.remove(field)
      this.passwordForm.errors.remove(field)
    },
    submit () {
      if (this.disabled) return
      if (!this.form.validate()) return
      this.isLoading = true
      const data = this.form.data()
      delete data.role
      this[UPDATE_EDITED_USER]({
        id: this.user.id,
        data
      }).then(() => {
        this.$buefy.toast.open('Пользователь был обновлен')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Пользователь не был обновлен')
      }).finally(() => {
        this.isLoading = false
      })
    },
    submitPassword () {
      if (this.disabled) return
      if (!this.passwordForm.validate()) return
      this.isLoading = true
      this[UPDATE_EDITED_USER_PASSWORD]({
        id: this.user.id,
        data: this.passwordForm.data()
      }).then(() => {
        this.$buefy.toast.open('Пароль был обновлен')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Пароль не был обновлен')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.is-danger {
  background: #ffb5b5;
}
.avatar-container {
  width: 200px;
  cursor: pointer;
  position: relative;
}
.avatar-sign {
  position: absolute;
  right: 10px;
  bottom: 10px;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #008fe2;
  color: #fff;
  border: 2px solid #fff;
}
input[type="file"] {
  display: none;
}
.error {
  color: #ff4e4e;
  font-size: 0.85em;
  height: 2em;
}
</style>
