<template>
  <GetData
    v-title="title"
    :callback="fetchData"
    :update="update"
  >
    <div>
      <b-button @click="isCreateOpen = true">
        <b-icon
          icon="folder"
        />
      </b-button>
      <b-button @click="isStateCreateOpen = true">
        <b-icon
          icon="plus"
        />
      </b-button>
      <create-payment-category
        :is-active="isCreateOpen"
        :type="$route.meta.type"
        @close="isCreateOpen = false"
      />
      <update-payment-category
        v-if="!!activeCategory"
        :is-active="!!activeCategory"
        :category="activeCategory"
        @close="activeCategory = null"
      />
      <paymentStatePopup
        v-if="!!activeState || isStateCreateOpen"
        :is-active="!!activeState || isStateCreateOpen"
        :state="activeState"
        :type="+$route.meta.type || 1"
        @close="() => { isStateCreateOpen = false; activeState = null }"
      />
    </div>
    <breadcrumbs
      :breadcrumbs="breadcrumbs"
    />
    <div
      v-if="categories.length || states.length"
      class="is-flex is-multiline"
    >
      <div
        v-for="category in categories"
        :key="'category_' + category.id"
        class="column is-4"
      >
        <Folder
          :id="category.id"
          :name="category.name"
          :parent-id="category.parent_id"
          remove-title="Удаление категории"
          remove-message="Вы действительно хотите удалить категорию?"
          label="категорию"
          @update="activeCategory = category"
          @click="setCategory(category.id)"
        />
      </div>
      <div
        v-for="state in states"
        :key="'state' + state.id"
        class="column is-3"
      >
        <Folder
          :id="state.id"
          :name="state.name"
          :parent-id="state.parent_id"
          remove-title="Удаление статьи"
          remove-message="Вы действительно хотите удалить статью?"
          label="статью"
          :icon="+state.type === 2 ? 'currency-usd-off' : 'currency-usd'"
          :removable="state.id !== 1"
          @click="activeState = state"
          @update="activeState = state"
        />
      </div>
    </div>
    <div
      v-if="!categories.length && !states.length"
      class="notice"
    >
      Вы еще не создали категорий
    </div>
  </GetData>
</template>

<script>
import { mapActions } from 'vuex'
import { PAYMENT_CATEGORY_MODULE, PAYMENT_STATE_MODULE } from '@/store/modules/TYPES'
import createPaymentCategory from '@/components/popups/CreatePaymentCategory'
import updatePaymentCategory from '@/components/popups/UpdatePaymentCategory'
import paymentStatePopup from '@/components/popups/paymentStatePopup'
import Folder from '@/components/Cards/Folder'
import Breadcrumbs from '@/components/layout/Breadcrumbs'
import TreeService from '@/helpers/Tree'

const { OUTCOME_PAYMENT_CATEGORIES, INCOME_PAYMENT_CATEGORIES } = PAYMENT_CATEGORY_MODULE
const { SET_PAYMENT_STATES, OUTCOME_PAYMENT_STATES, INCOME_PAYMENT_STATES } = PAYMENT_STATE_MODULE

export default {
  components: { Folder, createPaymentCategory, updatePaymentCategory, Breadcrumbs, paymentStatePopup },
  data () {
    return {
      update: false,
      isCreateOpen: false,
      activeCategory: null,
      isStateCreateOpen: false,
      activeState: null
    }
  },
  computed: {
    breadcrumbs () {
      return TreeService.getTreePath(this.parentId, this.allCategories, this.$route.path)
    },
    categories () {
      return this.allCategories.filter(cat => (+cat.parent_id || null) === this.parentId)
    },
    allCategories () {
      const type = +this.$route.meta.type || 1
      const action = type === 1 ? INCOME_PAYMENT_CATEGORIES : OUTCOME_PAYMENT_CATEGORIES
      return this.$store.getters[action]
    },
    parentId () {
      return +this.$route.query.parent_id || null
    },
    title () {
      const type = this.$route.meta.type || 1
      return +(type) === 1 ? 'Категории доходов' : 'Категории расходов'
    },
    states () {
      const type = +this.$route.meta.type || 1
      const action = type === 1 ? INCOME_PAYMENT_STATES : OUTCOME_PAYMENT_STATES
      return this.$store.getters[action]
    }
  },
  watch: {
    '$route': function () {
      this.update = !this.update
    }
  },
  methods: {
    ...mapActions([SET_PAYMENT_STATES]),
    setCategory (id) {
      this.$router.push({
        query: {
          parent_id: id
        }
      })
    },
    fetchData () {
      return this[SET_PAYMENT_STATES]({
        type: this.$route.meta.type || 1,
        parent_id: this.$route.query.parent_id
      })
    }
  }
}
</script>
