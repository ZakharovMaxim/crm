<template>
  <Popup
    :is-active="isActive"
    @close="$emit('close')"
  >
    <template slot="header">
      Обновить каталог
    </template>
    <template slot="content">
      <b-field
        label="Название каталога"
        :type="form.getBuefyType('name')"
        :message="form.errors.get('name')"
      >
        <b-input
          v-focus
          placeholder="Название каталога"
          :value="form.name"
          @input="e => handleInput(e, 'name')"
        />
      </b-field>
      <b-field>
        <b-select
          :value="form.parent_id"
          @input="e => handleInput(e, 'parent_id')"
        >
          <option
            v-for="(folder) in folders"
            :key="'folder' + folder.id"
            :value="folder.id"
          >
            {{ folder.name }}
          </option>
        </b-select>
      </b-field>
    </template>
    <template slot="footer">
      <b-button
        :loading="isLoading"
        @click="submit"
      >
        Изменить
      </b-button>
    </template>
  </Popup>
</template>

<script>
import { mapActions } from 'vuex'
import validations from '@/validations/simple-name'
import { FOLDER_MODULE } from '@/store/modules/TYPES'
const { UPDATE_FOLDER, FOLDERS_WITHOUT_CHILDS } = FOLDER_MODULE

export default {
  props: {
    isActive: Boolean,
    name: {
      type: String,
      required: true
    },
    parentId: {
      type: [Number],
      default: null
    },
    id: {
      type: Number,
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
    folders () {
      return this.$store.getters[FOLDERS_WITHOUT_CHILDS](this.id)
    }
  },
  created () {
    this.form.name = this.name
    this.form.parent_id = this.parentId
  },
  methods: {
    ...mapActions([UPDATE_FOLDER]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[UPDATE_FOLDER]({
        id: this.id,
        data: this.form.data()
      }).then(response => {
        this.$buefy.toast.open('Каталог обновлен')
        this.$emit('close')
      }).catch(e => {
        this.$buefy.toast.open('Каталог не обновлен :(')
      }).finally(() => {
        this.isLoading = false
      })
    }
  }
}
</script>
