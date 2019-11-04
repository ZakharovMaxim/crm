<template>
  <div>
    <div
      v-for="shop in shops"
      :key="'shop' + shop.id"
      class="shops"
    >
      <b-checkbox
        v-model="rules"
        :native-value="shop.id"
        @input="uncheckChildren(shop.id)"
      >
        {{ shop.name }}
      </b-checkbox>
      <div
        v-for="mod in modules"
        :key="'module' + mod.id"
        class="modules"
      >
        <b-checkbox
          v-model="rules"
          :native-value="shop.id + '.' + mod.id"
          :disabled="isDisabled(shop.id)"
          @input="uncheckChildren(shop.id + '.' + mod.id)"
        >
          {{ mod.name }}
        </b-checkbox>
        <div
          v-if="mod.rules"
          class="rules"
        >
          <div
            v-for="(rule, index) in mod.rules"
            :key="'rule' + rule.id + 'i_' + index"
          >
            <b-checkbox
              v-model="rules"
              :native-value="shop.id + '.' + mod.id + '.' + rule.id"
              :disabled="isDisabled(shop.id + '.' + mod.id)"
            >
              {{ rule.name }}
            </b-checkbox>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { MODULES_MODULE, SHOP_MODULE } from '@/store/modules/TYPES'
const { SHOPS } = SHOP_MODULE
const { ALL_MODULES } = MODULES_MODULE

export default {
  props: {
    role: {
      type: Object,
      default: null
    }
  },
  data () {
    return {
      rules: []
    }
  },
  computed: {
    ...mapState({
      shops: store => store.shopModule[SHOPS],
      modules: store => store.appModule[ALL_MODULES]
    }),
    shopsSelected () {
      return this.rules.filter(rule => (rule + '').split('.').length === 1)
    },
    modulesSelected () {
      return this.rules.filter(rule => (rule + '').split('.').length === 2)
    }
  },
  watch: {
    'rules': function () {
      this.$emit('change', {
        error: this.validate(),
        data: {
          shops: this.shopsSelected,
          modules: this.modulesSelected,
          rules: this.rules.filter(rule => (rule + '').split('.').length === 3)
        }
      })
    }
  },
  created () {
    if (this.role && this.role.shops && this.role.modules && this.role.rules) {
      this.rules = [
        ...this.role.shops,
        ...this.role.modules,
        ...this.role.rules
      ]
      return
    } else if (this.role) return
    this.shops.forEach(shop => {
      this.rules.push(shop.id)
      this.modules.forEach(mod => {
        this.rules.push(`${shop.id}.${mod.id}`)
        if (mod.rules) {
          mod.rules.forEach(rule => {
            this.rules.push(`${shop.id}.${mod.id}.${rule.id}`)
          })
        }
      })
    })
  },
  methods: {
    uncheckChildren (id) {
      if (!this.rules.includes(id)) {
        this.rules = this.rules.filter(rule => !(rule + '').startsWith(id + '.'))
      }
    },
    isDisabled (parentId) {
      return !this.rules.includes(parentId)
    },
    validate () {
      const shops = this.shopsSelected
      if (!shops.length) return 'Не выбран ни один магазин'
      const shopsWithoutModules = shops.filter(shop => {
        return !this.modulesSelected.some(mod => (mod + '').startsWith(`${shop}.`))
      })
      if (!shopsWithoutModules.length) return null
      const shopsNames = this.shops.filter(shop => shopsWithoutModules.includes(shop.id)).map(shop => shop.name)
      return `Магазин${shopsNames.length > 1 ? 'ы' : ''} ${shopsNames.join(', ')} не содержат модулей`
    }
  }
}
</script>

<style lang="scss" scoped>
  .modules {
    padding-left: 20px;
  }
  .rules {
    padding-left: 20px;
  }
</style>
