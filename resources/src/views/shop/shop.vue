<template>
  <GetData
    v-title="tabTitle"
    :callback="fetch"
    :update="update"
  >
    <div
      v-if="pageTitle"
      class="title tac"
    >
      {{ pageTitle }}
    </div>
    <AppForm
      :config="formConfig"
      :data="getFormData"
    />
    <b-button
      :loading="isLoading"
      :disabled="isDestroyLoading"
      @click="submit"
    >
      Сохранить
    </b-button>
    <ShopDestroy
      v-if="$route.params.id"
      :disabled="isLoading"
      :loading="isDestroyLoading"
      @submit="destroyConfirmed"
    />
  </GetData>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import { PLUGIN_MODULE, SHOP_MODULE } from '@/store/modules/TYPES'
import validations from '@/validations/shop'
import ShopDestroy from '@/components/shop/ShopDestroy'

const { GET_SHOP, UPDATE_SHOP, SHOP, DESTROY_SHOP, CREATE_SHOP, SET_SHOP } = SHOP_MODULE
const { PLUGINS } = PLUGIN_MODULE

export default {
  components: { ShopDestroy },
  data () {
    return {
      formConfig: validations(),
      isLoading: false,
      isDestroyLoading: false,
      update: false
    }
  },
  computed: {
    ...mapState({
      shop: store => store.shopModule[SHOP],
      plugins: store => store.pluginModule[PLUGINS]
    }),
    getFormData () {
      const displayName = (plugin) => `${plugin.name} #${plugin.id}`
      return {
        novaposhta_id: {
          items: this.getPlugins('np'),
          displayName
        },
        roistat_id: {
          list: this.getPlugins('roi'),
          displayName
        },
        turbosms_id: {
          list: this.getPlugins('turbo'),
          displayName
        }
      }
    },
    tabTitle () {
      if (this.shop && this.shop.name) {
        return `Магазин ${this.shop.name}`
      }
      return 'Создать новый магазин'
    },
    pageTitle () {
      return !this.$route.params.id ? 'Новый магазин' : ''
    },
    actionCompleted () {
      return this.$route.params.id ? 'обновлен' : 'создан'
    }
  },
  watch: {
    '$route' () {
      this.formConfig.form.reset()
      if (!this.$route.params.id) {
        this[SET_SHOP](null)
      } else {
        this.update = !this.update
      }
    }
  },
  methods: {
    ...mapActions([UPDATE_SHOP, GET_SHOP, DESTROY_SHOP, CREATE_SHOP, SET_SHOP]),
    getPlugins (type) {
      return this.plugins.filter(plugin => plugin.type === type)
    },
    fetch () {
      const { id } = this.$route.params
      return id ? this[GET_SHOP](id).then(response => {
        this.formConfig.form.setData(this.shop)
        return response
      }) : Promise.resolve()
    },
    async submit () {
      const data = this.formConfig.form.submit()
      if (!data) return
      const { id } = this.$route.params
      this.isLoading = true
      const actionName = id ? UPDATE_SHOP : CREATE_SHOP
      const { open } = this.$buefy.toast
      const { [actionName]: action, actionCompleted, formConfig } = this
      const { form } = formConfig
      const request = id ? {
        data,
        id
      } : data
      try {
        const { data } = await action(request)
        open(`Магазин ${actionCompleted}`)
        if (!id) {
          this.$router.push(`/shops/${data.id}`)
        }
      } catch (e) {
        form.setErrors(e)
      }
      this.isLoading = false
    },
    async destroyConfirmed () {
      const { id } = this.shop
      this.isDestroyLoading = true
      try {
        await this[DESTROY_SHOP](id)
        this.$buefy.toast.open('Магазин удален')
        this.$router.push(`/shops`)
      } catch (e) {
        this.$buefy.toast.open('Магазин не удален')
      }

      this.isDestroyLoading = false
    }
  }
}
</script>
