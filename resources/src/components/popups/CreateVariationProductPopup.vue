<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Создать товар
    </template>
    <template slot="content">
      <div>
        <GetData :callback="fetchAttributes">
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
          <div class="subtitle">
            Атрибуты
          </div>
          <div v-if="!attributes.length">
            Вы еще не создали атрибуты
          </div>
          <div>
            <div
              v-for="attr in attributesInProduct"
              :key="'attr_in_prod_' + attr.id"
              class="is-flex"
            >
              <span class="mr-5">{{ attr.name }}</span>
              <b-button
                class="button is-danger"
                size="is-small"
                @click="removeAttribute(attr.id)"
              >
                <b-icon
                  icon="minus"
                />
              </b-button>
            </div>
          </div>
          <div
            v-if="attributesLeft.length"
            class="is-flex mr-5 new-attributes"
          >
            <b-select
              v-model="newAttribute"
              placeholder="Выберите атрибут"
            >
              <option
                v-for="option in attributesLeft"
                :key="option.id"
                :value="option.id"
              >
                {{ option.name }}
              </option>
            </b-select>
            <button
              class="button is-primary"
              @click="addAttribute"
            >
              <b-icon
                icon="plus"
              />
            </button>
          </div>
        </GetData>
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
import { mapActions, mapState } from 'vuex'
import validations from '@/validations/product'
import { ATTRIBUTE_MODULE, PRODUCT_MODULE } from '@/store/modules/TYPES'
const { CREATE_PRODUCT } = PRODUCT_MODULE
const { GET_ATTRIBUTES, ATTRIBUTES } = ATTRIBUTE_MODULE

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
      form: validations(true),
      isLoading: false,
      attributesInProduct: [],
      newAttribute: ''
    }
  },
  computed: {
    ...mapState({
      attributes: store => store.attributeModule[ATTRIBUTES]
    }),
    attributesLeft () {
      return this.attributes.filter(attr => !this.attributesInProduct.includes(attr))
    }
  },
  methods: {
    ...mapActions([CREATE_PRODUCT, GET_ATTRIBUTES]),
    handleInput (v, type) {
      this.form[type] = v
      this.form.errors.remove(type)
    },
    submit () {
      if (!this.form.validate()) return
      this.isLoading = true
      this[CREATE_PRODUCT]({
        ...this.form.dataFilled(),
        root_id: this.rootDir ? this.rootDir.id : null,
        is_variation: true,
        attributes: this.attributesInProduct.map(attr => attr.id)
      }).then(response => {
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
    close () {
      this.newAttribute = ''
      this.attributesInProduct = []
      this.form.reset()
      this.$emit('close')
    },
    fetchAttributes () {
      return this[GET_ATTRIBUTES]()
    },
    addAttribute () {
      const attr = this.attributes.find(attr => attr.id === this.newAttribute)
      if (!attr) return
      this.newAttribute = ''
      this.attributesInProduct.push(attr)
    },
    removeAttribute (id) {
      this.attributesInProduct = this.attributesInProduct.filter(attr => attr.id !== id)
    }
  }
}
</script>

<style lang="scss" scoped>
.new-attributes {
  margin-top: 10px;
}
</style>
