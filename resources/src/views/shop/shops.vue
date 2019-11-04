<template>
  <Layout>
    <template #sidebar-title>
      <span>
        <b-icon
          icon="store"
          size="is-small"
        />
        Магазины
      </span>
    </template>
    <template #sidebar>
      <GetData :callback="fetch">
        <div
          v-for="shop in shops"
          :key="shop.id"
        >
          <router-link
            :to="'/shops/' + shop.id"
            class="sidebar-link"
          >
            <span>
              <b-icon
                icon="store"
                size="is-small"
              />
              {{ shop.name }}
            </span>
          </router-link>
        </div>
      </GetData>
      <div>
        <router-link
          to="/shops"
          class="sidebar-link is-exact"
        >
          <span>
            <b-icon
              icon="store"
              size="is-small"
            />
            Создать новый магазин
          </span>
        </router-link>
      </div>
    </template>
    <template #content>
      <router-view />
    </template>
  </Layout>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import Layout from '@/components/Layout/Layout'
import { SHOP_MODULE } from '@/store/modules/TYPES'
const { GET_SHOPS, SHOPS } = SHOP_MODULE

export default {
  components: { Layout },
  data () {
    return {
      isLoading: false
    }
  },
  computed: {
    ...mapState({
      shops: state => state.shopModule[SHOPS]
    })
  },
  methods: {
    ...mapActions([GET_SHOPS]),
    fetch () {
      return this[GET_SHOPS]()
    }
  }
}
</script>

<style>

</style>
