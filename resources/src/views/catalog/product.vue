<template>
  <GetData
    :callback="loadProduct"
    :update="updateTrigger"
  >
    <template #default="scopedSlots">
      <b-loading :active="isLoading" />
      <div
        v-title="title"
        class="columns is-multiline"
      >
        <div class="column is-12">
          <router-link
            :to="categoryLink"
            class="is-primary button"
          >
            <b-icon
              icon="arrow-left"
            />
          </router-link>
        </div>
        <div class="column is-12">
          <breadcrumbs
            :breadcrumbs="breadcrumbs"
          />
        </div>
        <div
          v-if="!product.is_variation"
          class="column is-12"
        >
          <b-button @click="statsOpened = true">
            <b-icon
              icon="package-variant"
            />
          </b-button>
          <ProductStatsPopup
            v-if="statsOpened"
            :id="product.id"
            :name="product.name"
            :is-active="statsOpened"
            @close="statsOpened = false"
          />
        </div>
        <div class="column is-12">
          <b-tabs type="is-boxed">
            <b-tab-item label="Информация">
              <div class="subtitle">
                Информация о товаре
              </div>
              <b-field
                label="Название товара"
                :type="form.getBuefyType('name')"
                :message="form.errors.get('name')"
              >
                <b-input
                  placeholder="Название товара"
                  :value="form.name"
                  @input="e => handleInput(e, 'name')"
                />
              </b-field>
              <b-field
                v-if="!product.is_variation"
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
              >
                <b-input
                  placeholder="Дополнительная информация"
                  :value="form.additional_info"
                  @input="e => handleInput(e, 'additional_info')"
                />
              </b-field>
              <b-field
                v-if="!product.is_variant"
                label="Каталог"
              >
                <b-select
                  :value="form.catalog_id"
                  @input="e => handleInput(e, 'catalog_id')"
                >
                  <option
                    v-for="folder in folders"
                    :key="'folder' + folder.id"
                    :value="folder.id"
                  >
                    {{ folder.name }}
                  </option>
                </b-select>
              </b-field>
              <template v-if="!product.is_variation">
                <b-field label="Торговая марка">
                  <b-select
                    :value="form.trademark_id"
                    @input="e => handleInput(e, 'trademark_id')"
                  >
                    <option
                      v-for="tm in trademarks"
                      :key="'tm_' + tm.id"
                      :value="tm.id"
                    >
                      {{ tm.name }}
                    </option>
                  </b-select>
                </b-field>
                <div class="subtitle">
                  Информация о цене
                </div>
                <b-field
                  v-if="product.purchase_price !== undefined"
                  label="Закупочная цена"
                  :type="form.getBuefyType('purchase_price')"
                  :message="form.errors.get('purchase_price')"
                >
                  <b-input
                    placeholder="Закупочная цена"
                    :value="form.purchase_price"
                    @input="e => handleInput(e, 'purchase_price')"
                  />
                </b-field>
                <b-field
                  v-if="!product.is_variation"
                  label="Цена продажи"
                  :type="form.getBuefyType('selling_price')"
                  :message="form.errors.get('selling_price')"
                >
                  <b-input
                    placeholder="Цена продажи"
                    :value="form.selling_price"
                    @input="e => handleInput(e, 'selling_price')"
                  />
                </b-field>
                <b-field
                  label="Минимальное количество"
                >
                  <b-input
                    placeholder="Минимальное количество"
                    :value="form.min_count"
                    @input="e => handleInput(e, 'min_count')"
                  />
                </b-field>
              </template>
              <template v-else>
                <div class="subtitle">
                  Информация об атрибутах
                </div>
                <div class="attributes-list">
                  <div class="subtitle">
                    Атрибуты
                  </div>
                  <table class="attributes-table">
                    <tr
                      v-for="attr in product.attributes"
                      :key="'attr_in_prod_' + attr.id"
                    >
                      <td>{{ attr.name }}</td>
                      <td>
                        <b-button
                          class="button is-danger"
                          size="is-small"
                          @click="removeAttribute(attr.id)"
                        >
                          <b-icon
                            icon="minus"
                          />
                        </b-button>
                      </td>
                    </tr>
                  </table>
                </div>
                <GetData :callback="fetchAttributes">
                  <div
                    v-if="attributesLeft.length"
                    class="is-flex"
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
              </template>
              <div class="subtitle">
                Описание товара
              </div>
              <div>
                <b-input
                  type="textarea"
                  placeholder="Описание товара"
                  :value="form.description"
                  @input="e => handleInput(e, 'description')"
                />
              </div>
              <template
                v-if="!!product.is_variant"
              >
                <div class="subtitle">
                  Атрибуты
                </div>
                <table class="attributes-table">
                  <tr
                    v-for="attribute in scopedSlots.data.parent_attributes"
                    :key="'attribute_' + attribute.id"
                  >
                    <td>
                      {{ attribute.name }}
                    </td>
                    <td>
                      <b-select
                        :value="getAttributeValue(attribute)"
                        @input="v => handleAttributeInput(v, attribute.id)"
                      >
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
              </template>
              <b-button
                :loading="isLoading"
                :disabled="isDeleteLoading"
                @click="submit"
              >
                Сохранить
              </b-button>
              <b-button
                :loading="isDeleteLoading"
                :disabled="isLoading"
                type="is-danger"
                @click="remove"
              >
                Удалить
              </b-button>
            </b-tab-item>
            <b-tab-item
              v-if="product.price_groups && priceGroups.length"
              label="Группы цен"
            >
              <div
                v-for="group in priceGroups"
                :key="'group_' + group.id"
              >
                <b-field :label="group.name">
                  <b-input
                    :value="findPriceGroupValue(group.id)"
                    @input="v => storePriceGroup(group.name, v)"
                  />
                </b-field>
              </div>
              <div>
                <b-button
                  @click="savePriceGroups"
                >
                  Сохранить
                </b-button>
              </div>
            </b-tab-item>
            <b-tab-item
              v-if="!!product.is_variation"
              label="Вариации"
            >
              <div class="subtitle">
                Вариации
              </div>
              <div class="new-variation-action">
                <b-button
                  size="is-small"
                  icon-left="plus"
                  type="is-info"
                  @click="isCreateVariationOpen = true"
                >
                  Новая вариация
                </b-button>
              </div>
              <CreateVariationPopup
                :is-active="isCreateVariationOpen"
                :product-id="product.id"
                :root-id="product.root_id"
                :parent-attributes="filteredAttributes"
                @close="isCreateVariationOpen = false"
              />
              <div class="variation-list">
                <router-link
                  v-for="variation in product.variations"
                  :key="'variation_' + variation.id"
                  :to="'/variations/' + variation.id"
                >
                  <ProductCard
                    :product="variation"
                    is-presentation
                  />
                </router-link>
              </div>
            </b-tab-item>
            <b-tab-item
              v-if="!product.is_variation"
              label="Изображения"
            >
              <sortable-image-list
                :images="product.images"
                :parent-id="product.id"
                :alt="product.name"
              />
            </b-tab-item>
          </b-tabs>
        </div>
      </div>
    </template>
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PRICE_GROUP_MODULE, PRODUCT_MODULE, TRADEMARK_MODULE, ATTRIBUTE_MODULE, FOLDER_MODULE } from '@/store/modules/TYPES'
import validation from '@/validations/product'
import TreeService from '@/helpers/Tree'
import ProductCard from '@/components/Cards/ProductCard'
import SortableImageList from '@/components/Layout/SortableImageList'
import Breadcrumbs from '@/components/layout/Breadcrumbs'
import CreateVariationPopup from '@/components/popups/CreateVariationPopup'
import ProductStatsPopup from '@/components/popups/productStats'
const { GET_PRODUCT, PRODUCT, DESTROY_PRODUCT, UPDATE_PRODUCT } = PRODUCT_MODULE
const { SET_PRODUCT_PRICE_GROUP, PRICE_GROUPS } = PRICE_GROUP_MODULE
const { DESTROY_PRODUCT_ATTRIBUTE, ATTRIBUTES, GET_ATTRIBUTES, ADD_PRODUCT_ATTRIBUTE } = ATTRIBUTE_MODULE
const { TRADEMARKS } = TRADEMARK_MODULE
const { FOLDERS } = FOLDER_MODULE

export default {
  components: {
    CreateVariationPopup,
    Breadcrumbs,
    ProductCard,
    SortableImageList,
    ProductStatsPopup
  },
  data () {
    return {
      form: validation(),
      isLoading: false,
      isDeleteLoading: false,
      updateTrigger: false,
      newAttribute: '',
      isCreateVariationOpen: false,
      lastUrl: '',
      fileList: [],
      priceGroupPrepared: [],
      attributeValues: {},
      statsOpened: false
    }
  },
  computed: {
    ...mapState({
      product: state => state.catalogModule[PRODUCT],
      attributes: state => state.attributeModule[ATTRIBUTES],
      folders: state => state.catalogModule[FOLDERS],
      priceGroups: state => state.priceGroupModule[PRICE_GROUPS],
      trademarks: state => state.trademarkModule[TRADEMARKS]
    }),
    attributesLeft () {
      const attrsInProduct = this.product.attributes.map(attr => attr.id)
      return this.attributes.filter(attr => !attrsInProduct.includes(attr.id))
    },
    filteredAttributes () {
      const attrsInProduct = this.product.attributes.map(attr => attr.id)
      return this.attributes.filter(attr => attrsInProduct.includes(attr.id))
    },
    title () {
      return (this.product && this.product.name) ? `Товар: ${this.product.name}` : ''
    },
    breadcrumbs () {
      const addToDefault = []
      if (this.product.is_variant) {
        addToDefault.push({
          id: 'parent_product_name',
          name: this.product.product.name,
          link: {
            path: this.productLink
          }
        })
      }
      addToDefault.push({
        id: 'product_name',
        name: this.product.name
      })
      return [
        ...TreeService.getTreePath(this.product.is_variant ? this.product.product.catalog_id : this.product.catalog_id, this.folders),
        ...addToDefault
      ]
    },
    categoryLink () {
      const result = {
        path: '/catalogs',
        query: {}
      }
      if (this.product.catalog_id) {
        result.query.parent_id = this.product.catalog_id
      }
      if (this.lastUrl.path === '/catalogs') {
        result.query = {
          ...result.query,
          ...this.lastUrl.query
        }
      }
      return result
    },
    productLink () {
      return `/catalogs/product/${this.product.parent_id}`
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.lastUrl = {
        path: from.path,
        query: {
          ...from.query
        }
      }
    })
  },
  watch: {
    '$route': function () {
      this.updateTrigger = !this.updateTrigger
    }
  },
  methods: {
    ...mapActions([SET_PRODUCT_PRICE_GROUP, GET_PRODUCT, UPDATE_PRODUCT, DESTROY_PRODUCT, DESTROY_PRODUCT_ATTRIBUTE, GET_ATTRIBUTES, ADD_PRODUCT_ATTRIBUTE]),
    handleInput (v, type) {
      this.form.errors.remove(type)
      this.form[type] = v
    },
    getAttributeValue (attribute) {
      const foundInVariation = this.product.attributes_values.find(varAttr => varAttr.attribute_id === attribute.id)
      if (!foundInVariation) return null
      const attrValue = foundInVariation.pivot.attribute_values_id
      return attrValue
    },
    handleAttributeInput (value, type) {
      this.attributeValues[type] = value
    },
    findPriceGroupValue (groupId) {
      const found = this.product.price_groups.find(group => group.id === groupId)
      return found ? found.pivot.value : 0
    },
    storePriceGroup (type, value) {
      this.priceGroupPrepared[type] = value
    },
    savePriceGroups () {
      this.isLoading = true
      return this[SET_PRODUCT_PRICE_GROUP]({
        id: this.product.id,
        data: this.priceGroups.map(group => {
          return {
            id: group.id,
            value: this.priceGroupPrepared[group.name] || this.findPriceGroupValue(group.id)
          }
        })
      }).then(() => {
        this.$buefy.toast.open('Группы цен обновлены')
      }).catch((e) => {
        console.log(e)
        this.$buefy.toast.open('Группы цен не обновлены')
      }).finally(() => {
        this.isLoading = false
      })
    },
    loadProduct () {
      return this[GET_PRODUCT](this.$route.params.id).then(response => {
        this.form.setData(this.product)
        if (this.product.is_variation) {
          this.form.changeValidateRule('sku', 'required', false)
        }
        return response
      })
    },
    submit () {
      if (this.form.validate()) {
        let data = {}
        if (this.product.is_variant) {
          const updatedAttributes = this.product.attributes_values.map((attr) => {
            return {
              attribute_id: attr.attribute_id,
              value: attr.pivot.attribute_values_id
            }
          })
          Object.keys(this.attributeValues).forEach((key, value) => {
            const oldAttribute = updatedAttributes.find(attr => +attr.attribute_id === +key)
            if (oldAttribute) oldAttribute.value = this.attributeValues[key]
            else {
              updatedAttributes.push({
                attribute_id: key,
                value: this.attributeValues[key]
              })
            }
          })
          data.updatedAttributes = updatedAttributes
        }
        data = {
          ...data,
          ...this.product,
          ...this.form.data()
        }
        this.isLoading = true
        this[UPDATE_PRODUCT]({
          data,
          id: this.product.id
        }).then(() => {
          this.$buefy.toast.open('Товар обновлен')
        }).catch(e => {
          this.form.setErrors(e.response.data.errors || {})
        }).finally(() => {
          this.isLoading = false
        })
      }
    },
    remove () {
      this.$buefy.dialog.confirm({
        title: 'Удаление товара',
        message: 'Вы действительно хотите удалить товар? Это действие нельзя отменить',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.removeConfirmed()
      })
    },
    removeConfirmed () {
      this.isDeleteLoading = true
      this[DESTROY_PRODUCT](this.product.id).then(() => {
        this.$buefy.toast.open('Товар удален')
        this.$router.push(`/catalogs`)
      }).finally(() => {
        this.isDeleteLoading = false
      })
    },
    removeAttribute (id) {
      this.$buefy.dialog.confirm({
        title: 'Удаление атрибута',
        message: 'Вы действительно хотите удалить атрибут? Это действие нельзя отменить',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: () => this.removeAttributeConfirmed(id)
      })
    },
    removeAttributeConfirmed (id) {
      this.isLoading = true
      this[DESTROY_PRODUCT_ATTRIBUTE]({
        attribute_id: id,
        id: this.product.id
      }).then(() => {
        this.$buefy.toast.open('Атрибут удален')
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.isLoading = false
      })
    },
    addAttribute () {
      if (!this.newAttribute) return
      this.isLoading = true
      this[ADD_PRODUCT_ATTRIBUTE]({
        attribute_id: this.newAttribute,
        id: this.product.id
      }).then(() => {
        this.$buefy.toast.open('Атрибут добавлен')
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.newAttribute = ''
        this.isLoading = false
      })
    },
    fetchAttributes () {
      return this[GET_ATTRIBUTES]()
    },
    getAttributeNameById (id) {
      const find = this.attributes.find(attr => attr.id === id)
      return find ? find.name : ''
    }
  }
}
</script>

<style lang="scss" scoped>
.new-variation-action {
  margin-bottom: 10px;
}
.card {
  transition: all 200ms;
  &:hover {
    background: #eee;
    cursor: pointer;
  }
}
</style>
