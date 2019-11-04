<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Создать товар
    </template>
    <template slot="content">
      <div class="columns">
        <div class="column is-6">
          <b-field
            label="Название товара"
            :type="form.getBuefyType('name')"
            :message="form.errors.get('name')"
          >
            <b-input
              v-focus
              placeholder="Название товара"
              :value="form.name"
              @input="e => handleInput(e, 'name')"
            />
          </b-field>
          <b-field
            label="SKU"
            :type="form.getBuefyType('sku')"
            :message="form.errors.get('sku')"
          >
            <b-input
              placeholder="SKU"
              :value="form.sku"
              @input="e => handleInput(e, 'sku')"
            />
          </b-field>
          <b-field
            label="Дополнительная информация"
            :type="form.getBuefyType('additional_info')"
            :message="form.errors.get('additional_info')"
          >
            <b-input
              placeholder="Дополнительная информация"
              :value="form.additional_info"
              @input="e => handleInput(e, 'additional_info')"
            />
          </b-field>
        </div>
        <div class="column is-6">
          <b-field label="Изображения">
            <b-upload
              v-model="dropFiles"
              multiple
              drag-drop
              @input="fileListChanged"
            >
              <section class="section">
                <div class="content has-text-centered">
                  <p>
                    <b-icon
                      icon="upload"
                      size="is-large"
                    />
                  </p>
                  <p>Перетащите сюда изображения товара</p>
                </div>
              </section>
            </b-upload>
          </b-field>
          <div class="tags">
            <span
              v-for="(file, index) in dropFiles"
              :key="index"
              class="tag is-primary"
            >
              {{ file.name }}
              <button
                class="delete is-small"
                type="button"
                @click="deleteDropFile(index)"
              />
            </span>
          </div>
        </div>
      </div>
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
import validations from '@/validations/product'
import { PRODUCT_MODULE } from '@/store/modules/TYPES'
const { CREATE_PRODUCT } = PRODUCT_MODULE

const acceptedTypes = ['image/png', 'image/jpeg']
export default {
  props: {
    isActive: Boolean,
    rootDir: {
      type: Object,
      default: null
    }
  },
  data () {
    return {
      form: validations(),
      isLoading: false,
      dropFiles: []
    }
  },
  methods: {
    ...mapActions([CREATE_PRODUCT]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      const data = this.form.formDataFilled()
      this.dropFiles.forEach(file => {
        data.append('photos[]', file, file.name)
      })
      data.append('root_id', this.rootDir ? this.rootDir.id : null)
      this[CREATE_PRODUCT](data).then(response => {
        this.form.reset()
        this.dropFiles = []
        this.$buefy.toast.open('Товар создан')
        this.$emit('close')
      }).catch(e => {
        this.form.setErrors(e.response.data.errors || {})
        this.$buefy.toast.open('Товар не создан :(')
      }).finally(() => {
        this.isLoading = false
      })
    },
    deleteDropFile (index) {
      this.dropFiles.splice(index, 1)
    },
    fileListChanged (list) {
      this.dropFiles = list.filter(file => acceptedTypes.includes(file.type))
    },
    close () {
      this.dropFiles = []
      this.form.reset()
      this.$emit('close')
    }
  }
}
</script>
