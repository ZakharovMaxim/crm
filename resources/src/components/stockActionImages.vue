<template>
  <div
    class="action-list"
    @click.prevent.stop
  >
    <div
      v-for="(product, i) in filteredProducts"
      :key="'image_' + product.id"
      class="action-img"
      :style="{'transform': `translateX(${i * 50}%)`}"
    >
      <router-link :to="`/catalogs/product/${product.id}`">
        <img
          :src="src(product)"
          :alt="product.name"
          :title="product.name"
        >
      </router-link>
    </div>
    <span
      v-if="isProductLengthOver"
      class="action-left"
      :style="{'transform': `translateX(${filteredProducts.length * 25}px)`}"
    >
      и еще {{ productsLeft }}
    </span>
  </div>
</template>

<script>
const MAX_LENGTH = 6
export default {
  props: {
    products: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    filteredProducts () {
      return this.products.slice(0, MAX_LENGTH)
    },
    productsLeft () {
      return this.products.length - MAX_LENGTH
    },
    isProductLengthOver () {
      return this.products.length > MAX_LENGTH
    }
  },
  methods: {
    src (product) {
      return product.images[0] ? product.images[0].url : '/noimageavailable.png'
    }
  }
}
</script>

<style scoped lang="scss">
.action-list {
  position: relative;
}
.action-left {
  display: inline-block;
  font-size: 12px;
  font-weight: bold;
}
.action-img {
  position: absolute;
  width: 25px;
  height: 25px;
  overflow: hidden;
  border-radius: 50%;
  border: 2px solid #eee;
  transition: all 200ms;
  background: #fff;
  &:hover {
    border-color: #555;
  }
  img {
    max-width: unset;
    height: 100%;
  }
}
</style>
