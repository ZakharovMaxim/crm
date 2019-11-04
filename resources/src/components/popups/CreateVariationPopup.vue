<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Создать вариацию
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
          <div class="subtitle">
            Атрибуты
          </div>
          <table class="attributes-table">
            <tr
              v-for="attribute in parentAttributes"
              :key="'attribute_' + attribute.id"
            >
              <td>
                {{ attribute.name }}
              </td>
              <td>
                <b-select @input="v => handleAttributeInput(v, attribute.id)">
                  <option
                    v-for="value in attribute.values"
                    :key="'value_' + value.id"
                    :value="value.id"
                  >
                    {{ value.value }}
                  </option>
                </b-select>
              </td>
            </tr>
          </table>
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
    productId: {
      type: Number,
      required: true
    },
    rootId: {
      type: Number,
      required: true
    },
    parentAttributes: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      form: validations(),
      isLoading: false,
      dropFiles: [],
      attributeValues: {}
    }
  },
  methods: {
    ...mapActions([CREATE_PRODUCT]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    handleAttributeInput (value, type) {
      this.attributeValues[type] = value
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      const data = this.form.formDataFilled()
      data.append('parent_id', this.productId)
      data.append('is_variant', 1)
      data.append('root_id', this.rootId)
      data.append('attributes', JSON.stringify(Object.keys(this.attributeValues).map((key, value) => {
        return {
          attribute_id: key,
          value: this.attributeValues[key]
        }
      })))
      this.dropFiles.forEach(file => {
        data.append('photos[]', file, file.name)
      })
      this[CREATE_PRODUCT](data).then(response => {
        this.form.reset()
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
      this.attributeValues = {}
      this.dropFiles = []
      this.$emit('close')
    }
  }
}
</script>
