<template>
  <Popup
    :is-active="isActive"
    @close="close"
  >
    <template slot="header">
      Экспорт каталога
    </template>
    <template slot="content">
      <b-field label="Каталог">
        <div>
          <b-checkbox
            v-model="fields"
            native-value="catalog_id"
          >
            Код каталога
          </b-checkbox>
          <b-checkbox
            v-model="fields"
            native-value="catalog_name"
          >
            Название каталога
          </b-checkbox>
        </div>
      </b-field>
      <b-field label="Товар">
        <div>
          <b-checkbox
            v-model="fields"
            native-value="product_name"
          >
            Название товара
          </b-checkbox>
          <b-checkbox
            v-model="fields"
            native-value="product_sku"
          >
            Артикул
          </b-checkbox>
          <b-checkbox
            v-model="fields"
            native-value="product_additional"
          >
            Дополнительное описание
          </b-checkbox>
          <b-checkbox
            v-model="fields"
            native-value="product_description"
          >
            Описание товара
          </b-checkbox>
        </div>
      </b-field>
      <b-field label="Торговая марка">
        <div>
          <b-checkbox
            v-model="fields"
            native-value="trademark_id"
          >
            Код торговой марки
          </b-checkbox>
          <b-checkbox
            v-model="fields"
            native-value="trademark_name"
          >
            Название
          </b-checkbox>
        </div>
      </b-field>
      <b-field label="Цена">
        <div>
          <b-checkbox
            v-model="fields"
            native-value="selling_price"
          >
            Розничная цена
          </b-checkbox>
          <b-checkbox
            v-model="fields"
            native-value="purchase_price"
          >
            Цена закупки
          </b-checkbox>
        </div>
      </b-field>
      <b-field label="Отстатки">
        <div>
          <b-checkbox
            v-model="fields"
            native-value="min_count"
          >
            Минимальное количество
          </b-checkbox>
        </div>
      </b-field>
    </template>
    <template slot="footer">
      <b-button
        @click="submit"
      >
        Экспортировать
      </b-button>
    </template>
  </Popup>
</template>

<script>

export default {
  props: {
    isActive: Boolean
  },
  data () {
    return {
      fields: []
    }
  },
  methods: {
    submit () {
      if (!this.fields.length) return
      this.$emit('submit', this.fields.join(','))
    },
    close () {
      this.$emit('close')
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
