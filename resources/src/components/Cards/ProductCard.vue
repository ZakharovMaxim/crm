<template>
  <div
    class="card-content product w-100"
    :class="{'is-active': isActive}"
    @click="$emit('click', product.id)"
  >
    <div
      v-if="!isPresentation"
      class="card-tools"
      @click.stop.prevent
    >
      <b-tooltip
        label="Изменить товар"
        position="is-top"
      >
        <div
          class="card-tool"
        >
          <router-link :to="`/catalogs/product/${product.id}`">
            <b-icon
              icon="pencil"
            />
          </router-link>
        </div>
      </b-tooltip>
      <b-tooltip
        label="Удалить товар"
        position="is-top"
      >
        <div
          class="card-tool card-tool--danger"
          @click="confirmDelete"
        >
          <b-icon
            icon="delete"
          />
        </div>
      </b-tooltip>
    </div>
    <div
      v-if="closeable"
      class="card-closeable"
    >
      <div
        class="card-closeable_trigger"
        @click="$emit('close')"
      >
        <b-icon
          icon="close"
        />
      </div>
    </div>
    <div class="card-top">
      <slot />
    </div>
    <div class="is-flex w-100 main-row">
      <div class="column is-narrow-tablet is-narrow-mobile is-centered is-2">
        <product-image
          :images="product.images"
          :alt="product.name"
        />
      </div>
      <div class="column">
        <div class="is-flex gapless is-multiline">
          <div class="column is-10 info-col">
            <div>
              <span class="product_name">
                {{ product.name }}
              </span>
              <span
                v-if="product.additional_info"
                class="product_additional_info"
              >
                ({{ product.additional_info }})
              </span>
            </div>
            <div class="is-flex">
              <div class="product_title">
                Код товара: <span>0{{ product.id }}</span>
              </div>
              <div
                v-if="!product.is_variation"
                class="product_title"
              >
                Артикул: <span>{{ product.sku }}</span>
              </div>
            </div>
            <div
              v-if="!product.is_variation"
              class="product_available"
            >
              <span :class="{'product-in-stock': stats.in_stock, 'product-not-in-stock': !stats.in_stock}">
                {{ stats.in_stock ? 'В наличии: ' + stats.in_stock : 'Нет в наличии' }}
              </span>
              <span
                v-if="stats.in_prepare"
                class="product-in-prepare"
              >
                В подготовке: {{ stats.in_prepare }}
              </span>
              <span
                v-if="stats.in_reserve"
                class="product-in-reserve"
              >
                В резерве: {{ stats.in_reserve }}
              </span>
            </div>
          </div>
          <div
            v-if="!product.is_variation"
            class="is-2 tar"
          >
            <div class="product_price">
              {{ product.selling_price || 0 }} UAH
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * Component for product presentation in list
 * @param {string} name product name
 * @param {number} id product id
 * @param {array} images product posters
 */
import ProductImage from './ProductImage'

export default {
  components: { ProductImage },
  props: {
    product: {
      type: Object,
      required: true
    },
    isPresentation: {
      type: Boolean,
      default: false
    },
    isActive: {
      type: Boolean,
      default: false
    },
    closeable: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    stats () {
      return (this.product.product_stats && this.product.product_stats[0]) || { in_stock: 0, in_prepare: 0, in_reserve: 0 }
    }
  },
  methods: {
    /**
     * Create confirm dialog for product remove
     */
    confirmDelete () {
      this.$buefy.dialog.confirm({
        title: 'Удаление товара',
        message: 'Вы действительно хотите удалить товар, это действие нельзя отменить?',
        confirmText: 'Удалить',
        cancelText: 'Отменить',
        type: 'is-danger',
        hasIcon: true,
        onConfirm: this.delete
      })
    },
    /**
     * Emit delete event to parent
     */
    delete () {
      this.$emit('delete', this.product.id)
    }
  }
}
</script>

<style lang="scss" scoped>
.product {
  &_available {
    font-size: 12px;
    font-weight: bold;
  }
  &_name {
    font-size: 0.90rem;
    color: #1a6c93;
  }
  &_additional_info {
    font-size: 0.75rem;
    color: #bababa;
  }
  &_title {
    color: #808080;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    &:not(:last-child) {
      margin-right: 5px;
      &:after {
        content: '';
        display: inline-block;
        height: 0.75rem;
        width: 1px;
        background: #808080;
      }
    }
    span {
      color: #1a6c93;
      display: inline-block;
      margin: 0 5px;
    }
  }
  &_price {
    font-size: 0.9rem;
    color: #ff4701;
  }
  &-in-stock {
    color: #00b057;
  }
  &-not-in-stock {
    color: #ff4e4e;
  }
  &-in-prepare {
    color: #96a900;
  }
  &-in-reserve {
    color: #aaa;
  }
}
.card-content {
  padding: 5px;
}
.card-content:hover, .card-content.is-active {
  background: #eee;
}
img {
  max-width: 100%;
}

.card-closeable {
  display: flex;
  justify-content: flex-end;
  &_trigger {
    cursor: pointer;
    display: inline-block;
  }
}
.card-top {
  align-self: flex-start;
}
@media screen and (max-width: 450px) {
  .main-row {
    .column {
      padding: 0;
    }
  }
}
@media screen and (max-width: 370px) {
  .info-col {
    width: 100%;
    flex: 1 1 auto;
  }
}
</style>
